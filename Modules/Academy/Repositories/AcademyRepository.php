<?php
/*
namespace Modules\Academy\Repositories;

use Core\Database\Repository;
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



    public function findById(int $id): mixed {
        return $this->query()
            ->leftJoin(
                'users',
                'academies.user_id',
                '=',
                'users.user_id'
            )
            ->where('academies.user_id', $id)
            ->first();
    }

}
*/

namespace Modules\Academy\Repositories;

use Core\Database\Repository;
use Modules\Academy\Requests\AcademyIndexRequest;

class AcademyRepository extends Repository {
    protected string $table = 'academies';
    protected string $primaryKey = 'academy_id';
    protected ?string $model = null;

    public function getActive(): array
    {
        return $this->query()
            ->leftJoin(
                'users',
                'academies.user_id',
                '=',
                'users.user_id'
            )
            ->where('users.type', 'academy')
            ->where('users.status', 1)
            ->latest('academies.academy_id')
            ->get();
    }

    public function getAll(): array
    {
        return $this->query()
            ->leftJoin(
                'users',
                'academies.user_id',
                '=',
                'users.user_id'
            )
            ->where('users.type', 'academy')
            ->latest('academies.academy_id')
            ->get();
    }

    public function paginateList(AcademyIndexRequest $request): array
    {
        $query = $this->query()
            ->leftJoin(
                'users',
                'academies.user_id',
                '=',
                'users.user_id'
            )
            ->where('users.type', 'academy');

        if ($request->search()) {

            $query->where(function ($q) use ($request) {

                $q->where(
                    'users.username',
                    'LIKE',
                    '%' . $request->search() . '%'
                );

                $q->orWhere(
                    'users.email',
                    'LIKE',
                    '%' . $request->search() . '%'
                );

            });

        }

        if ($request->status() !== null) {
            $query->where(
                'users.status',
                $request->status()
            );
        }

        return $query->paginate(
            page: $request->page(),
            perPage: $request->perPage()
        );
    }

    public function existsByUsername(string $username): bool
    {
        return $this->query()
            ->leftJoin(
                'users',
                'academies.user_id',
                '=',
                'users.user_id'
            )
            ->where('users.username', $username)
            ->exists();
    }

    public function existsByEmail(string $email): bool
    {
        return $this->query()
            ->leftJoin(
                'users',
                'academies.user_id',
                '=',
                'users.user_id'
            )
            ->where('users.email', $email)
            ->exists();
    }

    public function findById(int $academyId): mixed
    {
        return $this->query()
            ->leftJoin(
                'users',
                'academies.user_id',
                '=',
                'users.user_id'
            )
            ->where(
                'academies.academy_id',
                $academyId
            )
            ->first();
            
        dd($row);
    }
}