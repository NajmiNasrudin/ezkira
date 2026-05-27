-- Migration 002: Add scheduling + progress tracking to blast_logs
-- Run once in phpMyAdmin after selecting your database
-- Safe to run multiple times (IF NOT EXISTS / IGNORE)

ALTER TABLE `blast_logs`
  ADD COLUMN IF NOT EXISTS `status`       ENUM('scheduled','queued','running','done','failed') NOT NULL DEFAULT 'done',
  ADD COLUMN IF NOT EXISTS `scheduled_at` DATETIME     NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `started_at`   DATETIME     NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `finished_at`  DATETIME     NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `recipient_ids` TEXT        NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `image_path`   VARCHAR(500) NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `blast_link`   VARCHAR(500) NULL DEFAULT NULL;

-- Mark old completed records as done so they don't appear as queued
UPDATE `blast_logs`
   SET `status` = 'done'
 WHERE `status` = 'done'   -- default; no-op but safe
    OR (`sent_count` > 0 OR `failed_count` > 0);

-- Index for cron query performance
ALTER TABLE `blast_logs`
  ADD INDEX IF NOT EXISTS `idx_status_sched` (`status`, `scheduled_at`);
