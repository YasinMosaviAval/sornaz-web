<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use Core\translation\TranslationService;
use Modules\System\Services\UserService;
use RuntimeException;

class AcademyRegistrationService {
    private const MANAGER_PREFIX = 'test_academy_manager_';
    private const ACADEMY_PREFIX = 'test_academy_';
    private const BRANCH_PREFIX = 'test_main_branch_';

    public function __construct(protected UserService $users) {}

    public function register(array $data): int {
        return transaction(fn() => $this->createAcademy($data));
    }

    public function seedSamples(): array {
        return transaction(function () {
            $branchTypes = DB::table('academy_branch_types')->whereNull('deleted_at')->get();
            $provinces = DB::table('world_iran_provinces')->get();
            $counties = DB::table('world_iran_counties')->get();
            $managers = DB::table('users')->whereRaw("username LIKE '" . self::MANAGER_PREFIX . "%' ")
                ->whereNull('deleted_at')->get();
            usort($managers, fn(array $a, array $b) => strcmp((string)$a['username'], (string)$b['username']));
            if (!$managers) throw new RuntimeException('ابتدا تست ۱ (مدیران آموزشگاه) را اجرا کنید.');

            $samples = $this->sampleAcademies();
            $created = 0;
            $updated = 0;
            foreach ($managers as $index => $manager) {
                if (!isset($samples[$index])) break;
                $wasCreated = $this->createSampleAcademy(
                    $samples[$index], $index, (int)$manager['user_id'], $branchTypes, $provinces, $counties
                );
                $wasCreated ? $created++ : $updated++;
            }

            return [
                'created' => $created,
                'updated' => $updated,
                'branches_created' => $created + $updated,
                'skipped' => false,
                'message' => "تست ۲ تکمیل شد: {$created} آموزشگاه ایجاد و {$updated} آموزشگاه همگام‌سازی شد؛ برای هرکدام یک شعبه اصلی و دو عضویت مدیر ثبت شد.",
            ];
        });
    }

    public function deleteSamples(): array {
        return transaction(function () {
            $users = DB::table('users')->whereRaw(
                "(username LIKE '" . self::ACADEMY_PREFIX . "%' AND username NOT LIKE '" . self::MANAGER_PREFIX . "%')"
                . " OR username LIKE '" . self::BRANCH_PREFIX . "%'"
            )->get();
            $userIds = array_map(fn(array $user) => (int)$user['user_id'], $users);
            if (!$userIds) return ['deleted' => 0, 'message' => 'هیچ اطلاعات آزمایشی برای حذف وجود ندارد.'];

            $academies = DB::table('academies')->whereIn('user_id', $userIds)->get();
            $academyIds = array_map(fn(array $academy) => (int)$academy['academy_id'], $academies);
            $branches = $academyIds ? DB::table('academy_branches')->whereIn('academy_id', $academyIds)->get() : [];
            $branchIds = array_map(fn(array $branch) => (int)$branch['branch_id'], $branches);
            $managerIds = array_values(array_unique(array_map(fn(array $row) => (int)$row['created_by'], $academies)));
            $members = $branchIds ? DB::table('academy_branch_members')->whereIn('branch_id', $branchIds)->get() : [];
            if ($managerIds) {
                foreach (DB::table('academy_branch_members')->whereIn('user_id', $managerIds)->whereNull('branch_id')->get() as $member) {
                    if (in_array((int)$member['created_by'], $managerIds, true)) $members[] = $member;
                }
            }
            $memberIds = array_map(fn(array $member) => (int)$member['member_id'], $members);

            if ($memberIds) {
                DB::table('academy_branch_member_roles')->whereIn('member_id', $memberIds)->delete();
                DB::table('academy_branch_member_contracts')->whereIn('member_id', $memberIds)->delete();
            }
            if ($memberIds) DB::table('academy_branch_members')->whereIn('member_id', $memberIds)->delete();

            $contacts = DB::table('user_contacts')->whereIn('user_id', $userIds)->get();
            $addresses = DB::table('user_addresses')->whereIn('user_id', $userIds)->get();
            $this->deleteTranslations('user_contacts', array_map(fn(array $row) => (int)$row['user_contact_id'], $contacts));
            $this->deleteTranslations('user_addresses', array_map(fn(array $row) => (int)$row['address_id'], $addresses));
            $this->deleteTranslations('academy_branches', $branchIds);
            $this->deleteTranslations('academies', $academyIds);
            $this->deleteTranslations('users', $userIds);

            DB::table('user_contacts')->whereIn('user_id', $userIds)->delete();
            DB::table('user_addresses')->whereIn('user_id', $userIds)->delete();
            DB::table('z_user_profiles')->whereIn('user_id', $userIds)->delete();
            DB::table('financial_system_accounts')->whereIn('user_id', $userIds)->delete();
            DB::table('user_roles')->whereIn('user_id', $userIds)->delete();
            DB::table('user_sessions')->whereIn('user_id', $userIds)->delete();
            if ($branchIds) DB::table('academy_branches')->whereIn('branch_id', $branchIds)->delete();
            if ($academyIds) DB::table('academies')->whereIn('academy_id', $academyIds)->delete();
            DB::table('users')->whereIn('user_id', $userIds)->delete();

            return [
                'deleted' => count($academyIds),
                'message' => count($academyIds) . ' آموزشگاه و تمام اطلاعات آزمایشی مرتبط با آن‌ها حذف شد.',
            ];
        });
    }

    private function deleteTranslations(string $table, array $ids): void {
        if (!$ids) return;
        DB::table('translations')->where('table_name', $table)->whereIn('table_id', $ids)->delete();
    }

    private function createAcademy(array $data): int {
        $data['type'] = 'academy';
        $data['full_name'] = $data['academy_name'];
        $userId = $this->users->register($data);
        if (!$userId) throw new RuntimeException('ایجاد حساب آموزشگاه ناموفق بود.');

        $academyId = DB::table('academies')->insertGetId(['user_id' => $userId]);
        if (!$academyId) throw new RuntimeException('ایجاد آموزشگاه ناموفق بود.');

        $profile = DB::table('z_user_profiles')->where('user_id', $userId)->whereNull('deleted_at')->first();
        $profileId = $profile['user_profile_id'] ?? DB::table('z_user_profiles')->insertGetId(['user_id' => $userId]);
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

    private function createSampleAcademy(array $sample, int $index, int $managerId, array $branchTypes, array $provinces, array $counties): bool {
        $existingUser = DB::table('users')->where('username', sprintf(self::ACADEMY_PREFIX . '%02d', $index + 1))->first();
        $academyUserId = $this->seedUser([
            'username' => sprintf(self::ACADEMY_PREFIX . '%02d', $index + 1),
            'email' => $sample['email'],
            'phone' => $sample['phone'],
            'type' => 'academy',
            'gender' => 'other',
            'national_code' => null,
            'birthday' => sprintf('%04d-%02d-%02d', 1370 + ($index % 25), ($index % 12) + 1, ($index % 27) + 1),
            'full_name' => $sample['academy_name'],
            'slogan' => $sample['slogan'],
            'visibility' => 'unlisted',
        ], $managerId);

        $academy = DB::table('academies')->where('user_id', $academyUserId)->first();
        $academyData = [
            'user_id' => $academyUserId,
            'created_by' => $managerId,
            'updated_by' => $managerId,
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $managerId,
            'deleted_at' => null, 'deleted_by' => null,
        ];
        if ($academy) {
            $academyId = (int)$academy['academy_id'];
            DB::table('academies')->where('academy_id', $academyId)->update($academyData);
        } else $academyId = DB::table('academies')->insertGetId($academyData);
        if (!$academyId) throw new RuntimeException('ایجاد آموزشگاه نمونه ناموفق بود.');

        $this->setTranslations('academies', $academyId, [
            'title' => $sample['academy_name'],
            'slogan' => $sample['slogan'],
            'short_description' => $sample['short_description'],
            'description' => $sample['biography'],
        ], $managerId);

        $this->ensureProfile($academyUserId, $managerId);
        if (!DB::table('financial_system_accounts')->where('user_id', $academyUserId)->where('type', 'academy_main')->whereNull('deleted_at')->first()) {
            DB::table('financial_system_accounts')->insertGetId([
                'user_id' => $academyUserId, 'type' => 'academy_main', 'balance' => 0, 'status' => 'active',
                'created_by' => $managerId, 'updated_by' => $managerId,
            ]);
        }

        $this->createSampleBranch($academyId, $managerId, $sample, $index, 0, $branchTypes, $provinces, $counties);
        return !$existingUser;
    }

    private function createSampleBranch(int $academyId, int $managerId, array $sample, int $academyIndex, int $branchIndex, array $branchTypes, array $provinces, array $counties): void {
        $serial = $academyIndex * 5 + $branchIndex + 1;
        $branchLabels = ['مرکزی', 'شمال', 'شرق', 'غرب', 'آنلاین'];
        $branchName = $sample['academy_name'] . ' - شعبه ' . $branchLabels[$branchIndex];
        $branchUserId = $this->seedUser([
            'username' => sprintf(self::BRANCH_PREFIX . '%02d', $academyIndex + 1),
            'email' => sprintf('branch%03d@sornaz.test', $serial),
            'phone' => sprintf('0935%07d', 3000000 + $serial),
            'type' => 'branch',
            'gender' => 'other',
            'national_code' => null,
            'birthday' => sprintf('%04d-%02d-%02d', 1370 + ($academyIndex % 25), ($academyIndex % 12) + 1, ($academyIndex % 27) + 1),
            'full_name' => $branchName,
            'visibility' => 'unlisted',
        ], $managerId);
        $this->ensureProfile($branchUserId, $managerId);

        $branchType = $branchTypes ? $branchTypes[$serial % count($branchTypes)] : null;
        $modes = ['physical', 'hybrid', 'online'];
        $branch = DB::table('academy_branches')->where('user_id', $branchUserId)->whereNull('deleted_at')->first();
        $branchData = [
            'academy_id' => $academyId,
            'user_id' => $branchUserId,
            'is_main' => $branchIndex === 0 ? 1 : 0,
            'academy_branch_type_id' => $branchType['academy_branch_type_id'] ?? null,
            'mode' => $modes[$serial % count($modes)],
            'timezone' => 'Asia/Tehran',
            'created_by' => $managerId,
            'updated_by' => $managerId,
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $managerId,
        ];
        if ($branch) {
            $branchId = (int)$branch['branch_id'];
            DB::table('academy_branches')->where('branch_id', $branchId)->update($branchData);
        } else {
            $branchId = DB::table('academy_branches')->insertGetId($branchData);
        }
        if (!$branchId) throw new RuntimeException('ایجاد شعبه نمونه ناموفق بود.');

        $this->setTranslations('academy_branches', $branchId, [
            'name' => $branchName,
            'slogan' => $sample['slogan'],
            'description' => "شعبه {$branchLabels[$branchIndex]} {$sample['academy_name']} با فضای آموزشی مجهز و برنامه منظم کلاس‌ها.",
            'manager' => $sample['manager_name'],
        ], $managerId);
        $this->seedBranchContacts($branchUserId, $managerId, $serial);
        $this->seedBranchAddresses($branchUserId, $managerId, $serial, $provinces, $counties);

        $this->ensureAcademyMember($managerId, null);
        $this->ensureAcademyMember($managerId, $branchId);
    }

    private function ensureAcademyMember(int $managerId, ?int $branchId): void {
        $query = DB::table('academy_branch_members')->where('user_id', $managerId);
        $query = $branchId === null ? $query->whereNull('branch_id') : $query->where('branch_id', $branchId);
        $member = $query->first();
        $values = ['branch_id' => $branchId, 'user_id' => $managerId, 'status' => 'active',
            'joined_at' => date('Y-m-d'), 'created_by' => $managerId, 'updated_by' => $managerId,
            'approved_at' => date('Y-m-d H:i:s'), 'approved_by' => $managerId, 'deleted_at' => null, 'deleted_by' => null];
        if ($member) {
            $memberId = (int)$member['member_id'];
            DB::table('academy_branch_members')->where('member_id', $memberId)->update($values);
        } else {
            $memberId = DB::table('academy_branch_members')->insertGetId([
                'created_at' => date('Y-m-d H:i:s'),
            ] + $values);
        }
        if (!DB::table('academy_branch_member_contracts')->where('member_id', $memberId)->whereNull('deleted_at')->first()) {
            DB::table('academy_branch_member_contracts')->insertGetId([
                'member_id' => $memberId, 'type' => 'manager', 'start_date' => date('Y-m-d'),
                'created_by' => $managerId, 'updated_by' => $managerId,
                'approved_at' => date('Y-m-d H:i:s'), 'approved_by' => $managerId,
            ]);
        }
    }

    private function seedUser(array $data, ?int $creatorId = null): int {
        $user = DB::table('users')->where('username', $data['username'])->first();
        $values = [
            'email' => $data['email'], 'phone' => $data['phone'], 'password' => password_hash('123456789', PASSWORD_DEFAULT),
            'national_code' => $data['national_code'] ?? null, 'gender' => $data['gender'] ?? 'other',
            'birthday' => $data['birthday'] ?? null,
            'type' => $data['type'], 'status' => 'approved', 'locale' => 'fa', 'timezone' => 'Asia/Tehran',
            'register_method' => 'email', 'visibility' => $data['visibility'] ?? 'unlisted', 'deleted_at' => null,
            'created_by' => $creatorId, 'updated_by' => $creatorId,
        ];
        if ($user) {
            $userId = (int)$user['user_id'];
            DB::table('users')->where('user_id', $userId)->update($values);
        } else {
            $userId = DB::table('users')->insertGetId(['username' => $data['username']] + $values);
        }
        if (!$userId) throw new RuntimeException('ایجاد کاربر نمونه ناموفق بود.');
        $userTranslations = ['full_name' => $data['full_name']];
        if (array_key_exists('slogan', $data)) $userTranslations['slogan'] = $data['slogan'];
        $this->setTranslations('users', $userId, $userTranslations, $creatorId ?: $userId);
        $this->ensureProfile($userId, $creatorId ?: $userId);
        return $userId;
    }

    private function ensureProfile(int $userId, int $creatorId): void {
        if (!DB::table('z_user_profiles')->where('user_id', $userId)->whereNull('deleted_at')->first()) {
            DB::table('z_user_profiles')->insertGetId(['user_id' => $userId, 'created_by' => $creatorId, 'updated_by' => $creatorId]);
        }
    }

    private function seedBranchContacts(int $userId, int $managerId, int $serial): void {
        $contacts = [
            ['title' => 'تلفن اصلی شعبه', 'mode' => 'phone', 'platform' => 'other', 'value' => sprintf('021%08d', 44000000 + $serial), 'priority' => 'primary', 'is_main' => 1],
            ['title' => 'اینستاگرام شعبه', 'mode' => 'social', 'platform' => 'instagram', 'value' => 'https://instagram.com/sornaz_branch_' . $serial, 'priority' => 'secondary', 'is_main' => 0],
        ];
        if ($serial % 2 === 0) $contacts[] = ['title' => 'شماره همراه', 'mode' => 'phone', 'platform' => 'other', 'value' => sprintf('0919%07d', 4000000 + $serial), 'priority' => 'secondary', 'is_main' => 0];
        if ($serial % 3 === 0) $contacts[] = ['title' => 'وب‌سایت شعبه', 'mode' => 'social', 'platform' => 'website', 'value' => 'https://branch-' . $serial . '.sornaz.test', 'priority' => 'secondary', 'is_main' => 0];
        foreach ($contacts as $contact) {
            if ($this->contactExistsByTranslatedValue($userId, $contact['value'])) continue;
            $title = $contact['title'];
            $value = $contact['value'];
            unset($contact['title'], $contact['value']);
            $contactId = DB::table('user_contacts')->insertGetId($contact + [
                'user_id' => $userId, 'status' => 'active', 'created_by' => $managerId, 'updated_by' => $managerId,
                'approved_at' => date('Y-m-d H:i:s'), 'approved_by' => $managerId,
            ]);
            $this->setTranslations('user_contacts', $contactId, ['title' => $title, 'value' => $value], $managerId);
        }
    }

    private function contactExistsByTranslatedValue(int $userId, string $value): bool {
        $translations = DB::table('translations')->where('table_name', 'user_contacts')
            ->where('locale', 'fa')->where('field', 'value')->where('value', $value)->get();
        foreach ($translations as $translation) {
            if (DB::table('user_contacts')->where('user_contact_id', (int)$translation['table_id'])
                ->where('user_id', $userId)->whereNull('deleted_at')->first()) return true;
        }
        return false;
    }

    private function seedBranchAddresses(int $userId, int $managerId, int $serial, array $provinces, array $counties): void {
        $count = $serial % 4 === 0 ? 2 : 1;
        for ($i = 0; $i < $count; $i++) {
            $province = $provinces ? $provinces[($serial + $i) % count($provinces)] : null;
            $provinceId = $province['province_id'] ?? null;
            $provinceCounties = array_values(array_filter($counties, fn($county) => (string)$county['province_id'] === (string)$provinceId));
            $county = $provinceCounties ? $provinceCounties[($serial + $i) % count($provinceCounties)] : null;
            $postalCode = sprintf('%010d', 1400000000 + $serial * 10 + $i);
            $existing = DB::table('user_addresses')->where('user_id', $userId)->where('postal_code', $postalCode)->whereNull('deleted_at')->first();
            if ($existing) {
                $addressId = (int)$existing['address_id'];
            } else {
                $addressId = DB::table('user_addresses')->insertGetId([
                    'user_id' => $userId, 'country_id' => 1, 'province_id' => $provinceId,
                    'county_id' => $county['county_id'] ?? null, 'is_main' => $i === 0 ? 1 : 0,
                    'latitude' => 35.60 + (($serial % 20) / 100), 'longitude' => 51.20 + (($serial % 20) / 100),
                    'postal_code' => $postalCode, 'created_by' => $managerId, 'updated_by' => $managerId,
                    'approved_at' => date('Y-m-d H:i:s'), 'approved_by' => $managerId,
                ]);
            }
            $this->setTranslations('user_addresses', $addressId, [
                'address' => 'خیابان فرهنگ، کوچه هنر، پلاک ' . (($serial % 90) + 10) . ($i ? '، واحد دوم' : ''),
            ], $managerId);
        }
    }

    private function setTranslations(string $table, int $id, array $values, int $creatorId): void {
        $translations = TranslationService::manager();
        foreach ($values as $field => $value) {
            if (!$translations->set($table, $id, $field, $value, 'fa')) {
                throw new RuntimeException('ثبت ترجمه اطلاعات نمونه ناموفق بود.');
            }
            $english = match ($field) {
                'full_name', 'title', 'name' => 'Sornaz Music Academy ' . $id,
                'slogan' => 'Discover your musical voice',
                'short_description' => 'A sample music academy offering structured courses for learners of different ages and levels.',
                'description' => 'This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.',
                'manager' => 'Academy Manager',
                'address' => 'Sample registered address for the academy main branch in Iran.',
                default => (string)$value,
            };
            if (!$translations->set($table, $id, $field, $english, 'en')) {
                throw new RuntimeException('ثبت ترجمه انگلیسی اطلاعات نمونه ناموفق بود.');
            }
        }
        $this->auditTranslations($table, $id, $creatorId);
    }

    private function auditTranslations(string $table, int $id, int $creatorId): void {
        DB::table('translations')->where('table_name', $table)->where('table_id', $id)->update([
            'created_by' => $creatorId, 'updated_by' => $creatorId,
        ]);
    }

    private function sampleAcademies(): array {
        $names = ['آوای باران', 'نوای مهر', 'چکاد هنر', 'نغمه‌سرای پارس', 'خانه موسیقی سپیدار', 'آوای هیرکان', 'مهرآهنگ', 'ساز و سخن', 'نوای ارغوان', 'ترنم شرق'];
        $cities = ['تهران', 'شیراز', 'اصفهان', 'تبریز', 'مشهد', 'رشت', 'کرمان', 'اهواز', 'همدان', 'ساری'];
        $specialties = ['موسیقی ایرانی و ردیف دستگاهی', 'پیانو و موسیقی کلاسیک', 'آموزش تخصصی سازهای زهی', 'آواز و صداسازی', 'موسیقی کودک و ارف'];
        $slogans = ['صدای استعدادت را پیدا کن', 'آغاز مسیر حرفه‌ای موسیقی', 'هنر، تمرین و تجربه', 'با موسیقی زندگی کن', 'جایی برای رشد هنرمندان'];
        $variants = ['', 'مرکز', 'نوین', 'پارسی', 'هنر'];
        $firstNames = ['علی', 'مریم', 'رضا', 'سارا', 'امیر', 'نگار', 'حسین', 'الهام', 'مهدی', 'نرگس'];
        $lastNames = ['محمدی', 'احمدی', 'رضایی', 'کریمی', 'حسینی'];
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
                'password' => '123456789',
                'register_method' => 'email',
                'academy_name' => $name,
                'slogan' => $slogans[$i % count($slogans)],
                'short_description' => "آموزشگاه {$name} در {$city}؛ مرکز تخصصی {$specialty} برای هنرجویان کودک، نوجوان و بزرگسال.",
                'biography' => "آموزشگاه {$name} با بهره‌گیری از استادان باتجربه، دوره‌های مقدماتی تا پیشرفته {$specialty} را در فضایی حرفه‌ای برگزار می‌کند. برنامه آموزشی این مجموعه بر تمرین هدفمند، اجرای هنرجویی و رشد خلاقیت موسیقایی استوار است.",
                'status' => 'approved',
                'locale' => 'fa',
                'timezone' => 'Asia/Tehran',
                'manager_name' => $firstNames[$i % count($firstNames)] . ' ' . $lastNames[intdiv($i, count($firstNames))],
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
              AND users.type IN ('academy', 'manager')
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
