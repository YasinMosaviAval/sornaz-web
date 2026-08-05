<?php

use Core\router\Router;
use Modules\Enrollment\Controllers\Api\EnrollmentController;

Router::group(
    ['prefix' => '/api/enrollments'],
    function () {
        Router::get('/',        [EnrollmentController::class,'index']);
        Router::post('/',       [EnrollmentController::class,'store']);
        Router::get('/{id}',    [EnrollmentController::class,'show']);
        Router::put('/{id}',    [EnrollmentController::class,'update']);
        Router::delete('/{id}', [EnrollmentController::class,'destroy']);
    }

);
