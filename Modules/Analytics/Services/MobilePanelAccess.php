<?php
namespace Modules\Analytics\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;

final class MobilePanelAccess
{
    public function capabilities(array $user): array
    {
        $id = (int) $user['user_id'];
        $admin = SiteAdminAccess::allows($user);
        $member = DB::table('academy_branch_members')->where('user_id', $id)->where('status', 'active')->whereNull('deleted_at')->first();
        $role = DB::table('academy_branch_members')
            ->join('academy_branch_member_roles', 'academy_branch_member_roles.member_id', '=', 'academy_branch_members.member_id')
            ->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')
            ->where('academy_branch_members.user_id', $id)->where('academy_branch_members.status', 'active')
            ->whereIn('access_system_roles.name', ['academy_owner', 'academy_manager', 'branch_manager', 'academy_receptionist', 'branch_receptionist', 'academy_branch_owner', 'academy_branch_manager', 'academy_branch_receptionist'])
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
        $contract = DB::table('academy_branch_members')
            ->join('academy_branch_member_contracts', 'academy_branch_member_contracts.member_id', '=', 'academy_branch_members.member_id')
            ->where('academy_branch_members.user_id', $id)->where('academy_branch_members.status', 'active')
            ->whereIn('academy_branch_member_contracts.type', ['owner', 'manager', 'receptionist'])
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_contracts.deleted_at')->first();
        $academy = DB::table('academies')->where('user_id', $id)->whereNull('deleted_at')->first()
            ?: DB::table('academies')->where('created_by', $id)->whereNull('deleted_at')->first();
        $branch = DB::table('academy_branches')->where('user_id', $id)->whereNull('deleted_at')->first();
        $management = $admin || (bool) $role || (bool) $contract || (bool) $academy || (bool) $branch || in_array($user['type'] ?? '', ['academy', 'branch'], true);
        $academyMembership = $this->hasAcademyManagementMembership($id);
        $branchMembership = DB::table('academy_branch_members')->where('user_id', $id)->where('status', 'active')->whereNotNull('branch_id')->whereNull('deleted_at')->first();
        return ['common' => true, 'founder' => $id === 1, 'member' => (bool) $member || $management,
            'management' => $management,
            'branchContext' => !$admin && !$academy && !$academyMembership && ((bool) $branch || (bool) $branchMembership),
            'admin' => $admin];
    }

    private function hasAcademyManagementMembership(int $actor): bool
    {
        $members = DB::table('academy_branch_members')->where('user_id', $actor)->where('status', 'active')->whereNull('branch_id')->whereNull('deleted_at')->get();
        foreach ($members as $member) {
            $id = (int) $member['member_id'];
            $role = DB::table('academy_branch_member_roles')
                ->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')
                ->where('academy_branch_member_roles.member_id', $id)
                ->whereIn('access_system_roles.name', ['academy_owner', 'academy_manager', 'academy_receptionist'])
                ->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
            $contract = DB::table('academy_branch_member_contracts')->where('member_id', $id)
                ->whereIn('type', ['owner', 'manager', 'receptionist'])->whereNull('deleted_at')->first();
            if ($role || $contract) return true;
        }
        return false;
    }
}
