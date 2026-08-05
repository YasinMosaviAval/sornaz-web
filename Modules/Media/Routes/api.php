<?php

use Core\router\Router;
use Modules\Media\Controllers\Api\MediaController;

Router::group(
    ['prefix' => '/api/medias'],
    function () {
        Router::get('/',        [MediaController::class,'index']);
        Router::post('/',       [MediaController::class,'store']);
        Router::get('/{id}',    [MediaController::class,'show']);
        Router::put('/{id}',    [MediaController::class,'update']);
        Router::delete('/{id}', [MediaController::class,'destroy']);
    }

);
