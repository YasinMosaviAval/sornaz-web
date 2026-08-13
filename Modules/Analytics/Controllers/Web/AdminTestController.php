<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminTestDataService;
use Modules\Academy\Services\AcademyCourseService;

class AdminTestController {
    public function __construct(protected AdminTestDataService $tests, protected AcademyCourseService $courses) {}

    public function seedBranchCourses() {if(env('APP_ENV','production')!=='local')abort(404);try{$r=$this->courses->seedCourses((int)auth()->id());session()->flash('admin_test_message',"تست دوره‌ها تکمیل شد: {$r['created']} ایجاد و {$r['updated']} همگام‌سازی شد.");}catch(\Throwable$e){session()->flash('admin_test_error','ایجاد دوره‌های آزمایشی ناموفق بود: '.$e->getMessage());}return redirect('/analytics/admin-panel#tests');}
    public function deleteBranchCourses() {if(env('APP_ENV','production')!=='local')abort(404);try{$r=$this->courses->deleteSeedCourses((int)auth()->id());session()->flash('admin_test_message',"{$r['deleted']} دوره آزمایشی حذف شد.");}catch(\Throwable$e){session()->flash('admin_test_error','حذف دوره‌های آزمایشی ناموفق بود: '.$e->getMessage());}return redirect('/analytics/admin-panel#tests');}

    public function seedAcademyManagers() {
        if (env('APP_ENV', 'production') !== 'local') abort(404);
        try {
            $result = $this->tests->seedAcademyManagers();
            session()->flash('admin_test_message', $result['message']);
        } catch (\Throwable $e) {
            session()->flash('admin_test_error', 'ایجاد مدیران آموزشگاه آزمایشی ناموفق بود: ' . $e->getMessage());
        }
        return redirect('/analytics/admin-panel#tests');
    }

    public function deleteAcademyManagers() {
        if (env('APP_ENV', 'production') !== 'local') abort(404);
        try {
            $result = $this->tests->deleteAcademyManagers();
            session()->flash('admin_test_message', $result['message']);
        } catch (\Throwable $e) {
            session()->flash('admin_test_error', 'حذف مدیران آموزشگاه آزمایشی ناموفق بود: ' . $e->getMessage());
        }
        return redirect('/analytics/admin-panel#tests');
    }

    public function deleteAvailability(string $id) {
        try { return ResponseFactory::json($this->tests->deleteAvailability((int)$id, (int)auth()->id())); }
        catch (\Throwable $e) { return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],404); }
    }

    public function deleteAvailabilityException(string $id) {
        try { return ResponseFactory::json($this->tests->deleteAvailabilityException((int)$id, (int)auth()->id())); }
        catch (\Throwable $e) { return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],404); }
    }

    public function saveInlineTranslation() {
        try{return ResponseFactory::json($this->tests->saveInlineTranslation((string)($_POST['key']??''),(string)($_POST['fa']??''),(string)($_POST['en']??''),(int)auth()->id()));}
        catch(\Throwable $e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}
    }

    public function inlineTranslations() {
        return ResponseFactory::json(['success'=>true,'translations'=>$this->tests->inlineTranslationCatalog()]);
    }
}
