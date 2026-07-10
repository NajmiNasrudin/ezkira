<?php
/**
 * One-time setup script — Google Auth DB migration
 *
 * STEPS:
 *   1. Open in browser: https://yourdomain.com/tools/setup_google_auth.php?token=fikira-google-2025
 *   2. DELETE this file immediately after running (cPanel → File Manager)
 *
 * NEVER leave this file on the server after use.
 */

// ── Security: only allow if a secret token is passed ──────────────────────────
if (($_GET['token'] ?? '') !== 'fikira-google-2025') {
    http_response_code(403);
    die('<b>403 Forbidden.</b> Pass ?token=fikira-google-2025 in the URL.');
}

// ── Bootstrap — must define BASE_PATH before requiring config.php ─────────────
define('BASE_PATH', dirname(__DIR__));

if (!file_exists(BASE_PATH . '/config/config.php')) {
    die('Cannot find config/config.php. Make sure this script is inside the tools/ folder.');
}

require BASE_PATH . '/config/config.php';

// ── Connect to DB using constants from config ──────────────────────────────────
try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die('<b>DB connection failed:</b> ' . htmlspecialchars($e->getMessage()));
}

// ── Run migration ─────────────────────────────────────────────────────────────
$results = [];

// 1. Add google_id column
try {
    $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `google_id` VARCHAR(100) NULL DEFAULT NULL AFTER `profile_image`");
    $results[] = ['ok', 'Column <code>google_id</code> added (or already exists)'];
} catch (PDOException $e) {
    $results[] = ['err', 'Add column failed: ' . htmlspecialchars($e->getMessage())];
}

// 2. Add unique index (ignore if already exists)
try {
    $pdo->exec("ALTER TABLE `users` ADD UNIQUE KEY `uq_google_id` (`google_id`)");
    $results[] = ['ok', 'Unique index <code>uq_google_id</code> added'];
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate key name')) {
        $results[] = ['ok', 'Unique index <code>uq_google_id</code> already exists — skipped'];
    } else {
        $results[] = ['err', 'Add index failed: ' . htmlspecialchars($e->getMessage())];
    }
}

// ── Check config values ────────────────────────────────────────────────────────
$clientIdSet     = defined('GOOGLE_CLIENT_ID')     && GOOGLE_CLIENT_ID !== '';
$clientSecretSet = defined('GOOGLE_CLIENT_SECRET') && GOOGLE_CLIENT_SECRET !== '';

if ($clientIdSet && $clientSecretSet) {
    $results[] = ['ok', 'GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET are set in config.php ✓'];
} else {
    if (!$clientIdSet)     $results[] = ['warn', 'GOOGLE_CLIENT_ID is empty in config/config.php'];
    if (!$clientSecretSet) $results[] = ['warn', 'GOOGLE_CLIENT_SECRET is empty in config/config.php'];
}

$appUrl = defined('APP_URL') ? APP_URL : '';
$results[] = str_contains($appUrl, 'localhost')
    ? ['warn', "APP_URL masih <code>{$appUrl}</code> — tukar ke domain production dalam config/config.php"]
    : ['ok',   "APP_URL = <code>{$appUrl}</code> &nbsp;|&nbsp; Redirect URI: <code>{$appUrl}/auth/google/callback</code>"];

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Google Auth Setup</title>
<style>
body { font-family: system-ui, sans-serif; max-width: 700px; margin: 48px auto; padding: 0 20px; color: #1f2937; }
h1   { font-size: 1.4rem; margin-bottom: 24px; }
ul   { list-style: none; padding: 0; }
li   { padding: 10px 14px; margin-bottom: 8px; border-radius: 8px; font-size: 0.9rem; line-height: 1.5; }
.ok   { background:#f0fdf4; border-left:4px solid #22c55e; color:#166534; }
.warn { background:#fffbeb; border-left:4px solid #f59e0b; color:#92400e; }
.err  { background:#fef2f2; border-left:4px solid #ef4444; color:#991b1b; }
code  { background:rgba(0,0,0,.08); padding:1px 5px; border-radius:4px; font-size:.85em; word-break:break-all; }
.box  { margin-top:28px; padding:16px 20px; background:#fef9c3; border:1px solid #fde047; border-radius:10px; font-size:.85rem; }
</style>
</head>
<body>
<h1>🔐 Google Auth — One-Time Setup</h1>
<ul>
<?php foreach ($results as [$type, $msg]): ?>
    <li class="<?= $type ?>">
        <?= $type === 'ok' ? '✅' : ($type === 'warn' ? '⚠️' : '❌') ?> <?= $msg ?>
    </li>
<?php endforeach; ?>
</ul>

<div class="box">
    <b>⚠️ PENTING:</b> Delete fail ini dari server selepas selesai!<br>
    <b>cPanel → File Manager → tools/setup_google_auth.php → Delete</b>
</div>
</body>
</html>
