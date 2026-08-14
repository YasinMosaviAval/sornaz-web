ALTER TABLE academy_branch_course_term_sessions
    ADD COLUMN delivery_mode ENUM('in_person','online') NOT NULL DEFAULT 'in_person' AFTER branch_url_id;

UPDATE academy_branch_course_term_sessions
SET delivery_mode='online'
WHERE branch_url_id IS NOT NULL;
