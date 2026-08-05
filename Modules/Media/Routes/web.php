<?php

use Core\router\Router;
use Modules\Media\Controllers\Web\MediaController;

Router::group(
    ['prefix' => '/medias'],
    function () {
        Router::get('/',            [MediaController::class,'index']);
        Router::get('/create',      [MediaController::class,'create']);
        Router::post('/',           [MediaController::class,'store']);
        Router::get('/{id}',        [MediaController::class,'show']);
        Router::get('/{id}/edit',   [MediaController::class,'edit']);
        Router::put('/{id}',        [MediaController::class,'update']);
        Router::delete('/{id}',     [MediaController::class,'destroy']);
    }


);