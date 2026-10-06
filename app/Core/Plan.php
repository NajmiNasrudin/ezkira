<?php

namespace App\Core;

use DateTimeImmutable;
use DateTimeZone;
use Models\Billing;

/**
 * Free/Pro plan policy. Key-in stays free for everyone; only storing receipts is limited.
 * Until BILLING_START every account gets Pro for free.
 */
class Plan
{
    public const TIMEZONE           = 'Asia/Kuala_Lumpur';
    public const BILLING_START      = '2026-11-01 00:00:00';
    public const FREE_RECEIPT_LIMIT = 20;
    public const PRO_STORAGE_BYTES  = 1073741824; // 1 GB ≈ 4,000 compressed receipts
    public const PRICES             = ['monthly' => 570, 'yearly' => 5700]; // in sen

    private static array $cache = [];

    public static function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone(self::TIMEZONE));
    }

    public static function billingStart(): DateTimeImmutable
    {
        return new DateTimeImmutable(self::BILLING_START, new DateTimeZone(self::TIMEZONE));
    }

    public static function billingStarted(): bool
    {
        return self::now() >= self::billingStart();
    }

    /**
     * @return array{tier:string, launch_free:bool, paid_until:?DateTimeImmutable,
     *               count:int, bytes:int, available:bool}
     */
    public static function status(int $userId): array
    {
        if (isset(self::$cache[$userId])) {
            return self::$cache[$userId];
        }

        if (!Schema::ensureBilling()) {
            // Billing tables unavailable: never block users because of our own setup problem.
            return self::$cache[$userId] = [
                'tier' => 'pro', 'launch_free' => !self::billingStarted(), 'paid_until' => null,
                'count' => 0, 'bytes' => 0, 'available' => false,
            ];
        }

        $billing   = new Billing();
        $untilRaw  = $billing->getProUntil($userId);
        $paidUntil = $untilRaw ? new DateTimeImmutable($untilRaw, new DateTimeZone(self::TIMEZONE)) : null;
        $paidActive = $paidUntil !== null && $paidUntil > self::now();
        $launchFree = !self::billingStarted();
        $usage      = $billing->receiptUsage($userId);

        return self::$cache[$userId] = [
            'tier'        => ($paidActive || $launchFree) ? 'pro' : 'free',
            'launch_free' => $launchFree && !$paidActive,
            'paid_until'  => $paidActive ? $paidUntil : null,
            'count'       => $usage['count'],
            'bytes'       => $usage['bytes'],
            'available'   => true,
        ];
    }

    public static function forget(int $userId): void
    {
        unset(self::$cache[$userId]);
    }

    /** Lang key explaining why one more receipt of $bytes cannot be stored, or null if it can. */
    public static function uploadBlocker(array $status, int $bytes): ?string
    {
        if ($status['tier'] === 'free') {
            return $status['count'] + 1 > self::FREE_RECEIPT_LIMIT ? 'plan_receipt_limit_free' : null;
        }
        return $status['bytes'] + $bytes > self::PRO_STORAGE_BYTES ? 'plan_receipt_limit_pro' : null;
    }

    /**
     * Start/end of the Pro period bought now. Paying before launch or while still Pro
     * extends from the later date, so no paid day is lost.
     *
     * @return array{0:string,1:string} 'Y-m-d H:i:s' in Malaysia time
     */
    public static function periodFor(string $interval, ?string $currentUntil): array
    {
        $tz   = new DateTimeZone(self::TIMEZONE);
        $base = max(self::now(), self::billingStart());
        if ($currentUntil) {
            $base = max($base, new DateTimeImmutable($currentUntil, $tz));
        }
        $end = $base->modify($interval === 'yearly' ? '+1 year' : '+1 month');
        return [$base->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];
    }

    public static function priceLabel(string $interval): string
    {
        $sen = self::PRICES[$interval];
        return 'RM' . ($sen % 100 === 0 ? number_format($sen / 100) : number_format($sen / 100, 2));
    }

    public static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return rtrim(rtrim(number_format($bytes / 1073741824, 2), '0'), '.') . ' GB';
        if ($bytes >= 1048576)    return rtrim(rtrim(number_format($bytes / 1048576, 1), '0'), '.') . ' MB';
        return max(0, (int) round($bytes / 1024)) . ' KB';
    }
}
