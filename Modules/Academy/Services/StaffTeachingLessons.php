<?php
namespace Modules\Academy\Services;

use Core\database\DB;
use RuntimeException;

final class StaffTeachingLessons
{
    public static function ready(): bool
    {
        try { db()->query('SELECT member_contract_id FROM p_academy_branch_member_contract_lessons LIMIT 0'); return true; }
        catch (\PDOException $error) {
            if (in_array((string) $error->getCode(), ['42S02', 'HY000'], true) && (str_contains($error->getMessage(), 'exist') || str_contains($error->getMessage(), 'no such table'))) return false;
            throw $error;
        }
    }

    public static function relation(): string
    {
        $primary = 'SELECT member_contract_id,user_lesson_id FROM academy_branch_member_contracts WHERE deleted_at IS NULL AND user_lesson_id IS NOT NULL';
        return '(' . $primary . (self::ready() ? ' UNION SELECT member_contract_id,user_lesson_id FROM p_academy_branch_member_contract_lessons' : '') . ')';
    }

    public function validate(array $data, int $organization): array
    {
        $rows = $data['lessons'] ?? [['type' => $data['lessonId'] ?? 0, 'level' => $data['levelId'] ?? 0]];
        if (!is_array($rows) || !$rows || count($rows) > 30) throw new RuntimeException('یک تا ۳۰ درس تدریس انتخاب کنید.');
        $result = [];
        foreach ($rows as $row) {
            $lesson = (int) ($row['type'] ?? 0); $level = (int) ($row['level'] ?? 0);
            if (isset($result[$lesson])) throw new RuntimeException('هر درس را فقط یک بار انتخاب کنید.');
            (new LessonUsageService())->validateTeachingLevel($organization, $lesson, $level);
            $result[$lesson] = ['type' => $lesson, 'level' => $level];
        }
        if (count($result) > 1 && !self::ready()) throw new RuntimeException('امکان ثبت چند درس هنوز در این نسخهٔ سرور فعال نیست.');
        return array_values($result);
    }

    public function userLessons(int $actor, int $user, array $lessons, bool $approved): array
    {
        $ids = []; $now = date('Y-m-d H:i:s');
        foreach ($lessons as $row) {
            $existing = DB::table('user_lessons')->where('user_id', $user)->where('lesson_id', $row['type'])->where('level_id', $row['level'])->whereNull('deleted_at')->first();
            $ids[] = $existing ? (int) $existing['user_lesson_id'] : DB::table('user_lessons')->insertGetId(['user_id' => $user, 'lesson_id' => $row['type'], 'level_id' => $row['level'], 'status' => $approved ? 'active' : 'pending', 'is_primary' => DB::table('user_lessons')->where('user_id', $user)->whereNull('deleted_at')->count() ? 0 : 1, 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor, 'approved_at' => $approved ? $now : null, 'approved_by' => $approved ? $actor : null]);
        }
        return $ids;
    }

    public function sync(int $contract, array $ids): void
    {
        if (!self::ready()) return;
        $statement = db()->prepare('DELETE FROM p_academy_branch_member_contract_lessons WHERE member_contract_id=?');
        $statement->execute([$contract]);
        foreach ($ids as $id) DB::table('p_academy_branch_member_contract_lessons')->insert(['member_contract_id' => $contract, 'user_lesson_id' => $id]);
    }

    public function forContract(int $contract): array
    {
        $statement = db()->prepare('SELECT ul.* FROM ' . self::relation() . ' cl JOIN user_lessons ul ON ul.user_lesson_id=cl.user_lesson_id WHERE cl.member_contract_id=? AND ul.deleted_at IS NULL ORDER BY ul.user_lesson_id');
        $statement->execute([$contract]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function teaches(int $member, int $lesson): bool
    {
        $join = self::ready() ? 'JOIN ' . self::relation() . ' cl ON cl.member_contract_id=c.member_contract_id JOIN user_lessons ul ON ul.user_lesson_id=cl.user_lesson_id' : 'JOIN user_lessons ul ON ul.user_lesson_id=c.user_lesson_id';
        $approval = self::ready() ? ' AND (c.created_by IS NULL OR c.approved_at IS NOT NULL)' : '';
        $statement = db()->prepare("SELECT ul.user_lesson_id FROM academy_branch_member_contracts c $join WHERE c.member_id=? AND c.type='teacher' AND c.deleted_at IS NULL AND ul.lesson_id=? AND ul.deleted_at IS NULL $approval LIMIT 1");
        $statement->execute([$member, $lesson]);
        return (bool) $statement->fetchColumn();
    }

    public function memberRows(array $rows): array
    {
        $groups = [];
        foreach ($rows as $row) {
            $id = (int) $row['id'];
            if ($row['type'] === 'student') $row['studentSchedule'] = json_decode((string) \Core\translation\TranslationService::manager()->get('academy_branch_members', $id, 'student_schedule', 'fa'), true) ?: null;
            if ($row['type'] === 'teacher') {
                $row['lessons'] = array_map(static fn ($lesson) => ['type' => (int) $lesson['lesson_id'], 'level' => (int) $lesson['level_id']], $this->forContract((int) $row['contractId']));
            }
            $roles = DB::table('academy_branch_member_roles')->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')->where('academy_branch_member_roles.member_id', $id)->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get();
            foreach ($roles as $role) {
                if (str_contains((string) $role['name'], $row['type'])) { $row['roleId'] = (int) $role['role_id']; break; }
            }
            $contracts = $groups[$id]['contracts'] ?? [];
            $contracts[] = $row;
            if (!isset($groups[$id]) || $row['type'] === 'teacher') $groups[$id] = $row;
            $groups[$id]['contracts'] = $contracts;
            $groups[$id]['typeLabel'] = implode('، ', array_unique(array_column($contracts, 'typeLabel')));
            if ($groups[$id]['type'] === 'teacher') {
                $groups[$id]['lessonName'] = implode('، ', array_map(static fn ($lesson) => \Core\translation\TranslationService::manager()->get('lessons', (int) $lesson['type'], 'title', 'fa') ?: 'درس ' . $lesson['type'], $groups[$id]['lessons']));
            }
        }
        return array_values($groups);
    }
}
