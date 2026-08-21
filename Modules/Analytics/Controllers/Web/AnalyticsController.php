<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminTestDataService;
use Modules\Analytics\Services\PublicPostService;
use Modules\System\Services\SiteAdminAccess;
use Modules\Analytics\Services\AdminGuideService;

class AnalyticsController {

    public function __construct(protected AdminTestDataService $adminTests, protected PublicPostService $posts, protected AdminGuideService $guides) {}




    public function articles() { return ResponseFactory::view('Analytics::articles', ['articles'=>$this->posts->all(locale())])->layout('main')->title('سُرناز | مقاله‌های آموزشی'); }
    public function articleDetails() {
        try { $article=$this->posts->find((int)($_GET['id']??0), locale()); }
        catch (\Throwable) { abort(404); }
        return ResponseFactory::view('Analytics::article-details', ['article'=>$article])->layout('main')->title(($article['title']?:'مقاله').' | سُرناز');
    }



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
        $guides = SiteAdminAccess::allows(auth()->user()) ? $this->guides->all(locale()) : [];
        return ResponseFactory::view('Analytics::admin-panel', ['testStats' => $testStats, 'scheduleFixtures'=>$scheduleFixtures, 'guides'=>$guides, 'adminUiMap'=>$this->adminTests->adminUiMap(locale()),'inlineTranslationCatalog'=>$this->adminTests->inlineTranslationCatalog()])->layout('admin')->title('سُرناز | پنل مدیریت');
    }












}
