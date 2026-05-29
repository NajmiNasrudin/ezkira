<?php
/**
 * cron.php — WhatsApp Blast background processor
 *
 * Add to cPanel Cron Jobs (every minute):
 *   * * * * *  /usr/local/bin/php /home/YOUR_USER/public_html/cron.php >> /home/YOUR_USER/public_html/storage/logs/cron.log 2>&1
 *
 * Find your PHP path with:  which php   (in cPanel Terminal)
 */

declare(strict_types=1);

// ---- Bootstrap (minimal — no session, no router) ----
define('BASE_PATH', __DIR__);
require BASE_PATH . '/config/config.php';
require BASE_PATH . '/config/database.php';
require BASE_PATH . '/app/helpers.php';

spl_autoload_register(function (string $class): void {
    $map = [
        'App\\Core\\'       => BASE_PATH . '/app/Core/',
        'App\\Middleware\\' => BASE_PATH . '/app/Middleware/',
        'Controllers\\'     => BASE_PATH . '/controllers/',
        'Models\\'          => BASE_PATH . '/models/',
    ];
    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix))) . '.php';
            if (file_exists($file)) require $file;
            return;
        }
    }
});

set_time_limit(0);
ignore_user_abort(true);

$logFile = BASE_PATH . '/storage/logs/cron.log';

function cronLog(string $msg): void
{
    global $logFile;
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

// ---- Pick up next due blast ----
$blastModel = new Models\Blast();
$userModel  = new Models\User();

$job = $blastModel->getNextDue();

if (!$job) {
    // Nothing to process
    exit(0);
}

$blastId  = (int)$job['id'];
$blastLink = $job['blast_link'] ?? '';
$provider  = $job['provider']   ?? 'fonnte';
$delayMin  = max(3, (int)($job['delay_seconds'] ?? 12));

// Resolve message variations (JSON array or plain string — backward compat)
$rawMsg       = $job['custom_message'] ?? '';
$msgDecoded   = json_decode($rawMsg, true);
$msgVariants  = (json_last_error() === JSON_ERROR_NONE && is_array($msgDecoded) && count($msgDecoded) > 0)
                ? $msgDecoded : [$rawMsg];
$msgVariants  = array_values(array_filter($msgVariants)); // remove empty

// Resolve image variations (JSON array, single path, or empty — backward compat)
$rawImg       = $job['image_path'] ?? '';
$imgDecoded   = json_decode($rawImg, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($imgDecoded)) {
    $imgVariants = array_values(array_filter($imgDecoded));
} else {
    $imgVariants = ($rawImg !== '') ? [$rawImg] : [];
}

cronLog("START blast #{$blastId}");

// Mark as running immediately so no second cron instance picks it up
$blastModel->markRunning($blastId);

// ---- Resolve recipients ----
$recipientIds = json_decode($job['recipient_ids'] ?? '[]', true);

if (in_array('all', (array)$recipientIds, true)) {
    $recipients = $userModel->allWithPhone();
} else {
    $recipients = $userModel->findManyByIds(array_map('intval', (array)$recipientIds));
}

$recipients = array_filter($recipients, fn($u) => !empty($u['whatsapp_number']));
$recipients = array_values($recipients);

if (empty($recipients)) {
    cronLog("SKIP blast #{$blastId} — no recipients with WhatsApp number");
    $blastModel->markDone($blastId, 0, 0);
    exit(0);
}

$sentCount   = 0;
$failedCount = 0;
$total       = count($recipients);
$varCount    = count($msgVariants);
$imgCount    = count($imgVariants);

cronLog("Sending to {$total} recipients | {$varCount} msg variants, {$imgCount} img variants | delay rand({$delayMin}," . ($delayMin + 5) . ")s");

foreach ($recipients as $i => $user) {
    $phone = normalisePhone($user['whatsapp_number']);

    // Pick random message + image variation for this recipient
    $msgBody  = $msgVariants[array_rand($msgVariants)];
    $imgPath  = $imgCount > 0 ? $imgVariants[array_rand($imgVariants)] : '';

    $fullMsg  = "Hai {$user['name']},\n\n" . $msgBody;
    if ($blastLink !== '') {
        $fullMsg .= "\n\n" . $blastLink;
    }

    $result = match($provider) {
        'whatsapp_api'  => sendWhatsAppCloudAPI($phone, $fullMsg, $imgPath),
        'wasenderapi'   => sendWaSenderApi($phone, $fullMsg, $imgPath),
        default         => sendFonnte($phone, $fullMsg, $imgPath),
    };

    // Debug: log phone + raw response info
    cronLog("  [DEBUG] provider={$provider} phone={$phone} ok=" . ($result['ok'] ? 'true' : 'false') . (isset($result['error']) ? " err={$result['error']}" : '') . (isset($result['msg_id']) ? " id={$result['msg_id']}" : ''));

    if ($result['ok']) {
        $sentCount++;
        $blastModel->logRecipient($blastId, (int)$user['id'], $user['name'], $phone, 'sent', null);
        cronLog("  [{$sentCount}/{$total}] ✓ {$user['name']} ({$phone})");
    } else {
        $failedCount++;
        $blastModel->logRecipient($blastId, (int)$user['id'], $user['name'], $phone, 'failed', $result['error']);
        cronLog("  [FAIL] {$user['name']} ({$phone}): {$result['error']}");
    }

    // Update live progress in DB after every send
    $blastModel->updateProgress($blastId, $sentCount, $failedCount);

    // Random delay between sends (skip delay after the last one)
    if ($i < $total - 1) {
        sleep(rand($delayMin, $delayMin + 5));
    }
}

$blastModel->markDone($blastId, $sentCount, $failedCount);
cronLog("DONE blast #{$blastId} — sent:{$sentCount}, failed:{$failedCount}");

exit(0);

// ----------------------------------------------------------------
// Helpers
// ----------------------------------------------------------------

function normalisePhone(string $phone): string
{
    $phone = preg_replace('/\D/', '', $phone);
    if (str_starts_with($phone, '0')) {
        $phone = '60' . substr($phone, 1);
    }
    return $phone;
}

// ----------------------------------------------------------------
// WhatsApp Business Cloud API (Meta official)
// ----------------------------------------------------------------
function sendWhatsAppCloudAPI(string $toPhone, string $message, string $imagePath = ''): array
{
    if (!defined('WA_PHONE_NUMBER_ID') || !defined('WA_ACCESS_TOKEN')
        || WA_PHONE_NUMBER_ID === '' || WA_ACCESS_TOKEN === '') {
        return ['ok' => false, 'error' => 'WA_PHONE_NUMBER_ID / WA_ACCESS_TOKEN tidak dikonfigurasi'];
    }

    $url = 'https://graph.facebook.com/v25.0/' . WA_PHONE_NUMBER_ID . '/messages';

    // Image message (with caption) vs plain text
    if ($imagePath !== '' && file_exists($imagePath)) {
        // Build public URL for the image
        $imageUrl = rtrim(defined('APP_URL') ? APP_URL : '', '/') . '/blast/media/' . basename($imagePath);
        $body = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $toPhone,
            'type'              => 'image',
            'image'             => [
                'link'    => $imageUrl,
                'caption' => $message,
            ],
        ];
    } else {
        $body = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $toPhone,
            'type'              => 'text',
            'text'              => [
                'preview_url' => false,
                'body'        => $message,
            ],
        ];
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($body),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . WA_ACCESS_TOKEN,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) return ['ok' => false, 'error' => 'cURL: ' . $curlErr];

    $result = json_decode($response, true);

    // Success: response has messages[0].id
    if (!empty($result['messages'][0]['id'])) {
        return ['ok' => true, 'msg_id' => $result['messages'][0]['id']];
    }

    $errMsg = $result['error']['message'] ?? $response;
    cronLog('  [WA_RAW] ' . substr($response, 0, 500));
    return ['ok' => false, 'error' => substr($errMsg, 0, 300)];
}

// ----------------------------------------------------------------

// ----------------------------------------------------------------
// WASenderAPI  (wasenderapi.com)
// ----------------------------------------------------------------
function sendWaSenderApi(string $toPhone, string $message, string $imagePath = ''): array
{
    if (!defined('WASENDER_API_KEY') || trim(WASENDER_API_KEY) === '') {
        return ['ok' => false, 'error' => 'WASENDER_API_KEY tidak dikonfigurasi dalam config.php'];
    }

    $body = ['to' => $toPhone];

    if ($imagePath !== '' && file_exists($imagePath)) {
        // Build public URL for the image
        $imageUrl = rtrim(defined('APP_URL') ? APP_URL : '', '/') . '/blast/media/' . basename($imagePath);
        $body['mediaUrl'] = $imageUrl;
        $body['caption']  = $message;
    } else {
        $body['message'] = $message;
    }

    $ch = curl_init('https://app.wasenderapi.com/api/send-message');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($body),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . WASENDER_API_KEY,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $curlErr  = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlErr) {
        return ['ok' => false, 'error' => 'cURL: ' . $curlErr];
    }

    $result = json_decode($response, true);

    // WASenderAPI returns { "success": true, ... } on success
    if (!empty($result['success'])) {
        return ['ok' => true, 'msg_id' => $result['messageId'] ?? ''];
    }

    $errMsg = $result['message'] ?? $result['error'] ?? $response;
    cronLog('  [WASENDER_RAW] HTTP ' . $httpCode . ' ' . substr($response, 0, 500));
    return ['ok' => false, 'error' => substr($errMsg, 0, 300)];
}

// ----------------------------------------------------------------

function sendFonnte(string $toPhone, string $message, string $imagePath = ''): array
{
    $data = [
        'target'      => $toPhone,
        'message'     => $message,
        'countryCode' => '60',
    ];

    if ($imagePath !== '' && file_exists($imagePath)) {
        $mime         = mime_content_type($imagePath) ?: 'image/jpeg';
        $data['file'] = new \CURLFile($imagePath, $mime, basename($imagePath));
    }

    $ch = curl_init('https://api.fonnte.com/send');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $data,
        CURLOPT_HTTPHEADER     => ['Authorization: ' . FONNTE_TOKEN],
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        return ['ok' => false, 'error' => 'cURL: ' . $curlErr];
    }

    $result = json_decode($response, true);

    if (!empty($result['status']) && $result['status'] === true) {
        return ['ok' => true];
    }

    $errMsg = $result['reason'] ?? $response;
    return ['ok' => false, 'error' => substr($errMsg, 0, 300)];
}
