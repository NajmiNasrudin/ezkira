<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        // Custom save path for shared hosting open_basedir compatibility
        $savePath = BASE_PATH . '/storage/sessions';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0700, true);
        }
        session_save_path($savePath);

        session_name(SESSION_NAME);

        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    /** Store old POST input for repopulating forms after validation failure */
    public static function flashInput(array $data): void
    {
        $_SESSION['_old_input'] = $data;
    }

    public static function old(string $key, string $default = ''): string
    {
        $value = $_SESSION['_old_input'][$key] ?? $default;
        // Old input is cleared on next access by the view helper
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public static function clearOldInput(): void
    {
        unset($_SESSION['_old_input']);
    }
}
