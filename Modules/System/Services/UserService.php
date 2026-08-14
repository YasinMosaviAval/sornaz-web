<?php
namespace Modules\System\Services;

use Core\database\DB;
use Core\translation\TranslationService;
use Modules\System\Repositories\UserRepository;

class UserService {
    public function __construct(protected UserRepository $repository) {
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
                    'type'            => $data['type'] ?? 'student',
                    'status'          => $data['status'] ?? 'pending',
                    'locale'          => $data['locale'] ?? 'fa',
                    'timezone'        => $data['timezone'] ?? 'Asia/Tehran',
                    'register_method' => $data['register_method'],
                ]);

            if (!$userId) {
                throw new \RuntimeException('User could not be created.');
            }

            $accountCreated = DB::table('financial_system_accounts')->insert([
                'account_id' => $userId,
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
            'academy' => 'academy_main',
            'teacher' => 'teacher_wallet',
            default => 'student_wallet',
        };
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
            ->select('user_id', 'username', 'type', 'gender', 'status', 'visibility', 'email', 'phone', 'birthday', 'register_time', 'register_method', 'last_login_at')
            ->whereRaw("((status = 'approved' AND visibility = 'public') OR username LIKE 'test_academy_manager_%')")
            ->whereNull('deleted_at')
            ->latest('user_id')
            ->get();
        $translations = TranslationService::manager();
        $locale = app()->getLocale();
        $labels = [
            'teacher' => 'مدرس', 'student' => 'هنرجو', 'manager' => 'مدیر',
            'parent' => 'والد', 'employee' => 'همکار', 'company' => 'مجموعه',
        ];

        return array_map(function (array $user) use ($translations, $locale, $labels) {
            $id = (int)$user['user_id'];
            $media = DB::table('media_files')->where('user_id', $id)->whereNull('deleted_at')->orderBy('sort_order')->get();
            $byCollection = [];
            foreach ($media as $file) $byCollection[$file['collection']][] = '/' . ltrim((string)$file['path'], '/');
            $instruments = $this->userMusicRows('user_instruments', 'user_instrument_id', 'instrument_id', 'instruments', $id, $translations, $locale);
            $lessons = $this->userMusicRows('user_lessons', 'user_lesson_id', 'lesson_id', 'lessons', $id, $translations, $locale);
            $addresses = $this->translatedRows('user_addresses', 'address_id', $id, ['address', 'note'], $translations, $locale);
            $contacts = $this->translatedRows('user_contacts', 'user_contact_id', $id, ['value', 'note'], $translations, $locale);
            $availabilities = $this->translatedRows('user_availabilities', 'user_availability_id', $id, ['summary', 'description'], $translations, $locale);
            $availabilityExceptions = $this->translatedRows('user_availability_exceptions', 'user_availability_exception_id', $id, ['summary', 'description'], $translations, $locale);
            $firstAddress = $addresses[0]['address'] ?? '';
            return [
                'id' => $id,
                'name' => $translations->get('users', $id, 'full_name', $locale) ?: $user['username'],
                'role' => str_starts_with($user['username'], 'test_academy_manager_') ? 'manager' : $user['type'],
                'roleLabel' => str_starts_with($user['username'], 'test_academy_manager_') ? 'مدیر آموزشگاه' : ($labels[$user['type']] ?? 'کاربر'),
                'bio' => $translations->get('users', $id, 'bio', $locale) ?: '',
                'username' => $user['username'], 'gender' => $user['gender'], 'status' => $user['status'],
                'visibility' => $user['visibility'], 'email' => $user['email'], 'phone' => $user['phone'],
                'birthday' => $user['birthday'], 'register_time' => $user['register_time'],
                'register_method' => $user['register_method'], 'last_login_at' => $user['last_login_at'],
                'avatar' => $byCollection['teacher_avatar'][0] ?? null,
                'cover' => $byCollection['cover'][0] ?? null,
                'gallery' => $byCollection['teacher_gallery'] ?? [],
                'intro_video' => $byCollection['intro_video'][0] ?? null,
                'instrument_list' => $instruments, 'instruments' => array_column($instruments, 'title'),
                'instruments_count' => count($instruments), 'lessons' => $lessons,
                'experiences' => array_merge($instruments, $lessons),
                'addresses' => $addresses, 'contacts' => $contacts,
                'availabilities' => $availabilities, 'availability_exceptions' => $availabilityExceptions,
                'city' => $firstAddress ? (explode('،', $firstAddress)[0] ?? '') : '',
                'headline' => count($instruments) ? 'فعال در زمینه ' . implode('، ', array_slice(array_column($instruments, 'title'), 0, 3)) : 'مدیر آموزشگاه موسیقی',
            ];
        }, $rows);
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
