<?php
namespace Modules\System\Services;

use Core\database\DB;
use Core\translation\TranslationService;
use Modules\System\Repositories\UserRepository;

class UserService {
    public function __construct(protected UserRepository $repository, protected UserReferralService $referrals) {
    }

    public function register(array $data): int|false {
        $fullName = $data['full_name'] ?? '';
        $ownsTransaction = !db()->inTransaction();

        try {
            return transaction(function () use ($data, $fullName) {
                $userId = $this->repository->store([
                    'username'        => $data['username'],
                    'email'           => $data['email'] ?? null,
                    'phone'           => $data['phone'] ?? null,
                    'password'        => password_hash($data['password'], PASSWORD_DEFAULT),
                    'type'            => $data['type'] ?? 'human',
                    'status'          => $data['status'] ?? 'pending',
                    'locale'          => $data['locale'] ?? 'fa',
                    'timezone'        => $data['timezone'] ?? 'Asia/Tehran',
                    'register_method' => $data['register_method'],
                    'created_by'      => $data['created_by'] ?? null,
                    'updated_by'      => $data['updated_by'] ?? null,
                ]);

                if (empty($data['created_by']) && empty($data['updated_by'])) {
                    $this->repository->updateById($userId, ['created_by' => $userId, 'updated_by' => $userId]);
                }

            if (!$userId) {
                throw new \RuntimeException('User could not be created.');
            }

            $accountCreated = DB::table('financial_system_accounts')->insert([
                'user_id' => $userId,
                'type' => $this->initialAccountType((string)($data['type'] ?? 'human')),
                'balance' => 0,
                'status' => 'active',
            ]);

            if (!$accountCreated) {
                throw new \RuntimeException('Initial financial account could not be created.');
            }

            $translationCreated = TranslationService::manager()->set(
                'users',
                $userId,
                'full_name',
                $fullName,
                $data['locale'] ?? 'fa'
            );

            if (!$translationCreated) {
                throw new \RuntimeException('User translation could not be created.');
            }

            $this->referrals->ensureForUser($userId, $data['invite_code'] ?? null);
            if (empty($data['skip_default_role'])) $this->assignRole($userId, 'user', $userId);

                return $userId;
            });
        } catch (\Throwable $exception) {
            if (!$ownsTransaction) {
                throw $exception;
            }
            return false;
        }
    }

    private function initialAccountType(string $userType): string {
        return match ($userType) {
            'academy' => 'academy_wallet',
            'teacher' => 'teacher_wallet',
            'human' => 'user_wallet',
            'branch' => 'branch_wallet',
            default => 'user_wallet',
        };
    }

    public function assignRole(int $userId, string $roleName, int $actor): int {
        $role = DB::table('access_system_roles')->where('name', $roleName)->whereNull('deleted_at')->first();
        if (!$role) throw new \RuntimeException('نقش ' . $roleName . ' یافت نشد.');
        $existing = DB::table('user_roles')->where('user_id', $userId)->where('role_id', (int)$role['role_id'])->whereNull('deleted_at')->first();
        if ($existing) return (int)$existing['user_role_id'];
        return (int)DB::table('user_roles')->insertGetId(['user_id'=>$userId,'role_id'=>(int)$role['role_id'],'is_main'=>$roleName==='user'?1:0,'created_by'=>$actor,'updated_by'=>$actor,'approved_at'=>date('Y-m-d H:i:s'),'approved_by'=>$actor]);
    }

    public function attempt(string $identifier, string $password): array|false {
        $user = $this->repository->findForLogin($identifier);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'] ?? '')) {
            return false;
        }

        return $user;
    }

    public function publicDirectory(): array {
        $rows = $this->repository->builder()
            ->select('user_id', 'username', 'type', 'gender', 'status', 'visibility', 'email', 'phone', 'birthday', 'register_time', 'register_method', 'last_login_at','avatar_file_id')
            ->where('type', 'human')
            ->where('visibility', 'public')
            ->whereIn('register_method', ['email', 'phone'])
            ->whereNull('deleted_at')
            ->latest('user_id')
            ->get();
        $translations = TranslationService::manager();
        $locale = app()->getLocale();
        $labels = $locale === 'en'
            ? ['teacher'=>'Teacher','student'=>'Student','manager'=>'Manager','parent'=>'Parent','employee'=>'Staff','company'=>'Organization','user'=>'User']
            : ['teacher'=>'مدرس','student'=>'هنرجو','manager'=>'مدیر','parent'=>'والد','employee'=>'همکار','company'=>'مجموعه','user'=>'کاربر'];

        return array_map(function (array $user) use ($translations, $locale, $labels) {
            $id = (int)$user['user_id'];
            $roleNames = array_column(DB::table('user_roles')
                ->join('access_system_roles', 'access_system_roles.role_id', '=', 'user_roles.role_id')
                ->select('access_system_roles.name')
                ->where('user_roles.user_id', $id)
                ->whereNull('user_roles.deleted_at')
                ->whereNull('access_system_roles.deleted_at')
                ->get(), 'name');
            $memberRows = DB::table('academy_branch_members')->where('user_id', $id)->whereNull('deleted_at')->get();
            foreach ($memberRows as $member) {
                foreach (DB::table('academy_branch_member_roles')
                    ->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')
                    ->select('access_system_roles.name')
                    ->where('academy_branch_member_roles.member_id', (int)$member['member_id'])
                    ->whereNull('academy_branch_member_roles.deleted_at')
                    ->whereNull('access_system_roles.deleted_at')
                    ->get() as $memberRole) $roleNames[] = (string)$memberRole['name'];
                foreach (DB::table('academy_branch_member_contracts')
                    ->select('type')
                    ->where('member_id', (int)$member['member_id'])
                    ->whereNull('deleted_at')
                    ->get() as $contract) $roleNames[] = (string)$contract['type'];
            }
            $roleNames = array_values(array_unique(array_filter($roleNames)));
            $directoryRoles = $this->directoryRoles($roleNames, (string)$user['type'], (string)$user['username']);
            $directoryRole = $directoryRoles[0] ?? 'user';
            $roleLabels = $this->roleLabels($roleNames, $translations, $locale);
            $media = DB::table('media_files')->where('user_id', $id)->whereNull('deleted_at')->orderBy('sort_order')->get();
            $byCollection = [];
            foreach ($media as $file) $byCollection[$file['collection']][] = '/' . ltrim((string)$file['path'], '/');
            $instruments = $this->userMusicRows('user_instruments', 'user_instrument_id', 'instrument_id', 'instruments', $id, $translations, $locale);
            $lessons = $this->userMusicRows('user_lessons', 'user_lesson_id', 'lesson_id', 'lessons', $id, $translations, $locale);
            $addresses = $this->translatedRows('user_addresses', 'address_id', $id, ['address', 'note'], $translations, $locale);
            $contacts = $this->translatedRows('user_contacts', 'user_contact_id', $id, ['value', 'note'], $translations, $locale);
            $availabilityRows = $this->translatedRows('user_availabilities', 'user_availability_id', $id, ['summary', 'description'], $translations, $locale);
            $availabilityExceptions = array_values(array_filter($availabilityRows, fn(array $row): bool => !empty($row['unavailable_type'])));
            $availabilities = array_values(array_filter($availabilityRows, fn(array $row): bool => empty($row['unavailable_type'])));
            $firstAddress = $addresses[0]['address'] ?? '';
            $avatarFile=!empty($user['avatar_file_id'])?DB::table('media_files')->where('media_file_id',(int)$user['avatar_file_id'])->whereNull('deleted_at')->first():null;
            $starts=array_values(array_filter(array_merge(array_column($instruments,'start_date'),array_column($lessons,'start_date'))));sort($starts);$startYear=$starts?(int)substr((string)$starts[0],0,4):0;$currentYear=$startYear&&$startYear<1700?(int)date('Y')-621:(int)date('Y');$years=$startYear?max(0,$currentYear-$startYear):null;
            return [
                'id' => $id,
                'name' => $translations->get('users', $id, 'full_name', $locale) ?: $user['username'],
                'role' => $directoryRole,
                'directoryRoles' => $directoryRoles,
                'roles' => $roleNames,
                'roleLabels' => $roleLabels,
                'roleLabel' => $directoryRole !== 'user'
                    ? ($labels[$directoryRole] ?? $labels['user'])
                    : (implode($locale==='en'?', ':'، ', $roleLabels) ?: $labels['user']),
                'bio' => $translations->get('users', $id, 'bio', $locale) ?: '',
                'username' => $user['username'], 'gender' => $user['gender'], 'status' => $user['status'],
                'visibility' => $user['visibility'], 'email' => $user['email'], 'phone' => $user['phone'],
                'birthday' => $user['birthday'], 'register_time' => $user['register_time'],
                'register_method' => $user['register_method'], 'last_login_at' => $user['last_login_at'],
                'avatar' => $avatarFile?'/'.ltrim((string)$avatarFile['path'],'/'):($byCollection['avatar'][0]??$byCollection['logo'][0]??null),
                'cover' => $byCollection['cover'][0] ?? null,
                'gallery' => $byCollection['teacher_gallery'] ?? [],
                'intro_video' => $byCollection['intro_video'][0] ?? null,
                'instrument_list' => $instruments, 'instruments' => array_column($instruments, 'title'),
                'instruments_count' => count($instruments), 'lessons' => $lessons,
                'experiences' => array_merge($instruments, $lessons),
                'addresses' => $addresses, 'contacts' => $contacts,
                'availabilities' => $availabilities, 'availability_exceptions' => $availabilityExceptions,
                'city' => $firstAddress ? (explode('،', $firstAddress)[0] ?? '') : '',
                'years_of_experience'=>$years,
                'headline' => count($instruments) ? 'فعال در زمینه ' . implode('، ', array_slice(array_column($instruments, 'title'), 0, 3)) : 'مدیر آموزشگاه موسیقی',
            ];
        }, $rows);
    }

    public function publicDirectoryCategories(): array {
        $locale = app()->getLocale();
        $translations = TranslationService::manager();
        $rows = DB::table('categories')->where('`group`', 'users')->whereNull('deleted_at')->orderBy('category_id')->get();
        return array_map(function (array $category) use ($translations, $locale) {
            $id = (int)$category['category_id'];
            return [
                'id' => $id,
                'role' => (string)$category['slug'],
                'title' => $translations->get('categories', $id, 'title', $locale) ?: (string)$category['name'],
            ];
        }, $rows);
    }

    public function publicDirectoryUiLabels():array {
        $locale=app()->getLocale()==='en'?'en':'fa';$row=DB::table('settings')->join('translations','translations.table_id','=','settings.setting_id')->select('translations.value')->where('settings.variable_name','public_view_action')->where('translations.table_name','settings')->where('translations.field','value')->where('translations.locale',$locale)->whereNull('settings.deleted_at')->whereNull('translations.deleted_at')->first();
        return['view'=>(string)($row['value']??($locale==='en'?'View':'مشاهده'))];
    }

    private function directoryRoles(array $roles, string $type, string $username): array {
        $result = [];
        if (array_filter($roles, fn(string $role) => str_contains($role, 'teacher'))) $result[] = 'teacher';
        if (array_filter($roles, fn(string $role) => str_contains($role, 'student'))) $result[] = 'student';
        if (array_filter($roles, fn(string $role) => str_contains($role, 'manager') || str_contains($role, 'owner'))) $result[] = 'manager';
        if (str_starts_with($username, 'test_academy_manager_')) $result[] = 'manager';
        if (in_array($type, ['teacher', 'student', 'manager'], true)) $result[] = $type;
        return array_values(array_unique($result ?: ['user']));
    }

    private function roleLabels(array $roles, $translations, string $locale): array {
        $fallbacks = $locale==='en'
            ? ['user'=>'User','teacher'=>'Teacher','student'=>'Student','manager'=>'Manager','owner'=>'Manager','receptionist'=>'Receptionist','admin'=>'Site administrator','superadmin'=>'Super administrator']
            : ['user'=>'کاربر','teacher'=>'مدرس','student'=>'هنرجو','manager'=>'مدیر','owner'=>'مدیر','receptionist'=>'پذیرش','admin'=>'مدیر سایت','superadmin'=>'مدیر کل'];
        $labels = [];
        foreach ($roles as $name) {
            $role = DB::table('access_system_roles')->where('name', $name)->whereNull('deleted_at')->first();
            $roleTranslation=$role?DB::table('f_translations')->where('table_name','access_system_roles')->where('table_id',(int)$role['role_id'])->where('field','title')->where('locale',$locale)->whereNull('deleted_at')->first():null;
            $label = (string)($roleTranslation['value']??'');
            if (!$label) foreach ($fallbacks as $needle => $fallback) {
                if ($name === $needle || str_contains($name, $needle)) { $label = $fallback; break; }
            }
            $label = preg_replace('/[\p{Cf}\p{Z}]+/u', ' ', (string)$label);
            $label = trim((string)$label, " \t\n\r\0\x0B،,");
            if ($label !== '') $labels[] = $label;
        }
        return array_values(array_unique($labels ?: [$locale==='en'?'User':'کاربر']));
    }

    private function userMusicRows(string $pivot, string $pivotKey, string $foreignKey, string $catalog, int $userId, $translations, string $locale): array {
        $rows = DB::table($pivot)->where('user_id', $userId)->whereNull('deleted_at')->orderBy('is_primary', 'DESC')->get();
        return array_map(function (array $row) use ($pivot, $pivotKey, $foreignKey, $catalog, $translations, $locale) {
            $id=(int)$row[$pivotKey]; $catalogId=(int)$row[$foreignKey]; $levelId=(int)$row['level_id'];
            return ['id'=>$id, 'title'=>$translations->get($catalog, $catalogId, 'title', $locale) ?: '—',
                'level'=>$translations->get('levels', $levelId, 'title', $locale) ?: '—', 'start_date'=>$row['start_date'],
                'is_primary'=>(bool)$row['is_primary'], 'summary'=>$translations->get($pivot, $id, 'summary', $locale) ?: '',
                'description'=>$translations->get($pivot, $id, 'description', $locale) ?: ''];
        }, $rows);
    }

    private function translatedRows(string $table, string $key, int $userId, array $fields, $translations, string $locale): array {
        $rows=DB::table($table)->where('user_id',$userId)->whereNull('deleted_at')->get();
        return array_map(function(array $row) use($table,$key,$fields,$translations,$locale){
            $id=(int)$row[$key]; $result=$row;
            foreach($fields as $field) $result[$field]=$translations->get($table,$id,$field,$locale) ?: '';
            unset($result['created_by'],$result['updated_by'],$result['deleted_by']); return $result;
        },$rows);
    }
}
