<?php
namespace Modules\Analytics\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;

final class MobilePanelAccess {
    public function capabilities(array $user): array {
        $id = (int)$user['user_id'];
        $admin = SiteAdminAccess::allows($user);
        $member = DB::table('academy_branch_members')->where('user_id', $id)->where('status', 'active')->whereNull('deleted_at')->first();
        $role = DB::table('academy_branch_members')
            ->join('academy_branch_member_roles', 'academy_branch_member_roles.member_id', '=', 'academy_branch_members.member_id')
            ->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')
            ->where('academy_branch_members.user_id', $id)->where('academy_branch_members.status', 'active')
            ->whereIn('access_system_roles.name', ['academy_owner','academy_manager','branch_manager','academy_receptionist','branch_receptionist'])
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
        $contract = DB::table('academy_branch_members')
            ->join('academy_branch_member_contracts','academy_branch_member_contracts.member_id','=','academy_branch_members.member_id')
            ->where('academy_branch_members.user_id',$id)->where('academy_branch_members.status','active')
            ->whereIn('academy_branch_member_contracts.type',['owner','manager','receptionist'])
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_contracts.deleted_at')->first();
        $management = $admin || (bool)$role || (bool)$contract || in_array($user['type'] ?? '', ['academy','branch'], true);
        return ['common'=>true, 'member'=>(bool)$member || $management,
            'management'=>$management,
            'admin'=>$admin];
    }
}
