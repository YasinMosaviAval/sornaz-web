<?php

use Core\router\Router;
use Modules\Translation\Controllers\Web\TranslationController;

Router::group(
    ['prefix' => '/translations'],
    function () {
        Router::get('/', [TranslationController::class, 'index'])->middleware('site-admin');
        Router::get('/create', [TranslationController::class, 'create'])->middleware('site-admin');
        Router::post('/', [TranslationController::class, 'store'])->middleware(['site-admin','csrf']);
        Router::get('/{id}', [TranslationController::class, 'show'])->middleware('site-admin');
        Router::get('/{id}/edit', [TranslationController::class, 'edit'])->middleware('site-admin');
        Router::put('/{id}', [TranslationController::class, 'update'])->middleware(['site-admin','csrf']);
        Router::delete('/{id}', [TranslationController::class, 'destroy'])->middleware(['site-admin','csrf']);
    }
);
