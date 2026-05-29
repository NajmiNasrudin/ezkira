-- Migration 010: Add 'wasenderapi' as a blast provider option
-- Run once in phpMyAdmin

ALTER TABLE `blast_logs`
  MODIFY COLUMN `provider`
    ENUM('fonnte','whatsapp_api','wasenderapi')
    NOT NULL DEFAULT 'fonnte';
