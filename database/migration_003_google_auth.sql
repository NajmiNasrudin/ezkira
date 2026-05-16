-- Migration 003 — Google OAuth support
-- Run this once against your production database in phpMyAdmin
-- Safe to run multiple times (IF NOT EXISTS / IF column not exists)

ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `google_id` VARCHAR(100) NULL DEFAULT NULL AFTER `profile_image`;

-- Add unique index only if it doesn't already exist
-- (Some MySQL versions don't support IF NOT EXISTS on ADD INDEX — run manually if this fails)
ALTER TABLE `users`
    ADD UNIQUE KEY `uq_google_id` (`google_id`);
