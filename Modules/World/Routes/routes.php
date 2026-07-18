<?php

use Core\Router\Router;
use Modules\World\Controllers\Api\WorldController;

Router::post(
    '/api/world/google-address',
    [WorldController::class,'googleAddress']
);