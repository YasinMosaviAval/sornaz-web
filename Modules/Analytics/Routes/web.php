<?php

use Core\router\Router;
use Modules\Analytics\Controllers\Web\AnalyticsController;
use Modules\Analytics\Controllers\Web\AdminTestController;



Router::get('/analytics/admin-panel', [AnalyticsController::class, 'adminPanel'])->middleware('academy-panel');
Router::post('/analytics/_test/seed-academy-managers', [AdminTestController::class, 'seedAcademyManagers'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/_test/delete-academy-managers', [AdminTestController::class, 'deleteAcademyManagers'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/member-schedules/{id}/delete', [AdminTestController::class, 'deleteAvailability'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/availability-exceptions/{id}/delete', [AdminTestController::class, 'deleteAvailabilityException'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/admin-inline-translations/save', [AdminTestController::class, 'saveInlineTranslation'])->middleware(['site-admin', 'csrf']);



Router::get('/analytics/articles', [AnalyticsController::class, 'articles']);
Router::get('/analytics/article-details', [AnalyticsController::class, 'articleDetails']);



Router::get('/analytics/user', [AnalyticsController::class, 'user']);
Router::get('/analytics/users', [AnalyticsController::class, 'users']);
