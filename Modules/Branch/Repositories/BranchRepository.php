<?php

namespace Modules\Branch\Repositories;

use Core\Database\Repository;

class BranchRepository extends Repository {
    protected string $table='academy_branches';
    protected string $primaryKey='branch_id';
    protected ?string $model=null;

    public function academyBranches(int $academyId): array {
        return $this->query()
            ->where('academy_id',$academyId)
            ->whereNull('deleted_at')
            ->get();
    }

    public function findById(int $id): ?array {
        return $this->query()
            ->where('branch_id',$id)
            ->first();
    }


}