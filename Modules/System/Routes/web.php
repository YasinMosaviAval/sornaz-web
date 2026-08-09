<?php

use Core\router\Router;
use Modules\System\Controllers\Web\UserController;
use Modules\System\Controllers\Web\SystemController;

Router::get('/system/login', [SystemController::class, 'login']);
Router::get('/system/register', [SystemController::class, 'register']);
Router::get('/system/forgot-password', [SystemController::class, 'forgotPassword']);

Router::get('/login', [SystemController::class, 'login']);
Router::get('/register', [SystemController::class, 'register']);
Router::get('/forgot-password', [SystemController::class, 'forgotPassword']);

Router::post('/register', [UserController::class, 'store']);
Router::post('/register/send-otp', [UserController::class, 'sendRegistrationOtp']);
Router::post('/login', [UserController::class, 'login']);
Router::post('/logout', [UserController::class, 'logout']);





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
