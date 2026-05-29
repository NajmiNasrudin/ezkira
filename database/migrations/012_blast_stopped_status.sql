-- Migration 012: Add 'stopped' to blast_logs status ENUM
ALTER TABLE `blast_logs`
  MODIFY COLUMN `status`
    ENUM('scheduled','queued','running','done','failed','stopped')
    NOT NULL DEFAULT 'queued';
