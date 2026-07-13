<?php

namespace Modules\Address\Repositories;

use Core\Database\Repository;

class AddressRepository extends Repository
{
    protected string $table = 'user_addresses';
    protected string $primaryKey = 'address_id';
    protected ?string $model = null;

    public function findByUserId(int $userId): ?array
    {
        return $this->query()
            ->where('user_id',$userId)
            ->first();
    }

    public function createForUser(
        int $userId,
        array $data
    ): bool
    {
        $data['user_id']=$userId;

        return $this->create($data);
    }

    public function updateForUser(
        int $userId,
        array $data
    ): bool
    {
        return $this->query()
            ->where('user_id',$userId)
            ->update($data);
    }

    public function deleteForUser(
        int $userId
    ): bool
    {
        return $this->query()
            ->where('user_id',$userId)
            ->delete();
    }
}