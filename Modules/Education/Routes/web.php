<?php

use Core\router\Router;
use Modules\Education\Controllers\Web\EducationController;
use Modules\Education\Controllers\MusicPracticeController;

Router::get('/exams',[MusicPracticeController::class,'page']);
Router::get('/exams/payment/callback',[MusicPracticeController::class,'callback']);
foreach(['people'=>'people','catalog'=>'catalog','progress'=>'progress','plans'=>'plans'] as $path=>$method) Router::get('/exams/api/'.$path,[MusicPracticeController::class,$method]);
foreach(['results'=>'result','nodes'=>'node','exams'=>'exam','shares'=>'share','publish'=>'publish','plans/propose'=>'propose','plans/approve'=>'approve','purchase'=>'purchase'] as $path=>$method) Router::post('/exams/api/'.$path,[MusicPracticeController::class,$method])->middleware(['auth','csrf']);

Router::group(
    ['prefix' => '/educations'],
    function () {
        Router::get('/', [EducationController::class, 'index']);
        Router::get('/create', [EducationController::class, 'create']);
        Router::post('/', [EducationController::class, 'store']);
        Router::get('/{id}', [EducationController::class, 'show']);
        Router::get('/{id}/edit', [EducationController::class, 'edit']);
        Router::put('/{id}', [EducationController::class, 'update']);
        Router::delete('/{id}', [EducationController::class, 'destroy']);
    }
);
