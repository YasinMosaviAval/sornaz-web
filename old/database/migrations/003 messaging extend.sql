-- database/migrations/003_messaging_extend.sql

-- ── conversations ─────────────────────────────────────────────
ALTER TABLE `conversations`
  ADD COLUMN `last_message_id` bigint(20)   DEFAULT NULL  AFTER `title`,
  ADD COLUMN `updated_at`      datetime     DEFAULT NULL  AFTER `created_at`,
  ADD COLUMN `deleted_at`      datetime     DEFAULT NULL  AFTER `updated_at`,

  ADD KEY `idx_updated` (`updated_at`),
  ADD KEY `idx_last_msg` (`last_message_id`);

-- ── conversation_members ──────────────────────────────────────
ALTER TABLE `conversation_members`
  ADD COLUMN `last_read_message_id` bigint(20)  DEFAULT NULL  AFTER `role`,
  ADD COLUMN `is_muted`             tinyint(1)  DEFAULT 0     AFTER `last_read_message_id`,
  ADD COLUMN `left_at`              datetime    DEFAULT NULL  AFTER `is_muted`,

  ADD UNIQUE KEY `uq_conv_user` (`conversation_id`, `user_id`);

-- ── user_messages ─────────────────────────────────────────────
ALTER TABLE `user_messages`
  ADD COLUMN `reply_to_id` bigint(20)   DEFAULT NULL  AFTER `content`,
  ADD COLUMN `file_path`   varchar(500) DEFAULT NULL  AFTER `reply_to_id`,
  ADD COLUMN `file_size`   int(11)      DEFAULT NULL  AFTER `file_path`,
  ADD COLUMN `file_name`   varchar(255) DEFAULT NULL  AFTER `file_size`,
  ADD COLUMN `read_at`     datetime     DEFAULT NULL  AFTER `is_read`,
  ADD COLUMN `edited_at`   datetime     DEFAULT NULL  AFTER `read_at`,
  ADD COLUMN `deleted_at`  datetime     DEFAULT NULL  AFTER `edited_at`,

  ADD KEY `idx_conversation` (`conversation_id`),
  ADD KEY `idx_sender`       (`sender_id`),
  ADD KEY `idx_created`      (`created_at`),
  ADD KEY `idx_reply`        (`reply_to_id`);