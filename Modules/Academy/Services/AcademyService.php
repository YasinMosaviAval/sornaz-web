<?php

namespace Modules\Academy\Services;

use Modules\Academy\Models\AcademyModel;
use Modules\Academy\Repositories\AcademyRepository;
use Modules\System\Repositories\UserRepository;
use Modules\Academy\Requests\AcademyIndexRequest;
use Modules\System\Models\UserModel;

class AcademyService {

    protected AcademyRepository $academyRepository;
    protected UserRepository $userRepository;

    public function __construct(protected AcademyRepository $repository) {
        $this->academyRepository = $repository;
        $this->userRepository = app()->container()->make(UserRepository::class);
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
        // $user = $this->userRepository->create([
        //     'username' => $data['username'],
        //     'email'    => $data['email'] ?? null,
        //     'phone'    => $data['phone'] ?? null,
        //     'type'     => 'academy',
        //     'status'   => $data['status'],
        //     'locale'   => $data['locale'],
        //     'timezone' => $data['timezone'],
        // ]);

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
        // dump($user);
        return $this->academyRepository->create(['user_id' => $user->user_id,]);
    }





    public function update(int $id, array $data): bool {return $this->repository->update($id, $data);}

    public function delete(int $id): bool {return $this->repository->delete($id);}

    // public function paginate(AcademyIndexRequest $request) {
    //     $query = AcademyModel::query()->academies();
    //     if ($request->status() !== null) {
    //         $query->where('status', $request->status());
    //     }
    //     return $query
    //         ->orderBy($request->orderBy(), $request->direction())
    //         ->paginate($request->page(), $request->perPage());
    // }
    public function paginate(AcademyIndexRequest $request): array {
        return $this->repository->paginateList($request);
    }


    public function findById(int $id): ?AcademyModel {
        return $this->repository->findById($id);
    }




}