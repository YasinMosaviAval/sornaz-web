CREATE TABLE IF NOT EXISTS academy_branch_course_term_schedule_skips (
    term_schedule_skip_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    term_id BIGINT UNSIGNED NOT NULL,
    intended_session_number INT UNSIGNED NOT NULL,
    skipped_date DATE NOT NULL,
    replacement_date DATE NOT NULL,
    reason_type ENUM('organization_holiday','national_holiday') NOT NULL,
    reason_text VARCHAR(500) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NULL,
    updated_by BIGINT UNSIGNED NULL,
    deleted_at DATETIME NULL,
    deleted_by BIGINT UNSIGNED NULL,
    PRIMARY KEY (term_schedule_skip_id),
    KEY idx_term_schedule_skips_term (term_id, intended_session_number, deleted_at),
    KEY idx_term_schedule_skips_date (skipped_date, reason_type),
    CONSTRAINT fk_term_schedule_skips_term FOREIGN KEY (term_id)
        REFERENCES academy_branch_course_terms (term_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
