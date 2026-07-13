<?php

namespace Modules\Academy\Services;

use Modules\Academy\Models\AcademyModel;
use Modules\Academy\Repositories\AcademyRepository;
use Modules\System\Repositories\UserRepository;
use Modules\Academy\Requests\AcademyIndexRequest;
use Modules\System\Models\UserModel;
use Modules\Address\Services\AddressService;
use Modules\Address\Repositories\AddressRepository;
use Modules\Contact\Services\ContactService;
use Modules\World\Services\ProvinceService;

class AcademyService {

    protected AcademyRepository $academyRepository;
    protected UserRepository $userRepository;
    protected AddressService $addressService;
    protected ContactService $contactService;
    protected ProvinceService $provinceService;



    public function __construct(protected AcademyRepository $repository) {
        $this->academyRepository = $repository;
        $this->userRepository = app()->container()->make(UserRepository::class);
        $this->addressService = app()->container()->make(AddressService::class);
        $this->contactService = app()->container()->make(ContactService::class);
        $this->provinceService = app()->container()->make(ProvinceService::class);
    }



    public function list(): array{return $this->repository->getActive();}



    public function all(): array{return $this->repository->getAll();}



    public function active(): array {return $this->repository->getActive();}



    public function find(int $id) {return $this->repository->find($id);}



    public function create(array $data): mixed {
        /*
        |--------------------------------------------------------------------------
        | ایجاد User
        |--------------------------------------------------------------------------
        */
        $this->userRepository->create([
            'username' => $data['username'],
            'email'    => $data['email'] ?? null,
            'phone'    => $data['phone'] ?? null,
            'type'     => 'academy',
            'status'   => $data['status'],
            'locale'   => $data['locale'],
            'timezone' => $data['timezone'],
        ]);
        $user = UserModel::query()->where('username', $data['username'])->first();
        if(!$user){
            return false;
        }
        /*
        |--------------------------------------------------------------------------
        | ایجاد Academy
        |--------------------------------------------------------------------------
        */
        return $this->academyRepository->create(['user_id' => $user->user_id,]);
    }



    public function update(int $academyId, array $data): bool {
        $academy = $this->repository->findById($academyId);
        if (!$academy) {
            return false;
        }
        $userUpdated = $this->userRepository->update(
            $academy['user_id'],
            [
                'username' => $data['username'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'status'   => $data['status'],
                'locale'   => $data['locale'],
                'timezone' => $data['timezone'],
            ]
        );
        $this->addressService->save(
            $academy['user_id'],
            [
                'country_id'=>$data['country_id'],
                'province_id'=>$data['province_id'],
                'county_id'=>$data['county_id'],
                'postal_code'=>$data['postal_code'],
                'latitude'=>$data['latitude'],
                'longitude'=>$data['longitude'],
                'address'=>$data['address'],
            ]
        );
        $this->contactService->save(
            $academy['user_id'],
            [
                'telephone' => $data['telephone'] ?? null,
                'mobile'    => $data['mobile'] ?? null,
                'whatsapp'  => $data['whatsapp'] ?? null,
                'telegram'  => $data['telegram'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'website'   => $data['website'] ?? null,
            ]
        );
        return $userUpdated;
    }



    public function delete(int $id): bool {return $this->repository->delete($id);}



    public function paginate(AcademyIndexRequest $request): array {
        return $this->repository->paginateList($request);
    }



    public function findById(int $id): mixed {
        return $this->repository->findById($id);
    }



    public function editData(int $academyId): array {
        $academy = $this->findById($academyId);
        if (!$academy) {
            return [];
        }
        return [
            'academy'   => $academy,
            'address'   => $this->addressService->findByUserId($academy['user_id']),
            'contact'   => $this->contactService->findByUserId($academy['user_id']),
            'provinces' => $this->provinceService->options(),
            'counties'  => [],
        ];
    }








}