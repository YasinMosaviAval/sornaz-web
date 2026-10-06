<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

class AdminDashboardService
{
    public function data(int $actor, array $filters = []): array
    {
        $branchId = max(0, (int) ($filters['branchId'] ?? 0));
        $locale = locale() === 'en' ? 'en' : 'fa';
        $allowedIds = $this->allowedBranchIds($actor);
        if ($branchId && !in_array($branchId, $allowedIds, true)) {
            throw new RuntimeException('شما به این شعبه دسترسی ندارید.');
        }
        $scopeIds = $branchId ? [$branchId] : $allowedIds;
        $scopeSql = $scopeIds ? implode(',', $scopeIds) : '0';
        $branch = " AND c.branch_id IN ({$scopeSql})";
        $memberBranch = " AND m.branch_id IN ({$scopeSql})";
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01 00:00:00');
        $weekStart = date('Y-m-d 00:00:00', strtotime('-6 days'));

        $allowedSql = $allowedIds ? implode(',', $allowedIds) : '0';
        $branches = $this->rows("SELECT b.branch_id id,COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=b.branch_id AND field='name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',b.branch_id)) name FROM academy_branches b WHERE b.deleted_at IS NULL AND b.branch_id IN ({$allowedSql}) ORDER BY name");

        $activeStudents = $this->value("SELECT COUNT(DISTINCT m.user_id) FROM academy_branch_members m JOIN academy_branch_course_term_enrollments e ON e.member_id=m.member_id AND e.type='student' AND e.status='active' AND e.deleted_at IS NULL WHERE m.deleted_at IS NULL{$memberBranch}");
        $todayClasses = $this->value("SELECT COUNT(*) FROM academy_branch_course_term_sessions s JOIN academy_branch_bookings b ON b.booking_id=s.booking_id AND b.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=s.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL WHERE s.deleted_at IS NULL AND b.requested_date=?{$branch}", [$today]);
        $monthlyIncome = $this->value("SELECT COALESCE(SUM(i.amount),0) FROM academy_branch_course_term_invoice_installments i JOIN academy_branch_course_term_invoices f ON f.term_invoice_id=i.invoice_id AND f.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=f.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL WHERE i.deleted_at IS NULL AND i.status='paid' AND i.paid_at>=?{$branch}", [$monthStart]);
        $attendanceTotal = $this->value("SELECT COUNT(*) FROM academy_branch_course_term_session_attendances a JOIN academy_branch_course_term_sessions s ON s.term_session_id=a.session_id AND s.deleted_at IS NULL JOIN academy_branch_bookings b ON b.booking_id=s.booking_id AND b.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=s.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL WHERE a.deleted_at IS NULL AND b.requested_date>=?{$branch}", [date('Y-m-01')]);
        $attendancePresent = $this->value("SELECT COUNT(*) FROM academy_branch_course_term_session_attendances a JOIN academy_branch_course_term_sessions s ON s.term_session_id=a.session_id AND s.deleted_at IS NULL JOIN academy_branch_bookings b ON b.booking_id=s.booking_id AND b.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=s.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL WHERE a.deleted_at IS NULL AND a.status IN ('present','late','online') AND b.requested_date>=?{$branch}", [date('Y-m-01')]);
        $pendingPayments = $this->value("SELECT COUNT(*) FROM academy_branch_course_term_invoice_installments i JOIN academy_branch_course_term_invoices f ON f.term_invoice_id=i.invoice_id AND f.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=f.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL WHERE i.deleted_at IS NULL AND i.status<>'paid' AND i.due_date<?{$branch}", [$today]);
        $newMessages = $this->value("SELECT COUNT(*) FROM user_messages um WHERE um.deleted_at IS NULL AND um.type='message' AND um.receiver_user_id=? AND um.is_read=0", [$actor]);
        $newStudentsWeek = $this->value("SELECT COUNT(*) FROM academy_branch_course_term_enrollments e JOIN academy_branch_members m ON m.member_id=e.member_id AND m.deleted_at IS NULL WHERE e.deleted_at IS NULL AND e.type='student' AND e.created_at>=?{$memberBranch}", [$weekStart]);
        $pointsAwarded = $this->value("SELECT COALESCE(SUM(ABS(p.points)),0) FROM user_points p WHERE p.deleted_at IS NULL AND p.points>0 AND p.created_at>=? AND EXISTS(SELECT 1 FROM academy_branch_members m WHERE m.user_id=p.user_id AND m.branch_id IN ({$scopeSql}) AND m.deleted_at IS NULL)", [$monthStart]);

        $classes = $this->rows("SELECT s.term_session_id,b.start_time,c.branch_id,COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=c.branch_id AND field='name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',c.branch_id)) branch,COALESCE((SELECT value FROM translations WHERE table_name='lessons' AND table_id=c.lesson_id AND field='title' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('درس ',c.lesson_id)) instrument,(SELECT COUNT(*) FROM academy_branch_course_term_enrollments e WHERE e.term_id=t.term_id AND e.type='student' AND e.status='active' AND e.deleted_at IS NULL) student_count,(SELECT GROUP_CONCAT(COALESCE((SELECT value FROM translations WHERE table_name='users' AND table_id=m.user_id AND field='full_name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),u.username) SEPARATOR '، ') FROM academy_branch_course_term_enrollments e JOIN academy_branch_members m ON m.member_id=e.member_id AND m.deleted_at IS NULL JOIN users u ON u.user_id=m.user_id WHERE e.term_id=t.term_id AND e.type='student' AND e.status='active' AND e.deleted_at IS NULL) students,(SELECT COUNT(*) FROM academy_branch_course_term_enrollments e WHERE e.term_id=t.term_id AND e.type='teacher' AND e.status='active' AND e.deleted_at IS NULL) teacher_count,(SELECT GROUP_CONCAT(COALESCE((SELECT value FROM translations WHERE table_name='users' AND table_id=m.user_id AND field='full_name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),u.username) SEPARATOR '، ') FROM academy_branch_course_term_enrollments e JOIN academy_branch_members m ON m.member_id=e.member_id AND m.deleted_at IS NULL JOIN users u ON u.user_id=m.user_id WHERE e.term_id=t.term_id AND e.type='teacher' AND e.status='active' AND e.deleted_at IS NULL) teachers FROM academy_branch_course_term_sessions s JOIN academy_branch_bookings b ON b.booking_id=s.booking_id AND b.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=s.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL WHERE s.deleted_at IS NULL AND b.requested_date=?{$branch} ORDER BY b.start_time LIMIT 8", [$today]);
        $todayClassesList = array_map(fn ($r) => ['time' => substr((string) $r['start_time'], 0, 5), 'student' => (int) $r['student_count'] > 1 ? ((int) $r['student_count'] . ' هنرجو') : ($r['students'] ?: 'بدون هنرجو'), 'instrument' => $r['instrument'], 'teacher' => (int) $r['teacher_count'] > 1 ? ((int) $r['teacher_count'] . ' مدرس') : ($r['teachers'] ?: 'بدون مدرس'), 'branch' => $r['branch'], 'branchId' => (int) $r['branch_id']], $classes);

        $payments = $this->rows("SELECT i.amount,i.paid_at,c.branch_id,COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=c.branch_id AND field='name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',c.branch_id)) branch,COALESCE((SELECT value FROM translations WHERE table_name='users' AND table_id=m.user_id AND field='full_name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),u.username,CONCAT('کاربر ',m.user_id)) name FROM academy_branch_course_term_invoice_installments i JOIN academy_branch_course_term_invoices f ON f.term_invoice_id=i.invoice_id AND f.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=f.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL LEFT JOIN academy_branch_members m ON m.member_id=f.member_id LEFT JOIN users u ON u.user_id=m.user_id WHERE i.deleted_at IS NULL AND i.status='paid'{$branch} ORDER BY i.paid_at DESC LIMIT 6");
        $recentPayments = array_map(fn ($r) => ['name' => $r['name'], 'amount' => $this->amount($r['amount']), 'time' => $this->relative($r['paid_at']), 'branch' => $r['branch'], 'branchId' => (int) $r['branch_id']], $payments);

        $deposits = $this->rows("SELECT p.amount,p.method,p.paid_at,c.branch_id,COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=c.branch_id AND field='name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',c.branch_id)) branch FROM financial_system_payments p JOIN academy_branch_course_term_invoices f ON f.term_invoice_id=p.invoice_id AND f.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=f.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL WHERE p.deleted_at IS NULL AND p.record_type='academy_term' AND p.status='paid' AND COALESCE(p.gateway_message,'')<>'Verified payment requires manual reconciliation'{$branch} ORDER BY p.paid_at DESC LIMIT 6");
        $methods = ['cash' => 'نقدی', 'card' => 'کارت', 'bank_transfer' => 'انتقال بانکی', 'online' => 'آنلاین', 'pos' => 'کارت‌خوان', 'card_to_card' => 'کارت به کارت'];
        $recentDeposits = array_map(fn ($r) => ['title' => 'واریز ' . ($methods[$r['method']] ?? 'مالی'), 'amount' => $this->amount($r['amount']), 'bank' => $methods[$r['method']] ?? $r['method'], 'time' => $this->relative($r['paid_at']), 'branch' => $r['branch'], 'branchId' => (int) $r['branch_id']], $deposits);

        $absences = $this->rows("SELECT a.created_at,b.start_time,c.branch_id,COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=c.branch_id AND field='name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',c.branch_id)) branch,COALESCE((SELECT value FROM translations WHERE table_name='lessons' AND table_id=c.lesson_id AND field='title' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('درس ',c.lesson_id)) instrument,COALESCE((SELECT value FROM translations WHERE table_name='users' AND table_id=m.user_id AND field='full_name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),u.username) student FROM academy_branch_course_term_session_attendances a JOIN academy_branch_course_term_sessions s ON s.term_session_id=a.session_id AND s.deleted_at IS NULL JOIN academy_branch_bookings b ON b.booking_id=s.booking_id AND b.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=s.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL JOIN academy_branch_members m ON m.member_id=a.member_id AND m.deleted_at IS NULL JOIN users u ON u.user_id=m.user_id WHERE a.deleted_at IS NULL AND a.status IN ('absent','excused_absence') AND b.requested_date=?{$branch} ORDER BY b.start_time DESC LIMIT 6", [$today]);
        $todayAbsences = array_map(fn ($r) => ['student' => $r['student'], 'instrument' => $r['instrument'], 'teacher' => 'ثبت حضور و غیاب', 'time' => substr((string) $r['start_time'], 0, 5), 'branch' => $r['branch'], 'branchId' => (int) $r['branch_id']], $absences);

        $messages = $this->rows("SELECT um.*,COALESCE((SELECT value FROM translations WHERE table_name='users' AND table_id=um.sender_id AND field='full_name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),u.username,CONCAT('کاربر ',um.sender_id)) sender,COALESCE((SELECT value FROM translations WHERE table_name='user_messages' AND table_id=um.user_message_id AND field='message' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),'') content,(SELECT bm.branch_id FROM academy_branch_members bm WHERE bm.user_id=um.sender_id AND bm.deleted_at IS NULL ORDER BY bm.member_id LIMIT 1) branch_id,(SELECT COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=bm.branch_id AND field='name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',bm.branch_id)) FROM academy_branch_members bm WHERE bm.user_id=um.sender_id AND bm.deleted_at IS NULL ORDER BY bm.member_id LIMIT 1) branch FROM user_messages um LEFT JOIN users u ON u.user_id=um.sender_id WHERE um.deleted_at IS NULL AND um.type='message' AND um.receiver_user_id=? AND um.is_read=0 ORDER BY um.created_at DESC LIMIT 6", [$actor]);
        $unreadMessages = array_map(fn ($r) => ['from' => $r['sender'], 'preview' => $r['content'], 'time' => $this->relative($r['created_at']), 'branch' => $r['branch'] ?: 'بدون شعبه', 'branchId' => $r['branch_id'] ? (int) $r['branch_id'] : null], $messages);

        $registrations = $this->rows("SELECT e.created_at,c.branch_id,COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=c.branch_id AND field='name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',c.branch_id)) branch,COALESCE((SELECT value FROM translations WHERE table_name='lessons' AND table_id=c.lesson_id AND field='title' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('درس ',c.lesson_id)) instrument,COALESCE((SELECT value FROM translations WHERE table_name='users' AND table_id=m.user_id AND field='full_name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),u.username) name FROM academy_branch_course_term_enrollments e JOIN academy_branch_members m ON m.member_id=e.member_id AND m.deleted_at IS NULL JOIN users u ON u.user_id=m.user_id JOIN academy_branch_course_terms t ON t.term_id=e.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL WHERE e.deleted_at IS NULL AND e.type='student'{$branch} ORDER BY e.created_at DESC LIMIT 6");
        $recentRegistrations = array_map(fn ($r) => ['name' => $r['name'], 'instrument' => $r['instrument'], 'date' => $this->relative($r['created_at']), 'branch' => $r['branch'], 'branchId' => (int) $r['branch_id']], $registrations);

        $holidayBranch = " AND m.branch_id IN ({$scopeSql})";
        $holidays = $this->rows("SELECT x.date,x.unavailable_type type,m.branch_id,COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=m.branch_id AND field='name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',m.branch_id)) branch,COALESCE((SELECT value FROM translations WHERE table_name='users' AND table_id=x.user_id AND field='full_name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),u.username) person FROM user_availabilities x JOIN users u ON u.user_id=x.user_id LEFT JOIN academy_branch_members m ON m.user_id=x.user_id AND m.deleted_at IS NULL WHERE x.unavailable_type IS NOT NULL AND x.deleted_at IS NULL AND x.date>=?{$holidayBranch} GROUP BY x.user_availability_id ORDER BY x.date LIMIT 6", [$today]);
        $types = ['holiday' => 'تعطیلی', 'leave' => 'مرخصی', 'unavailable' => 'عدم دسترسی'];
        $upcomingHolidays = array_map(fn ($r) => ['title' => ($types[$r['type']] ?? $r['type']) . ($r['person'] ? ' ' . $r['person'] : ''), 'date' => $r['date'], 'branch' => $r['branch'] ?: 'بدون شعبه', 'branchId' => $r['branch_id'] ? (int) $r['branch_id'] : null], $holidays);

        $holidayConflictRows = $this->rows("SELECT s.term_session_id,s.term_id,s.session_type,s.cancellation_status,b.requested_date,b.start_time,br.branch_id,h.user_availability_id national_holiday_id,COALESCE((SELECT value FROM translations WHERE table_name='user_availabilities' AND table_id=h.user_availability_id AND field='summary' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),'تعطیل رسمی') holiday_title,COALESCE((SELECT value FROM translations WHERE table_name='user_availabilities' AND table_id=h.user_availability_id AND field='description' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),'') holiday_description,COALESCE((SELECT value FROM translations WHERE table_name='academy_branch_course_terms' AND table_id=t.term_id AND field='title' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('ترم ',t.term_id)) term_name,COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=br.branch_id AND field='name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',br.branch_id)) branch_name FROM user_availabilities h JOIN academy_branch_bookings b ON b.requested_date=h.date AND b.deleted_at IS NULL JOIN academy_branch_course_term_sessions s ON s.booking_id=b.booking_id AND s.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=s.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL LEFT JOIN academy_branch_classrooms room ON room.classroom_id=s.classroom_id AND room.deleted_at IS NULL JOIN academy_branches br ON br.branch_id=COALESCE(c.branch_id,room.branch_id) AND br.deleted_at IS NULL LEFT JOIN academy_national_holiday_settings hs ON hs.academy_id=br.academy_id WHERE h.unavailable_type='national_holiday' AND h.user_id IS NULL AND h.status='available' AND h.deleted_at IS NULL AND br.branch_id IN ({$scopeSql}) AND COALESCE(hs.allow_classes_on_national_holidays,0)=0 AND s.cancellation_status IN ('none','rejected') AND b.status NOT IN ('canceled','rejected','completed','held') ORDER BY b.requested_date,b.start_time,s.term_session_id");
        $holidayConflicts = array_map(fn ($r) => ['type' => 'national_holiday_conflict', 'holidayId' => (int) $r['national_holiday_id'], 'holidayTitle' => $r['holiday_title'], 'holidayDescription' => $r['holiday_description'], 'termId' => (int) $r['term_id'], 'termName' => $r['term_name'], 'sessionId' => (int) $r['term_session_id'], 'sessionType' => $r['session_type'], 'date' => $r['requested_date'], 'startTime' => substr((string) $r['start_time'], 0, 5), 'branchId' => (int) $r['branch_id'], 'branchName' => $r['branch_name']], $holidayConflictRows);
        $pendingRows = $this->rows("SELECT s.term_session_id,s.term_id,b.requested_date,br.branch_id,COALESCE((SELECT value FROM translations WHERE table_name='academy_branch_course_terms' AND table_id=t.term_id AND field='title' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('ترم ',t.term_id)) term_name,COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=br.branch_id AND field='name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',br.branch_id)) branch_name FROM academy_branch_course_term_sessions s JOIN academy_branch_bookings b ON b.booking_id=s.booking_id AND b.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=s.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL LEFT JOIN academy_branch_classrooms room ON room.classroom_id=s.classroom_id AND room.deleted_at IS NULL JOIN academy_branches br ON br.branch_id=COALESCE(c.branch_id,room.branch_id) AND br.deleted_at IS NULL WHERE s.deleted_at IS NULL AND s.cancellation_status='pending' AND br.branch_id IN ({$scopeSql}) ORDER BY s.cancellation_requested_at");
        $pendingCancellationActions = array_map(fn ($r) => ['type' => 'cancellation_pending', 'termId' => (int) $r['term_id'], 'termName' => $r['term_name'], 'sessionId' => (int) $r['term_session_id'], 'date' => $r['requested_date'], 'branchId' => (int) $r['branch_id'], 'branchName' => $r['branch_name']], $pendingRows);
        $waitingRows = $this->rows("SELECT w.term_waiting_list_id,w.term_id,w.created_at,c.branch_id,COALESCE((SELECT value FROM translations WHERE table_name='users' AND table_id=m.user_id AND field='full_name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),u.username) student_name,COALESCE((SELECT value FROM translations WHERE table_name='academy_branch_course_terms' AND table_id=w.term_id AND field='title' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('ترم ',w.term_id)) term_name,COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=c.branch_id AND field='name' AND locale='{$locale}' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',c.branch_id)) branch_name FROM academy_branch_course_term_waiting_list w JOIN academy_branch_members m ON m.member_id=w.member_id AND m.deleted_at IS NULL JOIN users u ON u.user_id=m.user_id AND u.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=w.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL WHERE w.deleted_at IS NULL AND c.branch_id IN ({$scopeSql}) AND NOT EXISTS (SELECT 1 FROM academy_branch_course_term_enrollments e WHERE e.term_id=w.term_id AND e.member_id=w.member_id AND e.type='student' AND e.deleted_at IS NULL) ORDER BY w.created_at DESC LIMIT 20");
        $academyWideIds = $this->allowedAcademyIds($actor);
        if ($academyWideIds && !$branchId) {
            $academyCourses = DB::table('academy_branch_courses')->whereIn('academy_id', $academyWideIds)->whereNull('branch_id')->whereNull('deleted_at')->get();
            $academyCourseIds = array_map(fn ($row) => (int) $row['course_id'], $academyCourses);
            $academyByCourse = array_column($academyCourses, 'academy_id', 'course_id');
            $academyTerms = $academyCourseIds ? DB::table('academy_branch_course_terms')->whereIn('course_id', $academyCourseIds)->whereNull('deleted_at')->get() : [];
            $academyTermIds = array_map(fn ($row) => (int) $row['term_id'], $academyTerms);
            $academyByTerm = [];
            foreach ($academyTerms as $academyTerm) $academyByTerm[(int) $academyTerm['term_id']] = (int) ($academyByCourse[(int) $academyTerm['course_id']] ?? 0);
            foreach ($academyTermIds ? DB::table('academy_branch_course_term_waiting_list')->whereIn('term_id', $academyTermIds)->whereNull('deleted_at')->orderBy('term_waiting_list_id', 'DESC')->limit(20)->get() : [] as $wideWaiting) {
                $wideMember = DB::table('academy_branch_members')->where('member_id', (int) $wideWaiting['member_id'])->whereNull('deleted_at')->first();
                if (!$wideMember || (int) $wideMember['academy_id'] !== ($academyByTerm[(int) $wideWaiting['term_id']] ?? 0)) continue;
                $wideUser = $wideMember ? DB::table('users')->where('user_id', (int) $wideMember['user_id'])->whereNull('deleted_at')->first() : null;
                if (!$wideUser) continue;
                $wideName = DB::table('translations')->where('table_name', 'users')->where('table_id', (int) $wideUser['user_id'])->where('field', 'full_name')->where('locale', $locale)->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
                $wideTermName = DB::table('translations')->where('table_name', 'academy_branch_course_terms')->where('table_id', (int) $wideWaiting['term_id'])->where('field', 'title')->where('locale', $locale)->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
                $wideRow = ['term_waiting_list_id' => (int) $wideWaiting['term_waiting_list_id'], 'term_id' => (int) $wideWaiting['term_id'], 'member_id' => (int) $wideWaiting['member_id'], 'user_id' => (int) $wideUser['user_id'], 'created_at' => $wideWaiting['created_at'], 'approved_at' => $wideWaiting['approved_at'], 'branch_id' => null, 'student_name' => (string) ($wideName['value'] ?? $wideUser['username']), 'term_name' => (string) ($wideTermName['value'] ?? ('ترم #' . $wideWaiting['term_id'])), 'branch_name' => 'آموزشگاه'];
                $wideEnrollment = DB::table('academy_branch_course_term_enrollments')->where('term_id', (int) $wideWaiting['term_id'])->where('member_id', (int) $wideWaiting['member_id'])->where('type', 'student')->whereNull('deleted_at')->first();
                if (empty($wideWaiting['approved_at']) && !$wideEnrollment) $waitingRows[] = $wideRow;
                if (!empty($wideWaiting['approved_at']) && $wideEnrollment && $wideEnrollment['status'] === 'pending') $academyPendingRows[] = $wideRow;
            }
        }
        $waitingActions = [];
        foreach ($waitingRows as $r) {
            $waiting = DB::table('academy_branch_course_term_waiting_list')->where('term_waiting_list_id', (int) $r['term_waiting_list_id'])->first();
            if (!empty($waiting['approved_at'])) continue;
            $member = DB::table('academy_branch_members')->where('member_id', (int) ($waiting['member_id'] ?? 0))->first();
            $requestTerm = DB::table('academy_branch_course_terms')->where('term_id', (int) $r['term_id'])->whereNull('deleted_at')->first();
            $requestCourse = $requestTerm ? DB::table('academy_branch_courses')->where('course_id', (int) $requestTerm['course_id'])->whereNull('deleted_at')->first() : null;
            if (!$member || !$requestCourse || (int) $member['academy_id'] !== (int) $requestCourse['academy_id'] || ($requestCourse['branch_id'] !== null && (int) $member['branch_id'] !== (int) $requestCourse['branch_id'])) continue;
            $student = DB::table('users')->where('user_id', (int) ($member['user_id'] ?? 0))->first();
            $profile = DB::table('translations')->where('table_name', 'users')->where('table_id', (int) ($member['user_id'] ?? 0))->where('field', 'father_name')->where('locale', $locale)->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
            $note = DB::table('translations')->where('table_name', 'academy_branch_course_term_waiting_list')->where('table_id', (int) $r['term_waiting_list_id'])->where('field', 'description')->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
            $chat = DB::table('translations')->where('table_name', 'academy_branch_course_term_waiting_list')->where('table_id', (int) $r['term_waiting_list_id'])->where('field', 'conversation_id')->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
            $addressRow = DB::table('user_addresses')->where('user_id', (int) ($member['user_id'] ?? 0))->where('is_main', 1)->whereNull('deleted_at')->first();
            $addressText = $addressRow ? DB::table('translations')->where('table_name', 'user_addresses')->where('table_id', (int) $addressRow['address_id'])->where('field', 'address')->where('locale', $locale)->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first() : null;
            $waitingActions[] = ['type' => 'student_waiting', 'requestId' => (int) $r['term_waiting_list_id'], 'termId' => (int) $r['term_id'], 'termName' => $r['term_name'], 'studentName' => $r['student_name'], 'phone' => (string) ($student['phone'] ?? ''), 'nationalId' => (string) ($student['national_code'] ?? ''), 'fatherName' => (string) ($profile['value'] ?? ''), 'birthDate' => (string) ($student['birthday'] ?? ''), 'address' => (string) ($addressText['value'] ?? ''), 'note' => (string) ($note['value'] ?? ''), 'conversationId' => (int) ($chat['value'] ?? 0), 'date' => $r['created_at'], 'branchId' => (int) $r['branch_id'], 'branchName' => $r['branch_name']];
        }
        $pendingScheduleActions = [];
        $pendingScheduleRows = $this->rows("SELECT w.term_waiting_list_id,w.term_id,w.member_id,c.branch_id,m.user_id,w.approved_at FROM academy_branch_course_term_waiting_list w JOIN academy_branch_members m ON m.member_id=w.member_id AND m.deleted_at IS NULL JOIN academy_branch_course_terms t ON t.term_id=w.term_id AND t.deleted_at IS NULL JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL JOIN academy_branch_course_term_enrollments e ON e.term_id=w.term_id AND e.member_id=w.member_id AND e.type='student' AND e.status='pending' AND e.deleted_at IS NULL WHERE w.deleted_at IS NULL AND w.approved_at IS NOT NULL AND c.branch_id IN ({$scopeSql}) ORDER BY w.approved_at DESC LIMIT 20");
        $pendingScheduleRows = array_merge($pendingScheduleRows, $academyPendingRows ?? []);
        foreach ($pendingScheduleRows as $r) {
            $termId = (int) $r['term_id'];
            if (DB::table('academy_branch_course_term_enrollments')->where('term_id', $termId)->where('type', 'student')->where('status', 'active')->whereNull('deleted_at')->first()) continue;
            $memberId = (int) $r['member_id'];
            $requestTerm = DB::table('academy_branch_course_terms')->where('term_id', $termId)->whereNull('deleted_at')->first();
            $requestCourse = $requestTerm ? DB::table('academy_branch_courses')->where('course_id', (int) $requestTerm['course_id'])->whereNull('deleted_at')->first() : null;
            $requestMember = DB::table('academy_branch_members')->where('member_id', $memberId)->whereNull('deleted_at')->first();
            if (!$requestMember || !$requestCourse || (int) $requestMember['academy_id'] !== (int) $requestCourse['academy_id'] || ($requestCourse['branch_id'] !== null && (int) $requestMember['branch_id'] !== (int) $requestCourse['branch_id'])) continue;
            $user = DB::table('users')->where('user_id', (int) $r['user_id'])->first();
            $name = DB::table('translations')->where('table_name', 'users')->where('table_id', (int) $r['user_id'])->where('field', 'full_name')->where('locale', $locale)->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
            $title = DB::table('translations')->where('table_name', 'academy_branch_course_terms')->where('table_id', $termId)->where('field', 'title')->where('locale', $locale)->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
            $chat = DB::table('translations')->where('table_name', 'academy_branch_course_term_waiting_list')->where('table_id', (int) $r['term_waiting_list_id'])->where('field', 'conversation_id')->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
            $teachers = [];
            foreach (DB::table('academy_branch_course_term_enrollments')->where('term_id', $termId)->where('type', 'teacher')->where('status', 'active')->whereNull('deleted_at')->get() as $teacherEnrollment) {
                $teacherMember = DB::table('academy_branch_members')->where('member_id', (int) $teacherEnrollment['member_id'])->whereNull('deleted_at')->first();
                if (!$teacherMember) continue;
                $teacherName = DB::table('translations')->where('table_name', 'users')->where('table_id', (int) $teacherMember['user_id'])->where('field', 'full_name')->where('locale', $locale)->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
                $teachers[] = ['id' => (int) $teacherMember['member_id'], 'name' => (string) ($teacherName['value'] ?? ('مدرس #' . $teacherMember['member_id']))];
            }
            $sessions = [];
            foreach (DB::table('academy_branch_course_term_sessions')->where('term_id', $termId)->whereNull('deleted_at')->get() as $session) {
                $booking = DB::table('academy_branch_bookings')->where('booking_id', (int) $session['booking_id'])->whereNull('deleted_at')->first();
                if ($booking && $booking['status'] === 'approved' && (string) $booking['requested_date'] >= $today) {
                    $sessions[] = ['id' => (int) $session['term_session_id'], 'date' => (string) $booking['requested_date'], 'startTime' => substr((string) $booking['start_time'], 0, 5), 'endTime' => substr((string) $booking['end_time'], 0, 5), 'classroomId' => (int) $session['classroom_id'], 'label' => $booking['requested_date'] . ' · ' . substr((string) $booking['start_time'], 0, 5) . ' تا ' . substr((string) $booking['end_time'], 0, 5)];
                }
            }
            $draftRow = !$sessions ? DB::table('translations')->where('table_name', 'academy_branch_course_terms')->where('table_id', $termId)->where('field', 'schedule_draft')->where('locale', 'fa')->whereNull('deleted_at')->first() : null;
            $draft = $draftRow ? json_decode((string) $draftRow['value'], true) : null;
            $draftDuration = is_array($draft) ? max(0, (int) substr((string) ($draft['endTime'] ?? ''), 0, 2) * 60 + (int) substr((string) ($draft['endTime'] ?? ''), 3, 2) - (int) substr((string) ($draft['startTime'] ?? ''), 0, 2) * 60 - (int) substr((string) ($draft['startTime'] ?? ''), 3, 2)) : 0;
            $cardRoomId = (int) ($draft['classroomId'] ?? ($sessions[0]['classroomId'] ?? 0));
            $cardRoom = $cardRoomId ? DB::table('academy_branch_classrooms')->where('classroom_id', $cardRoomId)->whereNull('deleted_at')->first() : null;
            $pendingScheduleActions[] = ['type' => 'student_pending_schedule', 'requestId' => (int) $r['term_waiting_list_id'], 'termId' => $termId, 'memberId' => $memberId, 'studentName' => (string) ($name['value'] ?? $user['username'] ?? ('هنرجو #' . $memberId)), 'termName' => (string) ($title['value'] ?? ('ترم #' . $termId)), 'branchId' => (int) ($r['branch_id'] ?: ($cardRoom['branch_id'] ?? 0)), 'classroomId' => $cardRoomId, 'teachers' => $teachers, 'sessions' => $sessions, 'needsFirstDate' => is_array($draft), 'sessionCount' => (int) ($requestTerm['session_count'] ?? 0), 'durationMinutes' => $draftDuration, 'draftTime' => is_array($draft) ? (string) ($draft['startTime'] ?? '') : '', 'draftTimezoneId' => (int) ($draft['timezoneId'] ?? 0), 'repeatType' => (string) ($requestTerm['session_period'] ?? ''), 'recurrence' => is_array($draft) ? ($draft['recurrence'] ?? []) : [], 'conversationId' => (int) ($chat['value'] ?? 0)];
        }
        $mergeActions = [];
        $currentUser = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (SiteAdminAccess::allows($currentUser)) {
            $mergeRows = $this->rows("SELECT um.user_merge_id,um.from_user_id,um.to_user_id,um.member_id,".\Core\translation\EntityText::expression('user_merges','um.user_merge_id','reason')." reason,um.created_at,COALESCE(tu.username,CONCAT('کاربر ',um.to_user_id)) target_name FROM user_merges um LEFT JOIN users tu ON tu.user_id=um.to_user_id WHERE um.status='pending' AND um.deleted_at IS NULL ORDER BY um.created_at");
            $mergeActions = array_map(fn ($r) => ['type' => 'user_merge_pending', 'requestId' => (int) $r['user_merge_id'], 'sourceUserId' => (int) $r['from_user_id'], 'targetUserId' => (int) $r['to_user_id'], 'memberId' => (int) $r['member_id'], 'targetName' => $r['target_name'], 'reason' => $r['reason'] ?: 'بدون توضیح', 'date' => $r['created_at'], 'branchId' => null], $mergeRows);
        }

        $absencesToday = count($todayAbsences);
        $urgent = [];
        if ($pendingPayments) {
            $urgent[] = ['type' => 'بدهی', 'text' => $pendingPayments . ' قسط معوق ثبت شده است', 'color' => 'rose', 'section' => 'finance', 'branchId' => $branchId ?: null];
        }
        if ($absencesToday) {
            $urgent[] = ['type' => 'غیبت', 'text' => $absencesToday . ' غیبت امروز ثبت شده است', 'color' => 'amber', 'section' => 'students', 'branchId' => $branchId ?: null];
        }
        if ($newMessages) {
            $urgent[] = ['type' => 'پیام', 'text' => $newMessages . ' پیام خوانده‌نشده وجود دارد', 'color' => 'indigo', 'section' => 'messages', 'branchId' => $branchId ?: null];
        }
        $highNotifications = $this->value("SELECT COUNT(*) FROM user_messages WHERE deleted_at IS NULL AND type='notification' AND receiver_user_id=? AND is_read=0", [$actor]);
        if ($highNotifications) {
            $urgent[] = ['type' => 'اعلان', 'text' => $highNotifications . ' اعلان سیستمی مهم وجود دارد', 'color' => 'red', 'section' => 'notifications', 'branchId' => null];
        }

        return ['branches' => $branches, 'stats' => ['activeStudents' => $activeStudents, 'todayClasses' => $todayClasses, 'monthlyIncome' => $this->amount($monthlyIncome), 'attendanceRate' => $attendanceTotal ? round($attendancePresent * 100 / $attendanceTotal) . '%' : '۰٪', 'pendingPayments' => $pendingPayments, 'absencesToday' => $absencesToday, 'newMessages' => $newMessages, 'urgentAlerts' => count($urgent) + count($holidayConflicts) + count($pendingCancellationActions) + count($mergeActions) + count($waitingActions) + count($pendingScheduleActions), 'newStudentsWeek' => $newStudentsWeek, 'pointsAwarded' => $pointsAwarded], 'todayClasses' => $todayClassesList, 'actionItems' => array_merge($waitingActions, $pendingScheduleActions, $mergeActions, $holidayConflicts, $pendingCancellationActions), 'urgentItems' => $urgent, 'recentPayments' => $recentPayments, 'recentDeposits' => $recentDeposits, 'todayAbsences' => $todayAbsences, 'unreadMessages' => $unreadMessages, 'recentRegistrations' => $recentRegistrations, 'upcomingHolidays' => $upcomingHolidays];
    }

    public function approveWaiting(int $actor, int $id, array $input): array
    {
        return \Modules\System\Services\PaymentMutex::run(db(), 'waiting-approval:' . $id, fn () => transaction(function () use ($actor, $id, $input) {
            $waiting = DB::table('academy_branch_course_term_waiting_list')->where('term_waiting_list_id', $id)->whereNull('deleted_at')->first();
            if (!$waiting || !empty($waiting['approved_at'])) {
                throw new RuntimeException('درخواست فعال و در انتظار تأیید یافت نشد.');
            }
            $member = DB::table('academy_branch_members')->where('member_id', (int) $waiting['member_id'])->whereNull('deleted_at')->first();
            $term = DB::table('academy_branch_course_terms')->join('academy_branch_courses', 'academy_branch_courses.course_id', '=', 'academy_branch_course_terms.course_id')->where('academy_branch_course_terms.term_id', (int) $waiting['term_id'])->whereNull('academy_branch_course_terms.deleted_at')->whereNull('academy_branch_courses.deleted_at')->first();
            if (!$member || !$term || (int) $member['academy_id'] !== (int) $term['academy_id'] || ($term['branch_id'] !== null && (int) $member['branch_id'] !== (int) $term['branch_id'])) {
                throw new RuntimeException('اطلاعات درخواست معتبر نیست.');
            }
            $branchId = (int) ($term['branch_id'] ?? 0);
            $academy = DB::table('academies')->where('academy_id', (int) $term['academy_id'])->whereNull('deleted_at')->first();
            $branch = $branchId ? DB::table('academy_branches')->where('branch_id', $branchId)->whereNull('deleted_at')->first() : null;
            $allowed = SiteAdminAccess::allows(DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first()) || $actor === (int) ($academy['user_id'] ?? 0) || $actor === (int) ($academy['created_by'] ?? 0) || $actor === (int) ($branch['user_id'] ?? 0);
            if (!$allowed) {
                $staff = DB::table('academy_branch_members')->where('academy_id', (int) $term['academy_id'])->where('user_id', $actor)->where('status', 'active')->whereNull('deleted_at')->get();
                foreach ($staff as $person) {
                    $roles = DB::table('academy_branch_member_roles')->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')->where('academy_branch_member_roles.member_id', (int) $person['member_id'])->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get();
                    foreach ($roles as $role) {
                        $name = strtolower((string) $role['name']);
                        if (in_array($name, ['academy_owner', 'academy_manager', 'academy_receptionist', 'branch_owner', 'branch_manager', 'branch_receptionist', 'academy_branch_owner', 'academy_branch_manager', 'academy_branch_receptionist'], true) && (!(str_starts_with($name, 'branch_') || str_starts_with($name, 'academy_branch_')) || (int) ($person['branch_id'] ?? 0) === $branchId)) {
                            $allowed = true;
                            break 2;
                        }
                    }
                }
            }
            if (!$allowed) {
                throw new RuntimeException('اجازه بررسی این درخواست را ندارید.');
            }
            $userId = (int) $member['user_id'];
            $user = DB::table('users')->where('user_id', $userId)->whereNull('deleted_at')->first();
            if (!$user) {
                throw new RuntimeException('حساب هنرجو یافت نشد.');
            }
            $name = trim((string) ($input['name'] ?? ''));
            $national = trim((string) ($input['national_id'] ?? ''));
            $father = trim((string) ($input['father_name'] ?? ''));
            $birth = trim((string) ($input['birth_date'] ?? ''));
            $phone = trim((string) ($input['phone'] ?? ''));
            $address = trim((string) ($input['address'] ?? ''));
            if (!$name || !$father || !$address || !preg_match('/^\d{10}$/', $national) || !preg_match('/^09\d{9}$/', $phone) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth) || !checkdate((int) substr($birth, 5, 2), (int) substr($birth, 8, 2), (int) substr($birth, 0, 4))) {
                throw new RuntimeException('نام، کد ملی، نام پدر، تاریخ تولد، شماره تماس و آدرس معتبر را کامل کنید.');
            }
            if (DB::table('users')->where('national_code', $national)->where('user_id', '!=', $userId)->whereNull('deleted_at')->first() || DB::table('users')->where('phone', $phone)->where('user_id', '!=', $userId)->whereNull('deleted_at')->first()) {
                throw new RuntimeException('کد ملی یا شماره تماس برای حساب دیگری ثبت شده است.');
            }
            $now = date('Y-m-d H:i:s');
            DB::table('users')->where('user_id', $userId)->update(['national_code' => $national, 'phone' => $phone, 'birthday' => $birth, 'updated_at' => $now, 'updated_by' => $actor]);
            \Core\translation\TranslationService::manager()->set('users', $userId, 'full_name', $name, 'fa');
            \Core\translation\TranslationService::manager()->set('users', $userId, 'father_name', $father, 'fa');
            $oldAddress = DB::table('user_addresses')->where('user_id', $userId)->where('is_main', 1)->whereNull('deleted_at')->first();
            $addressId = $oldAddress ? (int) $oldAddress['address_id'] : (int) DB::table('user_addresses')->insertGetId(['user_id' => $userId, 'country_id' => 1, 'is_main' => 1, 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            \Core\translation\TranslationService::manager()->set('user_addresses', $addressId, 'address', $address, 'fa');
            DB::table('academy_branch_members')->where('member_id', (int) $member['member_id'])->update(['status' => 'active', 'approved_at' => $now, 'approved_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            $existing = DB::table('academy_branch_course_term_enrollments')->where('term_id', (int) $waiting['term_id'])->where('member_id', (int) $member['member_id'])->where('type', 'student')->whereNull('deleted_at')->first();
            if (!$existing) {
                DB::table('academy_branch_course_term_enrollments')->insert(['term_id' => (int) $waiting['term_id'], 'member_id' => (int) $member['member_id'], 'type' => 'student', 'status' => 'pending', 'joined_at' => $now, 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            }
            DB::table('academy_branch_course_term_waiting_list')->where('term_waiting_list_id', $id)->update(['approved_at' => $now, 'approved_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            (new \Modules\Academy\Services\PublicAcademyEnrollmentService())->ensureConversation($id);
            return ['memberId' => (int) $member['member_id'], 'status' => 'pending_schedule'];
        }));
    }

    public function updateMyWaiting(int $actor, int $id, array $input): array
    {
        return \Modules\System\Services\PaymentMutex::run(db(), 'waiting-approval:' . $id, fn () => transaction(function () use ($actor, $id, $input) {
            $waiting = DB::table('academy_branch_course_term_waiting_list')->where('term_waiting_list_id', $id)->whereNull('deleted_at')->first();
            $member = $waiting ? DB::table('academy_branch_members')->where('member_id', (int) $waiting['member_id'])->where('user_id', $actor)->whereNull('deleted_at')->first() : null;
            if (!$member) throw new RuntimeException('درخواست ثبت‌نام شما یافت نشد یا به آن دسترسی ندارید.');
            if (!empty($waiting['approved_at'])) throw new RuntimeException('این درخواست قبلاً تأیید شده است و اطلاعات آن دیگر قابل ویرایش نیست.');
            $this->saveApplicantProfile($actor, $input);
            $now = date('Y-m-d H:i:s');
            \Core\translation\TranslationService::manager()->set('academy_branch_course_term_waiting_list', $id, 'description', trim((string) ($input['note'] ?? '')), 'fa');
            DB::table('academy_branch_course_term_waiting_list')->where('term_waiting_list_id', $id)->update(['updated_at' => $now, 'updated_by' => $actor]);
            return ['id' => $id];
        }));
    }

    public function saveApplicantProfile(int $actor, array $input): void
    {
        $name = trim((string) ($input['name'] ?? ''));
        $phone = trim((string) ($input['phone'] ?? ''));
        $national = trim((string) ($input['national_id'] ?? ''));
        $father = trim((string) ($input['father_name'] ?? ''));
        $birth = trim((string) ($input['birth_date'] ?? ''));
        $address = trim((string) ($input['address'] ?? ''));
        $note = trim((string) ($input['note'] ?? ''));
        if ($name === '' || !preg_match('/^09\d{9}$/', $phone) || ($national !== '' && !preg_match('/^\d{10}$/', $national)) || ($birth !== '' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth) || !checkdate((int) substr($birth, 5, 2), (int) substr($birth, 8, 2), (int) substr($birth, 0, 4)))) || mb_strlen($note) > 2000) {
            throw new RuntimeException('نام، شماره تماس یا سایر اطلاعات واردشده معتبر نیست.');
        }
        if (DB::table('users')->where('phone', $phone)->where('user_id', '!=', $actor)->whereNull('deleted_at')->first() || ($national !== '' && DB::table('users')->where('national_code', $national)->where('user_id', '!=', $actor)->whereNull('deleted_at')->first())) {
            throw new RuntimeException('شماره تماس یا کد ملی برای حساب دیگری ثبت شده است.');
        }
        $now = date('Y-m-d H:i:s');
        $values = ['phone' => $phone, 'updated_at' => $now, 'updated_by' => $actor];
        if ($national !== '') $values['national_code'] = $national;
        if ($birth !== '') $values['birthday'] = $birth;
        DB::table('users')->where('user_id', $actor)->update($values);
        \Core\translation\TranslationService::manager()->set('users', $actor, 'full_name', $name, 'fa');
        if ($father !== '') \Core\translation\TranslationService::manager()->set('users', $actor, 'father_name', $father, 'fa');
        if ($address !== '') {
            $oldAddress = DB::table('user_addresses')->where('user_id', $actor)->where('is_main', 1)->whereNull('deleted_at')->first();
            $addressId = $oldAddress ? (int) $oldAddress['address_id'] : (int) DB::table('user_addresses')->insertGetId(['user_id' => $actor, 'country_id' => 1, 'is_main' => 1, 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            \Core\translation\TranslationService::manager()->set('user_addresses', $addressId, 'address', $address, 'fa');
        }
    }

    public function ensureWaitingChat(int $actor, int $id): array
    {
        $waiting = DB::table('academy_branch_course_term_waiting_list')->where('term_waiting_list_id', $id)->whereNull('deleted_at')->first();
        $term = $waiting ? DB::table('academy_branch_course_terms')->where('term_id', (int) $waiting['term_id'])->whereNull('deleted_at')->first() : null;
        $course = $term ? DB::table('academy_branch_courses')->where('course_id', (int) $term['course_id'])->whereNull('deleted_at')->first() : null;
        if (!$course) throw new RuntimeException('درخواست یافت نشد.');
        $branchId = (int) ($course['branch_id'] ?? 0);
        $academy = DB::table('academies')->where('academy_id', (int) $course['academy_id'])->whereNull('deleted_at')->first();
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        $allowed = SiteAdminAccess::allows($user) || ($academy && ((int) $academy['user_id'] === $actor || (int) $academy['created_by'] === $actor));
        if (!$allowed && $branchId) $allowed = in_array($branchId, $this->allowedBranchIds($actor), true);
        if (!$allowed) throw new RuntimeException('اجازه دسترسی به این درخواست را ندارید.');
        return ['conversationId' => (new \Modules\Academy\Services\PublicAcademyEnrollmentService())->ensureConversation($id)];
    }

    public function finalizeWaiting(int $actor, int $id, int $teacherId, int $sessionId, string $firstDate = '', string $startTime = ''): array
    {
        return \Modules\System\Services\PaymentMutex::run(db(), 'schedule-write', function () use ($actor, $id, $teacherId, $sessionId, $firstDate, $startTime) {
            return transaction(function () use ($actor, $id, $teacherId, $sessionId, $firstDate, $startTime) {
                $waiting = DB::table('academy_branch_course_term_waiting_list')->where('term_waiting_list_id', $id)->whereNull('deleted_at')->first();
                $term = $waiting ? DB::table('academy_branch_course_terms')->where('term_id', (int) $waiting['term_id'])->whereNull('deleted_at')->first() : null;
                $course = $term ? DB::table('academy_branch_courses')->where('course_id', (int) $term['course_id'])->whereNull('deleted_at')->first() : null;
                $member = $waiting ? DB::table('academy_branch_members')->where('member_id', (int) $waiting['member_id'])->whereNull('deleted_at')->first() : null;
                if (!$waiting || !$course || !$member || empty($waiting['approved_at']) || (int) $member['academy_id'] !== (int) $course['academy_id'] || ($course['branch_id'] !== null && (int) $member['branch_id'] !== (int) $course['branch_id'])) throw new RuntimeException('درخواست تأییدشده یافت نشد.');
                $branchId = (int) ($course['branch_id'] ?? 0);
                $academy = DB::table('academies')->where('academy_id', (int) $course['academy_id'])->whereNull('deleted_at')->first();
                $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
                $allowed = SiteAdminAccess::allows($user) || ($academy && ((int) $academy['user_id'] === $actor || (int) $academy['created_by'] === $actor)) || ($branchId && in_array($branchId, $this->allowedBranchIds($actor), true));
                if (!$allowed) throw new RuntimeException('اجازه فعال‌سازی این کلاس را ندارید.');
                $enrollment = DB::table('academy_branch_course_term_enrollments')->where('term_id', (int) $waiting['term_id'])->where('member_id', (int) $member['member_id'])->where('type', 'student')->whereNull('deleted_at')->first();
                if (!$enrollment || $enrollment['status'] !== 'pending') throw new RuntimeException('وضعیت ثبت‌نام برای فعال‌سازی معتبر نیست.');
                if (DB::table('academy_branch_course_term_enrollments')->where('term_id', (int) $waiting['term_id'])->where('type', 'student')->where('status', 'active')->whereNull('deleted_at')->first()) throw new RuntimeException('این ترم از قبل هنرجوی فعال دارد.');
                $teacher = DB::table('academy_branch_course_term_enrollments')->where('term_id', (int) $waiting['term_id'])->where('member_id', $teacherId)->where('type', 'teacher')->where('status', 'active')->whereNull('deleted_at')->first();
                $teacherMember = DB::table('academy_branch_members')->where('member_id', $teacherId)->whereNull('deleted_at')->first();
                if (!$teacher || !$teacherMember || (int) $teacherMember['academy_id'] !== (int) $course['academy_id']) throw new RuntimeException('مدرس انتخاب‌شده عضو فعال این ترم نیست.');
                if ($sessionId === 0 && $firstDate !== '') {
                    // scheduleUndatedTerm creates the term-level invoice template (member_id NULL).
                    // The existing block below issues one separate invoice for this member.
                    $sessionId = (new \Modules\Academy\Services\AcademyTermService())->scheduleUndatedTerm($actor, (int) $waiting['term_id'], $firstDate, (int) $member['member_id'], $teacherId, $startTime);
                    $term = DB::table('academy_branch_course_terms')->where('term_id', (int) $waiting['term_id'])->first();
                }
                $session = DB::table('academy_branch_course_term_sessions')->where('term_session_id', $sessionId)->where('term_id', (int) $waiting['term_id'])->whereNull('deleted_at')->first();
                $booking = $session ? DB::table('academy_branch_bookings')->where('booking_id', (int) $session['booking_id'])->whereNull('deleted_at')->first() : null;
                if (!$booking || $booking['status'] !== 'approved' || (string) $booking['requested_date'] < date('Y-m-d')) throw new RuntimeException('جلسهٔ تأییدشده و معتبر آینده را انتخاب کنید.');
                $activeCount = DB::table('academy_branch_course_term_enrollments')->where('term_id', (int) $waiting['term_id'])->where('type', 'student')->where('status', 'active')->whereNull('deleted_at')->count();
                if ((int) ($course['student_capacity'] ?? 0) > 0 && $activeCount >= (int) $course['student_capacity']) throw new RuntimeException('ظرفیت هنرجوی ترم تکمیل شده است.');
                foreach (DB::table('academy_branch_course_term_sessions')->where('term_id', (int) $waiting['term_id'])->whereNull('deleted_at')->get() as $termSession) {
                    $termBooking = DB::table('academy_branch_bookings')->where('booking_id', (int) $termSession['booking_id'])->whereNull('deleted_at')->first();
                    if (!$termBooking || $termBooking['status'] !== 'approved' || (string) $termBooking['requested_date'] < date('Y-m-d')) continue;
                    \Modules\Academy\Services\ScheduleGuard::available((string) $termBooking['requested_date'], substr((string) $termBooking['start_time'], 0, 5), substr((string) $termBooking['end_time'], 0, 5), (int) ($termBooking['timezone_id'] ?? 0), (int) ($termSession['classroom_id'] ?? 0), [(int) $member['member_id'], $teacherId], (int) $termSession['term_session_id']);
                }
                $now = date('Y-m-d H:i:s');
                $invoice = DB::table('academy_branch_course_term_invoices')->where('term_id', (int) $waiting['term_id'])->where('member_id', (int) $member['member_id'])->whereNull('deleted_at')->first();
                if (!$invoice) {
                    $template = DB::table('academy_branch_course_term_invoices')->where('term_id', (int) $waiting['term_id'])->whereNull('member_id')->whereNull('deleted_at')->orderBy('term_invoice_id', 'DESC')->first();
                    $total = $template['payable_amount'] ?? $term['price'] ?? 0;
                    $invoiceId = (int) DB::table('academy_branch_course_term_invoices')->insertGetId(['term_id' => (int) $waiting['term_id'], 'member_id' => (int) $member['member_id'], 'discount_id' => $template['discount_id'] ?? null, 'payable_amount' => $total, 'currency_id' => $template['currency_id'] ?? $term['currency_id'], 'status' => 'issued', 'due_date' => $term['start_date'] ?? date('Y-m-d'), 'issued_at' => $now, 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
                    $parts = $template ? DB::table('academy_branch_course_term_invoice_installments')->where('invoice_id', (int) $template['term_invoice_id'])->whereNull('deleted_at')->orderBy('installment_number')->get() : [];
                    if (!$parts) $parts = [['installment_number' => 1, 'amount' => $total, 'due_date' => $term['start_date'] ?? date('Y-m-d')]];
                    foreach ($parts as $part) DB::table('academy_branch_course_term_invoice_installments')->insert(['invoice_id' => $invoiceId, 'installment_number' => (int) $part['installment_number'], 'amount' => $part['amount'], 'due_date' => $part['due_date'], 'status' => 'approved', 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
                    \Modules\Academy\Services\InvoiceLedger::snapshot($invoiceId);
                }
                DB::table('academy_branch_course_term_enrollments')->where('term_enrollment_id', (int) $enrollment['term_enrollment_id'])->update(['status' => 'active', 'approved_at' => $now, 'approved_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
                foreach (['final_teacher_id' => $teacherId, 'final_session_id' => $sessionId] as $field => $value) \Core\translation\TranslationService::manager()->set('academy_branch_course_term_waiting_list', $id, $field, (string) $value, 'fa');
                $chat = DB::table('translations')->where('table_name', 'academy_branch_course_term_waiting_list')->where('table_id', $id)->where('field', 'conversation_id')->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
                if ($chat) {
                    $conversationId = (int) $chat['value'];
                    $messageId = (int) DB::table('conversation_messages')->insertGetId(['conversation_id' => $conversationId, 'sender_id' => $actor, 'body' => 'ثبت‌نام نهایی شد. کلاس بر اساس جلسهٔ #' . $sessionId . ' و مدرس #' . $teacherId . ' فعال شد.', 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
                    DB::table('conversations')->where('conversation_id', $conversationId)->update(['last_message_id' => $messageId, 'updated_at' => $now, 'updated_by' => $actor]);
                }
                return ['memberId' => (int) $member['member_id'], 'status' => 'active'];
            });
        });
    }

    private function rows(string $sql, array $bindings = []): array
    {
        $statement = db()->prepare($sql);
        $statement->execute($bindings);
        return $statement->fetchAll();
    }

    private function value(string $sql, array $bindings = []): int|float
    {
        $statement = db()->prepare($sql);
        $statement->execute($bindings);
        return (float) $statement->fetchColumn();
    }

    private function amount(mixed $amount): string
    {
        return number_format((float) $amount, 0, '.', ',');
    }

    private function relative(?string $date): string
    {
        if (!$date) {
            return '—';
        }
        $seconds = max(0, time() - strtotime($date));
        if ($seconds < 60) {
            return 'همین حالا';
        }
        if ($seconds < 3600) {
            return floor($seconds / 60) . ' دقیقه پیش';
        }
        if ($seconds < 86400) {
            return floor($seconds / 3600) . ' ساعت پیش';
        }
        return floor($seconds / 86400) . ' روز پیش';
    }

    private function allowedBranchIds(int $actor): array
    {
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (!$user) {
            throw new RuntimeException('کاربر معتبر نیست.');
        }
        if (SiteAdminAccess::allows($user)) {
            return array_values(array_map(fn ($row) => (int) $row['branch_id'], DB::table('academy_branches')->whereNull('deleted_at')->get()));
        }
        $academyIds = [];
        $branchIds = [];
        foreach (DB::table('academies')->whereNull('deleted_at')->get() as $academy) {
            if ((int) $academy['user_id'] === $actor || (int) $academy['created_by'] === $actor) $academyIds[] = (int) $academy['academy_id'];
        }
        foreach (DB::table('academy_branches')->where('user_id', $actor)->whereNull('deleted_at')->get() as $branch) {
            $branchIds[] = (int) $branch['branch_id'];
        }
        foreach (DB::table('academy_branch_members')->where('user_id', $actor)->where('status', 'active')->whereNull('deleted_at')->get() as $member) {
            $roles = DB::table('academy_branch_member_roles')->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')->where('academy_branch_member_roles.member_id', (int) $member['member_id'])->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get();
            foreach ($roles as $role) {
                $name = strtolower((string) $role['name']);
                if (!in_array($name, ['academy_owner', 'academy_manager', 'academy_receptionist', 'branch_owner', 'branch_manager', 'branch_receptionist', 'academy_branch_owner', 'academy_branch_manager', 'academy_branch_receptionist'], true)) continue;
                if ((str_starts_with($name, 'branch_') || str_starts_with($name, 'academy_branch_'))) {
                    if ($member['branch_id']) $branchIds[] = (int) $member['branch_id'];
                } else {
                    $academyIds[] = (int) $member['academy_id'];
                }
            }
        }
        $academyIds = array_values(array_unique(array_filter($academyIds)));
        if ($academyIds) {
            foreach (DB::table('academy_branches')->whereIn('academy_id', $academyIds)->whereNull('deleted_at')->get() as $branch) $branchIds[] = (int) $branch['branch_id'];
        }
        return array_values(array_unique(array_filter($branchIds)));
    }

    private function allowedAcademyIds(int $actor): array
    {
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (SiteAdminAccess::allows($user)) {
            return array_values(array_map(fn ($academy) => (int) $academy['academy_id'], DB::table('academies')->whereNull('deleted_at')->get()));
        }
        return array_values(array_map(fn ($academy) => (int) $academy['academy_id'], DB::table('academies')->where('user_id', $actor)->whereNull('deleted_at')->get()));
    }
}
