<?php

use Core\router\Router;
use Modules\World\Controllers\Web\WorldController;

Router::group(
    ['prefix' => '/worlds'],
    function () {
        Router::get('/',            [WorldController::class,'index']);
        Router::get('/create',      [WorldController::class,'create']);
        Router::post('/',           [WorldController::class,'store']);
        Router::get('/{id}',        [WorldController::class,'show']);
        Router::get('/{id}/edit',   [WorldController::class,'edit']);
        Router::put('/{id}',        [WorldController::class,'update']);
        Router::delete('/{id}',     [WorldController::class,'destroy']);
    }


);