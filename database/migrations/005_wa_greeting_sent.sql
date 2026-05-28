ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `wa_greeting_sent` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
    COMMENT '1 = WA greeting message was successfully sent on registration';
