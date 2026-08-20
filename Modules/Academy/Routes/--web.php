<?php

use Core\router\Router;
use Modules\Academy\Controllers\Web\AcademyController;
use Modules\Academy\Controllers\Web\AcademyRegistrationController;
use Modules\Analytics\Controllers\Web\AnalyticsController;
use Modules\Academy\Controllers\Web\AcademyBranchController;
use Modules\Academy\Controllers\Web\AcademyClassroomController;
use Modules\Academy\Controllers\Web\AcademyBranchOfferingController;
use Modules\Academy\Controllers\Web\AcademyCourseController;
use Modules\Academy\Controllers\Web\AcademyTermController;
use Modules\Academy\Controllers\Web\AcademyTermAvailabilityController;
use Modules\Academy\Controllers\Web\AcademyClassScheduleController;
use Modules\Academy\Controllers\Web\AcademyWeeklyScheduleBoundsController;


Router::group(
    ['prefix' => '/academy'],
    function () {
        /*
        |--------------------------------------------------------------------------
        | Public / General
        |--------------------------------------------------------------------------
        */
        Router::group(
            ['prefix' => '/'],
            function () {

                Router::get('/', [AcademyController::class, 'index']);
                Router::get('/create', [AcademyController::class, 'create']);
                Router::post('/', [AcademyController::class, 'store']);

                Router::get('/{id}', [AcademyController::class, 'show']);
                Router::get('/{id}/edit', [AcademyController::class, 'edit']);
                Router::put('/{id}', [AcademyController::class, 'update']);
                Router::delete('/{id}', [AcademyController::class, 'destroy']);

                Router::get('/academy', [AnalyticsController::class, 'academy']);
                Router::get('/academies', [AcademyRegistrationController::class, 'index']);
                Router::get('/academy-enroll', [AnalyticsController::class, 'academyEnroll']);
                Router::get('/send-academy-request', [AcademyRegistrationController::class, 'create']);
                Router::post('/send-academy-request', [AcademyRegistrationController::class, 'store']);
                Router::post('/send-academy-request/send-otp', [AcademyRegistrationController::class, 'sendOtp']);
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
                Router::post('/seed-sample-academies', [AcademyRegistrationController::class, 'seedSamples']);
                Router::post('/delete-sample-academies', [AcademyRegistrationController::class, 'deleteSamples']);
                Router::post('/seed-branch-network', [AcademyRegistrationController::class, 'seedBranchNetwork']);
                Router::post('/delete-branch-network', [AcademyRegistrationController::class, 'deleteBranchNetwork']);
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Academy Admin Panel
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
                | Branches
                |--------------------------------------------------------------------------
                */
                Router::group(
                    ['prefix' => '/branches'],
                    function () {
                        Router::get('/', [AcademyBranchController::class, 'index']);
                        Router::post('/', [AcademyBranchController::class, 'store'])->middleware('csrf');
                        Router::post('/types', [AcademyBranchController::class, 'storeType'])->middleware('csrf');
                        Router::post('/{id}/update', [AcademyBranchController::class, 'update'])->middleware('csrf');
                        Router::post('/{id}/delete', [AcademyBranchController::class, 'destroy'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Branch Types - Site Admin
                |--------------------------------------------------------------------------
                */
                // not has   =>   'middleware' => 'academy-panel',
                Router::group(
                    [
                        'prefix' => '/branch-types',
                        'middleware' => 'site-admin',
                    ],
                    function () {
                        Router::post('/{id}/update', [AcademyBranchController::class, 'updateType'])->middleware('csrf');
                        Router::post('/{id}/delete', [AcademyBranchController::class, 'deleteType'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Members
                |--------------------------------------------------------------------------
                */
                Router::group(
                    ['prefix' => '/members'],
                    function () {
                        Router::post('/{id}/update', [AcademyBranchController::class, 'updateMember'])->middleware('csrf');
                        Router::post('/{id}/delete', [AcademyBranchController::class, 'deleteMember'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Classrooms
                |--------------------------------------------------------------------------
                */
                Router::group(
                    ['prefix' => '/classrooms'],
                    function () {
                        Router::get('/', [AcademyClassroomController::class, 'index']);
                        Router::post('/', [AcademyClassroomController::class, 'store'])->middleware('csrf');
                        Router::post('/{id}/update', [AcademyClassroomController::class, 'update'])->middleware('csrf');
                        Router::post('/{id}/delete', [AcademyClassroomController::class, 'delete'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Classroom Types - Site Admin
                |--------------------------------------------------------------------------
                */
                Router::group(
                    [
                        'prefix' => '/classroom-types',
                        'middleware' => 'site-admin',
                    ],
                    function () {
                        Router::post('/', [AcademyClassroomController::class, 'storeType'])->middleware('csrf');
                        Router::post('/{id}/update', [AcademyClassroomController::class, 'updateType'])->middleware('csrf');
                        Router::post('/{id}/delete', [AcademyClassroomController::class, 'deleteType'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Branch Offerings
                |--------------------------------------------------------------------------
                */
                Router::group(
                    ['prefix' => '/branch-offerings'],
                    function () {
                        Router::get('/', [AcademyBranchOfferingController::class, 'index']);
                        Router::group(
                            ['prefix' => '/schedules'],
                            function () {
                                Router::post('/', [AcademyBranchOfferingController::class, 'storeSchedule'])->middleware('csrf');
                                Router::post('/{id}/update', [AcademyBranchOfferingController::class, 'updateSchedule'])->middleware('csrf');
                            }
                        );
                        Router::post('/{type}/{id}/delete', [AcademyBranchOfferingController::class, 'delete'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Courses
                |--------------------------------------------------------------------------
                */
                Router::group(
                    ['prefix' => '/courses'],
                    function () {
                        Router::get('/', [AcademyCourseController::class, 'index']);
                        Router::post('/', [AcademyCourseController::class, 'store'])->middleware('csrf');
                        Router::post('/{id}/update', [AcademyCourseController::class, 'update'])->middleware('csrf');
                        Router::post('/{id}/delete', [AcademyCourseController::class, 'destroy'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Course Levels
                |--------------------------------------------------------------------------
                */
                Router::group(
                    ['prefix' => '/course-levels'],
                    function () {
                        Router::post('/', [AcademyCourseController::class, 'storeLevel'])->middleware('csrf');
                        Router::post('/{id}/update', [AcademyCourseController::class, 'updateLevel'])->middleware('csrf');
                        Router::post('/{id}/delete', [AcademyCourseController::class, 'deleteLevel'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Terms
                |--------------------------------------------------------------------------
                */
                Router::group(
                    ['prefix' => '/terms'],
                    function () {
                        Router::get('/', [AcademyTermController::class, 'index']);
                        Router::post('/', [AcademyTermController::class, 'store'])->middleware('csrf');
                        Router::post('/{id}/update', [AcademyTermController::class, 'update'])->middleware('csrf');
                        Router::post('/{id}/delete', [AcademyTermController::class, 'destroy'])->middleware('csrf');
                        Router::post('/discounts', [AcademyTermController::class, 'storeDiscount'])->middleware('csrf'); // error in prefix
                        // Router::post('/term-discounts', [AcademyTermController::class, 'storeDiscount'])->middleware(['academy-panel','csrf']);
                    }
                );



                Router::group(
                    ['prefix' => '/term'],
                    function () {
                        /*
                        |--------------------------------------------------------------------------
                        | Term Availability
                        |--------------------------------------------------------------------------
                        */
                        Router::get('/term-available-times', [AcademyTermAvailabilityController::class, 'index']);

                        /*
                        |--------------------------------------------------------------------------
                        | Term Discounts
                        |--------------------------------------------------------------------------
                        */
                        Router::post('/discounts', [AcademyTermController::class, 'storeDiscount'])->middleware('csrf'); // error in prefix
                        // Router::post('/term-discounts', [AcademyTermController::class, 'storeDiscount'])->middleware(['academy-panel','csrf']);
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Term Invoices
                |--------------------------------------------------------------------------
                */
                Router::group(
                    ['prefix' => '/term-invoices'],
                    function () {
                        Router::get('/', [AcademyTermController::class, 'invoices']);
                        Router::post('/{id}/update', [AcademyTermController::class, 'updateInvoice'])->middleware('csrf');
                        Router::post('/{id}/installments/{installmentId}/pay', [AcademyTermController::class, 'payInstallment'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Class Schedules
                |--------------------------------------------------------------------------
                */
                Router::group(
                    ['prefix' => '/class-schedules'],
                    function () {
                        Router::get('/', [AcademyClassScheduleController::class, 'index']);
                        Router::post('/{id}/attendance', [AcademyClassScheduleController::class, 'attendance'])->middleware('csrf');
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Weekly Schedule Bounds
                |--------------------------------------------------------------------------
                */
                Router::get('/class-schedule-week-bounds', [AcademyWeeklyScheduleBoundsController::class, 'index']);


            }
        );
    }
);