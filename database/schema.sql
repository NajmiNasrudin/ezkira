-- EZKIRA — Database Schema
-- Import fail ini dalam phpMyAdmin SELEPAS pilih database anda
-- Jangan import keseluruhan fail ini terus — pastikan database sudah dipilih dahulu

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(100)     NOT NULL,
  `pic_name`         VARCHAR(100)     NOT NULL DEFAULT '',
  `email`            VARCHAR(255)     NOT NULL,
  `password`         VARCHAR(255)     NOT NULL,
  `whatsapp_number`  VARCHAR(20)      NOT NULL DEFAULT '',
  `role`             ENUM('admin','team','client') NOT NULL DEFAULT 'client',
  `language`         ENUM('en','ms')  NOT NULL DEFAULT 'en',
  `dark_mode`        TINYINT(1)       NOT NULL DEFAULT 0,
  `profile_image`    VARCHAR(500)     NULL DEFAULT NULL,
  `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
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
INSERT INTO `settings` (`key`, `value`) VALUES
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
  `category`     ENUM('opex','marketing','cogs') NOT NULL,
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
