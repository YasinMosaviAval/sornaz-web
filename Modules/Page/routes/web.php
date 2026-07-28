<?php

use Core\Router\Router;
use Modules\Page\Controllers\Web\PageController;

Router::group(
    ['prefix' => '/pages'],
    function () {
        Router::get('/',            [PageController::class,'index']);
        Router::get('/create',      [PageController::class,'create']);
        Router::post('/',           [PageController::class,'store']);
        Router::get('/{id}',        [PageController::class,'show']);
        Router::get('/{id}/edit',   [PageController::class,'edit']);
        Router::put('/{id}',        [PageController::class,'update']);
        Router::delete('/{id}',     [PageController::class,'destroy']);
    }


);