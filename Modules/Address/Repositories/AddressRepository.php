<?php

namespace Modules\Address\Repositories;

use Core\Database\Repository;

class AddressRepository extends Repository
{
    protected string $table = 'user_addresses';

    protected string $primaryKey = 'address_id';

    protected ?string $model = null;



    public function findByUserId(int $userId): mixed
    {
        return $this->query()
            ->where(
                'user_id',
                $userId
            )
            ->first();
    }



    public function updateOrCreate(
        int $userId,
        array $data
    ): bool {

        $address = $this->findByUserId($userId);

        if ($address) {

            return $this->query()
                ->where(
                    'user_id',
                    $userId
                )
                ->update($data);

        }

        $data['user_id'] = $userId;

        return $this->create($data);
    }



    public function deleteByUserId(
        int $userId
    ): bool {

        return $this->query()
            ->where(
                'user_id',
                $userId
            )
            ->delete();

    }

}