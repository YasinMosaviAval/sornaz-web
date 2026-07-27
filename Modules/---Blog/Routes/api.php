<?php

use Core\Router\Router;
use Modules\Blog\Controllers\Api\BlogController;

Router::group(
    ['prefix' => '/api/blogs'],
    function () {
        Router::get('/',        [BlogController::class,'index']);
        Router::post('/',       [BlogController::class,'store']);
        Router::get('/{id}',    [BlogController::class,'show']);
        Router::put('/{id}',    [BlogController::class,'update']);
        Router::delete('/{id}', [BlogController::class,'destroy']);
    }

);