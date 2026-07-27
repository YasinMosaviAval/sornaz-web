<?php

namespace Modules\Address\Services;

use Core\Translation\TranslationService;
use Modules\Address\Repositories\AddressRepository;

class AddressService {

    protected AddressRepository $repository;



    public function __construct(AddressRepository $repository) {
        $this->repository = $repository;
    }



    public function findByUserId(int $userId): ?array {
        $address=$this->repository->findByUserId($userId);
        if(!$address){
            return null;
        }
        $address['address']=
            TranslationService::manager()->get(
                'user_addresses',
                $address['address_id'],
                'address'
            );
        return $address;
    }



    public function save(int $userId, array $data): bool {
        $addressText = $data['address'] ?? '';
        unset($data['address']);
        $address = $this->repository->findByUserId($userId);
        if ($address) {
            $this->repository->updateForUser($userId, $data);
        } else {
            $this->repository->createForUser($userId, $data);
        }
        $address = $this->repository->findByUserId($userId);
        if (!$address) {
            return false;
        }
        TranslationService::manager()->set(
            'user_addresses',
            $address['address_id'],
            'address',
            $addressText
        );
        return true;
    }



    public function delete(int $userId): bool {
        return $this->repository->deleteForUser($userId);
    }





}