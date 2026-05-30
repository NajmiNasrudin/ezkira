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

        $configured = (defined('FONNTE_TOKEN')     && FONNTE_TOKEN     !== '')
                   || (defined('WASENDER_API_KEY') && WASENDER_API_KEY !== '');

        // Clone: pre-fill form from existing blast
        $cloneData = null;
        if (!empty($_GET['clone']) && ctype_digit((string)$_GET['clone'])) {
            $src = $blast->findLog((int)$_GET['clone']);
            if ($src) {
                $msgs = json_decode($src['custom_message'] ?? '', true);
                if (!is_array($msgs)) {
                    $msgs = [$src['custom_message'] ?? ''];
                }
                $imgs = json_decode($src['image_path'] ?? '', true);
                if (!is_array($imgs)) {
                    $imgs = ($src['image_path'] !== '') ? [$src['image_path']] : [];
                }
                $cloneData = [
                    'blast_id'      => (int)$src['id'],
                    'messages'      => array_values(array_filter($msgs)),
                    'images'        => array_values(array_filter($imgs)),
                    'blast_link'    => $src['blast_link']    ?? '',
                    'provider'      => $src['provider']      ?? 'fonnte',
                    'delay_seconds' => (int)($src['delay_seconds'] ?? 30),
                ];
            }
        }

        $this->view('blast/index', [
            'allUsers'     => $allUsers,
            'history'      => $history,
            'configured'   => $configured,
            'businessTypes'=> \Models\User::BUSINESS_TYPES,
            'cloneData'    => $cloneData,
        ], 'main', 'WhatsApp Blast');
    }

    // ----------------------------------------------------------------
    // POST /blast/send  — save blast to queue, redirect to progress page
    // ----------------------------------------------------------------
    public function send(): void
    {
        $this->requireAdmin();
        CSRF::check();

        $selectedIds = $_POST['recipients']     ?? [];
        $customMsg   = trim($_POST['custom_message'] ?? '');
        $blastLink   = trim($_POST['blast_link']       ?? '');
        $scheduledAt = trim($_POST['scheduled_at']     ?? '');

        // Validate chosen provider is configured
        $provider = in_array($_POST['provider'] ?? '', ['fonnte', 'wasenderapi', 'whatsapp_api'], true)
                    ? $_POST['provider'] : 'fonnte';

        $providerConfigured = match($provider) {
            'wasenderapi'  => defined('WASENDER_API_KEY') && WASENDER_API_KEY !== '',
            'whatsapp_api' => defined('WA_PHONE_NUMBER_ID') && WA_PHONE_NUMBER_ID !== '',
            default        => defined('FONNTE_TOKEN') && FONNTE_TOKEN !== '',
        };

        if (!$providerConfigured) {
            $hint = match($provider) {
                'wasenderapi'  => 'Sila tambah WASENDER_API_KEY dalam config.php',
                'whatsapp_api' => 'Sila tambah WA_PHONE_NUMBER_ID dan WA_ACCESS_TOKEN dalam config.php',
                default        => 'Sila tambah FONNTE_TOKEN dalam config.php',
            };
            Session::flash('error', ucfirst($provider) . ' belum dikonfigurasi. ' . $hint);
            $this->redirect('/blast');
        }

        // Delay preset: 8 | 12 | 30 | 60 (default: 30 = Sangat Selamat)
        $allowedDelays = [8, 12, 30, 60];
        $delaySecs     = in_array((int)($_POST['delay_seconds'] ?? 30), $allowedDelays, true)
                         ? (int)$_POST['delay_seconds'] : 30;

        // Handle up to 3 image uploads — store as JSON array
        // Falls back to existing_image_X (basename only) when cloning without a new upload
        $imageFiles = [];
        for ($i = 1; $i <= 3; $i++) {
            $key = 'blast_image_' . $i;
            if (!empty($_FILES[$key]['tmp_name']) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                // New upload — validate and save
                $path = $this->handleImageUpload($_FILES[$key]);
                if ($path === null) {
                    Session::flash('error', "Format atau saiz gambar {$i} tidak sah. Gunakan JPG/PNG/WebP, max 2MB.");
                    $this->redirect('/blast');
                }
                $imageFiles[] = $path;
            } elseif (!empty($_POST['existing_image_' . $i])) {
                // Reuse existing image from a clone — validate only basenames (no path traversal)
                $base = basename(trim($_POST['existing_image_' . $i]));
                if (preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/i', $base)) {
                    $full = BASE_PATH . '/uploads/blast/' . $base;
                    if (file_exists($full)) {
                        $imageFiles[] = $full;
                    }
                }
            }
        }
        $imagePathJson = count($imageFiles) > 0 ? json_encode($imageFiles) : '';

        // Handle up to 3 message variations — store as JSON array
        $messages = [];
        for ($i = 1; $i <= 3; $i++) {
            $msg = trim($_POST['custom_message_' . $i] ?? '');
            if ($msg !== '') $messages[] = $msg;
        }

        if (empty($selectedIds)) {
            Session::flash('error', 'Pilih sekurang-kurangnya satu penerima.');
            $this->redirect('/blast');
        }

        if (empty($messages)) {
            Session::flash('error', 'Mesej tidak boleh kosong.');
            $this->redirect('/blast');
        }

        $customMsg     = count($messages) === 1 ? $messages[0] : json_encode($messages);

        $userModel = new User();

        // Resolve recipient list to get accurate total count
        if (in_array('all', $selectedIds, true)) {
            $recipientIds = json_encode(['all']);
            $total        = count($userModel->allWithPhone());
        } else {
            $ids          = array_map('intval', $selectedIds);
            $recipientIds = json_encode($ids);
            $recipients   = $userModel->findManyByIds($ids);
            $recipients   = array_filter($recipients, fn($u) => !empty($u['whatsapp_number']));
            $total        = count($recipients);
        }

        if ($total === 0) {
            Session::flash('error', 'Tiada penerima yang mempunyai nombor WhatsApp.');
            $this->redirect('/blast');
        }

        // Normalise scheduled_at — ignore past dates
        $scheduledAtNorm = null;
        if ($scheduledAt !== '' && strtotime($scheduledAt) > time()) {
            $scheduledAtNorm = date('Y-m-d H:i:s', strtotime($scheduledAt));
        }

        $blastModel = new Blast();
        $blastId    = $blastModel->queue(
            Auth::id(),
            $customMsg,
            $recipientIds,
            $total,
            $imagePathJson,
            $blastLink,
            $scheduledAtNorm,
            $provider,
            $delaySecs
        );

        // Try to trigger cron immediately (Linux only; falls back to cPanel cron)
        $this->triggerCron();

        if ($scheduledAtNorm) {
            Session::flash('success', 'Blast dijadualkan untuk ' . date('d M Y, H:i', strtotime($scheduledAtNorm)));
            $this->redirect('/blast');
        }

        $this->redirect('/blast/' . $blastId . '/progress');
    }

    // ----------------------------------------------------------------
    // GET /blast/{id}/progress — progress tracking page
    // ----------------------------------------------------------------
    public function progress(string $id): void
    {
        $this->requireAdmin();
        $blast = new Blast();
        $log   = $blast->findLog((int)$id);

        if (!$log) {
            http_response_code(404);
            include BASE_PATH . '/views/errors/404.php';
            exit;
        }

        $this->view('blast/progress', ['log' => $log], 'main', 'Blast Progress');
    }

    // ----------------------------------------------------------------
    // POST /blast/{id}/stop  — stop a running/queued blast
    // ----------------------------------------------------------------
    public function stop(string $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        $blast = new Blast();
        $log   = $blast->findLog((int)$id);

        if (!$log) {
            $this->json(['ok' => false, 'message' => 'Blast not found'], 404);
            return;
        }

        if (!in_array($log['status'], ['running', 'queued', 'scheduled'], true)) {
            $this->json(['ok' => false, 'message' => 'Blast cannot be stopped (status: ' . $log['status'] . ')']);
            return;
        }

        $blast->markStopped((int)$id, (int)$log['sent_count'], (int)$log['failed_count']);

        \App\Core\Logger::log('blast_stop', \App\Core\Auth::id(), "Blast #{$id} stopped manually");

        $this->json(['ok' => true, 'message' => 'Blast dihentikan.']);
    }

    // ----------------------------------------------------------------
    // GET /blast/{id}/status  — AJAX JSON progress
    // ----------------------------------------------------------------
    public function statusJson(string $id): void
    {
        $this->requireAdmin();
        $blast    = new Blast();
        $progress = $blast->getProgress((int)$id);

        if (!$progress) {
            $this->json(['error' => 'Not found'], 404);
            return;
        }

        // Compute ETA when blast is running
        if ($progress['status'] === 'running' && $progress['started_at']) {
            $elapsed   = time() - strtotime($progress['started_at']);
            $done      = (int)$progress['sent_count'] + (int)$progress['failed_count'];
            $remaining = (int)$progress['total_recipients'] - $done;
            $rate      = $done > 0 ? ($elapsed / $done) : 5; // seconds per message
            $progress['eta_seconds'] = max(0, (int)($remaining * $rate));
        }

        $this->json($progress);
    }

    // ----------------------------------------------------------------
    // GET /blast/{id}/recipients  (AJAX — returns JSON for modal)
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
    // ----------------------------------------------------------------
    public function serveMedia(string $filename): void
    {
        if (!preg_match('/^[a-f0-9]{32}\.(jpg|jpeg|png|webp)$/i', $filename)) {
            http_response_code(404); exit;
        }

        $path = BASE_PATH . '/uploads/blast/' . $filename;
        if (!file_exists($path)) {
            http_response_code(404); exit;
        }

        $mime = mime_content_type($path) ?: 'image/jpeg';
        header('Content-Type: '               . $mime);
        header('Content-Length: '             . filesize($path));
        header('Access-Control-Allow-Origin: *');
        header('Cache-Control: public, max-age=86400');
        readfile($path);
        exit;
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /**
     * Validate and save uploaded blast image.
     * Returns local file path on success, null on failure.
     */
    private function handleImageUpload(array $file): ?string
    {
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedExt  = ['jpg' => 'jpg', 'jpeg' => 'jpg', 'png' => 'png', 'webp' => 'webp'];
        $maxSize     = 2 * 1024 * 1024; // 2 MB

        if ($file['size'] > $maxSize) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowedMime, true)) {
            return null;
        }

        $origExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeExt = $allowedExt[$origExt] ?? null;

        if ($safeExt === null) {
            $mimeMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $safeExt = $mimeMap[$mime] ?? null;
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

        return $destPath;
    }

    /**
     * Try to fire cron.php in the background immediately (Linux/cPanel).
     * Falls back silently — the cPanel cron will pick it up within 1 minute.
     */
    private function triggerCron(): void
    {
        // Only on Unix systems and only if exec() is available
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) return;
        if (!function_exists('exec')) return;

        $php    = PHP_BINARY ?: '/usr/local/bin/php';
        $script = escapeshellarg(BASE_PATH . '/cron.php');

        // Non-blocking background exec
        exec($php . ' ' . $script . ' > /dev/null 2>&1 &');
    }
}
