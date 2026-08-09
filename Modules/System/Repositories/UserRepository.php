<?php
namespace Modules\System\Repositories;

use Core\database\Repository;
use Modules\System\Contracts\UserRepositoryInterface;

class UserRepository extends Repository implements UserRepositoryInterface {
    protected string $table = 'users';
    protected string $primaryKey = 'user_id';

    public function store(array $data): int|false {
        return $this->query()->insertGetId($data);
    }

    public function updateById(int $userId, array $data): bool {
        return $this->query()->where('user_id', $userId)->update($data);
    }

    public function softDelete(int $userId, ?int $deletedBy): bool {
        return $this->query()->where('user_id', $userId)->update([
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $deletedBy,
        ]);
    }

    public function find(int $userId): ?array {
        return $this->query()->where('user_id', $userId)->whereNull('deleted_at')->first();
    }
    
    public function findForLogin(string $identifier): ?array {
        return $this->query()
            ->whereRaw('(username = ? OR email = ? OR phone = ?)', [$identifier, $identifier, $identifier])
            ->whereNull('deleted_at')
            ->first();
    }

    public function findByContact(string $method, string $value): ?array {
        $column = $method === 'phone' ? 'phone' : 'email';
        return $this->query()->where($column, $value)->whereNull('deleted_at')->first();
    }

    public function updatePassword(int $userId, string $passwordHash): bool {
        return $this->query()->where('user_id', $userId)->update(['password' => $passwordHash]);
    }

}
