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

Router::get('/academy/academy', [AnalyticsController::class, 'academy']);
Router::get('/academy/academies', [AcademyRegistrationController::class, 'index']);
Router::get('/academy/academy-enroll', [AnalyticsController::class, 'academyEnroll']);
Router::get('/academy/send-academy-request', [AcademyRegistrationController::class, 'create']);
Router::post('/academy/send-academy-request', [AcademyRegistrationController::class, 'store']);
Router::post('/academy/send-academy-request/send-otp', [AcademyRegistrationController::class, 'sendOtp']);
Router::post('/academy/_test/seed-sample-academies', [AcademyRegistrationController::class, 'seedSamples'])->middleware(['site-admin', 'csrf']);
Router::post('/academy/_test/delete-sample-academies', [AcademyRegistrationController::class, 'deleteSamples'])->middleware(['site-admin', 'csrf']);
Router::post('/academy/_test/seed-branch-network', [AcademyRegistrationController::class, 'seedBranchNetwork'])->middleware(['site-admin', 'csrf']);
Router::post('/academy/_test/delete-branch-network', [AcademyRegistrationController::class, 'deleteBranchNetwork'])->middleware(['site-admin', 'csrf']);
Router::get('/academy/admin/branches', [AcademyBranchController::class, 'index'])->middleware('academy-panel');
Router::post('/academy/admin/branches', [AcademyBranchController::class, 'store'])->middleware(['academy-panel', 'csrf']);
Router::post('/academy/admin/branches/types', [AcademyBranchController::class, 'storeType'])->middleware(['academy-panel', 'csrf']);
Router::post('/academy/admin/branch-types/{id}/update', [AcademyBranchController::class, 'updateType'])->middleware(['site-admin', 'csrf']);
Router::post('/academy/admin/branch-types/{id}/delete', [AcademyBranchController::class, 'deleteType'])->middleware(['site-admin', 'csrf']);
Router::post('/academy/admin/branches/{id}/update', [AcademyBranchController::class, 'update'])->middleware(['academy-panel', 'csrf']);
Router::post('/academy/admin/branches/{id}/delete', [AcademyBranchController::class, 'destroy'])->middleware(['academy-panel', 'csrf']);
Router::post('/academy/admin/members/{id}/update', [AcademyBranchController::class, 'updateMember'])->middleware(['academy-panel', 'csrf']);
Router::post('/academy/admin/members/{id}/delete', [AcademyBranchController::class, 'deleteMember'])->middleware(['academy-panel', 'csrf']);
Router::get('/academy/admin/classrooms', [AcademyClassroomController::class, 'index'])->middleware('academy-panel');
Router::post('/academy/admin/classrooms', [AcademyClassroomController::class, 'store'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/classrooms/{id}/update', [AcademyClassroomController::class, 'update'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/classrooms/{id}/delete', [AcademyClassroomController::class, 'delete'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/classroom-types', [AcademyClassroomController::class, 'storeType'])->middleware(['site-admin','csrf']);
Router::post('/academy/admin/classroom-types/{id}/update', [AcademyClassroomController::class, 'updateType'])->middleware(['site-admin','csrf']);
Router::post('/academy/admin/classroom-types/{id}/delete', [AcademyClassroomController::class, 'deleteType'])->middleware(['site-admin','csrf']);
Router::get('/academy/admin/branch-offerings', [AcademyBranchOfferingController::class, 'index'])->middleware('academy-panel');
Router::post('/academy/admin/branch-offerings/schedules', [AcademyBranchOfferingController::class, 'storeSchedule'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/branch-offerings/schedules/{id}/update', [AcademyBranchOfferingController::class, 'updateSchedule'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/branch-offerings/{type}/{id}/delete', [AcademyBranchOfferingController::class, 'delete'])->middleware(['academy-panel','csrf']);
Router::get('/academy/admin/courses', [AcademyCourseController::class, 'index'])->middleware('academy-panel');
Router::post('/academy/admin/courses', [AcademyCourseController::class, 'store'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/courses/{id}/update', [AcademyCourseController::class, 'update'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/courses/{id}/delete', [AcademyCourseController::class, 'destroy'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/course-levels', [AcademyCourseController::class, 'storeLevel'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/course-levels/{id}/update', [AcademyCourseController::class, 'updateLevel'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/course-levels/{id}/delete', [AcademyCourseController::class, 'deleteLevel'])->middleware(['academy-panel','csrf']);
Router::get('/academy/admin/terms', [AcademyTermController::class, 'index'])->middleware('academy-panel');
Router::get('/academy/admin/term-available-times', [AcademyTermAvailabilityController::class, 'index'])->middleware('academy-panel');
Router::get('/academy/admin/term-invoices', [AcademyTermController::class, 'invoices'])->middleware('academy-panel');
Router::post('/academy/admin/term-invoices/{id}/update', [AcademyTermController::class, 'updateInvoice'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/term-invoices/{id}/installments/{installmentId}/pay', [AcademyTermController::class, 'payInstallment'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/terms', [AcademyTermController::class, 'store'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/terms/{id}/update', [AcademyTermController::class, 'update'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/terms/{id}/delete', [AcademyTermController::class, 'destroy'])->middleware(['academy-panel','csrf']);
Router::post('/academy/admin/term-discounts', [AcademyTermController::class, 'storeDiscount'])->middleware(['academy-panel','csrf']);
Router::get('/academy/admin/class-schedules', [AcademyClassScheduleController::class, 'index'])->middleware('academy-panel');
Router::post('/academy/admin/class-schedules/{id}/attendance', [AcademyClassScheduleController::class, 'attendance'])->middleware(['academy-panel','csrf']);

Router::group(
    ['prefix' => '/academy'],
    function () {
        Router::get('/',            [AcademyController::class,'index']);
        Router::get('/create',      [AcademyController::class,'create']);
        Router::post('/',           [AcademyController::class,'store']);
        Router::get('/{id}',        [AcademyController::class,'show']);
        Router::get('/{id}/edit',   [AcademyController::class,'edit']);
        Router::put('/{id}',        [AcademyController::class,'update']);
        Router::delete('/{id}',     [AcademyController::class,'destroy']);
    }


);
