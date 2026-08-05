<?php

use Core\router\Router;
use Modules\Analytics\Controllers\Web\AnalyticsController;



Router::get('/analytics/admin-panel', [AnalyticsController::class, 'adminPanel']);



Router::get('/analytics/articles', [AnalyticsController::class, 'articles']);
Router::get('/analytics/article-details', [AnalyticsController::class, 'articleDetails']);



Router::get('/analytics/user', [AnalyticsController::class, 'user']);
Router::get('/analytics/users', [AnalyticsController::class, 'users']);
Router::get('/analytics/academy', [AnalyticsController::class, 'academy']);
Router::get('/analytics/academies', [AnalyticsController::class, 'academies']);
Router::get('/analytics/academy-enroll', [AnalyticsController::class, 'academyEnroll']);
Router::get('/analytics/send-academy-request', [AnalyticsController::class, 'sendAcademyRequest']);
