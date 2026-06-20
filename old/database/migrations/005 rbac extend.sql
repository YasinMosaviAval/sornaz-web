-- database/migrations/005_rbac_extend.sql
-- توسعه جداول RBAC برای پنل superadmin
-- ─────────────────────────────────────────────────────────────


-- ══ access_system_roles ══════════════════════════════════════
-- is_system: نقش‌های سیستمی که superadmin نمی‌تونه حذف کنه
-- color:     رنگ badge در UI (hex)
-- sort_order: ترتیب نمایش
ALTER TABLE `access_system_roles`
  ADD COLUMN `is_system`  tinyint(1)   DEFAULT 0    AFTER `description`,
  ADD COLUMN `color`      varchar(7)   DEFAULT NULL AFTER `is_system`,
  ADD COLUMN `sort_order` tinyint(4)   DEFAULT 0    AFTER `color`;

-- نقش‌های سیستمی رو mark کن
UPDATE `access_system_roles` SET is_system = 1, color = '#ef4444'
  WHERE name IN ('superadmin', 'admin');
UPDATE `access_system_roles` SET is_system = 1, color = '#f97316'
  WHERE name = 'support';
UPDATE `access_system_roles` SET color = '#8b5cf6' WHERE name = 'academy_owner';
UPDATE `access_system_roles` SET color = '#3b82f6' WHERE name = 'academy_manager';
UPDATE `access_system_roles` SET color = '#06b6d4' WHERE name = 'academy_teacher';
UPDATE `access_system_roles` SET color = '#10b981' WHERE name = 'academy_student';
UPDATE `access_system_roles` SET color = '#6366f1' WHERE name = 'verified_teacher';
UPDATE `access_system_roles` SET color = '#f59e0b' WHERE name = 'vip_member';
UPDATE `access_system_roles` SET color = '#84cc16' WHERE name = 'author';
UPDATE `access_system_roles` SET color = '#6b7280' WHERE name = 'user';


-- ══ user_roles ════════════════════════════════════════════════
-- ردیابی: چه کسی این نقش رو داد و کِی
ALTER TABLE `user_roles`
  ADD COLUMN `granted_by`  bigint(20)   DEFAULT NULL AFTER `role_id`,
  ADD COLUMN `granted_at`  datetime     DEFAULT CURRENT_TIMESTAMP AFTER `granted_by`,
  ADD COLUMN `expires_at`  datetime     DEFAULT NULL AFTER `granted_at`,
  ADD COLUMN `note`        varchar(255) DEFAULT NULL AFTER `expires_at`;


-- ══ user_permissions ═════════════════════════════════════════
-- مجوزهای مستقیم با قابلیت انقضا و یادداشت
ALTER TABLE `user_permissions`
  ADD COLUMN `granted_by`  bigint(20)   DEFAULT NULL AFTER `permission_id`,
  ADD COLUMN `granted_at`  datetime     DEFAULT CURRENT_TIMESTAMP AFTER `granted_by`,
  ADD COLUMN `expires_at`  datetime     DEFAULT NULL AFTER `granted_at`,
  ADD COLUMN `note`        varchar(255) DEFAULT NULL AFTER `expires_at`;


-- ══ user_permission_cache ════════════════════════════════════
-- اضافه کردن source برای debug — این permission از role میاد یا direct
ALTER TABLE `user_permission_cache`
  ADD COLUMN `source`      enum('role','direct') DEFAULT 'role' AFTER `permission_name`,
  ADD COLUMN `updated_at`  datetime DEFAULT CURRENT_TIMESTAMP   AFTER `source`,

  ADD PRIMARY KEY (`user_id`, `permission_name`);


-- ══ user_audit_logs ══════════════════════════════════════════
-- ثبت لاگ تمام تغییرات access توسط superadmin
-- (این جدول احتمالاً وجود داره، فقط چک کن)
CREATE TABLE IF NOT EXISTS `user_audit_logs` (
  `id`          bigint(20)   NOT NULL AUTO_INCREMENT,
  `user_id`     bigint(20)   DEFAULT NULL,
  `action`      varchar(50)  DEFAULT NULL,
  `entity_type` varchar(50)  DEFAULT NULL,
  `entity_id`   bigint(20)   DEFAULT NULL,
  `old_data`    longtext     DEFAULT NULL,
  `new_data`    longtext     DEFAULT NULL,
  `ip`          varchar(50)  DEFAULT NULL,
  `user_agent`  text         DEFAULT NULL,
  `created_at`  datetime     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user`   (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;