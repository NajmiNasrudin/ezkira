<?php

namespace Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Session;
use Models\Blast;
use Models\User;

class BlastController extends Controller
{
    /** Abort with 403 if not admin */
    private function requireAdmin(): void
    {
        if (!Auth::hasRole('admin')) {
            http_response_code(403);
            include BASE_PATH . '/views/errors/403.php';
            exit;
        }
    }

    // ----------------------------------------------------------------
    // GET /blast
    // ----------------------------------------------------------------
    public function index(): void
    {
        $this->requireAdmin();

        $userModel = new User();
        $allUsers  = $userModel->allWithPhone();

        $blast   = new Blast();
        $history = $blast->history(10);

        $configured = defined('FONNTE_TOKEN') && FONNTE_TOKEN !== '';

        $this->view('blast/index', [
            'allUsers'   => $allUsers,
            'history'    => $history,
            'configured' => $configured,
        ], 'main', 'WhatsApp Blast');
    }

    // ----------------------------------------------------------------
    // POST /blast/send
    // ----------------------------------------------------------------
    public function send(): void
    {
        $this->requireAdmin();
        CSRF::check();

        if (!defined('FONNTE_TOKEN') || FONNTE_TOKEN === '') {
            Session::flash('error', 'Fonnte API belum dikonfigurasi. Sila tambah FONNTE_TOKEN dalam config.php');
            $this->redirect('/blast');
        }

        $selectedIds = $_POST['recipients']    ?? [];
        $customMsg   = trim($_POST['custom_message'] ?? '');
        $imageUrl    = trim($_POST['image_url']       ?? '');
        $blastLink   = trim($_POST['blast_link']       ?? '');

        if (empty($selectedIds)) {
            Session::flash('error', 'Pilih sekurang-kurangnya satu penerima.');
            $this->redirect('/blast');
        }

        if ($customMsg === '') {
            Session::flash('error', 'Mesej tidak boleh kosong.');
            $this->redirect('/blast');
        }

        $userModel = new User();

        if (in_array('all', $selectedIds, true)) {
            $recipients = $userModel->allWithPhone();
        } else {
            $recipients = $userModel->findManyByIds(array_map('intval', $selectedIds));
        }

        $recipients = array_filter($recipients, fn($u) => !empty($u['whatsapp_number']));

        if (empty($recipients)) {
            Session::flash('error', 'Tiada penerima yang mempunyai nombor WhatsApp.');
            $this->redirect('/blast');
        }

        // Build full message
        $message = $customMsg;
        if ($blastLink !== '') {
            $message .= "\n\n" . $blastLink;
        }

        $blastModel = new Blast();
        $blastId    = $blastModel->createLog(Auth::id(), 'fonnte', $customMsg, count($recipients));

        $sentCount   = 0;
        $failedCount = 0;

        foreach ($recipients as $user) {
            $phone      = $this->normalisePhone($user['whatsapp_number']);
            $fullMsg    = "Hai {$user['name']},\n\n" . $message;
            $result     = $this->sendFonnte($phone, $fullMsg, $imageUrl);

            if ($result['ok']) {
                $sentCount++;
                $blastModel->logRecipient($blastId, (int)$user['id'], $user['name'], $phone, 'sent', null);
            } else {
                $failedCount++;
                $blastModel->logRecipient($blastId, (int)$user['id'], $user['name'], $phone, 'failed', $result['error']);
            }

            usleep(500000); // 0.5s delay — Fonnte free tier limit
        }

        $blastModel->updateLog($blastId, $sentCount, $failedCount);

        Session::flash('success', "Blast selesai — {$sentCount} berjaya, {$failedCount} gagal.");
        $this->redirect('/blast');
    }

    // ----------------------------------------------------------------
    // GET /blast/{id}/recipients  (AJAX — returns JSON)
    // ----------------------------------------------------------------
    public function recipients(string $id): void
    {
        $this->requireAdmin();
        $blast = new Blast();
        $log   = $blast->findLog((int)$id);
        if (!$log) {
            $this->json(['error' => 'Not found'], 404);
        }
        $this->json([
            'log'        => $log,
            'recipients' => $blast->recipients((int)$id),
        ]);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '60' . substr($phone, 1);
        }
        return $phone;
    }

    /** Send message via Fonnte API */
    private function sendFonnte(string $toPhone, string $message, string $imageUrl = ''): array
    {
        $url = 'https://api.fonnte.com/send';

        $data = [
            'target'      => $toPhone,
            'message'     => $message,
            'countryCode' => '60',
        ];

        if ($imageUrl !== '') {
            $data['url'] = $imageUrl;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_HTTPHEADER     => [
                'Authorization: ' . FONNTE_TOKEN,
            ],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return ['ok' => false, 'error' => 'cURL: ' . $curlErr];
        }

        $data = json_decode($response, true);

        if (!empty($data['status']) && $data['status'] === true) {
            return ['ok' => true];
        }

        $errMsg = $data['reason'] ?? $response;
        return ['ok' => false, 'error' => substr($errMsg, 0, 300)];
    }
}
