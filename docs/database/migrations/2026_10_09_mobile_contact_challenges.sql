-- Apply on a reviewed test database before publishing the mobile contact API.
-- This migration does not alter existing accounts or contact values.
CREATE TABLE IF NOT EXISTS `f_mobile_contact_challenges` (
  `user_id` BIGINT UNSIGNED NOT NULL,
  `field` VARCHAR(8) NOT NULL,
  `destination` VARCHAR(254) NOT NULL,
  `code_hash` VARCHAR(255) NOT NULL,
  `sent_at` DATETIME NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `verified_at` DATETIME NULL,
  `consumed_at` DATETIME NULL,
  PRIMARY KEY (`user_id`, `field`),
  KEY `idx_mobile_contact_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
