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
    // ----------------------------------------------------------------
    // GET /blast
    // ----------------------------------------------------------------
    public function index(): void
    {
        $userModel = new User();
        $allUsers  = $userModel->allWithPhone();

        $blast   = new Blast();
        $history = $blast->history(10);

        $configured = defined('WA_PHONE_NUMBER_ID') && WA_PHONE_NUMBER_ID !== ''
                   && defined('WA_ACCESS_TOKEN')    && WA_ACCESS_TOKEN    !== '';

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
        CSRF::check();

        if (!defined('WA_PHONE_NUMBER_ID') || WA_PHONE_NUMBER_ID === '') {
            Session::flash('error', 'WhatsApp API belum dikonfigurasi.');
            $this->redirect('/blast');
        }

        $selectedIds  = $_POST['recipients']   ?? [];
        $templateName = trim($_POST['template_name']  ?? 'ezkira_blast_v2');
        $customMsg    = trim($_POST['custom_message']  ?? '');
        $imageUrl     = trim($_POST['image_url']       ?? '');
        $blastLink    = trim($_POST['blast_link']       ?? '');

        if (empty($selectedIds)) {
            Session::flash('error', 'Pilih sekurang-kurangnya satu penerima.');
            $this->redirect('/blast');
        }

        if ($customMsg === '') {
            Session::flash('error', 'Mesej custom tidak boleh kosong.');
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

        $blastModel = new Blast();
        $blastId    = $blastModel->createLog(Auth::id(), $templateName, $customMsg, count($recipients));

        $sentCount   = 0;
        $failedCount = 0;

        foreach ($recipients as $user) {
            $phone  = $this->normalisePhone($user['whatsapp_number']);
            $name   = $user['name'] ?? 'Pelanggan';
            $result = $this->sendWaTemplate($phone, $templateName, $name, $customMsg, $imageUrl, $blastLink);

            if ($result['ok']) {
                $sentCount++;
                $blastModel->logRecipient($blastId, (int)$user['id'], $user['name'], $phone, 'sent', null);
            } else {
                $failedCount++;
                $blastModel->logRecipient($blastId, (int)$user['id'], $user['name'], $phone, 'failed', $result['error']);
            }

            usleep(200000); // 0.2s delay
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

    /** Normalise phone to E.164 */
    private function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '60' . substr($phone, 1);
        }
        return $phone;
    }

    /**
     * Send WhatsApp template message with dynamic parameters.
     *
     * Template structure (ezkira_blast_v2):
     *   Header : image  → {{image_url}}   (optional)
     *   Body   : {{1}}  = name
     *            {{2}}  = custom message
     *            {{3}}  = link            (optional)
     */
    private function sendWaTemplate(
        string $toPhone,
        string $templateName,
        string $name      = '',
        string $customMsg = '',
        string $imageUrl  = '',
        string $blastLink = ''
    ): array {
        $url = 'https://graph.facebook.com/v19.0/' . WA_PHONE_NUMBER_ID . '/messages';

        // Build body components
        $bodyParams = [
            ['type' => 'text', 'text' => $name ?: 'Pelanggan'],
            ['type' => 'text', 'text' => $customMsg ?: '-'],
        ];

        // {{3}} link — only add if provided
        if ($blastLink !== '') {
            $bodyParams[] = ['type' => 'text', 'text' => $blastLink];
        }

        $components = [
            [
                'type'       => 'body',
                'parameters' => $bodyParams,
            ],
        ];

        // Header image — only add if URL provided
        if ($imageUrl !== '') {
            array_unshift($components, [
                'type'       => 'header',
                'parameters' => [
                    ['type' => 'image', 'image' => ['link' => $imageUrl]],
                ],
            ]);
        }

        $payload = json_encode([
            'messaging_product' => 'whatsapp',
            'to'                => $toPhone,
            'type'              => 'template',
            'template'          => [
                'name'       => $templateName,
                'language'   => ['code' => 'ms'],
                'components' => $components,
            ],
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . WA_ACCESS_TOKEN,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return ['ok' => false, 'error' => 'cURL: ' . $curlErr];
        }

        $data = json_decode($response, true);

        if ($httpCode === 200 && isset($data['messages'][0]['id'])) {
            return ['ok' => true];
        }

        $errMsg = $data['error']['message'] ?? $response;
        return ['ok' => false, 'error' => substr($errMsg, 0, 300)];
    }
}
