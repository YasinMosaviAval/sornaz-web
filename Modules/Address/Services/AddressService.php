<?php

namespace Modules\Address\Services;

use Modules\Address\Repositories\AddressRepository;

class AddressService
{

    protected AddressRepository $repository;



    public function __construct(
        AddressRepository $repository
    )
    {
        $this->repository = $repository;
    }



    public function findByUserId(
        int $userId
    ): mixed
    {
        return $this->repository
            ->findByUserId($userId);
    }



    public function save(
        int $userId,
        array $data
    ): bool
    {
        return $this->repository
            ->updateOrCreate(
                $userId,
                $data
            );
    }



    public function delete(
        int $userId
    ): bool
    {
        return $this->repository
            ->deleteByUserId($userId);
    }

}