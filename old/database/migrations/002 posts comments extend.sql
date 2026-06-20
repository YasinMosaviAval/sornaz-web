-- database/migrations/002_posts_comments_extend.sql
-- ستون‌هایی که به جداول موجود اضافه می‌شن

-- ── user_posts ────────────────────────────────────────────────
ALTER TABLE `user_posts`
  ADD COLUMN `title`       varchar(500)  DEFAULT NULL AFTER `author_id`,
  ADD COLUMN `content`     longtext      DEFAULT NULL AFTER `title`,
  ADD COLUMN `excerpt`     text          DEFAULT NULL AFTER `content`,
  ADD COLUMN `views_count` int(11)       DEFAULT 0    AFTER `visibility`,
  ADD COLUMN `approved_by` bigint(20)    DEFAULT NULL AFTER `views_count`,
  ADD COLUMN `approved_at` datetime      DEFAULT NULL AFTER `approved_by`,

  ADD UNIQUE KEY `uq_slug` (`slug`),
  ADD KEY `idx_author`   (`author_id`),
  ADD KEY `idx_status`   (`status`),
  ADD KEY `idx_type`     (`type`),
  ADD KEY `idx_approved` (`approved_at`);

-- ── user_post_translations (چندزبانه) ────────────────────────
-- اگه سایت چندزبانه نیاز داری این جدول رو به جای ستون‌های title/content بالا استفاده کن
CREATE TABLE IF NOT EXISTS `user_post_translations` (
  `id`         bigint(20)   NOT NULL AUTO_INCREMENT,
  `post_id`    bigint(20)   DEFAULT NULL,
  `locale`     varchar(5)   DEFAULT NULL,
  `title`      varchar(500) DEFAULT NULL,
  `content`    longtext     DEFAULT NULL,
  `excerpt`    text         DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_post_locale` (`post_id`, `locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── user_comments ─────────────────────────────────────────────
ALTER TABLE `user_comments`
  ADD COLUMN `updated_at`  datetime   DEFAULT NULL              AFTER `ip`,
  ADD COLUMN `deleted_at`  datetime   DEFAULT NULL              AFTER `updated_at`,
  ADD COLUMN `approved_by` bigint(20) DEFAULT NULL              AFTER `deleted_at`,
  ADD COLUMN `approved_at` datetime   DEFAULT NULL              AFTER `approved_by`,

  ADD KEY `idx_post_id`  (`post_id`),
  ADD KEY `idx_user_id`  (`user_id`),
  ADD KEY `idx_parent`   (`parent_id`),
  ADD KEY `idx_status`   (`status`);

-- ── tags ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `tags` (
  `id`         bigint(20)   NOT NULL AUTO_INCREMENT,
  `name`       varchar(100) DEFAULT NULL,
  `slug`       varchar(100) DEFAULT NULL,
  `created_at` datetime     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;