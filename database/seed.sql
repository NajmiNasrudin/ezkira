-- EZKIRA — Seed Data
-- Default admin user: admin@ezkira.com / Admin@1234
-- Import fail ini SELEPAS schema.sql

INSERT INTO `users` (`name`, `email`, `password`, `whatsapp_number`, `role`, `language`, `dark_mode`, `created_at`, `updated_at`)
VALUES (
  'System Admin',
  'admin@ezkira.com',
  '$2y$12$PPVwbcQhT6HUKFkdbqUBnOrEQIrUhhX6Qs6GzS7BlHrhBAfq1Z.wO',  -- Admin@1234
  '+60111234567',
  'admin',
  'en',
  0,
  NOW(),
  NOW()
);
