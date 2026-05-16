<?php

declare(strict_types=1);

// ============================================================
// Application Bootstrap
// ============================================================

define('BASE_PATH', __DIR__);

// ---- Load configuration ----
require BASE_PATH . '/config/config.php';
require BASE_PATH . '/config/database.php';

// ---- Global helpers (must be loaded before autoloader runs) ----
require BASE_PATH . '/app/helpers.php';

// ---- PSR-4-style autoloader (no Composer needed) ----
spl_autoload_register(function (string $class): void {
    $prefixMap = [
        'App\\Core\\'        => BASE_PATH . '/app/Core/',
        'App\\Middleware\\'  => BASE_PATH . '/app/Middleware/',
        'Controllers\\'      => BASE_PATH . '/controllers/',
        'Models\\'           => BASE_PATH . '/models/',
    ];

    foreach ($prefixMap as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file     = $dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
            if (file_exists($file)) {
                require $file;
            }
            return;
        }
    }
});

// ---- Core imports (used throughout bootstrap) ----
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Lang;
use App\Core\Router;
use App\Core\Session;
use Models\SessionToken;
use Models\User;

// ---- Start session ----
Session::start();

// ---- Remember-me auto-login ----
if (!Auth::check() && !empty($_COOKIE['remember_token'])) {
    $rawToken  = $_COOKIE['remember_token'];
    $tokenHash = hash('sha256', $rawToken);

    $tokenModel = new SessionToken();
    $row        = $tokenModel->findByTokenHash($tokenHash);

    if ($row) {
        $user = (new User())->findById((int)$row['user_id']);
        if ($user) {
            Auth::login($user);

            // Rotate token for security
            $tokenModel->deleteByTokenHash($tokenHash);
            $newToken     = bin2hex(random_bytes(32));
            $newHash      = hash('sha256', $newToken);
            $newExpiresAt = date('Y-m-d H:i:s', time() + REMEMBER_ME_DAYS * 86400);
            $tokenModel->create((int)$user['id'], $newHash, $newExpiresAt);

            setcookie('remember_token', $newToken, [
                'expires'  => time() + REMEMBER_ME_DAYS * 86400,
                'path'     => '/',
                'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
    } else {
        // Stale / expired cookie — clear it
        setcookie('remember_token', '', ['expires' => time() - 3600, 'path' => '/']);
    }
}

// ---- Load translations ----
Lang::load();

// ---- Route and dispatch ----
$router = new Router();
require BASE_PATH . '/routes/web.php';
$router->dispatch(
    $_SERVER['REQUEST_URI']    ?? '/',
    $_SERVER['REQUEST_METHOD'] ?? 'GET'
);
