ALTER TABLE `conversations`
    ADD COLUMN IF NOT EXISTS `avatar_path` VARCHAR(500) NULL AFTER `title`;
