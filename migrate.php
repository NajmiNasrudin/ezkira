<?php
/**
 * One-time migration runner.
 * Access via browser: https://yourdomain.com/migrate.php?key=fikira_migrate_2024
 * DELETE this file after running migrations.
 */

define('BASE_PATH', __DIR__);
define('SECRET_KEY', 'fikira_migrate_2024');

if (($_GET['key'] ?? '') !== SECRET_KEY) {
    http_response_code(403);
    die('403 Forbidden');
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/database.php';

$db = getDB();

$migrations = [
    '004_refunds' => "ALTER TABLE `revenues`
        ADD COLUMN IF NOT EXISTS `entry_type` ENUM('sale','refund') NOT NULL DEFAULT 'sale' AFTER `platform`;",

    '005_capitals' => "CREATE TABLE IF NOT EXISTS `capitals` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT UNSIGNED NOT NULL,
        `amount` DECIMAL(15,2) NOT NULL,
        `description` VARCHAR(500) NOT NULL DEFAULT '',
        `capital_date` DATE NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY `idx_capitals_user_date` (`user_id`, `capital_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    '006_payment_method' => "ALTER TABLE `revenues`
        ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(50) NOT NULL DEFAULT 'cash' AFTER `entry_type`;",

    '007_blast' => "CREATE TABLE IF NOT EXISTS `blast_logs` (
        `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `sent_by`          INT UNSIGNED NOT NULL,
        `template_name`    VARCHAR(100) NOT NULL DEFAULT 'hello_world',
        `custom_message`   TEXT NULL,
        `total_recipients` INT NOT NULL DEFAULT 0,
        `sent_count`       INT NOT NULL DEFAULT 0,
        `failed_count`     INT NOT NULL DEFAULT 0,
        `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY `idx_blast_user` (`sent_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE IF NOT EXISTS `blast_recipients` (
        `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `blast_id`      INT UNSIGNED NOT NULL,
        `user_id`       INT UNSIGNED NULL,
        `name`          VARCHAR(200) NOT NULL DEFAULT '',
        `phone`         VARCHAR(30)  NOT NULL,
        `status`        ENUM('sent','failed','pending') NOT NULL DEFAULT 'pending',
        `error_msg`     VARCHAR(500) NULL,
        `sent_at`       TIMESTAMP NULL,
        KEY `idx_br_blast` (`blast_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
];

$results = [];

foreach ($migrations as $name => $sql) {
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $ok = true;
    $err = '';
    foreach ($statements as $stmt) {
        if ($stmt === '') continue;
        try {
            $db->exec($stmt);
        } catch (\PDOException $e) {
            $ok  = false;
            $err = $e->getMessage();
            break;
        }
    }
    $results[$name] = ['ok' => $ok, 'error' => $err];
}

// Check current table structure
$tables = $db->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
$revenueColumns = $db->query("SHOW COLUMNS FROM revenues")->fetchAll(\PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Migration Runner</title>
<style>
  body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 2rem; }
  h1 { color: #38bdf8; }
  .ok  { color: #4ade80; }
  .err { color: #f87171; }
  .info { color: #94a3b8; font-size: 0.85rem; margin-top: 2rem; }
  table { border-collapse: collapse; margin-top: 1rem; }
  td, th { border: 1px solid #334155; padding: 0.4rem 1rem; }
  th { background: #1e293b; color: #7dd3fc; }
  .warn { background: #7f1d1d; color: #fca5a5; padding: 1rem; border-radius: 6px; margin-top: 2rem; }
</style>
</head>
<body>
<h1>EZKIRA — Migration Runner</h1>

<table>
  <tr><th>Migration</th><th>Status</th><th>Detail</th></tr>
  <?php foreach ($results as $name => $res): ?>
  <tr>
    <td><?= htmlspecialchars($name) ?></td>
    <td class="<?= $res['ok'] ? 'ok' : 'err' ?>"><?= $res['ok'] ? '✓ OK' : '✗ FAILED' ?></td>
    <td><?= $res['ok'] ? '—' : htmlspecialchars($res['error']) ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<br>
<strong>Tables in DB:</strong> <?= implode(', ', $tables) ?><br>
<strong>revenues columns:</strong> <?= implode(', ', $revenueColumns) ?>

<div class="warn">
  ⚠️ <strong>IMPORTANT:</strong> Delete <code>migrate.php</code> from your server immediately after this runs!
</div>

<p class="info">Migrations complete. You can now delete this file via cPanel File Manager.</p>
</body>
</html>
