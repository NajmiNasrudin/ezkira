<?php

namespace Controllers;

use App\Core\Auth;
use App\Core\Chip;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Logger;
use App\Core\Plan;
use App\Core\Schema;
use App\Core\Session;
use Models\Billing;

class BillingController extends Controller
{
    public function pricing(): void
    {
        $userId = (int) Auth::id();
        $plan   = Plan::status($userId);

        $payments = [];
        if ($plan['available']) {
            $payments = (new Billing())->paidPayments($userId);
        }

        $this->view('billing/pricing', [
            'plan'         => $plan,
            'payments'     => $payments,
            'checkoutOpen' => Chip::configured() && $plan['available'],
        ], 'main', __('pricing_title'));
    }

    public function checkout(): void
    {
        CSRF::check();

        $interval = $_POST['interval'] ?? '';
        if (!isset(Plan::PRICES[$interval])) {
            $this->redirect('/pricing');
        }
        if (!Chip::configured() || !Schema::ensureBilling()) {
            Session::flash('error', __('billing_not_ready'));
            $this->redirect('/pricing');
        }

        $user      = Auth::user();
        $amount    = Plan::PRICES[$interval];
        $billing   = new Billing();
        $paymentId = $billing->createPayment((int) $user['id'], $interval, $amount);
        $appUrl    = rtrim(APP_URL, '/');

        try {
            $purchase = Chip::createPurchase([
                'reference' => 'EZK-' . $paymentId,
                'client'    => [
                    'email'     => $user['email'],
                    'full_name' => mb_substr((string) ($user['name'] ?? ''), 0, 128),
                ],
                'purchase'  => [
                    'currency' => 'MYR',
                    'products' => [[
                        'name'  => $interval === 'yearly' ? 'ezkira Pro (1 tahun)' : 'ezkira Pro (1 bulan)',
                        'price' => $amount,
                    ]],
                ],
                'success_redirect' => $appUrl . '/billing/return?payment=' . $paymentId,
                'failure_redirect' => $appUrl . '/billing/return?payment=' . $paymentId,
                'cancel_redirect'  => $appUrl . '/pricing',
                'success_callback' => $appUrl . '/billing/chip-callback',
            ]);
        } catch (\Throwable $e) {
            error_log('CHIP checkout failed for payment ' . $paymentId . ': ' . $e->getMessage());
            $billing->markFailed($paymentId);
            Session::flash('error', __('billing_checkout_failed'));
            $this->redirect('/pricing');
        }

        if (empty($purchase['id']) || empty($purchase['checkout_url'])) {
            error_log('CHIP checkout returned no checkout_url for payment ' . $paymentId);
            $billing->markFailed($paymentId);
            Session::flash('error', __('billing_checkout_failed'));
            $this->redirect('/pricing');
        }

        $billing->setGatewayRef($paymentId, (string) $purchase['id']);
        Logger::log('billing_checkout', (int) $user['id'], "Checkout started: {$interval}, payment #{$paymentId}");

        header('Location: ' . $purchase['checkout_url']);
        exit;
    }

    /** Where CHIP sends the buyer's browser back after paying (or failing). */
    public function returnFromChip(): void
    {
        $paymentId = (int) ($_GET['payment'] ?? 0);
        $billing   = Schema::ensureBilling() ? new Billing() : null;
        $payment   = $billing?->findPayment($paymentId);

        if (!$payment || (int) $payment['user_id'] !== (int) Auth::id() || empty($payment['gateway_ref'])) {
            $this->redirect('/pricing');
        }

        try {
            $result = $this->settle($payment);
        } catch (\Throwable $e) {
            error_log('CHIP return check failed for payment ' . $paymentId . ': ' . $e->getMessage());
            $result = 'pending';
        }

        match ($result) {
            'paid'   => Session::flash('success', __('billing_paid_success')),
            'failed' => Session::flash('error', __('billing_payment_failed')),
            default  => Session::flash('info', __('billing_payment_pending')),
        };
        $this->redirect('/pricing');
    }

    /**
     * Server-to-server notice from CHIP (success_callback). The body is not trusted:
     * the purchase is re-fetched from CHIP with our secret key before anything changes.
     */
    public function chipCallback(): void
    {
        $body = json_decode((string) file_get_contents('php://input'), true);
        $ref  = is_array($body) ? (string) ($body['id'] ?? '') : '';

        if ($ref === '' || !Chip::configured() || !Schema::ensureBilling()) {
            http_response_code(200);
            exit('ignored');
        }

        $payment = (new Billing())->findPaymentByRef($ref);
        if (!$payment) {
            http_response_code(200);
            exit('unknown');
        }

        try {
            $this->settle($payment);
        } catch (\Throwable $e) {
            error_log('CHIP callback failed for payment ' . $payment['id'] . ': ' . $e->getMessage());
            http_response_code(500); // CHIP retries non-2xx responses
            exit('retry');
        }

        http_response_code(200);
        exit('ok');
    }

    /** @return string 'paid' | 'failed' | 'pending' */
    private function settle(array $payment): string
    {
        if ($payment['status'] === 'paid') {
            return 'paid';
        }

        $purchase = Chip::getPurchase((string) $payment['gateway_ref']);
        $status   = (string) ($purchase['status'] ?? '');
        $isTest   = !empty($purchase['is_test']);

        if (($purchase['id'] ?? '') !== $payment['gateway_ref']
            || ($purchase['reference'] ?? '') !== 'EZK-' . $payment['id']) {
            error_log('CHIP purchase mismatch for payment ' . $payment['id']);
            return 'pending';
        }

        if (in_array($status, Chip::PAID_STATUSES, true)) {
            if ($isTest && !Chip::testMode()) {
                error_log('Ignored CHIP test-mode purchase for payment ' . $payment['id'] . ' (CHIP_TEST_MODE is off)');
                return 'pending';
            }

            $periodEnd = null;
            $activated = (new Billing())->activatePayment(
                (int) $payment['id'], $isTest, [Plan::class, 'periodFor'], $periodEnd
            );
            if ($activated) {
                Plan::forget((int) $payment['user_id']);
                Logger::log('billing_paid', (int) $payment['user_id'],
                    "Payment #{$payment['id']} paid ({$payment['plan_interval']}), Pro until {$periodEnd}");
            }
            return 'paid';
        }

        if (in_array($status, ['error', 'cancelled', 'expired', 'blocked'], true)) {
            (new Billing())->markFailed((int) $payment['id']);
            return 'failed';
        }

        return 'pending';
    }
}
