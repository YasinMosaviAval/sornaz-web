<?php

use Core\router\Router;
use Modules\Academy\Controllers\Api\AcademyController;

Router::group(
    ['prefix' => '/api/academy'],
    function () {
        Router::get('/',        [AcademyController::class,'index']);
        Router::post('/',       [AcademyController::class,'store']);
        Router::get('/{id}',    [AcademyController::class,'show']);
        Router::put('/{id}',    [AcademyController::class,'update']);
        Router::delete('/{id}', [AcademyController::class,'destroy']);
    }

);
