# Existing students in multiple terms

Deploy these existing service files together:
- Modules/Academy/Services/AcademyCourseService.php
- Modules/Academy/Services/ExistingMemberUserService.php

A student selected through the existing authenticated user-selection token reuses their live membership in the selected academy/branch. Each new term gets its own enrollment and invoice; the membership, student role, and lesson are not recreated. A repeated enrollment in the same term or waiting list is rejected. Existing inactive membership restrictions and receptionist approval rules remain in effect. There is no student-wide term-count limit; each term retains its own capacity and status checks.

The existing schedule mutex, transaction, and ScheduleGuard remain authoritative. Final enrollment or session activation rejects overlapping classroom, teacher, and student sessions, including the same user with separate branch memberships. Sessions whose end equals another session's start do not overlap. Undated terms have no actual sessions to compare; their dates are checked when scheduled. Pending requests retain the existing approval workflow.

No migration, new class, package, or app build is required. Both web and mobile use this service. Clear/reload PHP OPcache according to the host's existing deployment procedure. Roll back the two PHP files together if needed; preserve enrollments and invoices already committed.

Local validation: PHP lint; scripts/student_multiple_terms_test.cjs (shared membership, multiple terms, isolated student overlap, adjacent sessions, rollback, token/tenant checks); scripts/existing_member_user_test.cjs (existing user/staff security and duplicate guards). These SQLite fixture tests do not claim production or device verification. After deployment, use a test account to enroll in a non-overlapping term, then verify an overlapping term is rejected without an additional invoice; verify another academy's actor is denied.
