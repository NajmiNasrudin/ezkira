-- Migration 005: Capital tracking table
CREATE TABLE IF NOT EXISTS `capitals` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`      INT UNSIGNED NOT NULL,
    `amount`       DECIMAL(15,2) NOT NULL,
    `description`  VARCHAR(500) NOT NULL DEFAULT '',
    `capital_date` DATE NOT NULL,
    `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_capitals_user_date` (`user_id`, `capital_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
