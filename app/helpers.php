<?php

/**
 * Global helper functions — NO namespace so they are callable from anywhere.
 */

/**
 * Translate a key using the loaded language strings.
 * Usage: __('welcome', ['name' => 'Ali'])
 */
function __(string $key, array $replace = []): string
{
    return \App\Core\Lang::get($key, $replace);
}

/**
 * Send a single WhatsApp greeting via Fonnte.
 * Called after new user registration. Fails silently.
 * If $userId > 0 and message delivers, marks users.wa_greeting_sent = 1.
 */
function sendWhatsAppGreeting(string $phone, string $name, int $userId = 0): void
{
    if (!defined('FONNTE_TOKEN') || FONNTE_TOKEN === '') return;

    $setting = new \Models\Setting();
    if (!$setting->get('wa_greeting_enabled', '0')) return;

    $template = trim($setting->get('wa_greeting_message', ''));
    if ($template === '') return;

    // Normalise phone
    $phone = preg_replace('/\D/', '', $phone);
    if ($phone === '') return;
    if (str_starts_with($phone, '0')) {
        $phone = '60' . substr($phone, 1);
    }

    // Replace placeholder
    $message = str_replace(['{name}', '{nama}'], $name, $template);

    $data = [
        'target'      => $phone,
        'message'     => $message,
        'countryCode' => '60',
    ];

    $ch = curl_init('https://api.fonnte.com/send');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $data,
        CURLOPT_HTTPHEADER     => ['Authorization: ' . FONNTE_TOKEN],
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Mark user as greeted if Fonnte accepted the request
    if ($userId > 0 && $httpCode === 200 && $response !== false) {
        $decoded = json_decode($response, true);
        // Fonnte returns {"status":true,...} on success
        if (!empty($decoded['status'])) {
            try {
                getDB()
                    ->prepare('UPDATE users SET wa_greeting_sent = 1 WHERE id = ?')
                    ->execute([$userId]);
            } catch (\Throwable) {
                // Silent — greeting was sent, just couldn't update flag
            }
        }
    }
}
