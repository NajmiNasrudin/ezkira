<?php

namespace App\Core;

/**
 * Applies the billing schema (migration 009) on first use, so production never
 * runs code against columns that were never migrated by hand.
 */
class Schema
{
    private const FLAG_FILE = '/storage/logs/schema_009_billing.done';

    private static ?bool $ready = null;

    public static function ensureBilling(): bool
    {
        if (self::$ready !== null) {
            return self::$ready;
        }
        if (is_file(BASE_PATH . self::FLAG_FILE)) {
            return self::$ready = true;
        }

        try {
            $db = getDB();

            if (!self::hasColumn($db, 'users', 'pro_until')) {
                $db->exec('ALTER TABLE `users` ADD COLUMN `pro_until` DATETIME NULL DEFAULT NULL');
            }
            if (!self::hasColumn($db, 'expense_receipts', 'size_bytes')) {
                $db->exec('ALTER TABLE `expense_receipts` ADD COLUMN `size_bytes` INT UNSIGNED NULL DEFAULT NULL');
            }
            $db->exec(
                "CREATE TABLE IF NOT EXISTS `payments` (
                    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `user_id`       INT UNSIGNED NOT NULL,
                    `gateway`       VARCHAR(20)  NOT NULL DEFAULT 'chip',
                    `gateway_ref`   VARCHAR(100) NULL,
                    `plan_interval` VARCHAR(10)  NOT NULL,
                    `amount_sen`    INT UNSIGNED NOT NULL,
                    `status`        VARCHAR(20)  NOT NULL DEFAULT 'pending',
                    `is_test`       TINYINT(1)   NOT NULL DEFAULT 0,
                    `period_start`  DATETIME     NULL,
                    `period_end`    DATETIME     NULL,
                    `paid_at`       DATETIME     NULL,
                    `created_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY `uq_payments_gateway_ref` (`gateway_ref`),
                    KEY `idx_payments_user` (`user_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );

            @file_put_contents(BASE_PATH . self::FLAG_FILE, date('c'));
            return self::$ready = true;
        } catch (\Throwable $e) {
            error_log('Billing schema migration failed: ' . $e->getMessage());
            return self::$ready = false;
        }
    }

    private static function hasColumn(\PDO $db, string $table, string $column): bool
    {
        $stmt = $db->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $stmt->execute([$table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
