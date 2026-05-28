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
 */
function sendWhatsAppGreeting(string $phone, string $name): void
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
    curl_exec($ch);
    curl_close($ch);
}
