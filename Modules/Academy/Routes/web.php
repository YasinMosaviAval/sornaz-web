<?php

use Core\router\Router;
use Modules\Academy\Controllers\Web\AcademyController;
use Modules\Academy\Controllers\Web\AcademyRegistrationController;
use Modules\Analytics\Controllers\Web\AnalyticsController;

Router::get('/academy/academy', [AnalyticsController::class, 'academy']);
Router::get('/academy/academies', [AnalyticsController::class, 'academies']);
Router::get('/academy/academy-enroll', [AnalyticsController::class, 'academyEnroll']);
Router::get('/academy/send-academy-request', [AcademyRegistrationController::class, 'create']);
Router::post('/academy/send-academy-request', [AcademyRegistrationController::class, 'store']);
Router::post('/academy/send-academy-request/send-otp', [AcademyRegistrationController::class, 'sendOtp']);

Router::group(
    ['prefix' => '/academy'],
    function () {
        Router::get('/',            [AcademyController::class,'index']);
        Router::get('/create',      [AcademyController::class,'create']);
        Router::post('/',           [AcademyController::class,'store']);
        Router::get('/{id}',        [AcademyController::class,'show']);
        Router::get('/{id}/edit',   [AcademyController::class,'edit']);
        Router::put('/{id}',        [AcademyController::class,'update']);
        Router::delete('/{id}',     [AcademyController::class,'destroy']);
    }


);
