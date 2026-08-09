<?php

use Core\router\Router;
use Modules\Page\Controllers\Web\PageController;


Router::get('/', [PageController::class, 'home']);
Router::get('/page/home', [PageController::class, 'home']);
Router::get('/page/about-us', [PageController::class, 'aboutUs']);
Router::get('/page/contact-us', [PageController::class, 'contactUs']);


/*
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
*/
