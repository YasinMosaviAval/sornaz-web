ALTER TABLE academy_branch_course_term_sessions
    ADD INDEX idx_term_sessions_term_date (term_id, deleted_at, booking_id),
    ADD INDEX idx_term_sessions_booking (booking_id);
ALTER TABLE academy_branch_bookings
    ADD INDEX idx_bookings_schedule (requested_date, start_time, end_time, status, deleted_at);
ALTER TABLE academy_branch_course_terms
    ADD INDEX idx_terms_course_deleted (course_id, deleted_at);
ALTER TABLE academy_branch_courses
    ADD INDEX idx_courses_branch_deleted (branch_id, deleted_at);
ALTER TABLE academy_branch_course_term_enrollments
    ADD INDEX idx_enrollments_term_type (term_id, type, deleted_at);
ALTER TABLE academy_branch_course_term_session_attendances
    ADD INDEX idx_attendance_session_member (session_id, member_id, deleted_at);
