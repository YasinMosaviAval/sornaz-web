<?php

use Core\router\Router;
use Modules\System\Controllers\Web\UserController;
use Modules\System\Controllers\Web\SystemController;
use Modules\System\Controllers\Web\UserReferralController;
use Modules\System\Controllers\Web\UserTrackingController;

Router::get('/system/login', [SystemController::class, 'login']);
Router::get('/system/register', [SystemController::class, 'register']);
Router::get('/system/forgot-password', [SystemController::class, 'forgotPassword']);

Router::get('/login', [SystemController::class, 'login']);
Router::get('/register', [SystemController::class, 'register']);
Router::get('/forgot-password', [SystemController::class, 'forgotPassword']);
Router::get('/language/{locale}', [SystemController::class, 'changeLanguage']);
Router::get('/users', [UserController::class, 'directory']);
Router::post('/contact', [\Modules\System\Controllers\Api\UserController::class, 'contactWeb'])->middleware(['csrf', 'auth-rate-limit']);
Router::get('/system/my-invite', [UserReferralController::class, 'show'])->middleware('auth');
Router::post('/system/tracking/ingest', [UserTrackingController::class, 'ingest']);

Router::post('/register', [UserController::class, 'store'])->middleware(['csrf', 'auth-rate-limit']);
Router::post('/register/send-otp', [UserController::class, 'sendRegistrationOtp'])->middleware(['csrf', 'auth-rate-limit']);
Router::post('/forgot-password/send-otp', [UserController::class, 'sendPasswordResetOtp'])->middleware(['csrf', 'auth-rate-limit']);
Router::post('/forgot-password/verify-otp', [UserController::class, 'verifyPasswordResetOtp'])->middleware(['csrf', 'auth-rate-limit']);
Router::post('/forgot-password/reset', [UserController::class, 'resetPassword'])->middleware(['csrf', 'auth-rate-limit']);
Router::post('/login', [UserController::class, 'login'])->middleware(['csrf', 'auth-rate-limit']);
Router::post('/logout', [UserController::class, 'logout'])->middleware('csrf');

/*
    Router::group(
        ['prefix' => '/systems'],
        function () {
            Router::get('/',            [SystemController::class,'index']);
            Router::get('/create',      [SystemController::class,'create']);
            Router::post('/',           [SystemController::class,'store']);
            Router::get('/{id}',        [SystemController::class,'show']);
            Router::get('/{id}/edit',   [SystemController::class,'edit']);
            Router::put('/{id}',        [SystemController::class,'update']);
            Router::delete('/{id}',     [SystemController::class,'destroy']);
        }
    );
*/
