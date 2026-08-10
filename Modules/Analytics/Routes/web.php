<?php

use Core\router\Router;
use Modules\Analytics\Controllers\Web\AnalyticsController;



Router::get('/analytics/admin-panel', [AnalyticsController::class, 'adminPanel'])->middleware('academy');



Router::get('/analytics/articles', [AnalyticsController::class, 'articles']);
Router::get('/analytics/article-details', [AnalyticsController::class, 'articleDetails']);



Router::get('/analytics/user', [AnalyticsController::class, 'user']);
Router::get('/analytics/users', [AnalyticsController::class, 'users']);
