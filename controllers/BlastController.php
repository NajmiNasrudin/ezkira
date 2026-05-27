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
        $blastLink   = trim($_POST['blast_link']       ?? '');

        // Handle optional image upload
        $imageUrl = '';
        if (!empty($_FILES['blast_image']['tmp_name']) && $_FILES['blast_image']['error'] === UPLOAD_ERR_OK) {
            $imageUrl = $this->handleImageUpload($_FILES['blast_image']);
            if ($imageUrl === null) {
                Session::flash('error', 'Format atau saiz gambar tidak sah. Gunakan JPG/PNG/WebP, max 2MB.');
                $this->redirect('/blast');
            }
        }

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
    // GET /blast/media/{filename}  — public, no auth, serves blast images
    // Used so Fonnte can fetch images with proper headers
    // ----------------------------------------------------------------
    public function serveMedia(string $filename): void
    {
        // Sanitise — only allow safe filenames (hex + extension)
        if (!preg_match('/^[a-f0-9]{32}\.(jpg|jpeg|png|webp)$/i', $filename)) {
            http_response_code(404);
            exit;
        }

        $path = BASE_PATH . '/uploads/blast/' . $filename;

        if (!file_exists($path)) {
            http_response_code(404);
            exit;
        }

        $mime = mime_content_type($path) ?: 'image/jpeg';

        header('Content-Type: '        . $mime);
        header('Content-Length: '      . filesize($path));
        header('Access-Control-Allow-Origin: *');
        header('Cache-Control: public, max-age=86400');
        header('X-Content-Type-Options: nosniff');

        readfile($path);
        exit;
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /**
     * Validate and save a blast image upload.
     * Returns the public URL on success, or null on failure.
     */
    private function handleImageUpload(array $file): ?string
    {
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedExt  = ['jpg' => 'jpg', 'jpeg' => 'jpg', 'png' => 'png', 'webp' => 'webp'];
        $maxSize     = 2 * 1024 * 1024; // 2 MB

        if ($file['size'] > $maxSize) {
            return null;
        }

        // MIME check via finfo (not trusting $_FILES['type'])
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowedMime, true)) {
            return null;
        }

        $origExt  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeExt  = $allowedExt[$origExt] ?? null;

        // Fall back to MIME-derived extension if original extension is dodgy
        if ($safeExt === null) {
            $mimeMap  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $safeExt  = $mimeMap[$mime] ?? null;
        }

        if ($safeExt === null) {
            return null;
        }

        $uploadDir = BASE_PATH . '/uploads/blast/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $safeExt;
        $destPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return null;
        }

        // Auto-upload to ImgBB so Fonnte can reliably fetch the image
        if (defined('IMGBB_API_KEY') && IMGBB_API_KEY !== '') {
            $imgbbUrl = $this->uploadToImgBB($destPath);
            if ($imgbbUrl) {
                return $imgbbUrl;
            }
        }

        // Fallback: serve via PHP media route
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? parse_url(APP_URL, PHP_URL_HOST);
        return $scheme . '://' . $host . BASE_URI . '/blast/media/' . $filename;
    }

    /** Upload image to ImgBB CDN and return the hosted URL */
    private function uploadToImgBB(string $localPath): ?string
    {
        $ch = curl_init('https://api.imgbb.com/1/upload?key=' . urlencode(IMGBB_API_KEY));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => ['image' => base64_encode(file_get_contents($localPath))],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['data']['url'] ?? null;
    }

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
