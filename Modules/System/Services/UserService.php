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
            'status'          => 'pending',
            'locale'          => $data['locale'] ?? 'fa',
            'timezone'        => $data['timezone'] ?? 'Asia/Tehran',
            'register_method' => 'email',
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
}