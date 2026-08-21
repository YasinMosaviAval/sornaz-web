<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use Core\translation\TranslationService;
use Modules\System\Services\UserService;
use Modules\System\Services\UserNotificationService;
use Modules\System\Services\UserReferralService;
use RuntimeException;

class AcademyRegistrationService {
    private const MANAGER_PREFIX = 'test_academy_manager_';
    private const ACADEMY_PREFIX = 'test_academy_';
    private const BRANCH_PREFIX = 'test_main_branch_';
    private const EXTRA_BRANCH_PREFIX = 'test_extra_branch_';
    private const MEMBER_PREFIX = 'test_branch_member_';

    public function __construct(protected UserService $users, protected UserNotificationService $notifications, protected UserReferralService $referrals) {}

    public function register(array $data): int {
        session()->put('suppress_database_notifications', true);
        try { return transaction(function () use ($data) {
            $academyId = $this->createAcademy($data);
            $managerId = (int)DB::table('academies')->where('academy_id', $academyId)->first()['created_by'];
            $this->notifications->send(1, 'درخواست ثبت آموزشگاه جدید', 'کاربر با آی‌دی ' . $managerId . ' یک درخواست ثبت آموزشگاه با آی‌دی ' . $academyId . ' ارسال کرد', 'academies', $academyId, $managerId);
            $this->notifications->send(1, 'ایجاد نقش موسس آموزشگاه', 'برای کاربر با آی‌دی ' . $managerId . ' نقش موسس آموزشگاه ایجاد شد.', 'user_roles', $managerId, $managerId);
            $this->notifications->send($managerId, 'ثبت درخواست آموزشگاه', 'درخواست ثبت آموزشگاه شما با موفقیت ارسال شد', 'academies', $academyId, $managerId);
            $this->notifications->send($managerId, 'درخواست ثبت آموزشگاه', 'درخواست ثبت آموزشگاه توسط کاربر با نام کاربری ' . (string)(DB::table('users')->where('user_id', $managerId)->first()['username'] ?? '') . ' ارسال شد', 'academies', $academyId, $managerId);
            return $academyId;
        }); } finally { session()->forget('suppress_database_notifications'); }
    }

    public function registerMainBranch(int $academyId, int $managerId, array $data): int {
        return transaction(function () use ($academyId, $managerId, $data) {
            $academy = DB::table('academies')->where('academy_id', $academyId)->whereNull('deleted_at')->first();
            if (!$academy) throw new RuntimeException('آموزشگاه یافت نشد.');
            $now = date('Y-m-d H:i:s');
            $userId = DB::table('users')->insertGetId(['username'=>$data['username'],'email'=>$data['email'],'phone'=>$data['phone'],'password'=>password_hash($data['password'], PASSWORD_DEFAULT),'type'=>'branch','status'=>'approved','locale'=>'fa','timezone'=>'Asia/Tehran','register_method'=>$data['register_method'],'visibility'=>'unlisted','created_by'=>$managerId,'updated_by'=>$managerId,'approved_at'=>$now,'approved_by'=>$managerId]);
            DB::table('financial_system_accounts')->insert(['account_id'=>$userId,'user_id'=>$userId,'type'=>'branch_wallet','balance'=>0,'status'=>'active']);
            $branchId = DB::table('academy_branches')->insertGetId(['academy_id'=>$academyId,'user_id'=>$userId,'is_main'=>1,'timezone'=>'Asia/Tehran','created_by'=>$managerId,'updated_by'=>$managerId]);
            if (!$userId || !$branchId) throw new RuntimeException('ایجاد شعبه اصلی ناموفق بود.');
            $this->referrals->ensureForUser($userId);
            $this->users->assignRole($userId, 'academy_branch_owner', $managerId);
            $this->users->assignRole($managerId, 'academy_branch_manager', $managerId);
            $this->notifications->send(1, 'ثبت شعبه جدید', "کاربر با آی‌دی {$managerId} شعبه اصلی آموزشگاه با آی‌دی {$academyId} را ثبت کرد.", 'academy_branches', $branchId, $managerId);
            $this->notifications->send((int)$academy['user_id'], 'ثبت شعبه آموزشگاه', "شعبه اصلی آموزشگاه با آی‌دی {$academyId} با موفقیت ثبت شد.", 'academy_branches', $branchId, $managerId);
            $this->notifications->send($managerId, 'ثبت درخواست شعبه', 'درخواست ثبت شعبه اصلی با موفقیت ارسال شد.', 'academy_branches', $branchId, $managerId);
            $this->notifications->send($userId, 'ایجاد حساب شعبه', 'حساب کاربری شعبه اصلی با موفقیت ایجاد و تأیید شد.', 'users', $userId, $managerId);
            return $branchId;
        });
    }

    public function seedSamples(int $limit=10): array {
        return transaction(function () use ($limit) {
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
            $limit=max(1,min(50,$limit));
            foreach (array_slice($managers,0,$limit) as $index => $manager) {
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
                'message' => "مرحله آموزشگاه‌های تست ۱ تکمیل شد: {$created} آموزشگاه ایجاد و {$updated} آموزشگاه همگام‌سازی شد؛ برای هر مدیر دقیقاً یک آموزشگاه و شعبه اصلی ثبت شد.",
            ];
        });
    }

    public function seedBranchNetwork(array $options=[]): array {
        return transaction(function () use ($options) {
            $branchTypes = DB::table('academy_branch_types')->whereNull('deleted_at')->get();
            $provinces = DB::table('world_iran_provinces')->get();
            $counties = DB::table('world_iran_counties')->get();
            $academies = DB::table('academies')->whereNull('deleted_at')->get();
            $academies = array_values(array_filter($academies, function (array $academy) {
                $user = DB::table('users')->where('user_id', (int)$academy['user_id'])->first();
                return $user && str_starts_with((string)$user['username'], self::ACADEMY_PREFIX);
            }));
            if (!$academies) throw new RuntimeException('ابتدا تست ۲ (آموزشگاه‌ها و شعب اصلی) را اجرا کنید.');

            $branchCount = 0; $staffCount = 0; $studentCount = 0; $contractCount = 0;
            foreach ($academies as $academyIndex => $academy) {
                $managerId = (int)$academy['created_by'];
                $sample = $this->sampleAcademies()[$academyIndex % 50];
                $extraCount = $this->fixtureCount($academyIndex,$options['branches_min']??0,$options['branches_max']??5);
                for ($branchIndex = 1; $branchIndex <= $extraCount; $branchIndex++) {
                    $branchId = $this->createSampleBranch((int)$academy['academy_id'], $managerId, $sample, $academyIndex, $branchIndex, $branchTypes, $provinces, $counties);
                    $branchCount++;
                    $counts = $this->seedBranchPeople($branchId, $managerId, $academyIndex, $branchIndex,$options);
                    $staffCount += $counts['staff']; $studentCount += $counts['students']; $contractCount += $counts['contracts'];
                }
            }
            $classroomResult = app()->container()->make(\Modules\Academy\Services\AcademyClassroomService::class)->seedFixtures();
            $branchCatalogResult = $this->seedBranchCatalogAndSchedules();
            return ['branches' => $branchCount, 'staff' => $staffCount, 'students' => $studentCount, 'contracts' => $contractCount,
                'classrooms'=>$classroomResult['classrooms'],'branch_catalog'=>$branchCatalogResult,'message' => "تست ۳ تکمیل شد: {$branchCount} شعبه فرعی، {$staffCount} عضو پرسنل، {$studentCount} هنرجو، {$contractCount} قرارداد و {$classroomResult['classrooms']} کلاس همگام‌سازی شد."];
        });
    }

    private function seedBranchCatalogAndSchedules(): array {
        $branches=DB::table('academy_branches')->whereNull('deleted_at')->get();$instruments=DB::table('instruments')->whereNull('deleted_at')->get();$lessons=DB::table('lessons')->whereNull('deleted_at')->get();$levels=DB::table('levels')->whereNull('deleted_at')->get();
        if(!$instruments||!$lessons||!$levels)return ['instruments'=>0,'lessons'=>0,'schedules'=>0];$totals=['instruments'=>0,'lessons'=>0,'schedules'=>0];
        foreach($branches as $bi=>$branch){$uid=(int)$branch['user_id'];$actor=(int)($branch['created_by']?:1);foreach([['user_instruments','user_instrument_id','instrument_id',$instruments,'instrument_id','instruments'],['user_lessons','user_lesson_id','lesson_id',$lessons,'lesson_id','lessons']] as [$table,$pk,$fk,$catalog,$catalogPk,$counter]){$count=min(count($catalog),10+($bi%41));for($i=0;$i<$count;$i++){$item=$catalog[($bi+$i)%count($catalog)];$level=$levels[($bi+$i)%count($levels)];$query=DB::table($table)->where('user_id',$uid)->where($fk,(int)$item[$catalogPk]);$row=$query->first();$values=[$fk=>(int)$item[$catalogPk],'level_id'=>(int)$level['level_id'],'start_date'=>sprintf('%04d-%02d-%02d',1390+(($bi+$i)%15),(($i+2)%12)+1,(($i+7)%27)+1),'is_primary'=>$i===0?1:0,'updated_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];if($row){$id=(int)$row[$pk];DB::table($table)->where($pk,$id)->update($values);}else$id=DB::table($table)->insertGetId(['user_id'=>$uid,'created_by'=>$actor]+$values);$kind=$table==='user_instruments'?'ساز':'درس';$this->setTranslations($table,$id,['summary'=>"{$kind} ارائه‌شده در این شعبه",'description'=>"این {$kind} با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود."],$actor);$totals[$counter]++;}}
            $days=['saturday','sunday','monday','tuesday','wednesday','thursday','friday'];foreach($days as $di=>$day){$closed=$day==='friday';$start=$closed?'00:00:00':($day==='thursday'?'09:00:00':'08:30:00');$end=$closed?'23:59:00':($day==='thursday'?'17:00:00':'21:30:00');$row=DB::table('user_availabilities')->where('user_id',$uid)->where('day_of_week',$day)->whereNull('date')->first();$values=['date'=>null,'day_of_week'=>$day,'start_time'=>$start,'end_time'=>$end,'timezone'=>'Asia/Tehran','type'=>$closed?'unavailable':'available','is_repeating'=>1,'repeat_period'=>'week','is_closed'=>$closed?1:0,'priority'=>$di+1,'updated_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];if($row){$id=(int)$row['user_availability_id'];DB::table('user_availabilities')->where('user_availability_id',$id)->update($values);}else$id=DB::table('user_availabilities')->insertGetId(['user_id'=>$uid,'created_by'=>$actor]+$values);$this->setTranslations('user_availabilities',$id,['summary'=>$closed?'تعطیلی هفتگی شعبه':'ساعات کاری هفتگی شعبه','description'=>$closed?'شعبه در روز جمعه تعطیل است.':"شعبه در روزهای کاری از {$start} تا {$end} به‌صورت پیوسته فعال است."],$actor);$totals['schedules']++;}
        }return $totals;
    }

    public function deleteBranchNetwork(): array {
        return transaction(function () {
            $branchUsers = DB::table('users')->whereRaw("username LIKE '" . self::EXTRA_BRANCH_PREFIX . "%' ")->get();
            $memberUsers = DB::table('users')->whereRaw("username LIKE '" . self::MEMBER_PREFIX . "%' ")->get();
            $branchUserIds = array_map(fn(array $row) => (int)$row['user_id'], $branchUsers);
            $memberUserIds = array_map(fn(array $row) => (int)$row['user_id'], $memberUsers);
            $branches = $branchUserIds ? DB::table('academy_branches')->whereIn('user_id', $branchUserIds)->get() : [];
            $branchIds = array_map(fn(array $row) => (int)$row['branch_id'], $branches);
            if (!$branchIds && !$memberUserIds) return ['deleted' => 0, 'message' => 'هیچ داده‌ای از تست ۳ برای حذف وجود ندارد.'];

            $members = $branchIds ? DB::table('academy_branch_members')->whereIn('branch_id', $branchIds)->get() : [];
            $memberIds = array_map(fn(array $row) => (int)$row['member_id'], $members);
            $rooms = $branchIds ? DB::table('academy_branch_classrooms')->whereIn('branch_id', $branchIds)->get() : [];
            $roomIds = array_map(fn(array $row) => (int)$row['classroom_id'], $rooms);
            if ($roomIds) {
                $assets = DB::table('academy_branch_classroom_assets')->whereIn('classroom_id', $roomIds)->get();
                $assetIds = array_map(fn(array $row) => (int)$row['classroom_asset_id'], $assets);
                $this->deleteTranslations('academy_branch_classroom_assets', $assetIds);
                $this->deleteTranslations('academy_branch_classrooms', $roomIds);
                DB::table('academy_branch_classroom_assets')->whereIn('classroom_id', $roomIds)->delete();
                DB::table('academy_branch_classrooms')->whereIn('classroom_id', $roomIds)->delete();
            }
            if ($memberIds) {
                DB::table('academy_branch_member_permissions')->whereIn('member_id', $memberIds)->delete();
                DB::table('academy_branch_member_roles')->whereIn('member_id', $memberIds)->delete();
                DB::table('academy_branch_member_contracts')->whereIn('member_id', $memberIds)->delete();
                DB::table('academy_branch_members')->whereIn('member_id', $memberIds)->delete();
            }

            $allUserIds = array_values(array_unique(array_merge($branchUserIds, $memberUserIds)));
            $contacts = $allUserIds ? DB::table('user_contacts')->whereIn('user_id', $allUserIds)->get() : [];
            $addresses = $allUserIds ? DB::table('user_addresses')->whereIn('user_id', $allUserIds)->get() : [];
            $this->deleteTranslations('user_contacts', array_map(fn(array $r)=>(int)$r['user_contact_id'], $contacts));
            $this->deleteTranslations('user_addresses', array_map(fn(array $r)=>(int)$r['address_id'], $addresses));
            $this->deleteTranslations('academy_branches', $branchIds);
            $this->deleteTranslations('users', $allUserIds);
            if ($allUserIds) {
                DB::table('user_contacts')->whereIn('user_id', $allUserIds)->delete();
                DB::table('user_addresses')->whereIn('user_id', $allUserIds)->delete();
                DB::table('z_user_profiles')->whereIn('user_id', $allUserIds)->delete();
                DB::table('financial_system_accounts')->whereIn('user_id', $allUserIds)->delete();
                DB::table('user_roles')->whereIn('user_id', $allUserIds)->delete();
                DB::table('user_sessions')->whereIn('user_id', $allUserIds)->delete();
            }
            if ($branchIds) DB::table('academy_branches')->whereIn('branch_id', $branchIds)->delete();
            if ($allUserIds) DB::table('users')->whereIn('user_id', $allUserIds)->delete();
            return ['deleted' => count($branchIds) + count($memberUserIds),
                'message' => count($branchIds) . ' شعبه فرعی و ' . count($memberUserIds) . ' کاربر تست ۳ به‌همراه عضویت‌ها و قراردادها کاملاً حذف شدند.'];
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

    private function fixtureCount(int $seed,int $minimum,int $maximum): int {$minimum=max(0,min(100,$minimum));$maximum=max(0,min(100,$maximum));if($minimum>$maximum)[$minimum,$maximum]=[$maximum,$minimum];return $minimum+($seed%($maximum-$minimum+1));}

    private function createAcademy(array $data): int {
        $data['type'] = 'academy';
        $data['skip_default_role'] = true;
        $data['full_name'] = $data['academy_name'];
        $requesterId = 0;
        try { $requesterId = (int)(auth()->id() ?? 0); } catch (\Throwable) {}
        $userId = $this->users->register($data);
        if (!$userId) throw new RuntimeException('ایجاد حساب آموزشگاه ناموفق بود.');
        $requesterId = $requesterId ?: (int)$userId;
        $now = date('Y-m-d H:i:s');
        DB::table('users')->where('user_id', $userId)->update(['type'=>'academy', 'status'=>'approved', 'approved_at'=>$now, 'approved_by'=>$requesterId, 'created_by'=>$requesterId, 'updated_by'=>$requesterId]);
        $this->users->assignRole($userId, 'academy_owner', $requesterId);
        $this->users->assignRole($requesterId, 'academy_manager', $requesterId);

        $academyId = DB::table('academies')->insertGetId(['user_id' => $userId, 'created_by' => $requesterId, 'updated_by' => $requesterId]);
        if (!$academyId) throw new RuntimeException('ایجاد آموزشگاه ناموفق بود.');

        $profile = DB::table('z_user_profiles')->where('user_id', $userId)->whereNull('deleted_at')->first();
        $profileId = $profile['user_profile_id'] ?? DB::table('z_user_profiles')->insertGetId(['user_id' => $userId]);
        $account = DB::table('financial_system_accounts')->where('user_id', $userId)->where('type', 'academy_wallet')->whereNull('deleted_at')->first();
        $accountId = $account['account_id'] ?? DB::table('financial_system_accounts')->insertGetId([
            'user_id' => $userId, 'type' => 'academy_wallet', 'balance' => 0, 'status' => 'active',
        ]);
        if (!$profileId || !$accountId) {
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
        if (!DB::table('financial_system_accounts')->where('user_id', $academyUserId)->where('type', 'academy_wallet')->whereNull('deleted_at')->first()) {
            DB::table('financial_system_accounts')->insertGetId([
                'user_id' => $academyUserId, 'type' => 'academy_wallet', 'balance' => 0, 'status' => 'active',
                'created_by' => $managerId, 'updated_by' => $managerId,
            ]);
        }

        $this->createSampleBranch($academyId, $managerId, $sample, $index, 0, $branchTypes, $provinces, $counties);
        return !$existingUser;
    }

    private function createSampleBranch(int $academyId, int $managerId, array $sample, int $academyIndex, int $branchIndex, array $branchTypes, array $provinces, array $counties): int {
        $serial = $academyIndex * 10 + $branchIndex + 1;
        $branchLabels = ['مرکزی', 'شمال', 'شرق', 'غرب', 'آنلاین', 'جنوب'];
        $branchName = $sample['academy_name'] . ' - شعبه ' . $branchLabels[$branchIndex];
        $branchUserId = $this->seedUser([
            'username' => $branchIndex === 0 ? sprintf(self::BRANCH_PREFIX . '%02d', $academyIndex + 1) : sprintf(self::EXTRA_BRANCH_PREFIX . '%02d_%02d', $academyIndex + 1, $branchIndex),
            'email' => $branchIndex === 0 ? sprintf('branch%03d@sornaz.test', $academyIndex * 5 + 1) : sprintf('extra.branch.%02d.%02d@sornaz.test', $academyIndex + 1, $branchIndex),
            'phone' => $branchIndex === 0 ? sprintf('0935%07d', 3000000 + $academyIndex * 5 + 1) : sprintf('0936%07d', 3000000 + $academyIndex * 10 + $branchIndex),
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
        return $branchId;
    }

    private function seedBranchPeople(int $branchId, int $managerId, int $academyIndex, int $branchIndex,array $options=[]): array {
        $serial = ($academyIndex + 1) * 10 + $branchIndex;
        $definitions = [
            'teacher' => $this->fixtureCount($serial,$options['teachers_min']??1,$options['teachers_max']??5),
            'receptionist' => $this->fixtureCount($serial,$options['receptionists_min']??1,$options['receptionists_max']??5),
            'other' => $this->fixtureCount($serial,$options['employees_min']??0,$options['employees_max']??3),
            'manager' => $this->fixtureCount($serial,$options['managers_min']??0,$options['managers_max']??3),
        ];
        $firstNames = ['آرمان','سارا','پرهام','نگار','کیان','مهسا','امیر','نرگس','رضا','مریم'];
        $lastNames = ['احمدی','محمدی','کریمی','رضایی','موسوی','نوری','حسینی'];
        $staff = 0; $students = 0; $contracts = 0; $teacherNumber = 0;
        foreach ($definitions as $role => $count) {
            $roleOffset = (int)array_search($role, array_keys($definitions), true);
            for ($i = 1; $i <= $count; $i++) {
                $key = sprintf('%02d_%02d_%s_%02d', $academyIndex + 1, $branchIndex, $role, $i);
                $name = $firstNames[($serial + $i) % count($firstNames)] . ' ' . $lastNames[($serial + $i * 2) % count($lastNames)];
                $userId = $this->seedUser(['username' => self::MEMBER_PREFIX . $key, 'email' => str_replace('_', '.', $key) . '@sornaz.test',
                    'phone' => sprintf('099%08d', (($serial * 1000 + $i * 10 + array_search($role, array_keys($definitions), true)) % 100000000)),
                    'type' => 'human', 'gender' => ($serial + $i) % 2 ? 'female' : 'male', 'national_code' => sprintf('%010d', 8000000000 + $serial * 1000 + $roleOffset * 100 + $i),
                    'birthday' => sprintf('%04d-%02d-%02d', 1360 + (($serial + $i) % 25), (($i + $branchIndex) % 12) + 1, (($serial + $i) % 27) + 1),
                    'full_name' => $name, 'visibility' => 'public'], $managerId);
                $this->ensureMemberContract($branchId, $userId, $managerId, $role, $serial + $i);
                $staff++; $contracts++;
                if ($role === 'teacher') {
                    $teacherNumber++;
                    $studentTotal = $this->fixtureCount($serial+$teacherNumber,$options['students_min']??0,$options['students_max']??5);
                    for ($s = 1; $s <= $studentTotal; $s++) {
                        $studentKey = sprintf('%02d_%02d_t%02d_s%03d', $academyIndex + 1, $branchIndex, $teacherNumber, $s);
                        $studentId = $this->seedUser(['username' => self::MEMBER_PREFIX . 'student_' . $studentKey,
                            'email' => 'student.' . str_replace('_', '.', $studentKey) . '@sornaz.test', 'phone' => sprintf('098%08d', (($serial * 10000 + $teacherNumber * 100 + $s) % 100000000)),
                            'type' => 'human', 'gender' => ($s + $serial) % 2 ? 'female' : 'male', 'national_code' => sprintf('%010d', 7000000000 + $serial * 1000 + $teacherNumber * 100 + $s),
                            'birthday' => sprintf('%04d-%02d-%02d', 1375 + (($serial + $s) % 22), (($s + 2) % 12) + 1, (($s + 8) % 27) + 1),
                            'full_name' => $firstNames[($serial + $s + 3) % count($firstNames)] . ' ' . $lastNames[($serial + $s) % count($lastNames)], 'visibility' => 'private'], $managerId);
                        $this->ensureMemberContract($branchId, $studentId, $managerId, 'student', $serial + $s);
                        $students++; $contracts++;
                    }
                }
            }
        }
        return compact('staff', 'students', 'contracts');
    }

    private function ensureMemberContract(int $branchId, int $userId, int $managerId, string $role, int $serial): void {
        $member = DB::table('academy_branch_members')->where('branch_id', $branchId)->where('user_id', $userId)->first();
        $values = ['branch_id' => $branchId, 'user_id' => $userId, 'status' => 'active', 'joined_at' => date('Y-m-d'),
            'created_by' => $managerId, 'updated_by' => $managerId, 'approved_at' => date('Y-m-d H:i:s'), 'approved_by' => $managerId,
            'deleted_at' => null, 'deleted_by' => null];
        if ($member) { $memberId = (int)$member['member_id']; DB::table('academy_branch_members')->where('member_id', $memberId)->update($values); }
        else $memberId = DB::table('academy_branch_members')->insertGetId($values);
        $type = in_array($role, ['teacher','receptionist','manager'], true) ? $role : 'other';
        $contract = DB::table('academy_branch_member_contracts')->where('member_id', $memberId)->whereNull('deleted_at')->first();
        $contractValues = ['member_id' => $memberId, 'type' => $type, 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+1 year')),
            'price' => $role === 'student' ? 0 : 5000000 + ($serial % 20) * 500000, 'currency_id' => 1,
            'created_by' => $managerId, 'updated_by' => $managerId, 'approved_at' => date('Y-m-d H:i:s'), 'approved_by' => $managerId,
            'deleted_at' => null, 'deleted_by' => null];
        if ($contract) DB::table('academy_branch_member_contracts')->where('member_contract_id', (int)$contract['member_contract_id'])->update($contractValues);
        else DB::table('academy_branch_member_contracts')->insertGetId($contractValues);
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
