<?php

use Core\router\Router;
use Modules\Education\Controllers\Web\EducationController;

Router::group(
    ['prefix' => '/educations'],
    function () {
        Router::get('/',            [EducationController::class,'index']);
        Router::get('/create',      [EducationController::class,'create']);
        Router::post('/',           [EducationController::class,'store']);
        Router::get('/{id}',        [EducationController::class,'show']);
        Router::get('/{id}/edit',   [EducationController::class,'edit']);
        Router::put('/{id}',        [EducationController::class,'update']);
        Router::delete('/{id}',     [EducationController::class,'destroy']);
    }


);