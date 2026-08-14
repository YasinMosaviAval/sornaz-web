START TRANSACTION;

CREATE TEMPORARY TABLE invalid_test_terms (
    term_id BIGINT UNSIGNED PRIMARY KEY
) ENGINE=InnoDB;

INSERT INTO invalid_test_terms (term_id)
SELECT t.term_id
FROM academy_branch_course_terms t
WHERE t.deleted_at IS NULL
  AND EXISTS (
      SELECT 1 FROM translations tr
      WHERE tr.table_name = 'academy_branch_course_terms'
        AND tr.table_id = t.term_id
        AND tr.field = 'code'
        AND tr.value LIKE 'test-term-c%'
        AND tr.deleted_at IS NULL
  )
  AND (
      NOT EXISTS (
          SELECT 1 FROM academy_branch_course_term_enrollments e
          WHERE e.term_id = t.term_id AND e.type = 'teacher' AND e.deleted_at IS NULL
      )
      OR NOT EXISTS (
          SELECT 1 FROM academy_branch_course_term_enrollments e
          WHERE e.term_id = t.term_id AND e.type = 'student' AND e.deleted_at IS NULL
      )
  );

CREATE TEMPORARY TABLE invalid_test_sessions (term_session_id BIGINT UNSIGNED PRIMARY KEY, booking_id BIGINT UNSIGNED);
INSERT INTO invalid_test_sessions
SELECT s.term_session_id, s.booking_id
FROM academy_branch_course_term_sessions s
JOIN invalid_test_terms x ON x.term_id = s.term_id;

CREATE TEMPORARY TABLE invalid_test_invoices (term_invoice_id BIGINT UNSIGNED PRIMARY KEY);
INSERT INTO invalid_test_invoices
SELECT i.term_invoice_id
FROM academy_branch_course_term_invoices i
JOIN invalid_test_terms x ON x.term_id = i.term_id;

CREATE TEMPORARY TABLE invalid_test_attendances (session_attendance_id BIGINT UNSIGNED PRIMARY KEY);
INSERT INTO invalid_test_attendances
SELECT a.session_attendance_id
FROM academy_branch_course_term_session_attendances a
JOIN invalid_test_sessions s ON s.term_session_id = a.session_id;

DELETE n FROM user_notifications n
JOIN invalid_test_attendances a
  ON n.entity_type = 'academy_branch_course_term_session_attendances'
 AND n.entity_id = a.session_attendance_id;

DELETE tr FROM translations tr
JOIN invalid_test_attendances a ON a.session_attendance_id = tr.table_id
WHERE tr.table_name = 'academy_branch_course_term_session_attendances';

DELETE a FROM academy_branch_course_term_session_attendances a
JOIN invalid_test_attendances x ON x.session_attendance_id = a.session_attendance_id;

DELETE tr FROM translations tr
JOIN invalid_test_sessions s ON s.term_session_id = tr.table_id
WHERE tr.table_name = 'academy_branch_course_term_sessions';

DELETE i FROM academy_branch_course_term_invoice_installments i
JOIN invalid_test_invoices x ON x.term_invoice_id = i.invoice_id;

DELETE i FROM academy_branch_course_term_invoices i
JOIN invalid_test_invoices x ON x.term_invoice_id = i.term_invoice_id;

DELETE e FROM academy_branch_course_term_enrollments e
JOIN invalid_test_terms x ON x.term_id = e.term_id;

DELETE s FROM academy_branch_course_term_sessions s
JOIN invalid_test_sessions x ON x.term_session_id = s.term_session_id;

DELETE b FROM academy_branch_bookings b
JOIN invalid_test_sessions x ON x.booking_id = b.booking_id;

DELETE tr FROM translations tr
JOIN invalid_test_terms x ON x.term_id = tr.table_id
WHERE tr.table_name = 'academy_branch_course_terms';

DELETE t FROM academy_branch_course_terms t
JOIN invalid_test_terms x ON x.term_id = t.term_id;

COMMIT;
