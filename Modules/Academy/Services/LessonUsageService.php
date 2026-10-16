<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

final class LessonUsageService
{
    public static function lock(int $lessonId): void
    {
        $suffix = db()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'mysql' ? ' FOR UPDATE' : '';
        $query = db()->prepare('SELECT lesson_id FROM lessons WHERE lesson_id=? AND deleted_at IS NULL' . $suffix);
        $query->execute([$lessonId]);
        if (!$query->fetch()) throw new RuntimeException('درس انتخاب‌شده معتبر نیست.');
    }

    public function scope(array $lesson): array
    {
        $uid = (int) $lesson['user_id'];
        $academy = DB::table('academies')->where('user_id', $uid)->whereNull('deleted_at')->first();
        if ($academy) return ['kind' => 'academy', 'academy' => (int) $academy['academy_id'], 'branch' => null, 'owner' => (int) ($academy['created_by'] ?? 0)];
        $branch = DB::table('academy_branches')->where('user_id', $uid)->whereNull('deleted_at')->first();
        if ($branch) return ['kind' => 'branch', 'academy' => (int) $branch['academy_id'], 'branch' => (int) $branch['branch_id'], 'owner' => 0];
        return ['kind' => 'personal', 'academy' => 0, 'branch' => null, 'owner' => 0];
    }

    public function available(int $organizationUserId, int $lessonId, bool $lock = false): array
    {
        if ($lock) self::lock($lessonId);
        $owners = $this->owners($organizationUserId);
        foreach ($owners as $owner) {
            $row = $this->query("SELECT * FROM user_lessons WHERE user_id=? AND lesson_id=? AND status='active' AND deleted_at IS NULL", [$owner, $lessonId])[0] ?? null;
            if ($row) return $row;
        }
        throw new RuntimeException('درس فعال انتخاب‌شده برای این سازمان قابل استفاده نیست.');
    }

    public function validateTeachingLevel(int $organizationUserId, int $lessonId, int $levelId): void
    {
        $this->available($organizationUserId, $lessonId, true);
        $level = DB::table('levels')->where('level_id', $levelId)->where('type', 'learning')->where('is_active', 1)->whereNull('deleted_at')->first();
        if (!$level || !in_array($levelId, $this->teachingLevelIds($organizationUserId, $lessonId), true)) {
            throw new RuntimeException('درس یا سطح تدریس معتبر نیست.');
        }
    }

    public function teachingLevelIds(int $organizationUserId, int $lessonId): array
    {
        foreach ($this->owners($organizationUserId) as $owner) {
            $rows = DB::table('user_lessons')->where('user_id', $owner)->where('lesson_id', $lessonId)
                ->where('status', 'active')->whereNull('deleted_at')->get();
            if ($rows) return array_values(array_unique(array_filter(array_map('intval', array_column($rows, 'level_id')))));
        }
        return [];
    }

    private function owners(int $organizationUserId): array
    {
        $owners = [$organizationUserId];
        $branch = DB::table('academy_branches')->where('user_id', $organizationUserId)->whereNull('deleted_at')->first();
        if ($branch) {
            $academy = DB::table('academies')->where('academy_id', (int) $branch['academy_id'])->whereNull('deleted_at')->first();
            if ($academy) $owners[] = (int) $academy['user_id'];
        }
        return $owners;
    }

    public function offeredRows(array $organizations): array
    {
        $result = [];
        foreach ($organizations as $organization) {
            $owner = (int) $organization['user_id'];
            $rows = [];
            foreach (array_reverse($this->owners($owner)) as $source) {
                foreach (DB::table('user_lessons')->where('user_id', $source)->where('status', 'active')->whereNull('deleted_at')->get() as $row) {
                    $row['source_user_id'] = (int) $row['user_id'];
                    $row['user_id'] = $owner;
                    $rows[(int) $row['lesson_id']] = $row;
                }
            }
            array_push($result, ...array_values($rows));
        }
        return $result;
    }

    public function dependencies(array $lesson): array
    {
        ['courses' => $courses, 'contracts' => $contracts] = $this->references($lesson);
        return $this->describe($courses, $contracts);
    }

    public function hasDependencies(array $lesson): bool
    {
        $rows = $this->references($lesson);
        return (bool) ($rows['courses'] || $rows['contracts']);
    }

    private function references(array $lesson): array
    {
        $scope = $this->scope($lesson);
        $params = [(int) $lesson['lesson_id']];
        if ($scope['kind'] === 'personal') {
            $courses = [];
            $where = 'c.user_lesson_id=?';
            $params = [(int) $lesson['user_lesson_id']];
        } else {
            $where = $scope['kind'] === 'branch' ? 'c.branch_id=?' : '((c.academy_id=? AND c.branch_id IS NULL) OR c.branch_id IN (SELECT branch_id FROM academy_branches WHERE academy_id=? AND deleted_at IS NULL))';
            $courseParams = $scope['kind'] === 'branch' ? [$scope['branch']] : [$scope['academy'], $scope['academy']];
            $courses = $this->query('SELECT c.course_id,c.academy_id,c.branch_id FROM academy_branch_courses c WHERE c.lesson_id=? AND c.deleted_at IS NULL AND ' . $where, array_merge($params, $courseParams));
            $where = 'ul.lesson_id=? AND ' . ($scope['kind'] === 'branch' ? 'm.branch_id=?' : 'm.academy_id=?');
            $params[] = $scope['kind'] === 'branch' ? $scope['branch'] : $scope['academy'];
        }
        $contracts = $this->query("SELECT c.member_contract_id,m.user_id,m.academy_id,m.branch_id FROM academy_branch_member_contracts c JOIN academy_branch_members m ON m.member_id=c.member_id JOIN user_lessons ul ON ul.user_lesson_id=c.user_lesson_id WHERE c.type='teacher' AND c.deleted_at IS NULL AND m.deleted_at IS NULL AND " . $where, $params);
        return compact('courses', 'contracts');
    }

    private function describe(array $courses, array $contracts): array
    {
        $courseNames = $this->names('academy_branch_courses', array_column($courses, 'course_id'));
        $contractNames = $this->names('academy_branch_member_contracts', array_column($contracts, 'member_contract_id'));
        $teacherNames = $this->names('users', array_column($contracts, 'user_id'), 'full_name');
        $academies = $this->names('academies', array_merge(array_column($courses, 'academy_id'), array_column($contracts, 'academy_id')));
        $branches = $this->names('academy_branches', array_merge(array_column($courses, 'branch_id'), array_column($contracts, 'branch_id')), 'name');
        $organization = static fn ($row) => !empty($row['branch_id']) ? ($branches[$row['branch_id']] ?? 'شعبه ' . $row['branch_id']) : ($academies[$row['academy_id']] ?? 'آموزشگاه ' . $row['academy_id']);
        return [
            'courses' => array_map(static fn ($row) => ['id' => (int) $row['course_id'], 'title' => $courseNames[$row['course_id']] ?? 'دوره ' . $row['course_id'], 'organization' => $organization($row)], $courses),
            'contracts' => array_map(static fn ($row) => ['id' => (int) $row['member_contract_id'], 'title' => $contractNames[$row['member_contract_id']] ?? 'قرارداد ' . $row['member_contract_id'], 'teacher' => $teacherNames[$row['user_id']] ?? 'مدرس ' . $row['user_id'], 'organization' => $organization($row)], $contracts),
        ];
    }

    public function canManageUsed(int $actor, array $lesson): bool
    {
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (SiteAdminAccess::allows($user)) return true;
        $scope = $this->scope($lesson);
        if ($scope['kind'] === 'personal') return false;
        if ((int) $lesson['user_id'] === $actor && in_array($user['type'] ?? '', ['academy', 'branch'], true)) return true;
        $academy = DB::table('academies')->where('academy_id', $scope['academy'])->whereNull('deleted_at')->first();
        if ($academy && $actor === (int) $academy['user_id'] && ($user['type'] ?? '') !== 'branch') return true;
        $members = DB::table('academy_branch_members')->where('user_id', $actor)->where('academy_id', $scope['academy'])->where('status', 'active')->whereNull('deleted_at')->get();
        foreach ($members as $member) {
            if ($member['branch_id'] !== null && ($scope['kind'] !== 'branch' || (int) $member['branch_id'] !== $scope['branch'])) continue;
            $roles = $member['branch_id'] === null ? ['academy_owner', 'academy_manager'] : ['branch_manager'];
            if (DB::table('academy_branch_member_roles')->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')->where('academy_branch_member_roles.member_id', (int) $member['member_id'])->whereIn('access_system_roles.name', $roles)->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first()) return true;
            if (DB::table('academy_branch_member_contracts')->where('member_id', (int) $member['member_id'])->where('type', 'manager')->whereNull('deleted_at')->first()) return true;
        }
        return false;
    }

    public function assertEditable(int $actor, array $lesson, ?array $usage = null): void
    {
        $usage ??= $this->references($lesson);
        if (($usage['courses'] || $usage['contracts']) && !$this->canManageUsed($actor, $lesson)) throw new RuntimeException('ویرایش درس استفاده‌شده فقط برای مدیر یا حساب آموزشگاه و شعبهٔ مجاز امکان‌پذیر است.');
    }

    private function query(string $sql, array $params): array
    {
        if (db()->inTransaction() && db()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'mysql') $sql .= ' FOR UPDATE';
        $query = db()->prepare($sql);
        $query->execute($params);
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function current(int $id): ?array
    {
        return (new self())->query('SELECT * FROM user_lessons WHERE user_lesson_id=? AND deleted_at IS NULL', [$id])[0] ?? null;
    }

    private function names(string $table, array $ids, string $field = 'title'): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (!$ids) return [];
        $locale = function_exists('locale') ? locale() : 'fa';
        $rows = DB::table('translations')->where('table_name', $table)->whereIn('table_id', $ids)->where('field', $field)->whereIn('locale', array_unique(['fa', $locale]))->whereNull('deleted_at')->get();
        $names = [];
        foreach ($rows as $row) if (!isset($names[$row['table_id']]) || $row['locale'] === $locale) $names[$row['table_id']] = (string) $row['value'];
        return $names;
    }
}
