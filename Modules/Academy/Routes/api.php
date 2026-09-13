<?php

use Core\router\Router;
use Modules\Academy\Controllers\Api\AcademyController;
use Modules\Academy\Controllers\Api\PublicAcademyController;

Router::get('/api/sornaz/v1/academies/options', [PublicAcademyController::class, 'options']);
Router::get('/api/sornaz/v1/academies/{id}', [PublicAcademyController::class, 'show']);
Router::get('/api/sornaz/v1/academies', [PublicAcademyController::class, 'index']);

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
