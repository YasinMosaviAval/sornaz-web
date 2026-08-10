<?php

namespace Modules\Analytics\Services;

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
