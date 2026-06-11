-- Migration 013: Expand expenses.category from ENUM to VARCHAR(50)
-- Reason: ENUM('opex','marketing','cogs','liability') did not include 'ppe' and 'purchases',
-- causing MySQL to silently store NULL when those values were submitted.
-- VARCHAR(50) allows all categories without requiring schema changes for new ones.

ALTER TABLE `expenses`
  MODIFY COLUMN `category` VARCHAR(50) NULL DEFAULT NULL;

-- Optional: clear any empty-string categories left by old inserts
UPDATE `expenses` SET `category` = NULL WHERE `category` = '';
