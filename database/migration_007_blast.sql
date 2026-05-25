-- Migration 007: WhatsApp blast logs
CREATE TABLE IF NOT EXISTS `blast_logs` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
