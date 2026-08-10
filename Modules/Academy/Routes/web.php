<?php

use Core\router\Router;
use Modules\Academy\Controllers\Web\AcademyController;
use Modules\Academy\Controllers\Web\AcademyRegistrationController;
use Modules\Analytics\Controllers\Web\AnalyticsController;
use Modules\Academy\Controllers\Web\AcademyBranchController;

Router::get('/academy/academy', [AnalyticsController::class, 'academy']);
Router::get('/academy/academies', [AcademyRegistrationController::class, 'index']);
Router::get('/academy/academy-enroll', [AnalyticsController::class, 'academyEnroll']);
Router::get('/academy/send-academy-request', [AcademyRegistrationController::class, 'create']);
Router::post('/academy/send-academy-request', [AcademyRegistrationController::class, 'store']);
Router::post('/academy/send-academy-request/send-otp', [AcademyRegistrationController::class, 'sendOtp']);
Router::post('/academy/_test/seed-sample-academies', [AcademyRegistrationController::class, 'seedSamples'])->middleware(['site-admin', 'csrf']);
Router::post('/academy/_test/delete-sample-academies', [AcademyRegistrationController::class, 'deleteSamples'])->middleware(['site-admin', 'csrf']);
Router::get('/academy/admin/branches', [AcademyBranchController::class, 'index'])->middleware('academy');
Router::post('/academy/admin/branches', [AcademyBranchController::class, 'store'])->middleware(['academy', 'csrf']);
Router::post('/academy/admin/branches/types', [AcademyBranchController::class, 'storeType'])->middleware(['academy', 'csrf']);
Router::post('/academy/admin/branches/{id}/update', [AcademyBranchController::class, 'update'])->middleware(['academy', 'csrf']);
Router::post('/academy/admin/branches/{id}/delete', [AcademyBranchController::class, 'destroy'])->middleware(['academy', 'csrf']);

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
