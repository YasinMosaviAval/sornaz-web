<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminTestDataService;
use Modules\System\Services\SiteAdminAccess;

class AnalyticsController {

    public function __construct(protected AdminTestDataService $adminTests) {}




    public function articles() { return ResponseFactory::view('Analytics::articles')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function articleDetails() { return ResponseFactory::view('Analytics::article-details')->layout('main')->title('سُرناز | صفحه اصلی'); }



    public function user() { return ResponseFactory::view('Analytics::user')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function users() { return ResponseFactory::view('Analytics::users')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function academy() { return ResponseFactory::view('Analytics::academy')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function academies() { return ResponseFactory::view('Analytics::academies')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function academyEnroll() { return ResponseFactory::view('Analytics::academy-enroll')->layout('main')->title('سُرناز | صفحه اصلی'); }



    public function adminPanel() {
        $testStats = SiteAdminAccess::allows(auth()->user()) && env('APP_ENV', 'production') === 'local'
            ? $this->adminTests->statistics()
            : [];
        $scheduleFixtures = env('APP_ENV', 'production') === 'local' ? $this->adminTests->scheduleFixtures() : ['schedules'=>[],'exceptions'=>[]];
        return ResponseFactory::view('Analytics::admin-panel', ['testStats' => $testStats, 'scheduleFixtures'=>$scheduleFixtures])->layout('admin')->title('سُرناز | پنل مدیریت');
    }












}
