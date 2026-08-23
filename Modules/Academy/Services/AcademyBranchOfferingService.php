<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

class AcademyBranchOfferingService
{
    public function all(int $actor): array
    {
        $branches = $this->scopedBranches($actor);
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
            'organizations' => [],
            'organization_selection' => 'select',
        ];

        $organizations = $this->scopedOrganizations($actor);
        $organizationUserIds = array_map(fn(array $row): int => (int) $row['user_id'], $organizations);
        $result['organizations'] = $organizations;
        $result['organization_selection'] = count($organizations) === 1 && $organizations[0]['kind'] === 'branch' ? 'fixed' : 'select';
        $result['lesson_status_mode'] = $this->lessonStatusMode($actor);

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
        foreach ($organizations as $organization) $organizationsByUser[(int) $organization['user_id']] = $organization;

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
            $organizationUserIds,
            $organizationsByUser,
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
                'user_id' => (int) $row['user_id'],
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
        $status = $statusMode === 'pending' ? 'pending' : ($statusMode === 'active' ? 'active' : (string) ($data['status'] ?? 'pending'));
        if (!in_array($status, ['pending', 'active', 'inactive', 'removed'], true)) throw new RuntimeException('وضعیت انتخاب‌شده معتبر نیست.');
        $startDate = trim((string) ($data['start_date'] ?? ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) throw new RuntimeException('زمان شروع معتبر نیست.');
        $isPrimary = !empty($data['is_primary']) ? 1 : 0;
        $now = date('Y-m-d H:i:s');

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
            if ($this->shouldApprove($actor)) $values += ['approved_at' => $now, 'approved_by' => $actor];
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

    public function createLesson(int $actor, array $data): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '' || mb_strlen($title) > 190) throw new RuntimeException('نام درس جدید معتبر نیست.');
        $duplicate = DB::table('translations')->where('table_name', 'lessons')->where('field', 'title')->where('locale', 'fa')->where('value', $title)->whereNull('deleted_at')->first();
        if ($duplicate) throw new RuntimeException('این درس قبلاً وجود دارد.');
        $now = date('Y-m-d H:i:s');
        $values = ['created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor];
        if ($this->shouldApprove($actor)) $values += ['approved_at' => $now, 'approved_by' => $actor];
        $id = DB::table('lessons')->insertGetId($values);
        $this->setTranslationValues('lessons', $id, ['title' => $title], $actor);
        return ['id' => $id, 'title' => $title];
    }

    public function saveSchedule(int $actor, array $data, int $id = 0): array
    {
        $branchId = (int) ($data['branchId'] ?? 0);
        $branch = $this->allowedBranch($actor, $branchId);
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
        $this->setTranslationValues('user_availabilities', $id, $values, $actor);
    }

    private function setTranslationValues(string $table, int $id, array $values, int $actor): void
    {
        foreach ($values as $field => $value) {
            $row = DB::table('translations')->where('table_name', $table)->where('table_id', $id)->where('field', $field)->where('locale', 'fa')->first();
            $update = ['value' => $value, 'version' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $actor, 'deleted_at' => null, 'deleted_by' => null];
            if ($row) DB::table('translations')->where('translation_id', (int) $row['translation_id'])->update($update);
            else DB::table('translations')->insert(['table_name' => $table, 'table_id' => $id, 'field' => $field, 'locale' => 'fa', 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $actor] + $update);
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
                'branchId' => $branch['id'],
                'branchName' => $branch['name'],
                'organization_user_id' => (int) $row['user_id'],
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
        $now = date('Y-m-d H:i:s');
        $deleteValues = [
            'deleted_at' => $now,
            'deleted_by' => $actor,
            'updated_by' => $actor,
        ];
        if ($table === 'user_lessons') $deleteValues['status'] = 'removed';
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
