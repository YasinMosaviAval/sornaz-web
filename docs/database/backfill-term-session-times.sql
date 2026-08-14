UPDATE academy_branch_bookings b
JOIN academy_branch_course_term_sessions s ON s.booking_id = b.booking_id
SET b.start_time = COALESCE(b.start_time, '10:00:00'),
    b.end_time = COALESCE(b.end_time, '11:30:00')
WHERE b.deleted_at IS NULL
  AND s.deleted_at IS NULL
  AND (b.start_time IS NULL OR b.end_time IS NULL);
