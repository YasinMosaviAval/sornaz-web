<?php
namespace Modules\System\Services;

use Core\translation\TranslationService;
use Modules\System\Repositories\UserRepository;

class UserService {
    public function __construct(protected UserRepository $repository) {
    }

    public function register(array $data): int|false {
        $fullName = $data['full_name'] ?? '';

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
            return false;
        }

        TranslationService::manager()->set(
            'users',
            $userId,
            'full_name',
            $fullName,
            $data['locale'] ?? 'fa'
        );

        return $userId;
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
            ->select('user_id', 'username', 'type')
            ->where('status', 'approved')
            ->where('visibility', 'public')
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
            return [
                'id' => $id,
                'name' => $translations->get('users', $id, 'full_name', $locale) ?: $user['username'],
                'role' => $user['type'],
                'roleLabel' => $labels[$user['type']] ?? 'کاربر',
                'bio' => $translations->get('users', $id, 'bio', $locale) ?: '',
            ];
        }, $rows);
    }
}
