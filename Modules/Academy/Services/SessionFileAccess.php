<?php
namespace Modules\Academy\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

/** Session files inherit live organization membership and term enrollment. */
final class SessionFileAccess
{
    public function authorize(int $actor, int $sessionId, bool $upload = false): array
    {
        if (!$actor || $actor !== (int) auth()->id()) throw new RuntimeException('برای ادامه وارد حساب شوید.', 401);
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (!$user || in_array($user['status'] ?? '', ['blocked', 'banned', 'suspended', 'inactive', 'rejected'], true)) $this->denied();
        $session = $this->session($sessionId);
        $manage = SiteAdminAccess::allows($user);
        $this->organizationIdentity($user, $session, $manage);
        $manage = $manage || (int) $session['academy_user_id'] === $actor
            || (!empty($session['branch_id']) && (int) $session['branch_user_id'] === $actor);
        $members = $this->members($actor, $session);
        foreach ($members as $member) if ($this->manages($member)) $manage = true;
        $types = $this->enrollmentTypes($session, $members);
        $canUpload = $manage || in_array('teacher', $types, true);
        if ((!$canUpload && !in_array('student', $types, true)) || ($upload && !$canUpload)) $this->denied();
        return $session + ['canUpload' => $canUpload, 'canManage' => $manage];
    }

    private function session(int $id): array
    {
        if ($id < 1) throw new RuntimeException('جلسه معتبر نیست.', 422);
        $query = db()->prepare('SELECT s.term_session_id session_id,s.term_id,c.academy_id,c.branch_id,a.user_id academy_user_id,br.user_id branch_user_id FROM academy_branch_course_term_sessions s JOIN academy_branch_bookings b ON b.booking_id=s.booking_id JOIN academy_branch_course_terms t ON t.term_id=s.term_id JOIN academy_branch_courses c ON c.course_id=t.course_id JOIN academies a ON a.academy_id=c.academy_id LEFT JOIN academy_branches br ON br.branch_id=c.branch_id AND br.academy_id=c.academy_id WHERE s.term_session_id=? AND s.deleted_at IS NULL AND b.deleted_at IS NULL AND t.deleted_at IS NULL AND c.deleted_at IS NULL AND a.deleted_at IS NULL AND (c.branch_id IS NULL OR (br.branch_id IS NOT NULL AND br.deleted_at IS NULL))');
        $query->execute([$id]);
        $row = $query->fetch(\PDO::FETCH_ASSOC);
        if (!$row) $this->denied();
        return $row;
    }

    private function organizationIdentity(array $user, array $session, bool $admin): void
    {
        if ($admin) return;
        // Organization accounts cannot inherit a wider scope from extra memberships.
        if (($user['type'] ?? '') === 'branch' && (empty($session['branch_id']) || (int) $session['branch_user_id'] !== (int) $user['user_id'])) $this->denied();
        if (($user['type'] ?? '') === 'academy' && (int) $session['academy_user_id'] !== (int) $user['user_id']) $this->denied();
    }

    private function members(int $actor, array $session): array
    {
        $rows = DB::table('academy_branch_members')->where('user_id', $actor)->where('academy_id', (int) $session['academy_id'])->where('status', 'active')->whereNull('deleted_at')->get();
        return array_values(array_filter($rows, static fn ($member) => empty($member['branch_id']) || (!empty($session['branch_id']) && (int) $member['branch_id'] === (int) $session['branch_id'])));
    }

    private function manages(array $member): bool
    {
        $roles = DB::table('academy_branch_member_roles')->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')->where('academy_branch_member_roles.member_id', (int) $member['member_id'])->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get();
        $prefix = empty($member['branch_id']) ? 'academy' : '(?:academy|branch|academy_branch)';
        foreach ($roles as $role) if (preg_match('/^' . $prefix . '_(?:owner|manager|receptionist)(?:_[1-9][0-9]*)?$/D', (string) $role['name'])) return true;
        foreach (DB::table('academy_branch_member_contracts')->where('member_id', (int) $member['member_id'])->whereNull('deleted_at')->get() as $contract) {
            if (!in_array(strtolower((string) $contract['type']), ['manager', 'owner', 'receptionist'], true)) continue;
            if (!empty($contract['start_date']) && substr($contract['start_date'], 0, 10) > date('Y-m-d')) continue;
            if (!empty($contract['end_date']) && substr($contract['end_date'], 0, 10) < date('Y-m-d')) continue;
            if (isset($contract['status']) && !in_array($contract['status'], ['active', 'approved', 'signed', 'ongoing'], true)) continue;
            return true;
        }
        return false;
    }

    private function enrollmentTypes(array $session, array $members): array
    {
        if (!$members) return [];
        $rows = DB::table('academy_branch_course_term_enrollments')->where('term_id', (int) $session['term_id'])->whereIn('member_id', array_map('intval', array_column($members, 'member_id')))->where('status', 'active')->whereNull('deleted_at')->get();
        return array_values(array_unique(array_column($rows, 'type')));
    }

    private function denied(): never
    {
        throw new RuntimeException('دسترسی به فایل‌های این جلسه ندارید.', 403);
    }
}
