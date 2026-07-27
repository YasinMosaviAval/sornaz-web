<?php

use Core\Router\Router;
use Modules\Academy\Controllers\Web\AcademyController;

Router::group(
    ['prefix' => '/academy'],
    function () {
        Router::get('/', [AcademyController::class, 'index']);
        Router::get('/create', [AcademyController::class, 'create']);
        Router::post('/', [AcademyController::class, 'store']);
        Router::get('/{id}', [AcademyController::class,'show']);
        Router::get('/{id}/edit', [AcademyController::class, 'edit']);
        Router::put('/{id}', [AcademyController::class, 'update']);
        Router::delete('/{id}', [AcademyController::class, 'destroy']);
    }
);
