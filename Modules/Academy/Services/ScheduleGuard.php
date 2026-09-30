<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use RuntimeException;

final class ScheduleGuard
{
    public static function session(int $id): void
    {
        $session = DB::table('academy_branch_course_term_sessions')->where('term_session_id', $id)->first();
        $booking = DB::table('academy_branch_bookings')->where('booking_id', (int) $session['booking_id'])->first();
        self::booking($session, $booking);
    }

    public static function booking(array $session, array $booking): void
    {
        self::forSession((int) $session['term_id'], $booking['requested_date'], substr($booking['start_time'], 0, 5), substr($booking['end_time'], 0, 5), (int) $booking['timezone_id'], (int) $session['classroom_id'], (int) $session['term_session_id']);
    }

    public static function available(string $date, string $start, string $end, int $zoneId, int $room, array $members, int $exclude = 0): void
    {
        ScheduleTime::validate($date, $start, $end);
        $zones = array_column(DB::table('f_timezone')->whereNull('deleted_at')->get(), 'timezone', 'timezone_id');
        [$from, $until] = self::interval($date, $start, $end, $zoneId, $zones);
        $people = self::people($members);
        $day = new \DateTimeImmutable($date);
        $query = db()->prepare("SELECT s.term_session_id,s.term_id,s.classroom_id,b.requested_date,b.start_time,b.end_time,b.timezone_id FROM academy_branch_course_term_sessions s JOIN academy_branch_bookings b ON b.booking_id=s.booking_id WHERE s.deleted_at IS NULL AND b.deleted_at IS NULL AND b.status NOT IN ('canceled','rejected') AND s.term_session_id<>? AND b.requested_date BETWEEN ? AND ?");
        $query->execute([$exclude, $day->modify('-2 days')->format('Y-m-d'), $day->modify('+2 days')->format('Y-m-d')]);
        foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $other) {
            [$a, $b] = self::interval($other['requested_date'], $other['start_time'], $other['end_time'], (int) $other['timezone_id'], $zones);
            if ($from >= $b || $until <= $a) {
                continue;
            }
            $busyMembers = DB::table('academy_branch_course_term_enrollments')->where('term_id', (int) $other['term_id'])->whereNull('deleted_at')->where('status', 'active')->get();
            if (($room > 0 && $room === (int) $other['classroom_id']) || array_intersect($members, array_column($busyMembers, 'member_id')) || array_intersect($people, self::people(array_column($busyMembers, 'member_id')))) {
                throw new RuntimeException('اتاق، مدرس یا هنرجو در این بازه جلسهٔ دیگری دارد.', 409);
            }
        }
    }

    public static function forSession(int $term, string $date, string $start, string $end, int $zone, int $room, int $exclude = 0): void
    {
        $members = DB::table('academy_branch_course_term_enrollments')->where('term_id', $term)->whereNull('deleted_at')->where('status', 'active')->get();
        self::available($date, $start, $end, $zone, $room, array_column($members, 'member_id'), $exclude);
    }

    private static function people(array $members): array
    {
        if (!$members) {
            return [];
        }
        return array_filter(array_column(DB::table('academy_branch_members')->whereIn('member_id', $members)->whereNull('deleted_at')->get(), 'user_id'));
    }

    private static function interval(string $date, string $start, string $end, int $zone, array $zones): array
    {
        if ($zone && !isset($zones[$zone])) {
            throw new RuntimeException('منطقهٔ زمانی جلسه معتبر نیست.', 422);
        }
        $tz = new \DateTimeZone($zones[$zone] ?? (string) env('APP_TIMEZONE', 'Asia/Tehran'));
        return [(new \DateTimeImmutable($date . ' ' . $start, $tz))->getTimestamp(), (new \DateTimeImmutable($date . ' ' . $end, $tz))->getTimestamp()];
    }
}
