<?php

use Core\router\Router;
use Modules\Cms\Controllers\Web\CmsController;

Router::group(
    ['prefix' => '/cmss'],
    function () {
        Router::get('/',            [CmsController::class,'index']);
        Router::get('/create',      [CmsController::class,'create']);
        Router::post('/',           [CmsController::class,'store']);
        Router::get('/{id}',        [CmsController::class,'show']);
        Router::get('/{id}/edit',   [CmsController::class,'edit']);
        Router::put('/{id}',        [CmsController::class,'update']);
        Router::delete('/{id}',     [CmsController::class,'destroy']);
    }


);