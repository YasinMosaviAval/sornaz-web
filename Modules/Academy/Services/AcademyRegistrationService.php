<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use Core\translation\TranslationService;
use Modules\System\Services\UserService;
use RuntimeException;

class AcademyRegistrationService {
    public function __construct(protected UserService $users) {}

    public function register(array $data): int {
        return transaction(function () use ($data) {
            $data['type'] = 'academy';
            $data['full_name'] = $data['academy_name'];
            $userId = $this->users->register($data);
            if (!$userId) throw new RuntimeException('ایجاد حساب آموزشگاه ناموفق بود.');

            $academyId = DB::table('academies')->insertGetId(['user_id' => $userId]);
            if (!$academyId) throw new RuntimeException('ایجاد آموزشگاه ناموفق بود.');

            $profileId = DB::table('user_profiles')->insertGetId(['user_id' => $userId]);
            $accountId = DB::table('financial_system_accounts')->insertGetId([
                'user_id' => $userId,
                'type' => 'academy_main',
                'balance' => 0,
                'status' => 'active',
            ]);
            $branchId = DB::table('academy_branches')->insertGetId([
                'academy_id' => $academyId,
                'user_id' => $userId,
                'is_main' => 1,
                'timezone' => $data['timezone'] ?? 'Asia/Tehran',
            ]);
            if (!$profileId || !$accountId || !$branchId) {
                throw new RuntimeException('ایجاد اطلاعات پایه آموزشگاه ناموفق بود.');
            }

            $locale = $data['locale'] ?? 'fa';
            $translations = TranslationService::manager();
            foreach ([
                'title' => $data['academy_name'],
                'slogan' => $data['slogan'] ?? '',
                'description' => $data['biography'] ?? '',
            ] as $field => $value) {
                if (!$translations->set('academies', $academyId, $field, $value, $locale)) {
                    throw new RuntimeException('ثبت اطلاعات ترجمه‌شده آموزشگاه ناموفق بود.');
                }
            }

            return $academyId;
        });
    }
}
