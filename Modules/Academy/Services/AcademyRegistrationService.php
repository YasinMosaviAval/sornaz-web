<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use Core\translation\TranslationService;
use Modules\System\Services\UserService;
use RuntimeException;

class AcademyRegistrationService {
    public function __construct(protected UserService $users) {}

    public function register(array $data): int {
        return transaction(fn() => $this->createAcademy($data));
    }

    public function seedSamples(): array {
        if (DB::table('academies')->count() > 0) {
            return ['created' => 0, 'skipped' => true, 'message' => 'آموزشگاه‌ها قبلاً ایجاد شده‌اند.'];
        }

        return transaction(function () {
            if (DB::table('academies')->count() > 0) {
                return ['created' => 0, 'skipped' => true, 'message' => 'آموزشگاه‌ها قبلاً ایجاد شده‌اند.'];
            }

            foreach ($this->sampleAcademies() as $sample) {
                $this->createAcademy($sample, true);
            }

            return ['created' => 50, 'skipped' => false, 'message' => '۵۰ آموزشگاه نمونه با موفقیت ایجاد شد.'];
        });
    }

    private function createAcademy(array $data, bool $reuseSampleUser = false): int {
        $data['type'] = 'academy';
        $data['full_name'] = $data['academy_name'];
        $existingUser = $reuseSampleUser
            ? DB::table('users')->where('username', $data['username'])->whereNull('deleted_at')->first()
            : null;
        $userId = $existingUser['user_id'] ?? $this->users->register($data);
        if (!$userId) throw new RuntimeException('ایجاد حساب آموزشگاه ناموفق بود.');
        if ($existingUser) {
            DB::table('users')->where('user_id', $userId)->update(['status' => 'approved', 'type' => 'academy']);
        }

        $academyId = DB::table('academies')->insertGetId(['user_id' => $userId]);
        if (!$academyId) throw new RuntimeException('ایجاد آموزشگاه ناموفق بود.');

        $profile = DB::table('user_profiles')->where('user_id', $userId)->whereNull('deleted_at')->first();
        $profileId = $profile['user_profile_id'] ?? DB::table('user_profiles')->insertGetId(['user_id' => $userId]);
        $account = DB::table('financial_system_accounts')->where('user_id', $userId)->where('type', 'academy_main')->whereNull('deleted_at')->first();
        $accountId = $account['account_id'] ?? DB::table('financial_system_accounts')->insertGetId([
            'user_id' => $userId, 'type' => 'academy_main', 'balance' => 0, 'status' => 'active',
        ]);
        $branch = DB::table('academy_branches')->where('user_id', $userId)->where('is_main', 1)->whereNull('deleted_at')->first();
        if ($branch) {
            $branchId = (int)$branch['branch_id'];
            DB::table('academy_branches')->where('branch_id', $branchId)->update(['academy_id' => $academyId]);
        } else {
            $branchId = DB::table('academy_branches')->insertGetId([
                'academy_id' => $academyId,
                'user_id' => $userId,
                'is_main' => 1,
                'timezone' => $data['timezone'] ?? 'Asia/Tehran',
            ]);
        }
        if (!$profileId || !$accountId || !$branchId) {
            throw new RuntimeException('ایجاد اطلاعات پایه آموزشگاه ناموفق بود.');
        }

        $locale = $data['locale'] ?? 'fa';
        $translations = TranslationService::manager();
        foreach ([
            'title' => $data['academy_name'],
            'slogan' => $data['slogan'] ?? '',
            'short_description' => $data['short_description'] ?? '',
            'description' => $data['biography'] ?? '',
        ] as $field => $value) {
            if (!$translations->set('academies', $academyId, $field, $value, $locale)) {
                throw new RuntimeException('ثبت اطلاعات ترجمه‌شده آموزشگاه ناموفق بود.');
            }
        }

        return $academyId;
    }

    private function sampleAcademies(): array {
        $names = ['آوای باران', 'نوای مهر', 'چکاد هنر', 'نغمه‌سرای پارس', 'خانه موسیقی سپیدار', 'آوای هیرکان', 'مهرآهنگ', 'ساز و سخن', 'نوای ارغوان', 'ترنم شرق'];
        $cities = ['تهران', 'شیراز', 'اصفهان', 'تبریز', 'مشهد', 'رشت', 'کرمان', 'اهواز', 'همدان', 'ساری'];
        $specialties = ['موسیقی ایرانی و ردیف دستگاهی', 'پیانو و موسیقی کلاسیک', 'آموزش تخصصی سازهای زهی', 'آواز و صداسازی', 'موسیقی کودک و ارف'];
        $slogans = ['صدای استعدادت را پیدا کن', 'آغاز مسیر حرفه‌ای موسیقی', 'هنر، تمرین و تجربه', 'با موسیقی زندگی کن', 'جایی برای رشد هنرمندان'];
        $variants = ['', 'مرکز', 'نوین', 'پارسی', 'هنر'];
        $samples = [];

        for ($i = 0; $i < 50; $i++) {
            $number = $i + 1;
            $city = $cities[$i % count($cities)];
            $specialty = $specialties[$i % count($specialties)];
            $name = trim($names[$i % count($names)] . ' ' . $variants[intdiv($i, count($names))]);
            $samples[] = [
                'username' => sprintf('sample_academy_%02d', $number),
                'email' => sprintf('academy%02d@sornaz.test', $number),
                'phone' => sprintf('0912%07d', 1000000 + $number),
                'password' => 'Sample@Academy1405',
                'register_method' => 'email',
                'academy_name' => $name,
                'slogan' => $slogans[$i % count($slogans)],
                'short_description' => "آموزشگاه {$name} در {$city}؛ مرکز تخصصی {$specialty} برای هنرجویان کودک، نوجوان و بزرگسال.",
                'biography' => "آموزشگاه {$name} با بهره‌گیری از استادان باتجربه، دوره‌های مقدماتی تا پیشرفته {$specialty} را در فضایی حرفه‌ای برگزار می‌کند. برنامه آموزشی این مجموعه بر تمرین هدفمند، اجرای هنرجویی و رشد خلاقیت موسیقایی استوار است.",
                'status' => 'approved',
                'locale' => 'fa',
                'timezone' => 'Asia/Tehran',
            ];
        }

        return $samples;
    }

    public function all(): array {
        $statement = db()->prepare(<<<SQL
            SELECT
                academies.academy_id AS id,
                academies.user_id,
                users.username,
                users.status,
                COUNT(DISTINCT academy_branches.branch_id) AS branches,
                COUNT(DISTINCT academy_branch_courses.course_id) AS classes,
                COUNT(DISTINCT CASE
                    WHEN academy_branch_course_term_enrollments.type = 'student'
                     AND academy_branch_course_term_enrollments.deleted_at IS NULL
                    THEN academy_branch_course_term_enrollments.member_id
                END) AS students
            FROM academies
            INNER JOIN users ON users.user_id = academies.user_id
            LEFT JOIN academy_branches
                ON academy_branches.academy_id = academies.academy_id
               AND academy_branches.deleted_at IS NULL
            LEFT JOIN academy_branch_courses
                ON academy_branch_courses.branch_id = academy_branches.branch_id
               AND academy_branch_courses.deleted_at IS NULL
            LEFT JOIN academy_branch_course_terms
                ON academy_branch_course_terms.course_id = academy_branch_courses.course_id
               AND academy_branch_course_terms.deleted_at IS NULL
            LEFT JOIN academy_branch_course_term_enrollments
                ON academy_branch_course_term_enrollments.term_id = academy_branch_course_terms.term_id
            WHERE academies.deleted_at IS NULL
              AND users.deleted_at IS NULL
              AND users.type = 'academy'
            GROUP BY academies.academy_id, academies.user_id, users.username, users.status
            ORDER BY academies.academy_id DESC
        SQL);
        $statement->execute();
        $rows = $statement->fetchAll();
        $translations = TranslationService::manager();
        $locale = app()->getLocale();

        return array_map(function (array $row) use ($translations, $locale) {
            $academyId = (int)$row['id'];
            return [
                'id' => $academyId,
                'name' => $translations->get('academies', $academyId, 'title', $locale) ?: $row['username'],
                'slogan' => $translations->get('academies', $academyId, 'slogan', $locale) ?: '',
                'summary' => $translations->get('academies', $academyId, 'short_description', $locale) ?: '',
                'bio' => $translations->get('academies', $academyId, 'description', $locale) ?: '',
                'status' => $row['status'],
                'branches' => (int)$row['branches'],
                'classes' => (int)$row['classes'],
                'students' => (int)$row['students'],
            ];
        }, $rows);
    }
}
