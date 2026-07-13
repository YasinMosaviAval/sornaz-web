<?php

namespace Modules\World\Services;

use Modules\World\Repositories\CountyRepository;

class CountyService {


    public function __construct(protected CountyRepository $repository) {
    }


    public function byProvince(int $provinceId): array {
        return $this->repository->byProvince($provinceId);
    }


    public function options(int $provinceId): array {
        $items = $this->repository->byProvince($provinceId);
        $options = [];
        foreach ($items as $item) {
            $options[$item['county_id']] = $item['county_name'];
        }
        return $options;
    }




}