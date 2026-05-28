CREATE TABLE IF NOT EXISTS `balance_sheet_entries` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT UNSIGNED NOT NULL,
    `as_of_date`  DATE NOT NULL,
    `section`     VARCHAR(50)  NOT NULL,
    `item_key`    VARCHAR(100) NOT NULL,
    `amount`      DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_user_date_item` (`user_id`, `as_of_date`, `section`, `item_key`),
    INDEX `idx_user_date` (`user_id`, `as_of_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
