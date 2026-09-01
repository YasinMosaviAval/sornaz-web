ALTER TABLE academy_term_invoice_payments
  ADD COLUMN IF NOT EXISTS payment_method VARCHAR(30) NOT NULL DEFAULT 'zarinpal' AFTER gateway,
  ADD COLUMN IF NOT EXISTS payer_name VARCHAR(190) NULL AFTER payment_method,
  ADD COLUMN IF NOT EXISTS bank_card_type VARCHAR(40) NULL AFTER payer_name,
  ADD COLUMN IF NOT EXISTS description VARCHAR(1000) NULL AFTER gateway_message;

CREATE TABLE IF NOT EXISTS academy_subscription_periods (
  subscription_period_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  academy_id BIGINT UNSIGNED NOT NULL,
  period_start DATE NOT NULL,
  period_end DATE NOT NULL,
  amount BIGINT UNSIGNED NOT NULL DEFAULT 0,
  currency ENUM('IRT','IRR') NOT NULL DEFAULT 'IRT',
  status ENUM('free','pending','paid','expired','canceled') NOT NULL,
  due_date DATE NOT NULL,
  paid_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  updated_at DATETIME NOT NULL,
  updated_by BIGINT UNSIGNED NOT NULL,
  deleted_at DATETIME NULL,
  deleted_by BIGINT UNSIGNED NULL,
  UNIQUE KEY uq_academy_subscription_period (academy_id,period_start),
  KEY idx_academy_subscription_due (status,due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS academy_subscription_payments (
  subscription_payment_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  subscription_period_id BIGINT UNSIGNED NOT NULL,
  academy_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  amount BIGINT UNSIGNED NOT NULL,
  currency ENUM('IRT','IRR') NOT NULL DEFAULT 'IRT',
  callback_token CHAR(64) NOT NULL,
  authority VARCHAR(64) NULL,
  reference_id VARCHAR(64) NULL,
  status ENUM('created','pending','paid','failed','canceled') NOT NULL DEFAULT 'created',
  gateway_code INT NULL,
  gateway_message VARCHAR(500) NULL,
  requested_at DATETIME NULL,
  verified_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  updated_at DATETIME NOT NULL,
  updated_by BIGINT UNSIGNED NOT NULL,
  deleted_at DATETIME NULL,
  deleted_by BIGINT UNSIGNED NULL,
  UNIQUE KEY uq_subscription_payment_token (callback_token),
  UNIQUE KEY uq_subscription_payment_authority (authority),
  KEY idx_subscription_payment_period (subscription_period_id,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
