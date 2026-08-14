<?php

use Core\router\Router;
use Modules\Analytics\Controllers\Web\AnalyticsController;
use Modules\Analytics\Controllers\Web\AdminTestController;
use Modules\Analytics\Controllers\Web\AdminNotificationController;
use Modules\Analytics\Controllers\Web\AdminSchedulingRuleController;
use Modules\Analytics\Controllers\Web\AdminPostController;



Router::get('/analytics/admin-panel', [AnalyticsController::class, 'adminPanel'])->middleware('academy-panel');
Router::get('/analytics/admin-notifications', [AdminNotificationController::class, 'index'])->middleware('academy-panel');
Router::post('/analytics/admin-notifications', [AdminNotificationController::class, 'store'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-notifications/{id}/publish', [AdminNotificationController::class, 'publish'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-notifications/{id}/expire', [AdminNotificationController::class, 'expire'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-notifications/{id}/delete', [AdminNotificationController::class, 'delete'])->middleware(['academy-panel','csrf']);
Router::get('/analytics/admin-scheduling-rules', [AdminSchedulingRuleController::class, 'index'])->middleware('academy-panel');
Router::post('/analytics/admin-scheduling-rules', [AdminSchedulingRuleController::class, 'store'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-scheduling-rules/{id}/update', [AdminSchedulingRuleController::class, 'update'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-scheduling-rules/{id}/delete', [AdminSchedulingRuleController::class, 'delete'])->middleware(['academy-panel','csrf']);
Router::get('/analytics/admin-posts', [AdminPostController::class, 'index'])->middleware('academy-panel');
Router::get('/analytics/admin-posts/{id}', [AdminPostController::class, 'show'])->middleware('academy-panel');
Router::post('/analytics/admin-posts', [AdminPostController::class, 'store'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-posts/{id}/update', [AdminPostController::class, 'update'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-posts/{id}/trash', [AdminPostController::class, 'trash'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-posts/{id}/restore', [AdminPostController::class, 'restore'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-posts/{id}/delete', [AdminPostController::class, 'destroy'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/_test/seed-academy-managers', [AdminTestController::class, 'seedAcademyManagers'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/_test/delete-academy-managers', [AdminTestController::class, 'deleteAcademyManagers'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/_test/seed-branch-courses', [AdminTestController::class, 'seedBranchCourses'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/_test/delete-branch-courses', [AdminTestController::class, 'deleteBranchCourses'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/_test/seed-branch-terms', [AdminTestController::class, 'seedBranchTerms'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/_test/delete-branch-terms', [AdminTestController::class, 'deleteBranchTerms'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/_test/delete-all-test-data', [AdminTestController::class, 'deleteAllTestData'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/_test/run-all', [AdminTestController::class, 'runAllTests'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/_test/seed-courses-and-terms', [AdminTestController::class, 'seedCoursesAndTerms'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/_test/seed-branch-network-courses-and-terms', [AdminTestController::class, 'seedBranchNetworkCoursesAndTerms'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/member-schedules/{id}/delete', [AdminTestController::class, 'deleteAvailability'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/availability-exceptions/{id}/delete', [AdminTestController::class, 'deleteAvailabilityException'])->middleware(['site-admin', 'csrf']);
Router::post('/analytics/admin-inline-translations/save', [AdminTestController::class, 'saveInlineTranslation'])->middleware(['site-admin', 'csrf']);
Router::get('/analytics/admin-inline-translations', [AdminTestController::class, 'inlineTranslations'])->middleware('site-admin');



Router::get('/analytics/articles', [AnalyticsController::class, 'articles']);
Router::get('/analytics/article-details', [AnalyticsController::class, 'articleDetails']);



Router::get('/analytics/user', [AnalyticsController::class, 'user']);
Router::get('/analytics/users', [AnalyticsController::class, 'users']);
