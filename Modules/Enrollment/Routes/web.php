<?php

use Core\router\Router;
use Modules\Enrollment\Controllers\Web\EnrollmentController;

Router::group(
    ['prefix' => '/enrollments'],
    function () {
        Router::get('/',            [EnrollmentController::class,'index']);
        Router::get('/create',      [EnrollmentController::class,'create']);
        Router::post('/',           [EnrollmentController::class,'store']);
        Router::get('/{id}',        [EnrollmentController::class,'show']);
        Router::get('/{id}/edit',   [EnrollmentController::class,'edit']);
        Router::put('/{id}',        [EnrollmentController::class,'update']);
        Router::delete('/{id}',     [EnrollmentController::class,'destroy']);
    }


);