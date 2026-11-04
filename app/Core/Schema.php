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

    private const INVITES_FLAG = '/storage/logs/schema_010_admin_invites.done';

    private static ?bool $invitesReady = null;

    /** Table that remembers which users the owner already sent a Pro invite to (admin dashboard). */
    public static function ensureAdminInvites(): bool
    {
        if (self::$invitesReady !== null) {
            return self::$invitesReady;
        }
        if (is_file(BASE_PATH . self::INVITES_FLAG)) {
            return self::$invitesReady = true;
        }

        try {
            getDB()->exec(
                "CREATE TABLE IF NOT EXISTS `admin_invites` (
                    `user_id`      INT UNSIGNED NOT NULL PRIMARY KEY,
                    `sent_count`   INT UNSIGNED NOT NULL DEFAULT 1,
                    `last_sent_at` DATETIME     NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
            @file_put_contents(BASE_PATH . self::INVITES_FLAG, date('c'));
            return self::$invitesReady = true;
        } catch (\Throwable $e) {
            error_log('Admin invites schema failed: ' . $e->getMessage());
            return self::$invitesReady = false;
        }
    }

    private const LOANS_FLAG = '/storage/logs/schema_011_loans.done';

    private static ?bool $loansReady = null;

    /** Loan tracking tables (Expenses → Pinjaman). Fails closed: the loan feature hides itself. */
    public static function ensureLoans(): bool
    {
        if (self::$loansReady !== null) {
            return self::$loansReady;
        }
        if (is_file(BASE_PATH . self::LOANS_FLAG)) {
            return self::$loansReady = true;
        }

        try {
            $db = getDB();
            $db->exec(
                "CREATE TABLE IF NOT EXISTS `loans` (
                    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `user_id`        INT UNSIGNED  NOT NULL,
                    `lender`         VARCHAR(100)  NOT NULL,
                    `principal`      DECIMAL(15,2) NOT NULL,
                    `rate`           DECIMAL(6,3)  NOT NULL DEFAULT 0,
                    `rate_type`      VARCHAR(10)   NOT NULL DEFAULT 'reducing',
                    `months`         SMALLINT UNSIGNED NOT NULL,
                    `installment`    DECIMAL(15,2) NOT NULL,
                    `loan_date`      DATE          NOT NULL,
                    `first_due_date` DATE          NOT NULL,
                    `paid_before`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                    `add_to_cash`    TINYINT(1)    NOT NULL DEFAULT 1,
                    `created_at`     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
                    `updated_at`     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    KEY `idx_loans_user` (`user_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
            $db->exec(
                "CREATE TABLE IF NOT EXISTS `loan_payments` (
                    `id`                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `loan_id`              INT UNSIGNED  NOT NULL,
                    `user_id`              INT UNSIGNED  NOT NULL,
                    `pay_date`             DATE          NOT NULL,
                    `amount`               DECIMAL(15,2) NOT NULL,
                    `principal`            DECIMAL(15,2) NOT NULL,
                    `interest`             DECIMAL(15,2) NOT NULL,
                    `principal_expense_id` INT UNSIGNED  NULL,
                    `interest_expense_id`  INT UNSIGNED  NULL,
                    `created_at`           TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
                    KEY `idx_lp_loan` (`loan_id`),
                    KEY `idx_lp_user` (`user_id`),
                    KEY `idx_lp_pexp` (`principal_expense_id`),
                    KEY `idx_lp_iexp` (`interest_expense_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
            @file_put_contents(BASE_PATH . self::LOANS_FLAG, date('c'));
            return self::$loansReady = true;
        } catch (\Throwable $e) {
            error_log('Loans schema failed: ' . $e->getMessage());
            return self::$loansReady = false;
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
