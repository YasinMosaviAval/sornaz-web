<?php

namespace Modules\Academy\Repositories;

use Core\Database\Repository;
use Modules\Academy\Models\AcademyModel;
use Modules\Academy\Requests\AcademyIndexRequest;

class AcademyRepository extends Repository {


    protected ?string $model = AcademyModel::class;



    public function getActive(): array {
        return AcademyModel::query()->active()->latest('user_id')->get();
    }



    public function getAll(): array {
        return AcademyModel::query()->latest('user_id')->get();
    }








    // public function query() {
    //     return AcademyModel::query()->academies();
    // }

    public function paginateList(AcademyIndexRequest $request): array {
        $query = $this->query();
        if ($request->search()) {
            $query->where(function ($q) use ($request) {
                $q->where('username', 'LIKE', '%' . $request->search() . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search() . '%');
            });
        }
        if ($request->status() !== null) {
            $query->where('status', $request->status());
        }
        return $query->paginate(
            page: $request->page(),
            perPage: $request->perPage()
        );
    }

    public function findById(int $id) {
        return $this->query()->where('user_id', $id)->first();
    }

    public function existsByUsername(string $username): bool {
        return $this->query()->where('username', $username)->exists();
    }

    public function existsByEmail(string $email): bool {
        return $this->query()->where('email', $email)->exists();
    }
}


