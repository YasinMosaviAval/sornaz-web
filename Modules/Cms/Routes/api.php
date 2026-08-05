<?php

use Core\router\Router;
use Modules\Cms\Controllers\Api\CmsController;

Router::group(
    ['prefix' => '/api/cmss'],
    function () {
        Router::get('/',        [CmsController::class,'index']);
        Router::post('/',       [CmsController::class,'store']);
        Router::get('/{id}',    [CmsController::class,'show']);
        Router::put('/{id}',    [CmsController::class,'update']);
        Router::delete('/{id}', [CmsController::class,'destroy']);
    }

);
