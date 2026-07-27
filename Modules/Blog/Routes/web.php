<?php

use Core\Router\Router;
use Modules\Blog\Controllers\Web\BlogController;

Router::group(
    ['prefix' => '/blogs'],
    function () {
        Router::get('/',            [BlogController::class,'index']);
        Router::get('/create',      [BlogController::class,'create']);
        Router::post('/',           [BlogController::class,'store']);
        Router::get('/{id}',        [BlogController::class,'show']);
        Router::get('/{id}/edit',   [BlogController::class,'edit']);
        Router::put('/{id}',        [BlogController::class,'update']);
        Router::delete('/{id}',     [BlogController::class,'destroy']);
    }
);


Router::get('/blog', [BlogController::class, 'index']);
Router::get('/blog/{slug}', [BlogController::class, 'show']);