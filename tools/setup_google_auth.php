<?php
/**
 * One-time setup script — Google Auth DB migration
 *
 * STEPS:
 *   1. Upload this file to your server root (same level as index.php)
 *   2. Open in browser: https://yourdomain.com/tools/setup_google_auth.php
 *   3. DELETE this file immediately after running
 *
 * NEVER leave this file on the server after use.
 */

// ── Security: only allow if a secret token is passed ──────────────────────────
define('SETUP_TOKEN', 'fikira-google-2025');   // change this if you want

if (($_GET['token'] ?? '') !== SETUP_TOKEN) {
    http_response_code(403);
    die('<b>403 Forbidden.</b> Pass ?token=fikira-google-2025 in the URL.');
}

// ── Bootstrap ─────────────────────────────────────────────────────────────────
$root = dirname(__DIR__);
if (!file_exists($root . '/config/config.php')) {
    die('Cannot find config/config.php. Make sure this script is inside the tools/ folder.');
}
require $root . '/config/config.php';

// ── Connect to DB ─────────────────────────────────────────────────────────────
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

// 2. Add unique index (may fail if already exists — that's fine)
try {
    $pdo->exec("ALTER TABLE `users` ADD UNIQUE KEY `uq_google_id` (`google_id`)");
    $results[] = ['ok', 'Unique index <code>uq_google_id</code> added'];
} catch (PDOException $e) {
    // Duplicate key error = already exists, safe to ignore
    if (str_contains($e->getMessage(), 'Duplicate key name')) {
        $results[] = ['ok', 'Unique index <code>uq_google_id</code> already exists — skipped'];
    } else {
        $results[] = ['err', 'Add index failed: ' . htmlspecialchars($e->getMessage())];
    }
}

// ── Check GOOGLE_CLIENT_ID is set ─────────────────────────────────────────────
$clientIdSet = defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID !== '';
$clientSecretSet = defined('GOOGLE_CLIENT_SECRET') && GOOGLE_CLIENT_SECRET !== '';

if ($clientIdSet && $clientSecretSet) {
    $results[] = ['ok', 'GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET are set in config.php ✓'];
} else {
    if (!$clientIdSet)     $results[] = ['warn', 'GOOGLE_CLIENT_ID is empty in config/config.php — Google login will not work until you add it'];
    if (!$clientSecretSet) $results[] = ['warn', 'GOOGLE_CLIENT_SECRET is empty in config/config.php — Google login will not work until you add it'];
}

// ── Check APP_URL ─────────────────────────────────────────────────────────────
$appUrl = defined('APP_URL') ? APP_URL : '';
$results[] = str_contains($appUrl, 'localhost')
    ? ['warn', "APP_URL is set to <code>{$appUrl}</code> — update to your production domain in config/config.php"]
    : ['ok',   "APP_URL = <code>{$appUrl}</code>. Redirect URI for Google Console: <code>{$appUrl}/auth/google/callback</code>"];

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Google Auth Setup</title>
<style>
body { font-family: system-ui, sans-serif; max-width: 680px; margin: 48px auto; padding: 0 20px; color: #1f2937; }
h1 { font-size: 1.4rem; margin-bottom: 24px; }
ul { list-style: none; padding: 0; }
li { padding: 10px 14px; margin-bottom: 8px; border-radius: 8px; font-size: 0.9rem; }
.ok   { background: #f0fdf4; border-left: 4px solid #22c55e; color: #166534; }
.warn { background: #fffbeb; border-left: 4px solid #f59e0b; color: #92400e; }
.err  { background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; }
code  { background: rgba(0,0,0,.07); padding: 1px 5px; border-radius: 4px; font-size: 0.85em; }
.box  { margin-top: 28px; padding: 16px 20px; background: #fef9c3; border: 1px solid #fde047; border-radius: 10px; font-size: 0.85rem; }
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
    <b>⚠️ PENTING:</b> Delete fail ini dari server sekarang!<br>
    Pergi ke <b>cPanel → File Manager → tools/setup_google_auth.php</b> → Delete.
</div>
</body>
</html>
