<?php

use Core\router\Router;
use Modules\Finance\Controllers\Api\FinanceController;

Router::group(
    ['prefix' => '/api/finances'],
    function () {
        Router::get('/',        [FinanceController::class,'index']);
        Router::post('/',       [FinanceController::class,'store']);
        Router::get('/{id}',    [FinanceController::class,'show']);
        Router::put('/{id}',    [FinanceController::class,'update']);
        Router::delete('/{id}', [FinanceController::class,'destroy']);
    }

);
