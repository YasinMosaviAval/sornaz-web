<?php

namespace Modules\Academy\Repositories;

use Core\Database\Repository;
use Modules\Academy\Models\AcademyModel;
use Modules\Academy\Requests\AcademyIndexRequest;

class AcademyRepository extends Repository {

    protected string $table = 'users';
    protected string $primaryKey = 'user_id';
    protected ?string $model = null;



    public function getActive(): array {
        return $this->query()
            ->leftJoin(
                'academies',
                'users.user_id',
                '=',
                'academies.user_id'
            )
            ->where('users.type', 'academy')
            ->where('users.status', 1)
            ->latest('users.user_id')
            ->get();
    }



    public function getAll(): array {

        return $this->query()
            ->leftJoin(
                'academies',
                'users.user_id',
                '=',
                'academies.user_id'
            )
            ->where('users.type', 'academy')
            ->latest('users.user_id')
            ->get();

    }



    public function paginateList(AcademyIndexRequest $request): array {
        $query = $this->query()
            ->leftJoin(
                'academies',
                'users.user_id',
                '=',
                'academies.user_id'
            )
            ->where('users.type', 'academy');
        if ($request->search()) {
            $query->where(function ($q) use ($request) {
                $q->where('users.username', 'LIKE', '%' . $request->search() . '%');
                $q->orWhere('users.email', 'LIKE', '%' . $request->search() . '%');
            });
        }
        if ($request->status() !== null) {
            $query->where('users.status', $request->status());
        }
        return $query->paginate(
            page: $request->page(),
            perPage: $request->perPage()
        );
    }



    public function existsByUsername(string $username): bool {
        return $this->query()->where('username', $username)->exists();
    }



    public function existsByEmail(string $email): bool {
        return $this->query()->where('email', $email)->exists();
    }



    public function findById(int $id): ?AcademyModel {
        return AcademyModel::find($id);
    }


}


