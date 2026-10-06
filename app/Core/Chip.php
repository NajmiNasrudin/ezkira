<?php

namespace App\Core;

/**
 * Minimal CHIP Collect client (https://docs.chip-in.asia).
 * Credentials live in config/config.php on the server: CHIP_BRAND_ID, CHIP_SECRET_KEY
 * and optionally CHIP_TEST_MODE (true while testing with CHIP test keys).
 */
class Chip
{
    private const BASE_URL = 'https://gate.chip-in.asia/api/v1';

    // 'paid' can later move on to 'cleared' / 'settled' for card payments
    public const PAID_STATUSES = ['paid', 'cleared', 'settled'];

    public static function configured(): bool
    {
        return defined('CHIP_BRAND_ID') && trim((string) CHIP_BRAND_ID) !== ''
            && defined('CHIP_SECRET_KEY') && trim((string) CHIP_SECRET_KEY) !== '';
    }

    public static function testMode(): bool
    {
        return defined('CHIP_TEST_MODE') && CHIP_TEST_MODE === true;
    }

    public static function createPurchase(array $body): array
    {
        $body['brand_id'] = CHIP_BRAND_ID;
        return self::request('POST', '/purchases/', $body);
    }

    public static function getPurchase(string $purchaseId): array
    {
        if (!preg_match('/^[A-Za-z0-9-]{8,64}$/', $purchaseId)) {
            throw new \InvalidArgumentException('Invalid purchase id');
        }
        return self::request('GET', '/purchases/' . $purchaseId . '/');
    }

    private static function request(string $method, string $path, ?array $body = null): array
    {
        $ch = curl_init(self::BASE_URL . $path);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . CHIP_SECRET_KEY,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $raw    = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error  = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new \RuntimeException('CHIP request failed: ' . $error);
        }
        $data = json_decode($raw, true);
        if ($status < 200 || $status >= 300 || !is_array($data)) {
            throw new \RuntimeException('CHIP ' . $method . ' ' . $path . ' returned HTTP ' . $status . ': ' . substr($raw, 0, 500));
        }
        return $data;
    }
}
