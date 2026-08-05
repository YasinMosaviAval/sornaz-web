<?php

use Core\router\Router;
use Modules\Communication\Controllers\Api\CommunicationController;

Router::group(
    ['prefix' => '/api/communications'],
    function () {
        Router::get('/',        [CommunicationController::class,'index']);
        Router::post('/',       [CommunicationController::class,'store']);
        Router::get('/{id}',    [CommunicationController::class,'show']);
        Router::put('/{id}',    [CommunicationController::class,'update']);
        Router::delete('/{id}', [CommunicationController::class,'destroy']);
    }

);
