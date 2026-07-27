<?php

namespace Modules\World\Repositories;

use Core\Database\Repository;

class CountyRepository extends Repository {

    protected string $table = 'world_iran_counties';
    protected string $primaryKey = 'county_id';
    protected ?string $model = null;

    public function byProvince(int $provinceId): array {
        return $this->query()
            ->where('province_id', $provinceId)
            ->orderBy('county_name')
            ->get();
    }



}