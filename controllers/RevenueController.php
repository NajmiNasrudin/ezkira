<?php

namespace Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Session;
use Models\Capital;
use Models\Revenue;

class RevenueController extends Controller
{
    public function index(): void
    {
        $userId = Auth::id();
        $model  = new Revenue();
        $year   = (int)($_GET['year']  ?? date('Y'));
        $month  = (int)($_GET['month'] ?? date('n'));

        $month = max(1, min(12, $month));
        $year  = max(2020, min((int)date('Y') + 1, $year));

        $target    = $model->getTarget($year, $month, $userId);
        $entries   = $model->byPeriod('monthly', $year, $month, $userId);
        $total     = $model->totalByPeriod('monthly', $year, $month, $userId);
        $platforms = $model->platformBreakdown('monthly', $year, $month, $userId);
        $daily     = $model->dailyTotals($year, $month, $userId);

        $pct = $target > 0 ? min(100, ($total / $target) * 100) : 0;

        $capitalModel   = new Capital();
        $capitalEntries = $capitalModel->byMonth($year, $month, $userId);
        $capitalTotal   = $capitalModel->totalByMonth($year, $month, $userId);

        $this->view('revenue/index', [
            'year'           => $year,
            'month'          => $month,
            'target'         => $target,
            'total'          => $total,
            'pct'            => $pct,
            'entries'        => $entries,
            'platforms'      => $platforms,
            'daily'          => $daily,
            'platforms_list' => Revenue::PLATFORMS,
            'capitalEntries' => $capitalEntries,
            'capitalTotal'   => $capitalTotal,
        ], 'main', __('revenue'));
    }

    public function store(): void
    {
        CSRF::check();

        $platform       = $_POST['platform']        ?? 'other';
        $platformCustom = trim($_POST['platform_custom'] ?? '');
        $amount         = trim($_POST['amount'] ?? '');
        $description    = trim($_POST['description'] ?? '');
        $date           = $_POST['sale_date']   ?? date('Y-m-d');

        // If "Other" chosen and custom name provided, store the custom name directly
        if ($platform === 'other' && $platformCustom !== '') {
            $platform = substr(htmlspecialchars($platformCustom, ENT_QUOTES, 'UTF-8'), 0, 100);
        } elseif (!array_key_exists($platform, Revenue::PLATFORMS)) {
            $platform = 'other';
        }

        $entryType     = in_array($_POST['entry_type'] ?? '', Revenue::ENTRY_TYPES, true)
            ? $_POST['entry_type'] : 'sale';
        $paymentMethod = array_key_exists($_POST['payment_method'] ?? '', Revenue::PAYMENT_METHODS)
            ? $_POST['payment_method'] : 'cash';

        if (!is_numeric($amount) || (float)$amount <= 0) {
            Session::flash('error', __('revenue_amount_invalid'));
            $this->redirect('/revenue');
        }

        (new Revenue())->create([
            'user_id'        => Auth::id(),
            'platform'       => $platform,
            'entry_type'     => $entryType,
            'payment_method' => $paymentMethod,
            'amount'         => (float)$amount,
            'description'    => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'sale_date'      => $date,
        ]);

        $y = date('Y', strtotime($date));
        $m = date('n', strtotime($date));

        Session::flash('success', __('revenue_added'));
        $this->redirect("/revenue?year={$y}&month={$m}");
    }

    public function update(string $id): void
    {
        CSRF::check();

        $model = new Revenue();
        $entry = $model->findById((int)$id);
        if (!$entry || (int)$entry['user_id'] !== Auth::id()) {
            Session::flash('error', 'Rekod tidak dijumpai.');
            $this->redirect('/revenue');
        }

        $platform       = $_POST['platform']         ?? 'other';
        $platformCustom = trim($_POST['platform_custom'] ?? '');
        $amount         = trim($_POST['amount']       ?? '');
        $description    = trim($_POST['description']  ?? '');
        $date           = $_POST['sale_date']         ?? $entry['sale_date'];
        $year           = $_POST['year']              ?? date('Y');
        $month          = $_POST['month']             ?? date('n');
        $entryType      = in_array($_POST['entry_type'] ?? '', Revenue::ENTRY_TYPES, true)
            ? $_POST['entry_type'] : 'sale';
        $paymentMethod  = array_key_exists($_POST['payment_method'] ?? '', Revenue::PAYMENT_METHODS)
            ? $_POST['payment_method'] : 'cash';

        if ($platform === 'other' && $platformCustom !== '') {
            $platform = substr(htmlspecialchars($platformCustom, ENT_QUOTES, 'UTF-8'), 0, 100);
        } elseif (!array_key_exists($platform, Revenue::PLATFORMS)) {
            $platform = 'other';
        }

        if (!is_numeric($amount) || (float)$amount <= 0) {
            Session::flash('error', __('revenue_amount_invalid'));
            $this->redirect("/revenue?year={$year}&month={$month}");
        }

        $model->update((int)$id, [
            'platform'       => $platform,
            'entry_type'     => $entryType,
            'payment_method' => $paymentMethod,
            'amount'         => (float)$amount,
            'description'    => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'sale_date'      => $date,
            'user_id'        => Auth::id(),
        ]);

        Session::flash('success', __('revenue_updated'));
        $this->redirect("/revenue?year={$year}&month={$month}");
    }

    public function delete(string $id): void
    {
        CSRF::check();

        $entry = (new Revenue())->findById((int)$id);
        if (!$entry || (int)$entry['user_id'] !== Auth::id()) {
            Session::flash('error', 'Rekod tidak dijumpai.');
            $this->redirect('/revenue');
        }

        (new Revenue())->delete((int)$id);
        Session::flash('success', __('revenue_deleted'));
        $this->redirect('/revenue');
    }

    public function setTarget(): void
    {
        CSRF::check();

        $year   = (int)($_POST['year']   ?? date('Y'));
        $month  = (int)($_POST['month']  ?? date('n'));
        $amount = trim($_POST['target_amount'] ?? '');

        if (!is_numeric($amount) || (float)$amount < 0) {
            Session::flash('error', __('revenue_target_invalid'));
            $this->redirect('/revenue');
        }

        (new Revenue())->setTarget($year, $month, (float)$amount, Auth::id());
        Session::flash('success', __('revenue_target_saved'));
        $this->redirect("/revenue?year={$year}&month={$month}");
    }

    public function exportPnl(): void
    {
        $userId = Auth::id();
        $period = $_GET['period'] ?? 'monthly';
        $year   = (int)($_GET['year']  ?? date('Y'));
        $month  = (int)($_GET['month'] ?? date('n'));
        $week   = (int)($_GET['week']  ?? date('W'));

        $user        = Auth::user();
        $companyName = $user['name']     ?? 'My Business';
        $picName     = $user['pic_name'] ?? '';

        $revenue       = new Revenue();
        $revTotal      = $revenue->totalByPeriod($period, $year, $month, $userId, $week);
        $revByPlatform = $revenue->platformBreakdown($period, $year, $month, $userId, $week);

        $expense   = new \Models\Expense();
        $opex      = $expense->totalByCategory('opex',      $userId);
        $marketing = $expense->totalByCategory('marketing', $userId);
        $cogs      = $expense->totalByCategory('cogs',      $userId);
        $totalExp  = $opex + $marketing + $cogs;
        $netProfit = $revTotal - $totalExp;

        $periodLabel = match($period) {
            'annual'  => "Year {$year}",
            'weekly'  => "Week {$week}, {$year}",
            'daily'   => date('d M Y'),
            default   => date('F Y', mktime(0,0,0,$month,1,$year)),
        };

        $filename = 'PnL_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $companyName) . '_' . str_replace([' ', ','], ['_', ''], $periodLabel) . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($out, [$companyName . ' — Profit & Loss Statement']);
        fputcsv($out, ['Period:', $periodLabel]);
        if ($picName !== '') fputcsv($out, ['Prepared by:', $picName]);
        fputcsv($out, ['Generated:', date('d M Y, H:i')]);
        fputcsv($out, ['Powered by:', APP_NAME]);
        fputcsv($out, []);

        fputcsv($out, ['REVENUE']);
        fputcsv($out, ['Platform', 'Amount (RM)']);
        foreach ($revByPlatform as $row) {
            $label = Revenue::PLATFORMS[$row['platform']] ?? ucfirst($row['platform']);
            fputcsv($out, [$label, number_format((float)$row['total'], 2)]);
        }
        fputcsv($out, ['TOTAL REVENUE', number_format($revTotal, 2)]);
        fputcsv($out, []);

        fputcsv($out, ['EXPENSES']);
        fputcsv($out, ['Category', 'Amount (RM)']);
        fputcsv($out, ['OPEX',               number_format($opex, 2)]);
        fputcsv($out, ['Marketing Expenses', number_format($marketing, 2)]);
        fputcsv($out, ['COGS',               number_format($cogs, 2)]);
        fputcsv($out, ['TOTAL EXPENSES',     number_format($totalExp, 2)]);
        fputcsv($out, []);

        fputcsv($out, ['NET PROFIT', number_format($netProfit, 2)]);
        fputcsv($out, ['PROFIT MARGIN', $revTotal > 0 ? number_format(($netProfit / $revTotal) * 100, 2) . '%' : 'N/A']);

        fclose($out);
        exit;
    }

    // ----------------------------------------------------------------
    // Capital
    // ----------------------------------------------------------------

    public function storeCapital(): void
    {
        CSRF::check();

        $amount      = trim($_POST['amount']      ?? '');
        $description = trim($_POST['description'] ?? '');
        $date        = $_POST['capital_date']     ?? date('Y-m-d');
        $year        = (int)($_POST['year']       ?? date('Y'));
        $month       = (int)($_POST['month']      ?? date('n'));

        if (!is_numeric($amount) || (float)$amount <= 0) {
            Session::flash('error', __('revenue_amount_invalid'));
            $this->redirect("/revenue?year={$year}&month={$month}");
        }

        (new Capital())->create([
            'user_id'     => Auth::id(),
            'amount'      => (float)$amount,
            'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'capital_date'=> $date,
        ]);

        Session::flash('success', __('capital_added'));
        $this->redirect("/revenue?year={$year}&month={$month}#capital");
    }

    public function updateCapital(string $id): void
    {
        CSRF::check();

        $model  = new Capital();
        $entry  = $model->findById((int)$id);
        if (!$entry || (int)$entry['user_id'] !== Auth::id()) {
            Session::flash('error', 'Rekod tidak dijumpai.');
            $this->redirect('/revenue');
        }

        $amount      = trim($_POST['amount']      ?? '');
        $description = trim($_POST['description'] ?? '');
        $date        = $_POST['capital_date']     ?? $entry['capital_date'];
        $year        = (int)($_POST['year']       ?? date('Y'));
        $month       = (int)($_POST['month']      ?? date('n'));

        if (!is_numeric($amount) || (float)$amount <= 0) {
            Session::flash('error', __('revenue_amount_invalid'));
            $this->redirect("/revenue?year={$year}&month={$month}");
        }

        $model->update((int)$id, [
            'amount'      => (float)$amount,
            'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'capital_date'=> $date,
            'user_id'     => Auth::id(),
        ]);

        Session::flash('success', __('capital_updated'));
        $this->redirect("/revenue?year={$year}&month={$month}#capital");
    }

    public function deleteCapital(string $id): void
    {
        CSRF::check();

        $entry = (new Capital())->findById((int)$id);
        if (!$entry || (int)$entry['user_id'] !== Auth::id()) {
            Session::flash('error', 'Rekod tidak dijumpai.');
            $this->redirect('/revenue');
        }

        (new Capital())->delete((int)$id);
        Session::flash('success', __('capital_deleted'));
        $this->redirect('/revenue');
    }
}
