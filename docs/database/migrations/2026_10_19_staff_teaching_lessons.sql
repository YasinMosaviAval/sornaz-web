-- Additive, rerunnable; select the application database first.
-- Existing contracts keep their primary user_lesson_id and need no backfill.
CREATE TABLE IF NOT EXISTS p_academy_branch_member_contract_lessons (
    member_contract_id BIGINT UNSIGNED NOT NULL,
    user_lesson_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (member_contract_id,user_lesson_id),
    INDEX idx_staff_contract_lesson (user_lesson_id,member_contract_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
