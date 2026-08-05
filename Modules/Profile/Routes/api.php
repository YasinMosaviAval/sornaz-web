<?php

use Core\router\Router;
use Modules\Profile\Controllers\Api\ProfileController;

Router::group(
    ['prefix' => '/api/profiles'],
    function () {
        Router::get('/',        [ProfileController::class,'index']);
        Router::post('/',       [ProfileController::class,'store']);
        Router::get('/{id}',    [ProfileController::class,'show']);
        Router::put('/{id}',    [ProfileController::class,'update']);
        Router::delete('/{id}', [ProfileController::class,'destroy']);
    }

);
