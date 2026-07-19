<?php

use Core\Router\Router;
use Modules\World\Controllers\Api\WorldController;


Router::get(
    '/api/world/provinces/{provinceId}/counties',
    [WorldController::class,'counties']
);


Router::post(
    '/api/world/google-address',
    [WorldController::class,'googleAddress']
);