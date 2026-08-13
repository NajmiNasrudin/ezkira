-- Migration 008: Rename revenue_targets.amount → target_amount
-- Run this once on any existing database created from the old schema.sql

ALTER TABLE `revenue_targets`
    CHANGE COLUMN `amount` `target_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00;
