-- EZKIRA — Database Schema
-- Import fail ini dalam phpMyAdmin SELEPAS pilih database anda
-- Jangan import keseluruhan fail ini terus — pastikan database sudah dipilih dahulu

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`                   INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`                 VARCHAR(100)     NOT NULL,
  `pic_name`             VARCHAR(100)     NOT NULL DEFAULT '',
  `email`                VARCHAR(255)     NOT NULL,
  `password`             VARCHAR(255)     NOT NULL,
  `whatsapp_number`      VARCHAR(20)      NOT NULL DEFAULT '',
  `role`                 ENUM('admin','team','client') NOT NULL DEFAULT 'client',
  `language`             ENUM('en','ms')  NOT NULL DEFAULT 'en',
  `dark_mode`            TINYINT(1)       NOT NULL DEFAULT 0,
  `profile_image`        VARCHAR(500)     NULL DEFAULT NULL,
  `google_id`            VARCHAR(100)     NULL DEFAULT NULL,
  `business_type`        VARCHAR(50)      NULL DEFAULT NULL,
  `business_type_other`  VARCHAR(255)     NULL DEFAULT NULL,
  `created_at`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  UNIQUE KEY `uq_google_id` (`google_id`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: sessions  (remember-me tokens)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `token_hash`  VARCHAR(64)  NOT NULL,
  `expires_at`  DATETIME     NOT NULL,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_token` (`token_hash`),
  KEY `idx_user_id`  (`user_id`),
  KEY `idx_expires`  (`expires_at`),
  CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: activity_logs
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED  NULL DEFAULT NULL,
  `action`      VARCHAR(100)  NOT NULL,
  `description` TEXT          NULL DEFAULT NULL,
  `ip_address`  VARCHAR(45)   NOT NULL DEFAULT '',
  `user_agent`  VARCHAR(500)  NULL DEFAULT NULL,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id`  (`user_id`),
  KEY `idx_action`   (`action`),
  KEY `idx_created`  (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: settings
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key`         VARCHAR(100) NOT NULL,
  `value`       TEXT         NULL DEFAULT NULL,
  `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default settings
INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
  ('app_name',       'EZKIRA'),
  ('app_version',    '1.0.0'),
  ('maintenance',    '0'),
  ('target_revenue', '0');

-- --------------------------------------------------------
-- Table: expenses
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `expenses` (
  `id`           INT UNSIGNED       NOT NULL AUTO_INCREMENT,
  `user_id`      INT UNSIGNED       NOT NULL,
  `category`     VARCHAR(50)        NULL DEFAULT NULL,
  `amount`       DECIMAL(15,2)      NOT NULL DEFAULT 0.00,
  `description`  VARCHAR(500)       NOT NULL DEFAULT '',
  `expense_date` DATE               NOT NULL,
  `receipt_path` VARCHAR(500)       NULL DEFAULT NULL,
  `receipt_name` VARCHAR(255)       NULL DEFAULT NULL,
  `created_at`   DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category`     (`category`),
  KEY `idx_expense_date` (`expense_date`),
  KEY `idx_user_id`      (`user_id`),
  CONSTRAINT `fk_expenses_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: expense_receipts  (multiple files per expense)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_receipts` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `expense_id` INT UNSIGNED NOT NULL,
  `path`       VARCHAR(500) NOT NULL,
  `name`       VARCHAR(255) NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_expense_id` (`expense_id`),
  CONSTRAINT `fk_receipts_expense` FOREIGN KEY (`expense_id`)
    REFERENCES `expenses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: revenue
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `revenues` (
  `id`          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED   NOT NULL,
  `platform`    VARCHAR(100)   NOT NULL DEFAULT '',
  `amount`      DECIMAL(15,2)  NOT NULL DEFAULT 0.00,
  `description` VARCHAR(500)   NOT NULL DEFAULT '',
  `sale_date`   DATE           NOT NULL,
  `notes`       TEXT           NULL DEFAULT NULL,
  `created_at`  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id`   (`user_id`),
  KEY `idx_sale_date` (`sale_date`),
  CONSTRAINT `fk_revenues_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: revenue_targets (monthly target per user)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `revenue_targets` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED  NOT NULL,
  `year`       SMALLINT      NOT NULL,
  `month`      TINYINT       NOT NULL,
  `amount`     DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `updated_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_ym` (`user_id`, `year`, `month`),
  CONSTRAINT `fk_revtarget_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: budget_pct (expense % allocations per user)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `budget_pct` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `category`   ENUM('opex','marketing','cogs') NOT NULL,
  `pct`        DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_cat` (`user_id`, `category`),
  CONSTRAINT `fk_budgetpct_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: password_resets
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(64)  NOT NULL,
  `expires_at` DATETIME     NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  KEY `idx_token`   (`token`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
