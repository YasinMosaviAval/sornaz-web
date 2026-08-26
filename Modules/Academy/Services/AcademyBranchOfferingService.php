<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

class AcademyBranchOfferingService
{
    private function now(): string
    {
        return (new \DateTimeImmutable('now', new \DateTimeZone('Asia/Tehran')))->format('Y-m-d H:i:s');
    }

    public function all(int $actor): array
    {
        $branches = $this->scopedBranches($actor);
        $branchIds = array_map(fn(array $row): int => (int) $row['branch_id'], $branches);
        $branchUserIds = array_map(fn(array $row): int => (int) $row['user_id'], $branches);
        $branchNames = $this->translations('academy_branches', $branchIds, ['name']);
        $academyIds = array_values(array_unique(array_map(fn(array $row): int => (int) $row['academy_id'], $branches)));
        $academyRows = $academyIds ? DB::table('academies')->whereIn('academy_id', $academyIds)->whereNull('deleted_at')->get() : [];
        $academyNames = $this->translations('academies', $academyIds, ['title', 'name']);
        $academiesById = [];
        foreach ($academyRows as $academyRow) $academiesById[(int) $academyRow['academy_id']] = $academyRow;

        $result = [
            'branches' => [],
            'instruments' => [],
            'lessons' => [],
            'instruments_catalog' => [],
            'lessons_catalog' => [],
            'levels' => [],
            'schedules' => [],
            'timezones' => [],
            'organizations' => [],
            'organization_selection' => 'select',
        ];

        $timezoneRows = DB::table('f_timezone')->where('status', 'active')->whereNull('deleted_at')->orderBy('sort_order')->get();
        $timezoneIds = array_map(fn(array $row): int => (int) $row['timezone_id'], $timezoneRows);
        $timezoneTitles = [];
        if ($timezoneIds) {
            foreach (DB::table('f_translations')->where('table_name', 'f_timezone')->whereIn('table_id', $timezoneIds)->where('field', 'title')->whereNull('deleted_at')->get() as $translation) {
                $id = (int) $translation['table_id'];
                $timezoneTitles[$id][(string) $translation['locale']] = (string) $translation['value'];
            }
        }
        $currentLocale = function_exists('locale') ? (string) locale() : 'fa';
        foreach ($timezoneRows as $timezoneRow) {
            $id = (int) $timezoneRow['timezone_id'];
            $value = (string) $timezoneRow['timezone'];
            $title = $timezoneTitles[$id][$currentLocale] ?? $timezoneTitles[$id]['fa'] ?? $value;
            $result['timezones'][] = ['value' => $value, 'label' => $title . ' (' . $value . ')'];
        }

        $manageableOrganizations = $this->scopedOrganizations($actor);
        $lessonOrganizations = $this->organizationsForDisplay($actor, $manageableOrganizations);
        $organizationUserIds = array_map(fn(array $row): int => (int) $row['user_id'], $manageableOrganizations);
        $lessonOrganizationUserIds = array_map(fn(array $row): int => (int) $row['user_id'], $lessonOrganizations);
        $result['organizations'] = $manageableOrganizations;
        $result['organization_selection'] = $this->hasFixedBranchOrganization($actor, $manageableOrganizations) ? 'fixed' : 'select';
        $result['lesson_status_mode'] = $this->lessonStatusMode($actor);

        $branchesByUser = [];
        foreach ($branches as $branch) {
            $branchId = (int) $branch['branch_id'];
            $item = [
                'id' => $branchId,
                'user_id' => (int) $branch['user_id'],
                'name' => $branchNames[$branchId]['name'] ?? ('شعبه ' . $branchId),
                'academy_id' => (int) $branch['academy_id'],
                'academy_user_id' => (int) ($academiesById[(int) $branch['academy_id']]['user_id'] ?? 0),
                'academy_name' => $academyNames[(int) $branch['academy_id']]['title'] ?? $academyNames[(int) $branch['academy_id']]['name'] ?? ('آموزشگاه ' . (int) $branch['academy_id']),
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

        $levelRows = DB::table('levels')->where('type', 'learning')->where('is_active', 1)->whereNull('deleted_at')->orderBy('sort_order')->get();
        $levelIds = array_map(fn(array $row): int => (int) $row['level_id'], $levelRows);
        $levelTranslations = $this->translations('levels', $levelIds, ['title']);
        foreach ($levelRows as $row) {
            $id = (int) $row['level_id'];
            $result['levels'][] = ['level_id' => $id, 'title' => $levelTranslations[$id]['title'] ?? '', 'type' => 'learning'];
        }

        if (!$organizationUserIds) {
            return $result;
        }

        $organizationsByUser = [];
        foreach ($manageableOrganizations as $organization) $organizationsByUser[(int) $organization['user_id']] = $organization;
        $lessonOrganizationsByUser = [];
        foreach ($lessonOrganizations as $organization) $lessonOrganizationsByUser[(int) $organization['user_id']] = $organization;

        $this->appendOfferings(
            $result['instruments'],
            'user_instruments',
            'user_instrument_id',
            'instrument_id',
            $organizationUserIds,
            $organizationsByUser,
            $instrumentTitles
        );
        $this->appendOfferings(
            $result['lessons'],
            'user_lessons',
            'user_lesson_id',
            'lesson_id',
            $lessonOrganizationUserIds,
            $lessonOrganizationsByUser,
            $lessonTitles
        );

        $scheduleRows = DB::table('user_availabilities')
            ->whereIn('user_id', $organizationUserIds)
            ->whereNull('deleted_at')
            ->get();
        $scheduleIds = array_map(fn(array $row): int => (int) $row['user_availability_id'], $scheduleRows);
        $scheduleTranslations = $this->translations('user_availabilities', $scheduleIds, ['summary', 'description']);
        $timezonesById = [];
        foreach ($timezoneRows as $timezoneRow) $timezonesById[(int) $timezoneRow['timezone_id']] = (string) $timezoneRow['timezone'];
        $days = ['saturday' => 'شنبه', 'sunday' => 'یکشنبه', 'monday' => 'دوشنبه', 'tuesday' => 'سه‌شنبه', 'wednesday' => 'چهارشنبه', 'thursday' => 'پنجشنبه', 'friday' => 'جمعه'];
        $statuses = ['available' => 'فعال', 'unavailable' => 'غیرفعال', 'reserved' => 'پر شده', 'pending' => 'در انتظار تأیید'];
        $repeats = ['week' => 'هفتگی', '2-week' => 'دو هفته', '3-week' => 'سه هفته', '4-week' => 'چهار هفته', 'month' => 'ماهانه', 'year' => 'سالانه', 'none' => 'بی‌تکرار'];

        foreach ($scheduleRows as $row) {
            $id = (int) $row['user_availability_id'];
            $organization = $organizationsByUser[(int) $row['user_id']];
            $start = substr((string) $row['start_time'], 0, 5);
            $end = substr((string) $row['end_time'], 0, 5);
            $result['schedules'][] = [
                'id' => $id,
                'user_id' => (int) $row['user_id'],
                'day' => $days[$row['day_of_week']] ?? $row['date'],
                'slots' => $this->timeSlots($start, $end),
                'timeLabel' => $start . '-' . $end,
                'time' => $start . '-' . $end,
                'branchId' => $organization['kind'] === 'branch' ? $organization['id'] : 0,
                'organizationUserId' => (int) $organization['user_id'],
                'organizationKind' => $organization['kind'],
                'branchName' => $organization['name'],
                'status' => $statuses[$row['type']] ?? 'در انتظار تأیید',
                'repeatPeriod' => $repeats[$row['repeat_period']] ?? 'هفتگی',
                'repeatDate' => $row['date'] ?: '',
                'timezone' => $timezonesById[(int) ($row['timezone_id'] ?? 0)] ?? 'Asia/Tehran',
                'summary' => $scheduleTranslations[$id]['summary'] ?? '',
                'description' => $scheduleTranslations[$id]['description'] ?? '',
            ];
        }

        return $result;
    }

    public function saveLesson(int $actor, array $data, int $id = 0): array
    {
        $organization = $this->allowedOrganization($actor, (int) ($data['organization_user_id'] ?? 0));
        $lessonId = (int) ($data['lesson_id'] ?? 0);
        $levelId = (int) ($data['level_id'] ?? 0);
        $lesson = DB::table('lessons')->where('lesson_id', $lessonId)->whereNull('deleted_at')->first();
        if (!$lesson) throw new RuntimeException('درس انتخاب‌شده معتبر نیست.');
        $level = DB::table('levels')->where('level_id', $levelId)->where('type', 'learning')->where('is_active', 1)->whereNull('deleted_at')->first();
        if (!$level) throw new RuntimeException('سطح انتخاب‌شده معتبر نیست.');
        $statusMode = $this->lessonStatusMode($actor);
        $status = $statusMode === 'pending' ? 'pending' : (string) ($data['status'] ?? 'active');
        if (!in_array($status, ['pending', 'active', 'inactive'], true)) throw new RuntimeException('وضعیت انتخاب‌شده معتبر نیست.');
        $startDate = trim((string) ($data['start_date'] ?? ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) throw new RuntimeException('زمان شروع معتبر نیست.');
        $isPrimary = !empty($data['is_primary']) ? 1 : 0;
        $now = $this->now();

        return transaction(function () use ($actor, $data, $id, $organization, $lessonId, $levelId, $status, $startDate, $isPrimary, $now) {
            $existing = null;
            if ($id) {
                $existing = DB::table('user_lessons')->where('user_lesson_id', $id)->whereNull('deleted_at')->first();
                if (!$existing) throw new RuntimeException('درس موردنظر یافت نشد.');
                $this->allowedOrganization($actor, (int) $existing['user_id']);
            }
            if ($isPrimary) {
                $primaryQuery = DB::table('user_lessons')->where('user_id', (int) $organization['user_id'])->where('is_primary', 1)->whereNull('deleted_at');
                $primary = $primaryQuery->first();
                if ($primary && (int) $primary['user_lesson_id'] !== $id) throw new RuntimeException('برای این سازمان قبلاً درس اصلی انتخاب شده است.');
            }
            $values = [
                'user_id' => (int) $organization['user_id'], 'lesson_id' => $lessonId, 'level_id' => $levelId,
                'start_date' => $startDate, 'status' => $status, 'is_primary' => $isPrimary,
                'updated_at' => $now, 'updated_by' => $actor, 'deleted_at' => null, 'deleted_by' => null,
            ];
            $values += ['approved_at' => $status === 'pending' ? null : ($existing['approved_at'] ?? $now), 'approved_by' => $status === 'pending' ? null : ($existing['approved_by'] ?? $actor)];
            if ($existing) {
                DB::table('user_lessons')->where('user_lesson_id', $id)->update($values);
                $savedId = $id;
            } else {
                $savedId = DB::table('user_lessons')->insertGetId(['created_at' => $now, 'created_by' => $actor] + $values);
            }
            if ($isPrimary) DB::table('user_lessons')->where('user_lesson_id', $savedId)->update(['is_primary' => 1, 'updated_at' => $now, 'updated_by' => $actor]);
            $this->setTranslationValues('user_lessons', $savedId, ['summary' => trim((string) ($data['summary'] ?? '')), 'description' => trim((string) ($data['description'] ?? ''))], $actor);
            return ['id' => $savedId];
        });
    }

    public function cycleLessonStatus(int $actor, int $id): array
    {
        $row = DB::table('user_lessons')->where('user_lesson_id', $id)->whereNull('deleted_at')->first();
        if (!$row) throw new RuntimeException('درس موردنظر یافت نشد.');
        $this->allowedOrganization($actor, (int) $row['user_id']);
        $next = match ((string) ($row['status'] ?? 'pending')) {
            'pending' => 'active',
            'active' => 'inactive',
            default => 'pending',
        };
        $now = $this->now();
        DB::table('user_lessons')->where('user_lesson_id', $id)->update([
            'status' => $next,
            'approved_at' => $next === 'pending' ? null : ($row['approved_at'] ?? $now),
            'approved_by' => $next === 'pending' ? null : ($row['approved_by'] ?? $actor),
            'updated_at' => $now,
            'updated_by' => $actor,
        ]);
        return ['id' => $id, 'status' => $next];
    }

    public function lessonsRealtimeVersion(int $actor): array
    {
        $data = $this->all($actor);
        $payload = [
            'lessons' => array_map(fn(array $row): array => [$row['id'], $row['status'], $row['lesson_id'], $row['level_id'], $row['start_date'], $row['is_primary'], $row['summary'], $row['description']], $data['lessons']),
            'catalog' => array_map(fn(array $row): array => [$row['id'], $row['title']], $data['lessons_catalog']),
        ];
        return ['resource' => 'lessons', 'version' => sha1(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))];
    }

    public function schedulesRealtimeVersion(int $actor): array
    {
        $organizations = $this->scopedOrganizations($actor);
        $userIds = array_map(fn(array $row): int => (int) $row['user_id'], $organizations);
        $rows = $userIds ? DB::table('user_availabilities')->whereIn('user_id', $userIds)->whereNull('deleted_at')->get() : [];
        $ids = array_map(fn(array $row): int => (int) $row['user_availability_id'], $rows);
        $translations = $ids ? DB::table('translations')->where('table_name', 'user_availabilities')->whereIn('table_id', $ids)->whereNull('deleted_at')->get() : [];
        $payload = [
            'rows' => array_map(fn(array $row): array => [$row['user_availability_id'], $row['user_id'], $row['day_of_week'], $row['start_time'], $row['end_time'], $row['timezone_id'] ?? null, $row['type'], $row['updated_at'] ?? null], $rows),
            'translations' => array_map(fn(array $row): array => [$row['table_id'], $row['locale'], $row['field'], $row['value'], $row['updated_at'] ?? null], $translations),
        ];
        return ['resource' => 'organization_schedules', 'version' => sha1(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))];
    }

    public function createLesson(int $actor, array $data): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '' || mb_strlen($title) > 190) throw new RuntimeException('نام درس جدید معتبر نیست.');
        $duplicate = DB::table('translations')->where('table_name', 'lessons')->where('field', 'title')->where('locale', 'fa')->where('value', $title)->whereNull('deleted_at')->first();
        if ($duplicate) throw new RuntimeException('این درس قبلاً وجود دارد.');
        $now = $this->now();
        $values = ['created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor];
        if ($this->shouldApprove($actor)) $values += ['approved_at' => $now, 'approved_by' => $actor];
        $id = DB::table('lessons')->insertGetId($values);
        $this->setTranslationValues('lessons', $id, ['title' => $title], $actor);
        return ['id' => $id, 'title' => $title];
    }

    public function saveSchedule(int $actor, array $data, int $id = 0): array
    {
        $organizationUserId = (int) ($data['organizationUserId'] ?? $data['branchId'] ?? 0);
        $organization = $this->allowedOrganization($actor, $organizationUserId);

        $days = ['شنبه' => 'saturday', 'یکشنبه' => 'sunday', 'دوشنبه' => 'monday', 'سه‌شنبه' => 'tuesday', 'چهارشنبه' => 'wednesday', 'پنجشنبه' => 'thursday', 'جمعه' => 'friday'];
        $statuses = ['فعال' => 'available', 'غیرفعال' => 'unavailable'];
        $day = $days[(string) ($data['day'] ?? '')] ?? null;
        if (!$day) throw new RuntimeException('روز برنامه زمانی معتبر نیست.');
        $ranges = $data['ranges'] ?? [];
        if (!is_array($ranges) || !$ranges) throw new RuntimeException('حداقل یک بازه زمانی الزامی است.');
        $timezoneValue = trim((string) ($data['timezone'] ?? 'Asia/Tehran')) ?: 'Asia/Tehran';
        $timezone = DB::table('f_timezone')->where('timezone', $timezoneValue)->where('status', 'active')->whereNull('deleted_at')->first();
        if (!$timezone) throw new RuntimeException('منطقه زمانی انتخاب‌شده معتبر نیست.');
        $timezoneId = (int) $timezone['timezone_id'];
        $pendingApproval = $this->isReceptionist($actor);
        $approvalTime = $this->now();

        return transaction(function () use ($actor, $data, $id, $organization, $day, $statuses, $ranges, $timezoneId, $pendingApproval, $approvalTime) {
            $savedIds = [];
            $normalized = [];
            foreach ($ranges as $range) {
                $start = $this->validTime((string) ($range['start'] ?? '')); $end = $this->validTime((string) ($range['end'] ?? ''));
                if (!$start || !$end || $start >= $end) throw new RuntimeException('بازه زمانی واردشده معتبر نیست.');
                $status = (string) ($range['status'] ?? $data['status'] ?? 'فعال');
                if (!isset($statuses[$status])) throw new RuntimeException('وضعیت بازه زمانی معتبر نیست.');
                $normalized[] = compact('start', 'end', 'status');
            }
            usort($normalized, fn(array $a,array $b): int => strcmp($a['start'],$b['start']));
            for($i=1;$i<count($normalized);$i++) {
                $previousEnd = $this->timeToMinutes($normalized[$i-1]['end']);
                $currentStart = $this->timeToMinutes($normalized[$i]['start']);
                if($currentStart < $previousEnd + 30) throw new RuntimeException('بین بازه‌های زمانی باید حداقل ۳۰ دقیقه فاصله وجود داشته باشد.');
            }
            $ranges = $normalized;
            $existingDayRows = DB::table('user_availabilities')->where('user_id', (int) $organization['user_id'])->where('day_of_week', $day)->whereNull('date')->whereNull('deleted_at')->get();
            foreach ($existingDayRows as $existingDayRow) {
                $existingDayId = (int) $existingDayRow['user_availability_id'];
                if ($id && $existingDayId === $id) continue;
                DB::table('user_availabilities')->where('user_availability_id', $existingDayId)->update(['deleted_at' => $this->now(), 'deleted_by' => $actor, 'updated_at' => $this->now(), 'updated_by' => $actor]);
            }
            foreach (array_values($ranges) as $index => $range) {
                $start = $this->validTime((string) ($range['start'] ?? ''));
                $end = $this->validTime((string) ($range['end'] ?? ''));
                if (!$start || !$end || $start >= $end) throw new RuntimeException('بازه زمانی واردشده معتبر نیست.');
                $repeat = 'week';
                $specificDate = '';
                $values = [
                    'user_id' => (int) $organization['user_id'],
                    'date' => $specificDate !== '' ? $specificDate : null,
                    'day_of_week' => $day,
                    'start_time' => $start . ':00',
                    'end_time' => $end . ':00',
                    'timezone_id' => $timezoneId,
                    'type' => $pendingApproval ? 'pending' : ($statuses[$range['status']] ?? 'available'),
                    'is_repeating' => $repeat === 'none' ? 0 : 1,
                    'repeat_period' => $repeat,
                    'is_closed' => $range['status'] === 'غیرفعال' ? 1 : 0,
                    'priority' => $index + 1,
                    'updated_at' => $this->now(),
                    'updated_by' => $actor,
                    'approved_at' => $pendingApproval ? null : $approvalTime,
                    'approved_by' => $pendingApproval ? null : $actor,
                    'deleted_at' => null,
                    'deleted_by' => null,
                ];
                if ($id && $index === 0) {
                    $existing = DB::table('user_availabilities')->where('user_availability_id', $id)->whereNull('deleted_at')->first();
                    if (!$existing) throw new RuntimeException('برنامه زمانی موردنظر یافت نشد.');
                    $this->allowedOrganization($actor, (int) $existing['user_id']);
                    DB::table('user_availabilities')->where('user_availability_id', $id)->update($values);
                    $savedId = $id;
                } else {
                    $savedId = DB::table('user_availabilities')->insertGetId(['created_at' => $this->now(), 'created_by' => $actor] + $values);
                }
                $this->setTranslations($savedId, ['summary' => trim((string) ($data['summary'] ?? '')), 'description' => trim((string) ($data['description'] ?? ''))], $actor);
                $savedIds[] = $savedId;
            }
            return ['ids' => $savedIds];
        });
    }

    private function validTime(string $time): ?string
    {
        return preg_match('/^(?:(?:[01]\d|2[0-3]):[0-5]\d|24:00)$/', $time) ? $time : null;
    }

    private function timeToMinutes(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));
        return $hour * 60 + $minute;
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
        $this->setTranslationValues('user_availabilities', $id, $values, $actor);
    }

    private function setTranslationValues(string $table, int $id, array $values, int $actor): void
    {
        foreach ($values as $field => $value) {
            $row = DB::table('translations')->where('table_name', $table)->where('table_id', $id)->where('field', $field)->where('locale', 'fa')->first();
            $update = ['value' => $value, 'version' => 1, 'updated_at' => $this->now(), 'updated_by' => $actor, 'deleted_at' => null, 'deleted_by' => null];
            if ($row) DB::table('translations')->where('translation_id', (int) $row['translation_id'])->update($update);
            else DB::table('translations')->insert(['table_name' => $table, 'table_id' => $id, 'field' => $field, 'locale' => 'fa', 'created_at' => $this->now(), 'created_by' => $actor] + $update);
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
            if (!empty($branch['read_only']) && ($table !== 'user_lessons' || ($row['status'] ?? 'pending') !== 'active')) continue;
            $target[] = [
                'id' => $id,
                'user_id' => (int) $row['user_id'],
                $foreignKey => (int) $row[$foreignKey],
                'title' => $titles[(int) $row[$foreignKey]] ?? '',
                'level_id' => (int) $row['level_id'],
                'start_date' => $row['start_date'],
                'is_primary' => (int) $row['is_primary'],
                'status' => $row['status'] ?? 'pending',
                'summary' => $translations[$id]['summary'] ?? '',
                'description' => $translations[$id]['description'] ?? '',
                'branchId' => $branch['kind'] === 'branch' ? $branch['id'] : null,
                'branchName' => $branch['name'],
                'organizationKind' => $branch['kind'],
                'organizationId' => $branch['id'],
                'organization_user_id' => (int) $row['user_id'],
                'canChangeStatus' => empty($branch['read_only']),
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
        $record = DB::table($table)->where($primaryKey, $id)->whereNull('deleted_at')->first();
        if (!$record) throw new RuntimeException('رکورد موردنظر یافت نشد.');
        if (in_array($type, ['instrument', 'lesson'], true)) {
            $this->allowedOrganization($actor, (int) $record['user_id']);
        } else {
            $branch = DB::table('academy_branches')->where('user_id', (int) $record['user_id'])->whereNull('deleted_at')->first();
            if (!$branch) throw new RuntimeException('شعبه مرتبط با این رکورد یافت نشد.');
            $this->allowedBranch($actor, (int) $branch['branch_id']);
        }
        $now = $this->now();
        $deleteValues = [
            'deleted_at' => $now,
            'deleted_by' => $actor,
            'updated_by' => $actor,
        ];
        DB::table($table)->where($primaryKey, $id)->whereNull('deleted_at')->update($deleteValues);
        DB::table('translations')->where('table_name', $table)->where('table_id', $id)->whereNull('deleted_at')->update([
            'deleted_at' => $now,
            'deleted_by' => $actor,
            'updated_by' => $actor,
        ]);
    }

    private function scopedBranches(int $actor): array
    {
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (!$user) throw new RuntimeException('حساب کاربری معتبر نیست.');
        if (SiteAdminAccess::allows($user)) {
            return DB::table('academy_branches')->whereNull('deleted_at')->get();
        }

        if (($user['type'] ?? '') === 'branch') {
            return DB::table('academy_branches')->where('user_id', $actor)->whereNull('deleted_at')->get();
        }

        $academies = DB::table('academies')->where('user_id', $actor)->whereNull('deleted_at')->get();
        $created = DB::table('academies')->where('created_by', $actor)->whereNull('deleted_at')->get();
        $academyIds = array_values(array_unique(array_map(
            fn(array $row): int => (int) $row['academy_id'],
            array_merge($academies, $created)
        )));
        if ($academyIds) return DB::table('academy_branches')->whereIn('academy_id', $academyIds)->whereNull('deleted_at')->get();
        $managedBranchIds = [];
        foreach (DB::table('academy_branch_members')->where('user_id', $actor)->whereNull('deleted_at')->get() as $member) {
            if ($member['branch_id'] === null) continue;
            $managerRole = $this->hasLessonManagementRole((int) $member['member_id']);
            if ($managerRole) $managedBranchIds[] = (int) $member['branch_id'];
        }
        return $managedBranchIds ? DB::table('academy_branches')->whereIn('branch_id', array_values(array_unique($managedBranchIds)))->whereNull('deleted_at')->get() : [];
    }

    private function scopedOrganizations(int $actor): array
    {
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (!$user) throw new RuntimeException('حساب کاربری معتبر نیست.');
        if (($user['type'] ?? '') === 'branch') {
            $branch = DB::table('academy_branches')->where('user_id', $actor)->whereNull('deleted_at')->first();
            if (!$branch) return [];
            $name = $this->translations('academy_branches', [(int) $branch['branch_id']], ['name']);
            return [['id' => (int) $branch['branch_id'], 'user_id' => $actor, 'kind' => 'branch', 'name' => $name[(int) $branch['branch_id']]['name'] ?? 'شعبه']];
        }

        $academyIds = [];
        foreach (DB::table('academies')->where('user_id', $actor)->whereNull('deleted_at')->get() as $row) $academyIds[] = (int) $row['academy_id'];
        foreach (DB::table('academies')->where('created_by', $actor)->whereNull('deleted_at')->get() as $row) $academyIds[] = (int) $row['academy_id'];
        $managedBranches = [];
        foreach (DB::table('academy_branch_members')->where('user_id', $actor)->whereNull('deleted_at')->get() as $member) {
            $hasManagerRole = $this->hasLessonManagementRole((int) $member['member_id']);
            if (!$hasManagerRole) continue;
            if ($member['branch_id'] !== null) {
                $branch = DB::table('academy_branches')->where('branch_id', (int) $member['branch_id'])->whereNull('deleted_at')->first();
                if ($branch) $managedBranches[] = $branch;
                continue;
            }
            $academyIds[] = (int) $member['academy_id'];
        }
        if (!$academyIds && $managedBranches) {
            $branchIds = array_map(fn(array $row): int => (int) $row['branch_id'], $managedBranches);
            $names = $this->translations('academy_branches', $branchIds, ['name']);
            return array_map(fn(array $branch): array => ['id' => (int) $branch['branch_id'], 'user_id' => (int) $branch['user_id'], 'kind' => 'branch', 'name' => $names[(int) $branch['branch_id']]['name'] ?? 'شعبه'], $managedBranches);
        }
        if (SiteAdminAccess::allows($user) && !$academyIds) $academyIds = array_map(fn(array $row): int => (int) $row['academy_id'], DB::table('academies')->whereNull('deleted_at')->get());
        $academyIds = array_values(array_unique(array_filter($academyIds)));
        if (!$academyIds) return [];
        $academies = DB::table('academies')->whereIn('academy_id', $academyIds)->whereNull('deleted_at')->get();
        $academyNames = $this->translations('academies', $academyIds, ['title', 'name']);
        $organizations = [];
        foreach ($academies as $academy) {
            $id = (int) $academy['academy_id'];
            $organizations[] = ['id' => $id, 'user_id' => (int) $academy['user_id'], 'kind' => 'academy', 'name' => $academyNames[$id]['title'] ?? $academyNames[$id]['name'] ?? ('آموزشگاه ' . $id)];
        }
        $branches = DB::table('academy_branches')->whereIn('academy_id', $academyIds)->whereNull('deleted_at')->get();
        $branchIds = array_map(fn(array $row): int => (int) $row['branch_id'], $branches);
        $branchNames = $this->translations('academy_branches', $branchIds, ['name']);
        foreach ($branches as $branch) {
            $id = (int) $branch['branch_id'];
            $organizations[] = ['id' => $id, 'user_id' => (int) $branch['user_id'], 'kind' => 'branch', 'name' => $branchNames[$id]['name'] ?? ('شعبه ' . $id)];
        }
        return $organizations;
    }

    private function hasFixedBranchOrganization(int $actor, array $organizations): bool
    {
        if (!$organizations || array_filter($organizations, fn(array $row): bool => $row['kind'] !== 'branch')) return false;
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (($user['type'] ?? '') === 'branch') return true;
        return (bool) DB::table('academy_branch_members')
            ->join('academy_branch_member_roles', 'academy_branch_member_roles.member_id', '=', 'academy_branch_members.member_id')
            ->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')
            ->where('academy_branch_members.user_id', $actor)
            ->whereIn('access_system_roles.name', ['branch_manager', 'branch_receptionist'])
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
    }

    private function organizationsForDisplay(int $actor, array $manageable): array
    {
        $organizations = array_map(function (array $organization): array {
            $organization['read_only'] = false;
            return $organization;
        }, $manageable);
        if (!$organizations || array_filter($organizations, fn(array $organization): bool => $organization['kind'] === 'academy')) return $organizations;

        $branchIds = array_map(fn(array $organization): int => (int) $organization['id'], array_filter($organizations, fn(array $organization): bool => $organization['kind'] === 'branch'));
        if (!$branchIds) return $organizations;
        $branches = DB::table('academy_branches')->whereIn('branch_id', $branchIds)->whereNull('deleted_at')->get();
        $academyIds = array_values(array_unique(array_map(fn(array $branch): int => (int) $branch['academy_id'], $branches)));
        if (!$academyIds) return $organizations;
        $academies = DB::table('academies')->whereIn('academy_id', $academyIds)->whereNull('deleted_at')->get();
        $names = $this->translations('academies', $academyIds, ['title', 'name']);
        foreach ($academies as $academy) {
            $id = (int) $academy['academy_id'];
            $organizations[] = [
                'id' => $id,
                'user_id' => (int) $academy['user_id'],
                'kind' => 'academy',
                'name' => $names[$id]['title'] ?? $names[$id]['name'] ?? ('آموزشگاه ' . $id),
                'read_only' => true,
            ];
        }
        return $organizations;
    }

    private function allowedOrganization(int $actor, int $userId): array
    {
        $organizations = $this->scopedOrganizations($actor);
        if (count($organizations) === 1 && $organizations[0]['kind'] === 'branch') return $organizations[0];
        foreach ($organizations as $organization) if ((int) $organization['user_id'] === $userId) return $organization;
        throw new RuntimeException('شما به سازمان انتخاب‌شده دسترسی ندارید.');
    }

    private function shouldApprove(int $actor): bool
    {
        return $this->lessonStatusMode($actor) === 'active';
    }

    private function isReceptionist(int $actor): bool
    {
        $role = DB::table('academy_branch_members')
            ->join('academy_branch_member_roles', 'academy_branch_member_roles.member_id', '=', 'academy_branch_members.member_id')
            ->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')
            ->where('academy_branch_members.user_id', $actor)
            ->whereRaw("access_system_roles.name LIKE '%receptionist%'")
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
        if ($role) return true;
        return (bool) DB::table('academy_branch_members')
            ->join('academy_branch_member_contracts', 'academy_branch_member_contracts.member_id', '=', 'academy_branch_members.member_id')
            ->where('academy_branch_members.user_id', $actor)->where('academy_branch_member_contracts.type', 'receptionist')
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_contracts.deleted_at')->first();
    }

    private function lessonStatusMode(int $actor): string
    {
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (!$user) return 'pending';
        if (in_array((string) ($user['type'] ?? ''), ['academy', 'branch'], true)) return 'active';
        $role = DB::table('academy_branch_members')
            ->join('academy_branch_member_roles', 'academy_branch_member_roles.member_id', '=', 'academy_branch_members.member_id')
            ->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')
            ->where('academy_branch_members.user_id', $actor)
            ->whereIn('access_system_roles.name', ['academy_owner', 'academy_manager', 'branch_manager', 'academy_receptionist', 'branch_receptionist'])
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
        $name = (string) ($role['name'] ?? '');
        if (str_contains($name, 'receptionist')) return 'pending';
        if ($name !== '') return 'active';
        $contract = DB::table('academy_branch_members')->join('academy_branch_member_contracts', 'academy_branch_member_contracts.member_id', '=', 'academy_branch_members.member_id')
            ->where('academy_branch_members.user_id', $actor)->where('academy_branch_member_contracts.type', 'receptionist')
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_contracts.deleted_at')->first();
        return $contract ? 'pending' : 'pending';
    }

    private function hasLessonManagementRole(int $memberId): bool
    {
        return (bool) DB::table('academy_branch_member_roles')
            ->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')
            ->where('academy_branch_member_roles.member_id', $memberId)
            ->whereIn('access_system_roles.name', ['academy_owner', 'academy_manager', 'branch_manager', 'academy_receptionist', 'branch_receptionist'])
            ->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
    }

    private function allowedBranch(int $actor, int $branchId): array
    {
        foreach ($this->scopedBranches($actor) as $branch) {
            if ((int) $branch['branch_id'] === $branchId) return $branch;
        }
        throw new RuntimeException('شما به این شعبه دسترسی ندارید.');
    }
}
