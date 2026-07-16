<?php

namespace Modules\World\Controllers\Api;

use Modules\World\Services\CountyService;
use Core\Http\ResponseFactory;

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




}