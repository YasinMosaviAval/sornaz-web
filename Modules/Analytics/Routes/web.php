<?php

use Core\router\Router;
use Modules\Analytics\Controllers\Web\AnalyticsController;
use Modules\Analytics\Controllers\Web\AdminTestController;
use Modules\Analytics\Controllers\Web\AdminNotificationController;
use Modules\Analytics\Controllers\Web\AdminSchedulingRuleController;
use Modules\Analytics\Controllers\Web\AdminPostController;
use Modules\Analytics\Controllers\Web\AdminPostCategoryController;
use Modules\Analytics\Controllers\Web\AdminCommentController;
use Modules\Analytics\Controllers\Web\SitePageContentController;
use Modules\Analytics\Controllers\Web\AdminMediaController;
use Modules\Analytics\Controllers\Web\AdminSettingController;
use Modules\Analytics\Controllers\Web\AdminUserAccessController;
use Modules\Analytics\Controllers\Web\AdminAccessCatalogController;
use Modules\Analytics\Controllers\Web\AdminDashboardController;
use Modules\Analytics\Controllers\Web\AdminAccountController;



Router::get('/analytics/admin-panel', [AnalyticsController::class, 'adminPanel'])->middleware('academy-panel');
Router::get('/analytics/admin-account', [AdminAccountController::class, 'show'])->middleware('academy-panel');
Router::post('/analytics/admin-account/profile', [AdminAccountController::class, 'profile'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-account/bio', [AdminAccountController::class, 'bio'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-account/privacy', [AdminAccountController::class, 'privacy'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-account/security', [AdminAccountController::class, 'security'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-account/media/{kind}', [AdminAccountController::class, 'upload'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-account/media/{id}/delete', [AdminAccountController::class, 'deleteMedia'])->middleware(['academy-panel','csrf']);
Router::get('/analytics/admin-account/media/{id}/download', [AdminAccountController::class, 'downloadMedia'])->middleware('academy-panel');
Router::post('/analytics/admin-account/sessions/{id}/end', [AdminAccountController::class, 'endSession'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-account/backups', [AdminAccountController::class, 'backup'])->middleware(['academy-panel','csrf']);
Router::get('/analytics/admin-account/backups/{id}/download', [AdminAccountController::class, 'download'])->middleware('academy-panel');
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
Router::get('/analytics/admin-post-categories', [AdminPostCategoryController::class, 'index'])->middleware('academy-panel');
Router::post('/analytics/admin-post-categories', [AdminPostCategoryController::class, 'store'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-post-categories/{id}/update', [AdminPostCategoryController::class, 'update'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-post-categories/{id}/delete', [AdminPostCategoryController::class, 'delete'])->middleware(['academy-panel','csrf']);
Router::get('/analytics/admin-comments', [AdminCommentController::class, 'index'])->middleware('academy-panel');
Router::post('/analytics/admin-comments/{id}/update', [AdminCommentController::class, 'update'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-comments/{id}/reply', [AdminCommentController::class, 'reply'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-comments/{id}/delete', [AdminCommentController::class, 'delete'])->middleware(['academy-panel','csrf']);
Router::get('/analytics/admin-media', [AdminMediaController::class, 'index'])->middleware('academy-panel');
Router::post('/analytics/admin-media/upload', [AdminMediaController::class, 'upload'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-media/{id}/update', [AdminMediaController::class, 'update'])->middleware(['academy-panel','csrf']);
Router::post('/analytics/admin-media/{id}/delete', [AdminMediaController::class, 'delete'])->middleware(['academy-panel','csrf']);
Router::get('/analytics/site-settings', [AdminSettingController::class, 'show']);
Router::post('/analytics/admin-settings', [AdminSettingController::class, 'save'])->middleware(['academy-panel','csrf']);
Router::get('/analytics/admin-user-access', [AdminUserAccessController::class, 'index'])->middleware('site-admin');
Router::get('/analytics/admin-dashboard', [AdminDashboardController::class, 'index'])->middleware('academy-panel');
Router::post('/analytics/admin-user-access/{id}', [AdminUserAccessController::class, 'save'])->middleware(['site-admin','csrf']);
Router::get('/analytics/admin-access-catalog', [AdminAccessCatalogController::class, 'index'])->middleware('site-admin');
Router::post('/analytics/admin-roles', [AdminAccessCatalogController::class, 'saveRole'])->middleware(['site-admin','csrf']);
Router::post('/analytics/admin-roles/{id}', [AdminAccessCatalogController::class, 'saveRole'])->middleware(['site-admin','csrf']);
Router::post('/analytics/admin-roles/{id}/delete', [AdminAccessCatalogController::class, 'deleteRole'])->middleware(['site-admin','csrf']);
Router::post('/analytics/admin-permissions', [AdminAccessCatalogController::class, 'savePermission'])->middleware(['site-admin','csrf']);
Router::post('/analytics/admin-permissions/{id}', [AdminAccessCatalogController::class, 'savePermission'])->middleware(['site-admin','csrf']);
Router::post('/analytics/admin-permissions/{id}/delete', [AdminAccessCatalogController::class, 'deletePermission'])->middleware(['site-admin','csrf']);
Router::get('/analytics/admin-site-pages', [SitePageContentController::class, 'pages'])->middleware('academy-panel');
Router::get('/analytics/site-page-content', [SitePageContentController::class, 'content']);
Router::post('/analytics/admin-site-page-content', [SitePageContentController::class, 'save'])->middleware(['site-admin','csrf']);
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
