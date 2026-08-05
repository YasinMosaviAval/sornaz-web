<?php

use Core\router\Router;
use Modules\Communication\Controllers\Web\CommunicationController;

Router::group(
    ['prefix' => '/communications'],
    function () {
        Router::get('/',            [CommunicationController::class,'index']);
        Router::get('/create',      [CommunicationController::class,'create']);
        Router::post('/',           [CommunicationController::class,'store']);
        Router::get('/{id}',        [CommunicationController::class,'show']);
        Router::get('/{id}/edit',   [CommunicationController::class,'edit']);
        Router::put('/{id}',        [CommunicationController::class,'update']);
        Router::delete('/{id}',     [CommunicationController::class,'destroy']);
    }


);