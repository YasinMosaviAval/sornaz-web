<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminTestDataService;
use Modules\Academy\Services\AcademyCourseService;
use Modules\Academy\Services\AcademyTermTestService;
use Modules\Academy\Services\AcademyTermBatchService;
use Modules\Academy\Services\AcademyRegistrationService;

class AdminTestController {
    public function __construct(protected AdminTestDataService $tests, protected AcademyCourseService $courses, protected AcademyTermTestService $terms, protected AcademyTermBatchService $termBatch, protected AcademyRegistrationService $academies) {}

    public function deleteAllTestData() {
        if (env('APP_ENV','production') !== 'local') abort(404);
        try {
            $actor=(int)auth()->id();
            $terms=$this->terms->delete();
            $courses=$this->courses->deleteSeedCourses($actor);
            $network=$this->academies->deleteBranchNetwork();
            $academies=$this->academies->deleteSamples();
            $managers=$this->tests->deleteAcademyManagers();
            $total=(int)($terms['deleted']??0)+(int)($courses['deleted']??0)+(int)($network['deleted']??0)+(int)($academies['deleted']??0)+(int)($managers['deleted']??0);
            session()->flash('admin_test_message',"پاک‌سازی کامل تست‌ها انجام شد؛ {$total} رکورد اصلی آزمایشی به‌همراه تمام وابستگی‌هایشان حذف شدند.");
        } catch (\Throwable $e) {
            session()->flash('admin_test_error','پاک‌سازی کامل اطلاعات تستی ناموفق بود: '.$e->getMessage());
        }
        return redirect('/analytics/admin-panel#tests');
    }

    public function seedBranchTerms(){if(env('APP_ENV','production')!=='local')abort(404);try{$r=$this->terms->seed((int)auth()->id(),[(int)($_POST['terms_min']??1),(int)($_POST['terms_max']??50)],[(int)($_POST['sessions_min']??4),(int)($_POST['sessions_max']??8)]);session()->flash('admin_test_message',"تست ترم‌ها تکمیل شد: {$r['created']} ترم، {$r['sessions']} جلسه و {$r['attendance']} حضور و غیاب ایجاد شد.");}catch(\Throwable$e){session()->flash('admin_test_error','ایجاد ترم‌های آزمایشی ناموفق بود: '.$e->getMessage());}return redirect('/analytics/admin-panel#tests');}
    public function deleteBranchTerms(){if(env('APP_ENV','production')!=='local')abort(404);try{$r=$this->terms->delete();session()->flash('admin_test_message',"{$r['deleted']} ترم آزمایشی و تمام وابستگی‌های آن حذف شد.");}catch(\Throwable$e){session()->flash('admin_test_error','حذف ترم‌های آزمایشی ناموفق بود: '.$e->getMessage());}return redirect('/analytics/admin-panel#tests');}

    public function seedBranchCourses() {if(env('APP_ENV','production')!=='local')abort(404);try{$r=$this->courses->seedCourses((int)auth()->id(),(int)($_POST['courses_min']??10),(int)($_POST['courses_max']??50));session()->flash('admin_test_message',"تست دوره‌ها تکمیل شد: {$r['created']} ایجاد و {$r['updated']} همگام‌سازی شد.");}catch(\Throwable$e){session()->flash('admin_test_error','ایجاد دوره‌های آزمایشی ناموفق بود: '.$e->getMessage());}return redirect('/analytics/admin-panel#tests');}
    public function deleteBranchCourses() {if(env('APP_ENV','production')!=='local')abort(404);try{$r=$this->courses->deleteSeedCourses((int)auth()->id());session()->flash('admin_test_message',"{$r['deleted']} دوره آزمایشی حذف شد.");}catch(\Throwable$e){session()->flash('admin_test_error','حذف دوره‌های آزمایشی ناموفق بود: '.$e->getMessage());}return redirect('/analytics/admin-panel#tests');}

    public function seedAcademyManagers() {
        if (env('APP_ENV', 'production') !== 'local') abort(404);
        try {
            $result = $this->tests->seedAcademyManagers(max(1, min(50, (int)($_POST['manager_count'] ?? 10))));
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
