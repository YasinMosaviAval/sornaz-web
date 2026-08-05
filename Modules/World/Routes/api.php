<?php

use Core\router\Router;
use Modules\World\Controllers\Api\WorldController;

Router::group(
    ['prefix' => '/api/worlds'],
    function () {
        Router::get('/',        [WorldController::class,'index']);
        Router::post('/',       [WorldController::class,'store']);
        Router::get('/{id}',    [WorldController::class,'show']);
        Router::put('/{id}',    [WorldController::class,'update']);
        Router::delete('/{id}', [WorldController::class,'destroy']);
    }

);
