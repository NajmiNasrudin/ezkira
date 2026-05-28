-- Migration 004: add delay_seconds to blast_logs
-- Run once in cPanel phpMyAdmin

ALTER TABLE `blast_logs`
  ADD COLUMN IF NOT EXISTS `delay_seconds` TINYINT UNSIGNED NOT NULL DEFAULT 12
    COMMENT 'Min seconds between sends; cron uses rand(delay, delay+5)'
    AFTER `blast_link`;
