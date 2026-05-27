-- Migration 003: Add provider column to blast_logs
-- Run once in phpMyAdmin

ALTER TABLE `blast_logs`
  ADD COLUMN IF NOT EXISTS `provider` ENUM('fonnte','whatsapp_api') NOT NULL DEFAULT 'fonnte'
  AFTER `status`;
