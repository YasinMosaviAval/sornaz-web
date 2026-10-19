<?php
namespace Modules\Academy\Services;

use Core\database\DB;

final class NationalHolidayPolicy
{
    public const BRANCH_KEY = 'allow_classes_on_national_holidays';

    public static function allows(int $academyId, int $branchId = 0): bool
    {
        if ($branchId > 0) {
            $branch = DB::table('academy_branches')->where('branch_id', $branchId)->where('academy_id', $academyId)->whereNull('deleted_at')->first();
            if ($branch) {
                $setting = DB::table('z_user_settings')->where('user_id', (int) $branch['user_id'])->where('`key`', self::BRANCH_KEY)->whereNull('deleted_at')->first();
                if ($setting) return (string) $setting['value'] === '1';
            }
        }
        $setting = DB::table('academy_national_holiday_settings')->where('academy_id', $academyId)->first();
        return (bool) ($setting['allow_classes_on_national_holidays'] ?? false);
    }

    public static function saveBranch(int $userId, bool $allow, int $actor): void
    {
        $existing = DB::table('z_user_settings')->where('user_id', $userId)->where('`key`', self::BRANCH_KEY)->whereNull('deleted_at')->first();
        $values = ['value' => $allow ? '1' : '0', 'type' => 'bool', 'visibility' => 'private', 'updated_by' => $actor, 'deleted_at' => null, 'deleted_by' => null];
        if ($existing) DB::table('z_user_settings')->where('user_setting_id', (int) $existing['user_setting_id'])->update($values);
        else DB::table('z_user_settings')->insert(['user_id' => $userId, '`key`' => self::BRANCH_KEY, 'created_by' => $actor] + $values);
    }
}
