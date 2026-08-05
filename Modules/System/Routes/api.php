<?php

use Core\router\Router;
use Modules\System\Controllers\Api\SystemController;

Router::group(
    ['prefix' => '/api/systems'],
    function () {
        Router::get('/',        [SystemController::class,'index']);
        Router::post('/',       [SystemController::class,'store']);
        Router::get('/{id}',    [SystemController::class,'show']);
        Router::put('/{id}',    [SystemController::class,'update']);
        Router::delete('/{id}', [SystemController::class,'destroy']);
    }

);
