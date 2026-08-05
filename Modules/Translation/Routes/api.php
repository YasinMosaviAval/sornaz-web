<?php

use Core\router\Router;
use Modules\Translation\Controllers\Api\TranslationController;

Router::group(
    ['prefix' => '/api/translations'],
    function () {
        Router::get('/',        [TranslationController::class,'index']);
        Router::post('/',       [TranslationController::class,'store']);
        Router::get('/{id}',    [TranslationController::class,'show']);
        Router::put('/{id}',    [TranslationController::class,'update']);
        Router::delete('/{id}', [TranslationController::class,'destroy']);
    }

);
