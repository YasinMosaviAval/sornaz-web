<?php
namespace Modules\Academy\Services;

use Core\database\DB;
use Core\database\TableNames;
use Modules\System\Services\SiteAdminAccess;
use PDO;
use RuntimeException;

/** Organization ownership for rooms; the old branch-only schema remains readable. */
final class ClassroomOrganizationScope
{
    public static function supportsAcademies(): bool
    {
        static $connections;
        $connections ??= new \WeakMap();
        $pdo = db();
        if (!isset($connections[$pdo])) {
            $columns = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite'
                ? array_column($pdo->query('PRAGMA table_info(' . ($pdo instanceof \Core\database\PrefixedPDO ? TableNames::physical('academy_branch_classrooms') : 'academy_branch_classrooms') . ')')->fetchAll(PDO::FETCH_ASSOC), 'name')
                : array_column($pdo->query('SHOW COLUMNS FROM academy_branch_classrooms')->fetchAll(PDO::FETCH_ASSOC), 'Field');
            $connections[$pdo] = in_array('academy_id', $columns, true);
        }
        return $connections[$pdo];
    }

    public static function academies(int $actor): array
    {
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (!$user) throw new RuntimeException('کاربر معتبر نیست.', 403);
        if (SiteAdminAccess::allows($user)) return DB::table('academies')->whereNull('deleted_at')->get();
        $rows = array_merge(DB::table('academies')->where('user_id', $actor)->whereNull('deleted_at')->get(), DB::table('academies')->where('created_by', $actor)->whereNull('deleted_at')->get());
        $members = DB::table('academy_branch_members')->where('user_id', $actor)->where('status', 'active')->whereNull('branch_id')->whereNull('deleted_at')->get();
        foreach ($members as $member) {
            $role = DB::table('academy_branch_member_roles')->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')->where('academy_branch_member_roles.member_id', (int) $member['member_id'])->whereIn('access_system_roles.name', ['academy_owner', 'academy_manager', 'academy_receptionist'])->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
            if ($role) {
                $academy = DB::table('academies')->where('academy_id', (int) $member['academy_id'])->whereNull('deleted_at')->first();
                if ($academy) $rows[] = $academy;
            }
        }
        $unique = [];
        foreach ($rows as $row) $unique[(int) $row['academy_id']] = $row;
        return array_values($unique);
    }

    public static function rooms(array $branches, array $academies = []): array
    {
        $conditions = [];
        $bindings = [];
        if ($branches) {
            $conditions[] = 'branch_id IN (' . implode(',', array_fill(0, count($branches), '?')) . ')';
            $bindings = array_map('intval', $branches);
        }
        if ($academies && self::supportsAcademies()) {
            $conditions[] = '(branch_id IS NULL AND academy_id IN (' . implode(',', array_fill(0, count($academies), '?')) . '))';
            array_push($bindings, ...array_map('intval', $academies));
        }
        if (!$conditions) return [];
        $stmt = db()->prepare('SELECT * FROM academy_branch_classrooms WHERE deleted_at IS NULL AND (' . implode(' OR ', $conditions) . ')');
        $stmt->execute($bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id, int $branch, int $academy = 0): ?array
    {
        $query = DB::table('academy_branch_classrooms')->where('classroom_id', $id)->whereNull('deleted_at');
        if ($branch > 0) return $query->where('branch_id', $branch)->first() ?: null;
        if (!$academy || !self::supportsAcademies()) return null;
        return $query->whereNull('branch_id')->where('academy_id', $academy)->first() ?: null;
    }

    public static function allowedAcademy(int $actor, int $id): array
    {
        foreach (self::academies($actor) as $academy) {
            if ((int) $academy['academy_id'] === $id) return $academy;
        }
        throw new RuntimeException('آموزشگاه خارج از محدوده مدیریت شما است.', 403);
    }
}
