<?php
use Core\router\Router;
use Modules\CourseMarket\Controllers\CourseController;

Router::get('/course-market', [CourseController::class,'index']);
Router::get('/course-market/manage', [CourseController::class,'manage'])->middleware('auth');
Router::get('/course-market/library', [CourseController::class,'library'])->middleware('auth');
Router::get('/course-market/create', [CourseController::class,'create'])->middleware('auth');
Router::get('/course-market/payment/callback', [CourseController::class,'callback']);
Router::get('/course-market/media/{id}', [CourseController::class,'media']);
Router::get('/course-market/courses/{id}/edit', [CourseController::class,'edit'])->middleware('auth');
Router::get('/course-market/courses/{id}', [CourseController::class,'show']);
Router::post('/course-market/courses', [CourseController::class,'store'])->middleware(['auth','csrf']);
Router::post('/course-market/courses/{id}', [CourseController::class,'update'])->middleware(['auth','csrf']);
Router::post('/course-market/courses/{id}/media', [CourseController::class,'upload'])->middleware(['auth','csrf']);
Router::post('/course-market/courses/{id}/buy', [CourseController::class,'buy'])->middleware(['auth','csrf']);

Router::post('/course-market/courses/{id}/lessons/{postId}/unlock', [CourseController::class,'unlockLesson'])->middleware(['auth','csrf']);
