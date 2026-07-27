<?php

namespace Modules\World\Repositories;

use Core\Database\Repository;

class ProvinceRepository extends Repository {
    
    protected string $table = 'world_iran_provinces';
    protected string $primaryKey = 'province_id';
    protected ?string $model = null;

    public function all(): array {
        return $this->query()->orderBy('province_name')->get();
    }




}