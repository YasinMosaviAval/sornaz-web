<?php

use Core\Router\Router;
use Modules\Blog\Controllers\Web\BlogController;
use Modules\Blog\Repositories\BlogRepository;

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


Router::get('/blog-test', function () {

    $repo = app()->container()->make(BlogRepository::class);

    dd(
        $repo->find(1)
    );

});