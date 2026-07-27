<?php

use Core\Router\Router;
use Modules\Analytics\Controllers\Web\AnalyticsController;

Router::get('/analytics/home', [AnalyticsController::class, 'home']);
Router::get('/analytics/login', [AnalyticsController::class, 'login']);
Router::get('/analytics/register', [AnalyticsController::class, 'register']);
Router::get('/analytics/forgot-password', [AnalyticsController::class, 'forgotPassword']);



Router::get('/analytics/about-us', [AnalyticsController::class, 'aboutUs']);
Router::get('/analytics/contact-us', [AnalyticsController::class, 'contactUs']);
Router::get('/analytics/admin-panel', [AnalyticsController::class, 'adminPanel']);



Router::get('/analytics/articles', [AnalyticsController::class, 'articles']);
Router::get('/analytics/article-details', [AnalyticsController::class, 'articleDetails']);



Router::get('/analytics/user', [AnalyticsController::class, 'user']);
Router::get('/analytics/users', [AnalyticsController::class, 'users']);
Router::get('/analytics/academy', [AnalyticsController::class, 'academy']);
Router::get('/analytics/academies', [AnalyticsController::class, 'academies']);
Router::get('/analytics/academy-enroll', [AnalyticsController::class, 'academyEnroll']);
Router::get('/analytics/send-academy-request', [AnalyticsController::class, 'sendAcademyRequest']);
