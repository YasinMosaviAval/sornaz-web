ALTER TABLE academy_branch_course_term_sessions
    ADD COLUMN session_type ENUM('regular', 'makeup') NOT NULL DEFAULT 'regular' AFTER delivery_mode,
    ADD COLUMN makeup_for_session_id BIGINT UNSIGNED NULL AFTER session_type,
    ADD COLUMN cancellation_status ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none' AFTER makeup_for_session_id,
    ADD COLUMN cancellation_requested_at DATETIME NULL AFTER cancellation_status,
    ADD COLUMN cancellation_requested_by BIGINT UNSIGNED NULL AFTER cancellation_requested_at,
    ADD COLUMN cancellation_decided_at DATETIME NULL AFTER cancellation_requested_by,
    ADD COLUMN cancellation_decided_by BIGINT UNSIGNED NULL AFTER cancellation_decided_at,
    ADD INDEX idx_term_sessions_makeup_for (makeup_for_session_id),
    ADD INDEX idx_term_sessions_cancellation (term_id, cancellation_status),
    ADD CONSTRAINT fk_term_sessions_makeup_for
        FOREIGN KEY (makeup_for_session_id)
        REFERENCES academy_branch_course_term_sessions (term_session_id)
        ON DELETE SET NULL;
