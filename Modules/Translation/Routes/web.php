<?php

use Core\router\Router;
use Modules\Translation\Controllers\Web\TranslationController;

Router::group(
    ['prefix' => '/translations'],
    function () {
        Router::get('/',            [TranslationController::class,'index']);
        Router::get('/create',      [TranslationController::class,'create']);
        Router::post('/',           [TranslationController::class,'store']);
        Router::get('/{id}',        [TranslationController::class,'show']);
        Router::get('/{id}/edit',   [TranslationController::class,'edit']);
        Router::put('/{id}',        [TranslationController::class,'update']);
        Router::delete('/{id}',     [TranslationController::class,'destroy']);
    }


);