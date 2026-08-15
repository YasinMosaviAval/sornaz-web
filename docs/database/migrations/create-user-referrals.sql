CREATE TABLE IF NOT EXISTS user_referrals (
  user_referral_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  invite_code VARCHAR(24) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  referred_by_user_id BIGINT UNSIGNED NULL,
  status ENUM('active','converted','blocked') NOT NULL DEFAULT 'active',
  converted_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by BIGINT UNSIGNED NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_by BIGINT UNSIGNED NULL,
  deleted_at DATETIME NULL,
  deleted_by BIGINT UNSIGNED NULL,
  PRIMARY KEY (user_referral_id),
  UNIQUE KEY uq_user_referrals_user (user_id),
  UNIQUE KEY uq_user_referrals_code (invite_code),
  KEY idx_user_referrals_referrer (referred_by_user_id),
  KEY idx_user_referrals_status (status, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
