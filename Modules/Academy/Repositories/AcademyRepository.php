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

}


    // public function paginateList(AcademyIndexRequest $request): array {
    //     $query = AcademyModel::query()->academies();
    //     if ($request->status() !== null) {
    //         $query->where('status', $request->status());
    //     }
    //     if ($request->search()) {
    //         $query->whereLike('username', '%' . $request->search() . '%');
    //     }
    //     $query->orderBy($request->sort(), $request->direction());
    //     return $query->paginate($request->page(), $request->perPage());
    // }

    // public function paginate(AcademyIndexRequest $request) {
    //     $query = AcademyModel::query()->academies();
    //     if ($request->status() !== null) {
    //         $query->where('status', $request->status());
    //     }
    //     if ($request->search()) {
    //         $query->where(function ($q) use ($request) {
    //             $q->whereLike('username', '%' . $request->search() . '%');
    //             $q->orWhereLike('email', '%' . $request->search() . '%');
    //         });
    //     }
    //     return $query
    //         ->orderBy($request->orderBy(), $request->direction())
    //         ->paginate($request->page(), $request->perPage());
    // }


