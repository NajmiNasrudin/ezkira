-- Migration 004: Add refund support to revenues table
ALTER TABLE `revenues`
    ADD COLUMN IF NOT EXISTS `entry_type` ENUM('sale','refund') NOT NULL DEFAULT 'sale' AFTER `platform`;
