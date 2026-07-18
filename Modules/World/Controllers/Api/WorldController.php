<?php

namespace Modules\World\Controllers\Api;

use Modules\World\Services\CountyService;
use Core\Http\ResponseFactory;
use Modules\World\Services\GoogleAddressMapper;

class WorldController {
    protected CountyService $countyService;

    public function __construct() {
        $this->countyService = app()
            ->container()
            ->make(CountyService::class);
    }



    public function counties(int $provinceId) {
        return ResponseFactory::json($this->countyService->options($provinceId));
    }


    public function googleAddress() {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );
        $mapper = new GoogleAddressMapper();
        return ResponseFactory::json(
            $mapper->map($data ?? [])
        );
    }

}