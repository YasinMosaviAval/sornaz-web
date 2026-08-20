<?php

use Core\router\Router;

use Modules\Analytics\Controllers\Web\AnalyticsController;
use Modules\Analytics\Controllers\Web\AdminAccountController;
use Modules\Analytics\Controllers\Web\AdminNotificationController;
use Modules\Analytics\Controllers\Web\AdminSchedulingRuleController;
use Modules\Analytics\Controllers\Web\AdminPostController;
use Modules\Analytics\Controllers\Web\AdminPostCategoryController;
use Modules\Analytics\Controllers\Web\AdminCommentController;
use Modules\Analytics\Controllers\Web\AdminMediaController;
use Modules\Analytics\Controllers\Web\AdminSettingController;
use Modules\Analytics\Controllers\Web\AdminUserAccessController;
use Modules\Analytics\Controllers\Web\AdminDashboardController;
use Modules\Analytics\Controllers\Web\AdminAccessCatalogController;
use Modules\Analytics\Controllers\Web\SitePageContentController;
use Modules\Analytics\Controllers\Web\AdminTestController;


/*
|--------------------------------------------------------------------------
| Analytics
|--------------------------------------------------------------------------
*/

Router::group(
    ['prefix' => '/analytics'],
    function () {

        /*
        |--------------------------------------------------------------------------
        | Public
        |--------------------------------------------------------------------------
        */
        Router::get('/articles', [AnalyticsController::class, 'articles']);
        Router::get('/article-details', [AnalyticsController::class, 'articleDetails']);
        Router::get('/user', [AnalyticsController::class, 'user']);
        Router::get('/users', [AnalyticsController::class, 'users']);
        Router::get('/site-settings', [AdminSettingController::class, 'show']);
        Router::get('/site-page-content', [SitePageContentController::class, 'content']);


        /*
        |--------------------------------------------------------------------------
        | Academy Panel
        |--------------------------------------------------------------------------
        */

        Router::group(
            [
                'prefix' => '/admin',
                'middleware' => 'academy-panel',
            ],
            function () {

                /*
                |--------------------------------------------------------------------------
                | Dashboard
                |--------------------------------------------------------------------------
                */
                Router::get('/panel', [AnalyticsController::class, 'adminPanel']);
                Router::get('/dashboard', [AdminDashboardController::class, 'index']);


                /*
                |--------------------------------------------------------------------------
                | Admin Account
                |--------------------------------------------------------------------------
                */

                Router::group(
                    ['prefix' => '/account'],
                    function () {
                        Router::get('/', [AdminAccountController::class, 'show']);
                        Router::post('/profile', [AdminAccountController::class, 'profile'])->middleware('csrf');
                        Router::post('/bio', [AdminAccountController::class, 'bio'])->middleware('csrf');
                        Router::post('/privacy', [AdminAccountController::class, 'privacy'])->middleware('csrf');
                        Router::post('/security', [AdminAccountController::class, 'security'])->middleware('csrf');


                        /*
                        |--------------------------------------------------------------------------
                        | Media
                        |--------------------------------------------------------------------------
                        */

                        Router::group(
                            ['prefix' => '/media'],
                            function () {
                                Router::post('/{kind}', [AdminAccountController::class, 'upload'])->middleware('csrf');
                                Router::post('/{id}/delete', [AdminAccountController::class, 'deleteMedia'])->middleware('csrf');
                                Router::get('/{id}/download', [AdminAccountController::class, 'downloadMedia']);
                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | Sessions
                        |--------------------------------------------------------------------------
                        */
                        Router::post('/sessions/{id}/end', [AdminAccountController::class, 'endSession'])->middleware('csrf');


                        /*
                        |--------------------------------------------------------------------------
                        | Backups
                        |--------------------------------------------------------------------------
                        */

                        Router::group(
                            ['prefix' => '/backups'],
                            function () {
                                Router::post('/', [AdminAccountController::class, 'backup'])->middleware('csrf');
                                Router::get('/{id}/download', [AdminAccountController::class, 'download']);
                            }
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Notifications
                |--------------------------------------------------------------------------
                */

                Router::group(
                    ['prefix' => '/notifications'],
                    function () {
                        Router::get('/', [AdminNotificationController::class, 'index']);
                        Router::post('/', [AdminNotificationController::class, 'store'])->middleware('csrf');
                        Router::post('/{id}/publish', [AdminNotificationController::class, 'publish'])->middleware('csrf');
                        Router::post('/{id}/expire', [AdminNotificationController::class, 'expire'])->middleware('csrf');
                        Router::post('/{id}/delete', [AdminNotificationController::class, 'delete'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Scheduling Rules
                |--------------------------------------------------------------------------
                */

                Router::group(
                    ['prefix' => '/scheduling-rules'],
                    function () {
                        Router::get('/', [AdminSchedulingRuleController::class, 'index']);
                        Router::post('/', [AdminSchedulingRuleController::class, 'store'])->middleware('csrf');
                        Router::post('/{id}/update', [AdminSchedulingRuleController::class, 'update'])->middleware('csrf');
                        Router::post('/{id}/delete', [AdminSchedulingRuleController::class, 'delete'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Posts
                |--------------------------------------------------------------------------
                */

                Router::group(
                    ['prefix' => '/posts'],
                    function () {
                        Router::get('/', [AdminPostController::class, 'index']);
                        Router::get('/{id}', [AdminPostController::class, 'show']);
                        Router::post('/', [AdminPostController::class, 'store'])->middleware('csrf');
                        Router::post('/{id}/update', [AdminPostController::class, 'update'])->middleware('csrf');
                        Router::post('/{id}/trash', [AdminPostController::class, 'trash'])->middleware('csrf');
                        Router::post('/{id}/restore', [AdminPostController::class, 'restore'])->middleware('csrf');
                        Router::post('/{id}/delete', [AdminPostController::class, 'destroy'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Post Categories
                |--------------------------------------------------------------------------
                */

                Router::group(
                    ['prefix' => '/post-categories'],
                    function () {
                        Router::get('/', [AdminPostCategoryController::class, 'index']);
                        Router::post('/', [AdminPostCategoryController::class, 'store'])->middleware('csrf');
                        Router::post('/{id}/update', [AdminPostCategoryController::class, 'update'])->middleware('csrf');
                        Router::post('/{id}/delete', [AdminPostCategoryController::class, 'delete'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Comments
                |--------------------------------------------------------------------------
                */

                Router::group(
                    ['prefix' => '/comments'],
                    function () {
                        Router::get('/', [AdminCommentController::class, 'index']);
                        Router::post('/{id}/update', [AdminCommentController::class, 'update'])->middleware('csrf');
                        Router::post('/{id}/reply', [AdminCommentController::class, 'reply'])->middleware('csrf');
                        Router::post('/{id}/delete', [AdminCommentController::class, 'delete'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Media
                |--------------------------------------------------------------------------
                */

                Router::group(
                    ['prefix' => '/media'],
                    function () {
                        Router::get('/', [AdminMediaController::class, 'index']);
                        Router::post('/upload', [AdminMediaController::class, 'upload'])->middleware('csrf');
                        Router::post('/{id}/update', [AdminMediaController::class, 'update'])->middleware('csrf');
                        Router::post('/{id}/delete', [AdminMediaController::class, 'delete'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Settings
                |--------------------------------------------------------------------------
                */
                Router::post('/settings', [AdminSettingController::class, 'save'])->middleware('csrf');


                /*
                |--------------------------------------------------------------------------
                | Site Pages
                |--------------------------------------------------------------------------
                */
                Router::get('/site-pages', [SitePageContentController::class, 'pages']);
                Router::post('/site-page-content', [SitePageContentController::class, 'save'])->middleware('site-admin')->middleware('csrf');
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Site Admin
        |--------------------------------------------------------------------------
        */

        Router::group(
            [
                'prefix' => '/admin',
                'middleware' => 'site-admin',
            ],
            function () {

                /*
                |--------------------------------------------------------------------------
                | User Access
                |--------------------------------------------------------------------------
                */

                Router::group(
                    ['prefix' => '/user-access'],
                    function () {
                        Router::get('/', [AdminUserAccessController::class, 'index']);
                        Router::post('/{id}', [AdminUserAccessController::class, 'save'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Access Catalog
                |--------------------------------------------------------------------------
                */

                Router::group(
                    ['prefix' => '/access-catalog'],
                    function () {
                        Router::get('/', [AdminAccessCatalogController::class, 'index']);


                        /*
                        |--------------------------------------------------------------------------
                        | Roles
                        |--------------------------------------------------------------------------
                        */

                        Router::group(
                            ['prefix' => '/roles'],
                            function () {
                                Router::post('/', [AdminAccessCatalogController::class, 'saveRole'])->middleware('csrf');
                                Router::post('/{id}', [AdminAccessCatalogController::class, 'saveRole'])->middleware('csrf');
                                Router::post('/{id}/delete', [AdminAccessCatalogController::class, 'deleteRole'])->middleware('csrf');
                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | Permissions
                        |--------------------------------------------------------------------------
                        */

                        Router::group(
                            ['prefix' => '/permissions'],
                            function () {
                                Router::post('/', [AdminAccessCatalogController::class, 'savePermission'])->middleware('csrf');
                                Router::post('/{id}', [AdminAccessCatalogController::class, 'savePermission'])->middleware('csrf');
                                Router::post('/{id}/delete', [AdminAccessCatalogController::class, 'deletePermission'])->middleware('csrf');
                            }
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Inline Translations
                |--------------------------------------------------------------------------
                */

                Router::group(
                    ['prefix' => '/inline-translations'],
                    function () {
                        Router::get('/', [AdminTestController::class, 'inlineTranslations']);
                        Router::post('/save', [AdminTestController::class, 'saveInlineTranslation'])->middleware('csrf');
                    }
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Test / Development
        |--------------------------------------------------------------------------
        */

        Router::group(
            [
                'prefix' => '/_test',
                'middleware' => ['site-admin', 'csrf'],
            ],
            function () {
                Router::post('/seed-academy-managers', [AdminTestController::class, 'seedAcademyManagers']);
                Router::post('/delete-academy-managers', [AdminTestController::class, 'deleteAcademyManagers']);
                Router::post('/seed-branch-courses', [AdminTestController::class, 'seedBranchCourses']);
                Router::post('/delete-branch-courses', [AdminTestController::class, 'deleteBranchCourses']);
                Router::post('/seed-branch-terms', [AdminTestController::class, 'seedBranchTerms']);
                Router::post('/delete-branch-terms', [AdminTestController::class, 'deleteBranchTerms']);
                Router::post('/delete-all-test-data', [AdminTestController::class, 'deleteAllTestData']);
                Router::post('/run-all', [AdminTestController::class, 'runAllTests']);
                Router::post('/seed-courses-and-terms', [AdminTestController::class, 'seedCoursesAndTerms']);
                Router::post('/seed-branch-network-courses-and-terms', [AdminTestController::class, 'seedBranchNetworkCoursesAndTerms']);
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Test / Data Management
        |--------------------------------------------------------------------------
        */

        Router::group(
            [
                'middleware' => ['site-admin', 'csrf'],
            ],
            function () {
                Router::post('/member-schedules/{id}/delete', [AdminTestController::class, 'deleteAvailability']);
                Router::post('/availability-exceptions/{id}/delete', [AdminTestController::class, 'deleteAvailabilityException']);
            }
        );
    }
);





// Router::get('/analytics/admin-panel', [AnalyticsController::class, 'adminPanel'])->middleware('academy-panel');
// Router::get('/analytics/admin-account', [AdminAccountController::class, 'show'])->middleware('academy-panel');
// Router::post('/analytics/admin-account/profile', [AdminAccountController::class, 'profile'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-account/bio', [AdminAccountController::class, 'bio'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-account/privacy', [AdminAccountController::class, 'privacy'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-account/security', [AdminAccountController::class, 'security'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-account/media/{kind}', [AdminAccountController::class, 'upload'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-account/media/{id}/delete', [AdminAccountController::class, 'deleteMedia'])->middleware(['academy-panel','csrf']);
// Router::get('/analytics/admin-account/media/{id}/download', [AdminAccountController::class, 'downloadMedia'])->middleware('academy-panel');
// Router::post('/analytics/admin-account/sessions/{id}/end', [AdminAccountController::class, 'endSession'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-account/backups', [AdminAccountController::class, 'backup'])->middleware(['academy-panel','csrf']);
// Router::get('/analytics/admin-account/backups/{id}/download', [AdminAccountController::class, 'download'])->middleware('academy-panel');
// Router::get('/analytics/admin-notifications', [AdminNotificationController::class, 'index'])->middleware('academy-panel');
// Router::post('/analytics/admin-notifications', [AdminNotificationController::class, 'store'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-notifications/{id}/publish', [AdminNotificationController::class, 'publish'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-notifications/{id}/expire', [AdminNotificationController::class, 'expire'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-notifications/{id}/delete', [AdminNotificationController::class, 'delete'])->middleware(['academy-panel','csrf']);
// Router::get('/analytics/admin-scheduling-rules', [AdminSchedulingRuleController::class, 'index'])->middleware('academy-panel');
// Router::post('/analytics/admin-scheduling-rules', [AdminSchedulingRuleController::class, 'store'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-scheduling-rules/{id}/update', [AdminSchedulingRuleController::class, 'update'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-scheduling-rules/{id}/delete', [AdminSchedulingRuleController::class, 'delete'])->middleware(['academy-panel','csrf']);
// Router::get('/analytics/admin-posts', [AdminPostController::class, 'index'])->middleware('academy-panel');
// Router::get('/analytics/admin-posts/{id}', [AdminPostController::class, 'show'])->middleware('academy-panel');
// Router::post('/analytics/admin-posts', [AdminPostController::class, 'store'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-posts/{id}/update', [AdminPostController::class, 'update'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-posts/{id}/trash', [AdminPostController::class, 'trash'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-posts/{id}/restore', [AdminPostController::class, 'restore'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-posts/{id}/delete', [AdminPostController::class, 'destroy'])->middleware(['academy-panel','csrf']);
// Router::get('/analytics/admin-post-categories', [AdminPostCategoryController::class, 'index'])->middleware('academy-panel');
// Router::post('/analytics/admin-post-categories', [AdminPostCategoryController::class, 'store'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-post-categories/{id}/update', [AdminPostCategoryController::class, 'update'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-post-categories/{id}/delete', [AdminPostCategoryController::class, 'delete'])->middleware(['academy-panel','csrf']);
// Router::get('/analytics/admin-comments', [AdminCommentController::class, 'index'])->middleware('academy-panel');
// Router::post('/analytics/admin-comments/{id}/update', [AdminCommentController::class, 'update'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-comments/{id}/reply', [AdminCommentController::class, 'reply'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-comments/{id}/delete', [AdminCommentController::class, 'delete'])->middleware(['academy-panel','csrf']);
// Router::get('/analytics/admin-media', [AdminMediaController::class, 'index'])->middleware('academy-panel');
// Router::post('/analytics/admin-media/upload', [AdminMediaController::class, 'upload'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-media/{id}/update', [AdminMediaController::class, 'update'])->middleware(['academy-panel','csrf']);
// Router::post('/analytics/admin-media/{id}/delete', [AdminMediaController::class, 'delete'])->middleware(['academy-panel','csrf']);
// Router::get('/analytics/site-settings', [AdminSettingController::class, 'show']);
// Router::post('/analytics/admin-settings', [AdminSettingController::class, 'save'])->middleware(['academy-panel','csrf']);
// Router::get('/analytics/admin-user-access', [AdminUserAccessController::class, 'index'])->middleware('site-admin');
// Router::get('/analytics/admin-dashboard', [AdminDashboardController::class, 'index'])->middleware('academy-panel');
// Router::post('/analytics/admin-user-access/{id}', [AdminUserAccessController::class, 'save'])->middleware(['site-admin','csrf']);
// Router::get('/analytics/admin-access-catalog', [AdminAccessCatalogController::class, 'index'])->middleware('site-admin');
// Router::post('/analytics/admin-roles', [AdminAccessCatalogController::class, 'saveRole'])->middleware(['site-admin','csrf']);
// Router::post('/analytics/admin-roles/{id}', [AdminAccessCatalogController::class, 'saveRole'])->middleware(['site-admin','csrf']);
// Router::post('/analytics/admin-roles/{id}/delete', [AdminAccessCatalogController::class, 'deleteRole'])->middleware(['site-admin','csrf']);
// Router::post('/analytics/admin-permissions', [AdminAccessCatalogController::class, 'savePermission'])->middleware(['site-admin','csrf']);
// Router::post('/analytics/admin-permissions/{id}', [AdminAccessCatalogController::class, 'savePermission'])->middleware(['site-admin','csrf']);
// Router::post('/analytics/admin-permissions/{id}/delete', [AdminAccessCatalogController::class, 'deletePermission'])->middleware(['site-admin','csrf']);
// Router::get('/analytics/admin-site-pages', [SitePageContentController::class, 'pages'])->middleware('academy-panel');
// Router::get('/analytics/site-page-content', [SitePageContentController::class, 'content']);
// Router::post('/analytics/admin-site-page-content', [SitePageContentController::class, 'save'])->middleware(['site-admin','csrf']);
// Router::post('/analytics/_test/seed-academy-managers', [AdminTestController::class, 'seedAcademyManagers'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/_test/delete-academy-managers', [AdminTestController::class, 'deleteAcademyManagers'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/_test/seed-branch-courses', [AdminTestController::class, 'seedBranchCourses'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/_test/delete-branch-courses', [AdminTestController::class, 'deleteBranchCourses'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/_test/seed-branch-terms', [AdminTestController::class, 'seedBranchTerms'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/_test/delete-branch-terms', [AdminTestController::class, 'deleteBranchTerms'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/_test/delete-all-test-data', [AdminTestController::class, 'deleteAllTestData'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/_test/run-all', [AdminTestController::class, 'runAllTests'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/_test/seed-courses-and-terms', [AdminTestController::class, 'seedCoursesAndTerms'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/_test/seed-branch-network-courses-and-terms', [AdminTestController::class, 'seedBranchNetworkCoursesAndTerms'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/member-schedules/{id}/delete', [AdminTestController::class, 'deleteAvailability'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/availability-exceptions/{id}/delete', [AdminTestController::class, 'deleteAvailabilityException'])->middleware(['site-admin', 'csrf']);
// Router::post('/analytics/admin-inline-translations/save', [AdminTestController::class, 'saveInlineTranslation'])->middleware(['site-admin', 'csrf']);
// Router::get('/analytics/admin-inline-translations', [AdminTestController::class, 'inlineTranslations'])->middleware('site-admin');

// Router::get('/analytics/articles', [AnalyticsController::class, 'articles']);
// Router::get('/analytics/article-details', [AnalyticsController::class, 'articleDetails']);

// Router::get('/analytics/user', [AnalyticsController::class, 'user']);
// Router::get('/analytics/users', [AnalyticsController::class, 'users']);
