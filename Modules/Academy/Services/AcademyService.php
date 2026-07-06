<?php

namespace Modules\Academy\Services;

use Modules\Academy\Models\AcademyModel;
use Modules\Academy\Repositories\AcademyRepository;
use Modules\Academy\Requests\AcademyIndexRequest;

class AcademyService {

    public function __construct(protected AcademyRepository $repository) {}

    public function list(): array{return $this->repository->getActive();}

    public function all(): array{return $this->repository->getAll();}
    
    public function active(): array {return $this->repository->getActive();}

    public function find(int $id) {return $this->repository->find($id);}

    public function create(array $data): bool {
        $data['type'] = 'academy';
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool {return $this->repository->update($id, $data);}

    public function delete(int $id): bool {return $this->repository->delete($id);}

    public function paginate(AcademyIndexRequest $request) {
        $query = AcademyModel::query();
        $query->academies();
        if ($request->status() !== null) {
            $query->where('status', $request->status());
        }
        /*
        فعلاً Search را بعداً اضافه می‌کنیم
        چون Builder هنوز whereLike ندارد.
        */
        return $query
            ->orderBy($request->orderBy(), $request->direction())
            ->paginate($request->page(), $request->perPage());
    }

}