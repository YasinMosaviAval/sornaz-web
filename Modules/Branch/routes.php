<?php

use Core\Router\Router;
use Modules\Branch\Controllers\Web\BranchController;

Router::group(
    ['prefix' => '/academies/{academy}/branches'],
    function () {
        Router::get('/', [BranchController::class,'index']);
        Router::get('/create', [BranchController::class,'create']);
        Router::post('/', [BranchController::class,'store']);
        Router::get('/{branch}/edit', [BranchController::class,'edit']);
        Router::post('/{branch}', [BranchController::class,'update']);
        Router::post('/{branch}/delete', [BranchController::class,'delete']);
    }
);

dd(Router::dispatch(
    'GET',
    '/academies/24/branches/create'
));

