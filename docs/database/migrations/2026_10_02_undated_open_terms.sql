-- Apply before uploading the PHP code that creates undated open terms.
-- Existing dates are preserved; only new open terms may have no scheduled dates.
ALTER TABLE academy_branch_course_terms
    MODIFY COLUMN start_date DATE NULL,
    MODIFY COLUMN end_date DATE NULL;
