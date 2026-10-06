<?php

use Core\router\Router;
use Modules\Translation\Controllers\Api\TranslationController;

Router::group(
    ['prefix' => '/api/translations'],
    function () {
        Router::get('/', [TranslationController::class, 'index'])->middleware('site-admin');
        Router::post('/', [TranslationController::class, 'store'])->middleware(['site-admin','csrf']);
        Router::get('/{id}', [TranslationController::class, 'show'])->middleware('site-admin');
        Router::put('/{id}', [TranslationController::class, 'update'])->middleware(['site-admin','csrf']);
        Router::delete('/{id}', [TranslationController::class, 'destroy'])->middleware(['site-admin','csrf']);
    }
);
