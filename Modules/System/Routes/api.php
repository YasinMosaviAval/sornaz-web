<?php

use Core\router\Router;
use Modules\System\Controllers\Api\UserController;

Router::group(['prefix' => '/api/sornaz/v1/auth'], function () {
    Router::post('/login', [UserController::class, 'login']);
    Router::post('/register/send-otp', [UserController::class, 'sendRegistrationOtp']);
    Router::post('/register', [UserController::class, 'register']);
    Router::get('/me', [UserController::class, 'me']);
    Router::post('/logout', [UserController::class, 'logout']);
});

Router::post('/api/sornaz/v1/contact', [UserController::class, 'contact']);


// Router::group(
//     ['prefix' => '/api/systems'],
//     function () {
//         Router::get('/',        [SystemController::class,'index']);
//         Router::post('/',       [SystemController::class,'store']);
//         Router::get('/{id}',    [SystemController::class,'show']);
//         Router::put('/{id}',    [SystemController::class,'update']);
//         Router::delete('/{id}', [SystemController::class,'destroy']);
//     }

// );
