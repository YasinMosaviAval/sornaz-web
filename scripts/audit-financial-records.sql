-- READ ONLY. Run on a backup or during a quiet period; do not publish results.
-- These tables must support transactions for rollback guarantees to hold.
SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES
WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN (
 'academy_branch_course_terms', 'academy_branch_bookings',
 'academy_branch_course_term_sessions', 'academy_branch_course_term_enrollments',
 'academy_branch_course_term_session_attendances',
 'academy_branch_course_term_invoices', 'academy_branch_course_term_invoice_installments',
 'academy_term_invoice_payments', 'translations', 'user_points'
) AND ENGINE<>'InnoDB';

-- Invoice totals that disagree with active installments, including missing rows.
SELECT i.term_invoice_id, i.term_id, i.payable_amount,
       COUNT(s.term_invoice_installment_id) AS installment_count,
       COALESCE(SUM(s.amount), 0) AS installment_total,
       COALESCE(SUM(CASE WHEN s.status='paid' THEN s.amount ELSE 0 END), 0) AS paid_total
FROM academy_branch_course_term_invoices i
LEFT JOIN academy_branch_course_term_invoice_installments s
  ON s.invoice_id=i.term_invoice_id AND s.deleted_at IS NULL
WHERE i.deleted_at IS NULL
GROUP BY i.term_invoice_id, i.term_id, i.payable_amount, i.status
HAVING installment_count=0 OR installment_total<>i.payable_amount
    OR (i.status='paid' AND paid_total<>i.payable_amount)
    OR (i.status='partial' AND (paid_total=0 OR paid_total>=i.payable_amount))
    OR (i.status IN ('draft','issued','canceled') AND paid_total>0);

-- Receipts whose invoice or installment is missing, hidden, or mismatched.
SELECT p.payment_id, p.invoice_id, p.installment_id, p.status, p.gateway_message
FROM academy_term_invoice_payments p
LEFT JOIN academy_branch_course_term_invoices i ON i.term_invoice_id=p.invoice_id
LEFT JOIN academy_branch_course_term_invoice_installments s ON s.term_invoice_installment_id=p.installment_id
WHERE p.status='paid' AND (i.term_invoice_id IS NULL OR i.deleted_at IS NOT NULL
 OR s.term_invoice_installment_id IS NULL OR s.deleted_at IS NOT NULL
 OR s.invoice_id<>p.invoice_id OR p.gateway_message='Verified payment requires manual reconciliation');

-- Attendance hidden by historical term/session deletion.
SELECT a.session_attendance_id, a.session_id, a.member_id, s.term_id
FROM academy_branch_course_term_session_attendances a
LEFT JOIN academy_branch_course_term_sessions s ON s.term_session_id=a.session_id
LEFT JOIN academy_branch_course_terms t ON t.term_id=s.term_id
WHERE a.deleted_at IS NULL AND (s.term_session_id IS NULL OR s.deleted_at IS NOT NULL
 OR t.term_id IS NULL OR t.deleted_at IS NOT NULL);

-- Duplicate active attendance records require review before any unique index.
SELECT session_id, member_id, COUNT(*) AS records
FROM academy_branch_course_term_session_attendances
WHERE deleted_at IS NULL GROUP BY session_id, member_id HAVING COUNT(*)>1;
