<?php

namespace App\Core;

class Logger
{
    public static function log(
        string $action,
        ?int $userId = null,
        ?string $description = null
    ): void {
        try {
            $db   = getDB();
            $uid  = $userId ?? Auth::id();
            $ip   = self::getIp();
            $ua   = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

            $stmt = $db->prepare(
                'INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, created_at)
                 VALUES (:uid, :action, :desc, :ip, :ua, NOW())'
            );
            $stmt->execute([
                ':uid'    => $uid,
                ':action' => $action,
                ':desc'   => $description,
                ':ip'     => $ip,
                ':ua'     => $ua,
            ]);
        } catch (\Throwable $e) {
            // Never let logging break the app
            error_log('[Logger] ' . $e->getMessage(), 3, BASE_PATH . '/storage/logs/error.log');
        }
    }

    private static function getIp(): string
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return 'unknown';
    }
}
