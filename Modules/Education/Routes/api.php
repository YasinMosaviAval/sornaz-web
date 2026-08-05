<?php

use Core\router\Router;
use Modules\Education\Controllers\Api\EducationController;

Router::group(
    ['prefix' => '/api/educations'],
    function () {
        Router::get('/',        [EducationController::class,'index']);
        Router::post('/',       [EducationController::class,'store']);
        Router::get('/{id}',    [EducationController::class,'show']);
        Router::put('/{id}',    [EducationController::class,'update']);
        Router::delete('/{id}', [EducationController::class,'destroy']);
    }

);
