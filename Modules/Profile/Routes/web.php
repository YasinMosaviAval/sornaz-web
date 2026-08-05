<?php

use Core\router\Router;
use Modules\Profile\Controllers\Web\ProfileController;

Router::group(
    ['prefix' => '/profiles'],
    function () {
        Router::get('/',            [ProfileController::class,'index']);
        Router::get('/create',      [ProfileController::class,'create']);
        Router::post('/',           [ProfileController::class,'store']);
        Router::get('/{id}',        [ProfileController::class,'show']);
        Router::get('/{id}/edit',   [ProfileController::class,'edit']);
        Router::put('/{id}',        [ProfileController::class,'update']);
        Router::delete('/{id}',     [ProfileController::class,'destroy']);
    }


);