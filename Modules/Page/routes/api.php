<?php

use Core\router\Router;
use Modules\Page\Controllers\Api\PageController;

Router::get('/api/sornaz/v1/site/home', [PageController::class, 'home']);
Router::get('/api/sornaz/v1/site/about-us', [PageController::class, 'about']);
Router::get('/api/sornaz/v1/site/contact-us', [PageController::class, 'contact']);

Router::group(
    ['prefix' => '/api/pages'],
    function () {
        Router::get('/', [PageController::class, 'index']);
        Router::post('/', [PageController::class, 'store']);
        Router::get('/{id}', [PageController::class, 'show']);
        Router::put('/{id}', [PageController::class, 'update']);
        Router::delete('/{id}', [PageController::class, 'destroy']);
    }
);
