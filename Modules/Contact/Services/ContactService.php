<?php

namespace Modules\Contact\Services;

use Modules\Contact\Repositories\ContactRepository;

class ContactService
{

    protected ContactRepository $repository;

    public function __construct(
        ContactRepository $repository
    ){

        $this->repository=$repository;

    }



    public function findByUserId(
        int $userId
    ):mixed{

        return $this->repository
            ->findByUserId($userId);

    }



    public function save(
        int $userId,
        array $data
    ):bool{

        return $this->repository
            ->updateOrCreate(
                $userId,
                $data
            );

    }

}