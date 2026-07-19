<?php

namespace Modules\Branch\Repositories;

use Core\Database\Repository;

class BranchTypeRepository extends Repository {


    protected string $table = 'academy_branch_types';
    protected string $primaryKey = 'academy_branch_type_id';
    protected ?string $model = null;

    public function options(): array {
        return $this->query()
            ->orderBy('sort_order')
            ->get();
    }





}