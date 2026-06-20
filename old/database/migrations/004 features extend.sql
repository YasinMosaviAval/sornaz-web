-- database/migrations/004_features_extend.sql
-- توسعه جداول: polls, ratings, instruments, availabilities
-- ─────────────────────────────────────────────────────────────


-- ══ user_polls ════════════════════════════════════════════════
ALTER TABLE `user_polls`
  ADD COLUMN `title`        varchar(255) DEFAULT NULL         AFTER `creator_id`,
  ADD COLUMN `description`  text         DEFAULT NULL         AFTER `title`,
  ADD COLUMN `is_anonymous` tinyint(1)   DEFAULT 0           AFTER `type`,
  ADD COLUMN `votes_count`  int(11)      DEFAULT 0           AFTER `status`,
  ADD COLUMN `updated_at`   datetime     DEFAULT NULL         AFTER `created_at`,
  ADD COLUMN `deleted_at`   datetime     DEFAULT NULL         AFTER `updated_at`,

  ADD KEY `idx_creator`     (`creator_id`),
  ADD KEY `idx_status`      (`status`),
  ADD KEY `idx_target`      (`target_type`, `target_id`),
  ADD KEY `idx_expires`     (`expires_at`);


-- ══ user_poll_options ════════════════════════════════════════
ALTER TABLE `user_poll_options`
  ADD COLUMN `votes_count`  int(11)      DEFAULT 0           AFTER `text`,
  ADD COLUMN `sort_order`   tinyint(4)   DEFAULT 0           AFTER `votes_count`;


-- ══ user_poll_votes ══════════════════════════════════════════
-- جلوگیری از رأی تکراری: هر کاربر فقط یک‌بار می‌تونه به هر گزینه رأی بده
ALTER TABLE `user_poll_votes`
  ADD UNIQUE KEY `uq_user_poll_option` (`poll_id`, `option_id`, `user_id`);


-- ══ user_ratings ═════════════════════════════════════════════
ALTER TABLE `user_ratings`
  ADD COLUMN `is_private`   tinyint(1)   DEFAULT 0           AFTER `review`,
  ADD COLUMN `is_anonymous` tinyint(1)   DEFAULT 0           AFTER `is_private`,
  ADD COLUMN `updated_at`   datetime     DEFAULT NULL         AFTER `created_at`,
  ADD COLUMN `deleted_at`   datetime     DEFAULT NULL         AFTER `updated_at`,

  -- جلوگیری از امتیاز تکراری
  ADD UNIQUE KEY `uq_user_item`  (`user_id`, `item_type`, `item_id`),
  ADD KEY `idx_item`             (`item_type`, `item_id`),
  ADD KEY `idx_is_private`       (`is_private`);


-- ══ user_rating_summaries ════════════════════════════════════
-- primary key اضافه می‌کنیم که upsert درست کار کنه
ALTER TABLE `user_rating_summaries`
  ADD PRIMARY KEY (`target_type`, `target_id`);


-- ══ user_instruments ═════════════════════════════════════════
ALTER TABLE `user_instruments`
  ADD COLUMN `is_primary`   tinyint(1)   DEFAULT 0           AFTER `years_of_experience`,
  ADD COLUMN `created_at`   datetime     DEFAULT CURRENT_TIMESTAMP AFTER `is_primary`,
  ADD COLUMN `updated_at`   datetime     DEFAULT NULL         AFTER `created_at`,

  ADD PRIMARY KEY (`user_id`, `instrument_id`);


-- ══ user_specialties ═════════════════════════════════════════
ALTER TABLE `user_specialties`
  ADD COLUMN `updated_at`   datetime     DEFAULT NULL         AFTER `created_at`,
  ADD COLUMN `deleted_at`   datetime     DEFAULT NULL         AFTER `updated_at`;


-- ══ user_availabilities ══════════════════════════════════════
ALTER TABLE `user_availabilities`
  ADD COLUMN `label`        varchar(100) DEFAULT NULL         AFTER `type`,
  -- مثلاً: "کلاس خصوصی"، "تدریس گروهی"، "جلسه آزاد"
  ADD COLUMN `is_recurring` tinyint(1)   DEFAULT 1           AFTER `label`,
  ADD COLUMN `updated_at`   datetime     DEFAULT NULL         AFTER `created_at`,

  ADD KEY `idx_user_day`    (`user_id`, `day_of_week`);


-- ══ user_availability_cache ══════════════════════════════════
-- برای query سریع‌تر
ALTER TABLE `user_availability_cache`
  ADD UNIQUE KEY `uq_user_date_slot` (`user_id`, `date`, `start_time`),
  ADD KEY `idx_date_avail`  (`date`, `is_available`);