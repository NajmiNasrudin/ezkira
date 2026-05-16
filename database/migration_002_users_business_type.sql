-- Migration 002: Add business_type columns to users table
-- Run this in phpMyAdmin on production DB (unztjxybjh_ezkira)
-- These columns are required for user registration to work

ALTER TABLE `users`
  ADD COLUMN `business_type`       VARCHAR(50)  NULL DEFAULT NULL AFTER `profile_image`,
  ADD COLUMN `business_type_other` VARCHAR(255) NULL DEFAULT NULL AFTER `business_type`;
