<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use RuntimeException;

class AcademyBranchOfferingService
{
    public function all(): array
    {
        $branches = DB::table('academy_branches')->whereNull('deleted_at')->get();
        $branchIds = array_map(fn(array $row): int => (int) $row['branch_id'], $branches);
        $branchUserIds = array_map(fn(array $row): int => (int) $row['user_id'], $branches);
        $branchNames = $this->translations('academy_branches', $branchIds, ['name']);

        $result = [
            'branches' => [],
            'instruments' => [],
            'lessons' => [],
            'instruments_catalog' => [],
            'lessons_catalog' => [],
            'levels' => [],
            'schedules' => [],
        ];

        $branchesByUser = [];
        foreach ($branches as $branch) {
            $branchId = (int) $branch['branch_id'];
            $item = [
                'id' => $branchId,
                'user_id' => (int) $branch['user_id'],
                'name' => $branchNames[$branchId]['name'] ?? ('شعبه ' . $branchId),
            ];
            $result['branches'][] = $item;
            $branchesByUser[$item['user_id']] = $item;
        }

        $instrumentRows = DB::table('instruments')->whereNull('deleted_at')->get();
        $instrumentIds = array_map(fn(array $row): int => (int) $row['instrument_id'], $instrumentRows);
        $instrumentTranslations = $this->translations('instruments', $instrumentIds, ['title']);
        $instrumentTitles = [];
        foreach ($instrumentRows as $row) {
            $id = (int) $row['instrument_id'];
            $title = $instrumentTranslations[$id]['title'] ?? '';
            $instrumentTitles[$id] = $title;
            $result['instruments_catalog'][] = ['id' => $id, 'title' => $title];
        }

        $lessonRows = DB::table('lessons')->whereNull('deleted_at')->get();
        $lessonIds = array_map(fn(array $row): int => (int) $row['lesson_id'], $lessonRows);
        $lessonTranslations = $this->translations('lessons', $lessonIds, ['title']);
        $lessonTitles = [];
        foreach ($lessonRows as $row) {
            $id = (int) $row['lesson_id'];
            $title = $lessonTranslations[$id]['title'] ?? '';
            $lessonTitles[$id] = $title;
            $result['lessons_catalog'][] = ['id' => $id, 'title' => $title];
        }

        $levelRows = DB::table('levels')->whereNull('deleted_at')->get();
        $levelIds = array_map(fn(array $row): int => (int) $row['level_id'], $levelRows);
        $levelTranslations = $this->translations('levels', $levelIds, ['title']);
        foreach ($levelRows as $row) {
            $id = (int) $row['level_id'];
            $result['levels'][] = ['level_id' => $id, 'title' => $levelTranslations[$id]['title'] ?? ''];
        }

        if (!$branchUserIds) {
            return $result;
        }

        $this->appendOfferings(
            $result['instruments'],
            'user_instruments',
            'user_instrument_id',
            'instrument_id',
            $branchUserIds,
            $branchesByUser,
            $instrumentTitles
        );
        $this->appendOfferings(
            $result['lessons'],
            'user_lessons',
            'user_lesson_id',
            'lesson_id',
            $branchUserIds,
            $branchesByUser,
            $lessonTitles
        );

        $scheduleRows = DB::table('user_availabilities')
            ->whereIn('user_id', $branchUserIds)
            ->whereNull('deleted_at')
            ->get();
        $scheduleIds = array_map(fn(array $row): int => (int) $row['user_availability_id'], $scheduleRows);
        $scheduleTranslations = $this->translations('user_availabilities', $scheduleIds, ['summary', 'description']);
        $days = ['saturday' => 'شنبه', 'sunday' => 'یکشنبه', 'monday' => 'دوشنبه', 'tuesday' => 'سه‌شنبه', 'wednesday' => 'چهارشنبه', 'thursday' => 'پنجشنبه', 'friday' => 'جمعه'];
        $statuses = ['available' => 'فعال', 'unavailable' => 'غیرفعال', 'reserved' => 'پر شده', 'pending' => 'در انتظار تأیید'];
        $repeats = ['week' => 'هفتگی', '2-week' => 'دو هفته', '3-week' => 'سه هفته', '4-week' => 'چهار هفته', 'month' => 'ماهانه', 'year' => 'سالانه', 'none' => 'بی‌تکرار'];

        foreach ($scheduleRows as $row) {
            $id = (int) $row['user_availability_id'];
            $branch = $branchesByUser[(int) $row['user_id']];
            $start = substr((string) $row['start_time'], 0, 5);
            $end = substr((string) $row['end_time'], 0, 5);
            $result['schedules'][] = [
                'id' => $id,
                'day' => $days[$row['day_of_week']] ?? $row['date'],
                'slots' => $this->timeSlots($start, $end),
                'timeLabel' => $start . '-' . $end,
                'time' => $start . '-' . $end,
                'branchId' => $branch['id'],
                'branchName' => $branch['name'],
                'status' => $statuses[$row['type']] ?? 'در انتظار تأیید',
                'repeatPeriod' => $repeats[$row['repeat_period']] ?? 'هفتگی',
                'repeatDate' => $row['date'] ?: '',
                'timezone' => $row['timezone'],
                'summary' => $scheduleTranslations[$id]['summary'] ?? '',
                'description' => $scheduleTranslations[$id]['description'] ?? '',
            ];
        }

        return $result;
    }

    public function saveSchedule(int $actor, array $data, int $id = 0): array
    {
        $branchId = (int) ($data['branchId'] ?? 0);
        $branch = DB::table('academy_branches')->where('branch_id', $branchId)->whereNull('deleted_at')->first();
        if (!$branch) throw new RuntimeException('شعبه انتخاب‌شده معتبر نیست.');

        $days = ['شنبه' => 'saturday', 'یکشنبه' => 'sunday', 'دوشنبه' => 'monday', 'سه‌شنبه' => 'tuesday', 'چهارشنبه' => 'wednesday', 'پنجشنبه' => 'thursday', 'جمعه' => 'friday'];
        $statuses = ['فعال' => 'available', 'غیرفعال' => 'unavailable', 'پر شده' => 'reserved', 'در انتظار تأیید' => 'pending'];
        $repeats = ['هفتگی' => 'week', 'دو هفته' => '2-week', 'سه هفته' => '3-week', 'چهار هفته' => '4-week', 'ماهانه' => 'month', 'سالانه' => 'year', 'بی‌تکرار' => 'none'];
        $day = $days[(string) ($data['day'] ?? '')] ?? null;
        if (!$day) throw new RuntimeException('روز برنامه زمانی معتبر نیست.');
        $ranges = $data['ranges'] ?? [];
        if (!is_array($ranges) || !$ranges) throw new RuntimeException('حداقل یک بازه زمانی الزامی است.');

        return transaction(function () use ($actor, $data, $id, $branch, $day, $statuses, $repeats, $ranges) {
            $savedIds = [];
            foreach (array_values($ranges) as $index => $range) {
                $start = $this->validTime((string) ($range['start'] ?? ''));
                $end = $this->validTime((string) ($range['end'] ?? ''));
                if (!$start || !$end || $start >= $end) throw new RuntimeException('بازه زمانی واردشده معتبر نیست.');
                $repeat = $repeats[(string) ($data['repeatPeriod'] ?? 'هفتگی')] ?? 'week';
                $specificDate = in_array($repeat, ['month', 'year'], true) ? trim((string) ($data['repeatDate'] ?? '')) : '';
                $values = [
                    'user_id' => (int) $branch['user_id'],
                    'date' => $specificDate !== '' ? $specificDate : null,
                    'day_of_week' => $day,
                    'start_time' => $start . ':00',
                    'end_time' => $end . ':00',
                    'timezone' => trim((string) ($data['timezone'] ?? 'Asia/Tehran')) ?: 'Asia/Tehran',
                    'type' => $statuses[(string) ($data['status'] ?? 'فعال')] ?? 'available',
                    'is_repeating' => $repeat === 'none' ? 0 : 1,
                    'repeat_period' => $repeat,
                    'is_closed' => (($data['status'] ?? 'فعال') === 'غیرفعال') ? 1 : 0,
                    'priority' => $index + 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $actor,
                    'deleted_at' => null,
                    'deleted_by' => null,
                ];
                if ($id && $index === 0) {
                    $existing = DB::table('user_availabilities')->where('user_availability_id', $id)->whereNull('deleted_at')->first();
                    if (!$existing) throw new RuntimeException('برنامه زمانی موردنظر یافت نشد.');
                    DB::table('user_availabilities')->where('user_availability_id', $id)->update($values);
                    $savedId = $id;
                } else {
                    $savedId = DB::table('user_availabilities')->insertGetId(['created_at' => date('Y-m-d H:i:s'), 'created_by' => $actor] + $values);
                }
                $this->setTranslations($savedId, ['summary' => trim((string) ($data['summary'] ?? '')), 'description' => trim((string) ($data['description'] ?? ''))], $actor);
                $savedIds[] = $savedId;
            }
            return ['ids' => $savedIds];
        });
    }

    private function validTime(string $time): ?string
    {
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time) ? $time : null;
    }

    private function timeSlots(string $start, string $end): array
    {
        [$sh, $sm] = array_map('intval', explode(':', $start));
        [$eh, $em] = array_map('intval', explode(':', $end));
        $slots = [];
        for ($minute = $sh * 60 + $sm, $until = $eh * 60 + $em; $minute < $until; $minute += 30) {
            $slots[] = sprintf('%02d:%02d', intdiv($minute, 60), $minute % 60);
        }
        return $slots;
    }

    private function setTranslations(int $id, array $values, int $actor): void
    {
        foreach ($values as $field => $value) {
            $row = DB::table('translations')->where('table_name', 'user_availabilities')->where('table_id', $id)->where('field', $field)->where('locale', 'fa')->first();
            $update = ['value' => $value, 'version' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $actor, 'deleted_at' => null, 'deleted_by' => null];
            if ($row) DB::table('translations')->where('translation_id', (int) $row['translation_id'])->update($update);
            else DB::table('translations')->insert(['table_name' => 'user_availabilities', 'table_id' => $id, 'field' => $field, 'locale' => 'fa', 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $actor] + $update);
        }
    }

    private function appendOfferings(
        array &$target,
        string $table,
        string $primaryKey,
        string $foreignKey,
        array $branchUserIds,
        array $branchesByUser,
        array $titles
    ): void {
        $rows = DB::table($table)
            ->whereIn('user_id', $branchUserIds)
            ->whereNull('deleted_at')
            ->get();
        $ids = array_map(fn(array $row): int => (int) $row[$primaryKey], $rows);
        $translations = $this->translations($table, $ids, ['summary', 'description']);

        foreach ($rows as $row) {
            $id = (int) $row[$primaryKey];
            $branch = $branchesByUser[(int) $row['user_id']];
            $startYear = (int) substr((string) $row['start_date'], 0, 4);
            $target[] = [
                'id' => $id,
                'user_id' => (int) $row['user_id'],
                $foreignKey => (int) $row[$foreignKey],
                'title' => $titles[(int) $row[$foreignKey]] ?? '',
                'level_id' => (int) $row['level_id'],
                'start_date' => $row['start_date'],
                'years_of_experience' => $startYear ? max(0, 1405 - $startYear) : 0,
                'is_primary' => (int) $row['is_primary'],
                'status' => 'فعال',
                'summary' => $translations[$id]['summary'] ?? '',
                'description' => $translations[$id]['description'] ?? '',
                'branchId' => $branch['id'],
                'branchName' => $branch['name'],
            ];
        }
    }

    private function translations(string $table, array $ids, array $fields): array
    {
        if (!$ids) {
            return [];
        }

        $map = [];
        $rows = DB::table('translations')
            ->where('table_name', $table)
            ->whereIn('table_id', array_values(array_unique($ids)))
            ->whereIn('field', $fields)
            ->where('locale', 'fa')
            ->whereNull('deleted_at')
            ->get();
        foreach ($rows as $row) {
            $map[(int) $row['table_id']][(string) $row['field']] = (string) $row['value'];
        }
        return $map;
    }

    public function delete(string $type, int $id, int $actor): void
    {
        $config = [
            'instrument' => ['user_instruments', 'user_instrument_id'],
            'lesson' => ['user_lessons', 'user_lesson_id'],
            'schedule' => ['user_availabilities', 'user_availability_id'],
        ][$type] ?? null;
        if (!$config) {
            throw new RuntimeException('نوع نامعتبر است.');
        }

        [$table, $primaryKey] = $config;
        $now = date('Y-m-d H:i:s');
        DB::table($table)->where($primaryKey, $id)->whereNull('deleted_at')->update([
            'deleted_at' => $now,
            'deleted_by' => $actor,
            'updated_by' => $actor,
        ]);
        DB::table('translations')->where('table_name', $table)->where('table_id', $id)->whereNull('deleted_at')->update([
            'deleted_at' => $now,
            'deleted_by' => $actor,
            'updated_by' => $actor,
        ]);
    }
}
