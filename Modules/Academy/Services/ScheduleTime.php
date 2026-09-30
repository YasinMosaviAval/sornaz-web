<?php

namespace Modules\Academy\Services;

final class ScheduleTime
{
    public static function validate(string $date, string $start, string $end): void
    {
        $day = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (!$day || $day->format('Y-m-d') !== $date
            || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $start)
            || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $end) || $end <= $start) {
            throw new \RuntimeException('تاریخ یا ساعت جلسه معتبر نیست.', 422);
        }
    }
}
