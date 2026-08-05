<?php

use Core\router\Router;
use Modules\Finance\Controllers\Web\FinanceController;

Router::group(
    ['prefix' => '/finances'],
    function () {
        Router::get('/',            [FinanceController::class,'index']);
        Router::get('/create',      [FinanceController::class,'create']);
        Router::post('/',           [FinanceController::class,'store']);
        Router::get('/{id}',        [FinanceController::class,'show']);
        Router::get('/{id}/edit',   [FinanceController::class,'edit']);
        Router::put('/{id}',        [FinanceController::class,'update']);
        Router::delete('/{id}',     [FinanceController::class,'destroy']);
    }


);