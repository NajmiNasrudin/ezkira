-- Migration 011: Widen image_path to TEXT to support JSON array of multiple images
-- Also widen custom_message to TEXT if not already
-- Safe to run on any state — MODIFY COLUMN is idempotent in effect

ALTER TABLE `blast_logs`
  MODIFY COLUMN `image_path`     TEXT          NULL DEFAULT NULL,
  MODIFY COLUMN `custom_message` MEDIUMTEXT    NULL DEFAULT NULL;
