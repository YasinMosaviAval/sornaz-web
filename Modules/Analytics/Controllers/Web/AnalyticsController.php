<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminTestDataService;
use Modules\Analytics\Services\PublicPostService;
use Modules\System\Services\SiteAdminAccess;
use Modules\Analytics\Services\AdminGuideService;
use Modules\Analytics\Services\PublicCommentService;
use Modules\Academy\Services\PublicAcademyEnrollmentService;
use Modules\Academy\Services\AcademyRegistrationService;
use Modules\System\Services\UserService;

class AnalyticsController {

    public function __construct(protected AdminTestDataService $adminTests, protected PublicPostService $posts, protected AdminGuideService $guides, protected PublicCommentService $comments, protected PublicAcademyEnrollmentService $enrollments, protected AcademyRegistrationService $academies, protected UserService $users) {}




    public function articles() { return ResponseFactory::view('Analytics::articles', ['articles'=>$this->posts->all(locale())])->layout('main')->title('سُرناز | مقاله‌های آموزشی'); }
    public function articleDetails() {
        try { $article=$this->posts->find((int)($_GET['id']??0), locale()); }
        catch (\Throwable) { abort(404); }
        return ResponseFactory::view('Analytics::article-details', ['article'=>$article,'comments'=>$this->comments->forPost((int)($_GET['id']??0),locale())])->layout('main')->title(($article['title']?:'مقاله').' | سُرناز');
    }



    public function user() {$id=(int)($_GET['id']??0);$items=$this->users->publicDirectory();if($id<1||!array_filter($items,fn($x)=>(int)$x['id']===$id))abort(404);return ResponseFactory::view('Analytics::user',['users'=>$items,'selectedUserId'=>$id])->layout('main')->title(locale()==='en'?'User profile | Sornaz':'پروفایل کاربر | سُرناز');}
    public function users() { return ResponseFactory::view('Analytics::users')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function academy() {$id=(int)($_GET['id']??0);$items=$this->academies->all();if($id<1||!array_filter($items,fn($x)=>(int)$x['id']===$id))abort(404);$branches=(new \Modules\Analytics\Services\PublicRatingService())->branchesForAcademy($id,locale());return ResponseFactory::view('Analytics::academy',['academies'=>$items,'selectedAcademyId'=>$id,'publicBranches'=>$branches])->layout('main')->title(locale()==='en'?'Music academy | Sornaz':'آموزشگاه موسیقی | سُرناز');}
    public function academies() { return ResponseFactory::view('Analytics::academies')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function academyEnroll() {
        $academyId=(int)($_GET['academy']??0);if($academyId<1)abort(404);
        try{$data=$this->enrollments->formData($academyId,(int)auth()->id(),locale());}catch(\Throwable){abort(404);}
        $data['selectedTerm']=(int)($_GET['term']??0);return ResponseFactory::view('Analytics::academy-enroll',['enrollmentData'=>$data])->layout('main')->title(locale()==='en'?'Class registration | Sornaz':'ثبت‌نام در کلاس | سُرناز');
    }
    public function academyEnrollStore(){try{$academyId=(int)($_POST['academy_id']??0);$result=$this->enrollments->joinWaitingList($academyId,(int)auth()->id(),(int)($_POST['term_id']??0),(int)($_POST['level_id']??0),(string)($_POST['phone']??''),(string)($_POST['note']??''),locale());$message=locale()==='en'?'You were successfully added to the academy waiting list.':'با موفقیت در فهرست انتظار آموزشگاه قرار گرفتید.';session()->flash('auth_success',$message);return ResponseFactory::json(['success'=>true,'message'=>$message,'redirect'=>'/academy/academy?id='.$academyId,'data'=>$result],201);}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}}



    public function adminPanel() {
        $testStats = SiteAdminAccess::allows(auth()->user()) && env('APP_ENV', 'production') === 'local'
            ? $this->adminTests->statistics()
            : [];
        $scheduleFixtures = env('APP_ENV', 'production') === 'local' ? $this->adminTests->scheduleFixtures() : ['schedules'=>[],'exceptions'=>[]];
        $guides = SiteAdminAccess::allows(auth()->user()) ? $this->guides->all(locale()) : [];
        return ResponseFactory::view('Analytics::admin-panel', ['testStats' => $testStats, 'scheduleFixtures'=>$scheduleFixtures, 'guides'=>$guides, 'adminUiMap'=>$this->adminTests->adminUiMap(locale()),'inlineTranslationCatalog'=>$this->adminTests->inlineTranslationCatalog()])->layout('admin')->title('سُرناز | پنل کاربری');
    }












}
