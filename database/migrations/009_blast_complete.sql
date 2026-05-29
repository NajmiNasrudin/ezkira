-- Migration 009: Ensure blast_logs has ALL required columns
-- Safe to run on any state of the DB — all ADD COLUMN use IF NOT EXISTS
-- Run once in phpMyAdmin after selecting your database

ALTER TABLE `blast_logs`
  ADD COLUMN IF NOT EXISTS `status`
        ENUM('scheduled','queued','running','done','failed')
        NOT NULL DEFAULT 'queued'
        AFTER `id`,

  ADD COLUMN IF NOT EXISTS `provider`
        ENUM('fonnte','whatsapp_api')
        NOT NULL DEFAULT 'fonnte'
        AFTER `status`,

  ADD COLUMN IF NOT EXISTS `scheduled_at`
        DATETIME NULL DEFAULT NULL
        AFTER `total_recipients`,

  ADD COLUMN IF NOT EXISTS `started_at`
        DATETIME NULL DEFAULT NULL
        AFTER `scheduled_at`,

  ADD COLUMN IF NOT EXISTS `finished_at`
        DATETIME NULL DEFAULT NULL
        AFTER `started_at`,

  ADD COLUMN IF NOT EXISTS `recipient_ids`
        TEXT NULL DEFAULT NULL
        AFTER `finished_at`,

  ADD COLUMN IF NOT EXISTS `image_path`
        VARCHAR(500) NULL DEFAULT NULL
        AFTER `recipient_ids`,

  ADD COLUMN IF NOT EXISTS `blast_link`
        VARCHAR(500) NULL DEFAULT NULL
        AFTER `image_path`,

  ADD COLUMN IF NOT EXISTS `delay_seconds`
        TINYINT UNSIGNED NOT NULL DEFAULT 12
        COMMENT 'Min seconds between sends'
        AFTER `blast_link`;

-- Mark any existing old records (no status) as done so cron ignores them
UPDATE `blast_logs`
   SET `status` = 'done'
 WHERE (`sent_count` > 0 OR `failed_count` > 0)
   AND `status` NOT IN ('scheduled','queued','running','done','failed');

-- Performance index for cron query
ALTER TABLE `blast_logs`
  ADD INDEX IF NOT EXISTS `idx_status_sched` (`status`, `scheduled_at`);
