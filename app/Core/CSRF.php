<?php

namespace App\Core;

class CSRF
{
    private const TOKEN_KEY = 'csrf_token';

    public static function generate(): string
    {
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::TOKEN_KEY];
    }

    public static function verify(string $token): bool
    {
        return isset($_SESSION[self::TOKEN_KEY])
            && hash_equals($_SESSION[self::TOKEN_KEY], $token);
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="'
            . htmlspecialchars(self::generate(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Abort with 419 if CSRF token is missing or invalid.
     * Accepts token from POST body or X-CSRF-Token header (for AJAX).
     */
    public static function check(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $token = $_POST['csrf_token']
            ?? getallheaders()['X-Csrf-Token']
            ?? getallheaders()['X-CSRF-Token']
            ?? '';

        if (!self::verify($token)) {
            http_response_code(419);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'CSRF token mismatch.']);
            } else {
                include BASE_PATH . '/views/errors/419.php';
            }
            exit;
        }
    }

    public static function regenerate(): void
    {
        $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
    }
}
