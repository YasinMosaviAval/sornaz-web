ALTER TABLE academy_branch_course_term_session_attendances
    MODIFY COLUMN status ENUM('present','absent','late','leave','excused_absence','online') NOT NULL;
