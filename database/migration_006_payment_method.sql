-- Migration 006: Add payment_method column to revenues
ALTER TABLE `revenues`
    ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(50) NOT NULL DEFAULT 'cash' AFTER `entry_type`;
