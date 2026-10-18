-- Apply separately on the reviewed database before enabling result uploads.
-- No existing user, course or financial rows are changed.
CREATE TABLE IF NOT EXISTS `f_practice_sessions` (
  `session_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `client_run_id` CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `tool_key` VARCHAR(64) NOT NULL,
  `music` VARCHAR(16) NOT NULL,
  `curriculum_version` INT UNSIGNED NOT NULL,
  `level_number` INT UNSIGNED NOT NULL,
  `mode` VARCHAR(16) NOT NULL,
  `status` VARCHAR(16) NOT NULL DEFAULT 'in_progress',
  `answer_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `correct_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `current_streak` INT UNSIGNED NOT NULL DEFAULT 0,
  `best_streak` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `uq_practice_user_run` (`user_id`, `client_run_id`),
  KEY `idx_practice_user_progress` (`user_id`, `tool_key`, `music`, `curriculum_version`, `status`, `level_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Old local completion marks have no answer history. Keep their origin explicit
-- instead of inventing passed sessions or answers during migration.
CREATE TABLE IF NOT EXISTS `f_practice_legacy_progress` (
  `user_id` BIGINT UNSIGNED NOT NULL,
  `tool_key` VARCHAR(64) NOT NULL,
  `music` VARCHAR(16) NOT NULL,
  `curriculum_version` INT UNSIGNED NOT NULL,
  `level_number` INT UNSIGNED NOT NULL,
  `source` VARCHAR(64) NOT NULL,
  `received_at` DATETIME NOT NULL,
  PRIMARY KEY (`user_id`, `tool_key`, `music`, `curriculum_version`, `level_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `f_practice_answers` (
  `answer_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` BIGINT UNSIGNED NOT NULL,
  `sequence_number` INT UNSIGNED NOT NULL,
  `expected_quarter_steps` TINYINT UNSIGNED NOT NULL,
  `selected_quarter_steps` TINYINT UNSIGNED NOT NULL,
  `root_midi` TINYINT UNSIGNED NOT NULL,
  `direction` VARCHAR(16) NOT NULL,
  `is_correct` TINYINT UNSIGNED NOT NULL,
  `elapsed_ms` INT UNSIGNED NOT NULL,
  `answered_at_client` VARCHAR(40) NOT NULL,
  `payload_hash` CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `received_at` DATETIME NOT NULL,
  PRIMARY KEY (`answer_id`),
  UNIQUE KEY `uq_practice_session_sequence` (`session_id`, `sequence_number`),
  CONSTRAINT `fk_practice_answer_session` FOREIGN KEY (`session_id`) REFERENCES `f_practice_sessions` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
