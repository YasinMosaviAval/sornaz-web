<?php

namespace Modules\World\Services;

use Modules\World\Repositories\ProvinceRepository;

class ProvinceService {

    public function __construct(protected ProvinceRepository $repository) {
    }

    public function all(): array {
        return $this->repository->all();
    }

    public function options(): array {
        $items = $this->repository->all();
        $options = [];
        foreach ($items as $item) {
            $options[$item['province_id']] = $item['province_name'];
        }
        return $options;
    }





}