<?php
namespace Modules\Academy\Services;

use Core\database\DB;
use RuntimeException;

/** Caller must authorize the term and hold schedule-write inside a transaction. */
final class TermEnrollmentCapacity
{
    public static function counts(int $termId, array $statuses = ['active']): array
    {
        $people = ['teacher' => [], 'student' => []];
        $sql = 'SELECT type,member_id FROM academy_branch_course_term_enrollments WHERE term_id=? AND status IN (' . implode(',', array_fill(0, count($statuses), '?')) . ') AND deleted_at IS NULL';
        if (self::locking()) $sql .= ' FOR UPDATE';
        $statement = db()->prepare($sql);
        $statement->execute(array_merge([$termId], $statuses));
        foreach ($statement->fetchAll() as $row) {
            if (isset($people[$row['type']])) $people[$row['type']][(int) $row['member_id']] = true;
        }
        return array_map('count', $people);
    }

    public static function ready(array $course, int $teachers, int $students): bool
    {
        return $teachers >= max(1, (int) ($course['teacher_capacity'] ?? 1))
            && $students >= max(1, (int) ($course['student_capacity'] ?? 1));
    }

    public static function assertStudentSlot(int $termId, array $course, int $existingMember = 0, array $allowedStatuses = ['open', 'pending']): void
    {
        $lock = db()->prepare('SELECT status FROM academy_branch_course_terms WHERE term_id=? AND deleted_at IS NULL' . (self::locking() ? ' FOR UPDATE' : ''));
        $lock->execute([$termId]);
        if (!in_array($lock->fetchColumn(), $allowedStatuses, true)) throw new RuntimeException('وضعیت ترم تغییر کرده است؛ ترم باز را دوباره انتخاب کنید.', 409);
        $sql = "SELECT member_id FROM academy_branch_course_term_enrollments WHERE term_id=? AND type='student' AND status IN ('active','pending') AND deleted_at IS NULL AND member_id<>?";
        if (self::locking()) $sql .= ' FOR UPDATE';
        $statement = db()->prepare($sql);
        $statement->execute([$termId, $existingMember]);
        $members = array_unique(array_column($statement->fetchAll(), 'member_id'));
        if (count($members) >= max(1, (int) ($course['student_capacity'] ?? 1))) throw new RuntimeException('ظرفیت هنرجوی ترم تکمیل است.', 409);
    }

    public static function synchronize(int $termId, int $actor): string
    {
        $term = DB::table('academy_branch_course_terms')->where('term_id', $termId)->whereNull('deleted_at')->first();
        if (!$term || !in_array($term['status'], ['open', 'ongoing'], true)) return (string) ($term['status'] ?? '');
        $course = DB::table('academy_branch_courses')->where('course_id', (int) $term['course_id'])->whereNull('deleted_at')->first();
        $counts = self::counts($termId);
        // Administrative enrollment status is independent of calendar readiness.
        $status = $course && self::ready($course, $counts['teacher'], $counts['student']) ? 'ongoing' : 'open';
        if ($status !== $term['status']) DB::table('academy_branch_course_terms')->where('term_id', $termId)->update(['status' => $status, 'updated_by' => $actor]);
        return $status;
    }

    public static function checkScheduledMembers(int $termId): void
    {
        foreach (DB::table('academy_branch_course_term_sessions')->where('term_id', $termId)->whereNull('deleted_at')->get() as $session) {
            $booking = DB::table('academy_branch_bookings')->where('booking_id', (int) $session['booking_id'])->whereNull('deleted_at')->first();
            if ($booking && !in_array($booking['status'], ['canceled', 'rejected'], true)) ScheduleGuard::booking($session, $booking);
        }
    }

    private static function locking(): bool
    {
        return db()->inTransaction() && db()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'mysql';
    }
}
