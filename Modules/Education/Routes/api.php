<?php

use Core\router\Router;
use Modules\Education\Controllers\Api\EducationController;
use Modules\Education\Controllers\Api\PracticeController;

// Validated mobile Bearer tokens only; never cookie authentication.
Router::get('/api/sornaz/v1/practice/interval-results', [PracticeController::class, 'index']);
Router::post('/api/sornaz/v1/practice/interval-results', [PracticeController::class, 'store']);
Router::post('/api/sornaz/v1/practice/interval-progress/import', [PracticeController::class, 'importLegacy']);

Router::group(
    ['prefix' => '/api/educations'],
    function () {
        Router::get('/', [EducationController::class, 'index']);
        Router::post('/', [EducationController::class, 'store']);
        Router::get('/{id}', [EducationController::class, 'show']);
        Router::put('/{id}', [EducationController::class, 'update']);
        Router::delete('/{id}', [EducationController::class, 'destroy']);
    }
);
