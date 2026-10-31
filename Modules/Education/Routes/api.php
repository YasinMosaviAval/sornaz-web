<?php

use Core\router\Router;
use Modules\Education\Controllers\Api\EducationController;
use Modules\Education\Controllers\Api\PracticeController;
use Modules\Education\Controllers\MusicPracticeController;

Router::get('/api/sornaz/v1/music-practice/people',[MusicPracticeController::class,'people']);
Router::get('/api/sornaz/v1/music-practice/catalog',[MusicPracticeController::class,'catalog']);
Router::get('/api/sornaz/v1/music-practice/progress',[MusicPracticeController::class,'progress']);
Router::get('/api/sornaz/v1/music-practice/plans',[MusicPracticeController::class,'plans']);
foreach(['results'=>'result','nodes'=>'node','exams'=>'exam','exams/delete'=>'deleteExam','nodes/delete'=>'deleteNode','shares'=>'share','publish'=>'publish','plans/propose'=>'propose','plans/approve'=>'approve','purchase'=>'purchase'] as $path=>$method) Router::post('/api/sornaz/v1/music-practice/'.$path,[MusicPracticeController::class,$method]);

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
