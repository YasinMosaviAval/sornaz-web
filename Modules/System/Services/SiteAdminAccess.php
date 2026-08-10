<?php

namespace Modules\System\Services;

use Core\database\DB;

class SiteAdminAccess {
    public static function allows(?array $user): bool {
        if (!$user) return false;
        $userId = (int)($user['user_id'] ?? 0);
        if (!$userId) return false;

        // حساب بنیان‌گذار سایت مستقل از type و داده‌های نقش، همیشه مدیر سایت است.
        if ($userId === 1) return true;

        if (($user['type'] ?? null) !== 'manager') return false;

        $role = DB::table('user_roles')
            ->join('access_system_roles', 'access_system_roles.role_id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->whereIn('access_system_roles.name', ['admin', 'superadmin'])
            ->whereNull('user_roles.deleted_at')
            ->whereNull('access_system_roles.deleted_at')
            ->first();
        if ($role) return true;

        $firstManager = DB::table('users')
            ->where('type', 'manager')
            ->whereNull('deleted_at')
            ->orderBy('user_id')
            ->first();

        return (int)($firstManager['user_id'] ?? 0) === $userId;
    }
}
