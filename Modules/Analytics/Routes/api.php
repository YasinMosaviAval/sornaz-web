<?php
use Core\router\Router;
use Modules\Analytics\Controllers\Api\MobilePanelController;
use Modules\Analytics\Controllers\Api\MobileLearningController;

Router::get('/analytics/admin-learning', [MobileLearningController::class, 'index'])->middleware('auth');
Router::get('/analytics/admin-guides', [\Modules\Analytics\Controllers\Web\AdminGuideController::class, 'index'])->middleware('site-admin');

Router::get('/api/sornaz/v1/panel', [MobilePanelController::class, 'index']);
Router::get('/api/sornaz/v1/panel/{section}/{operation}', [MobilePanelController::class, 'execute']);
Router::post('/api/sornaz/v1/panel/{section}/{operation}', [MobilePanelController::class, 'execute']);
