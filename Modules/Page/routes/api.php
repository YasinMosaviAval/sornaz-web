<?php

use Core\router\Router;
use Modules\Page\Controllers\Api\PageController;

Router::group(
    ['prefix' => '/api/pages'],
    function () {
        Router::get('/',        [PageController::class,'index']);
        Router::post('/',       [PageController::class,'store']);
        Router::get('/{id}',    [PageController::class,'show']);
        Router::put('/{id}',    [PageController::class,'update']);
        Router::delete('/{id}', [PageController::class,'destroy']);
    }

);
