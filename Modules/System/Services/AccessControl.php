<?php
namespace Modules\System\Services;
use Core\database\DB;
class AccessControl{
 public function allows(int$userId,string$permission):bool{if($userId===1)return true;$sql="SELECT 1 FROM access_system_permissions p WHERE p.name=? AND p.deleted_at IS NULL AND (EXISTS(SELECT 1 FROM user_permissions up WHERE up.permission_id=p.permission_id AND up.user_id=? AND up.deleted_at IS NULL AND (up.expires_at IS NULL OR up.expires_at>NOW())) OR EXISTS(SELECT 1 FROM user_roles ur JOIN access_system_role_permissions rp ON rp.role_id=ur.role_id AND rp.deleted_at IS NULL JOIN access_system_roles r ON r.role_id=ur.role_id AND r.deleted_at IS NULL WHERE ur.user_id=? AND ur.deleted_at IS NULL AND (ur.expires_at IS NULL OR ur.expires_at>NOW()) AND rp.permission_id=p.permission_id)) LIMIT 1";$s=db()->prepare($sql);$s->execute([$permission,$userId,$userId]);return(bool)$s->fetchColumn();}
 public function settingAllows(int$userId,int$settingId):bool{if($userId===1)return true;$rows=DB::table('access_system_setting_permissions')->where('setting_id',$settingId)->whereNull('deleted_at')->get();if(!$rows)return$this->allows($userId,'settings.manage');foreach($rows as$row)if($this->allowsId($userId,(int)$row['permission_id']))return true;return false;}
 private function allowsId(int$userId,int$permissionId):bool{$p=DB::table('access_system_permissions')->where('permission_id',$permissionId)->whereNull('deleted_at')->first();return$p?$this->allows($userId,(string)$p['name']):false;}
}
