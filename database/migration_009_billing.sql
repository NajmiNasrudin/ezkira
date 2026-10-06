-- Migration 009: Free/Pro plans and CHIP payments
-- Applied automatically on first use by App\Core\Schema::ensureBilling().
-- Kept here as the record of the change (and for manual runs via phpMyAdmin if ever needed).

ALTER TABLE `users`
    ADD COLUMN `pro_until` DATETIME NULL DEFAULT NULL;

ALTER TABLE `expense_receipts`
    ADD COLUMN `size_bytes` INT UNSIGNED NULL DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `payments` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
