<?php

/**
 * Router script for PHP built-in dev server.
 * Usage: php -S localhost:8001 router.php
 *
 * Replicates .htaccess behaviour:
 * - Static files (assets, uploads) → served directly
 * - Everything else → index.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve existing static files directly (assets/, uploads/, favicon, etc.)
if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

// Block direct access to sensitive files/directories only when they physically exist on disk.
// Routes like /lang/switch must NOT be blocked — only real files like /lang/en.php.
$blocked  = ['app', 'config', 'controllers', 'models', 'views', 'lang', 'routes', 'database', 'storage'];
$first    = explode('/', ltrim($uri, '/'))[0];
$fullPath = __DIR__ . $uri;
if (in_array($first, $blocked, true) && file_exists($fullPath)) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

// Everything else → index.php
require __DIR__ . '/index.php';
