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
            $resetTables=$this->resetAutoIncrements();
            $total=(int)($terms['deleted']??0)+(int)($courses['deleted']??0)+(int)($network['deleted']??0)+(int)($academies['deleted']??0)+(int)($managers['deleted']??0);
            session()->flash('admin_test_message',"پاک‌سازی کامل تست‌ها انجام شد؛ {$total} رکورد اصلی آزمایشی حذف و شمارنده شناسه {$resetTables} جدول بازتنظیم شد.");
        } catch (\Throwable $e) {
            session()->flash('admin_test_error','پاک‌سازی کامل اطلاعات تستی ناموفق بود: '.$e->getMessage());
        }
        return redirect('/analytics/admin-panel#tests');
    }

    public function runAllTests() {
        if(env('APP_ENV','production')!=='local')abort(404);
        try{$actor=(int)auth()->id();$report=[];
            $managerOptions=['addresses_min'=>(int)($_POST['addresses_min']??1),'addresses_max'=>(int)($_POST['addresses_max']??3),'contact_phone_min'=>(int)($_POST['contact_phone_min']??1),'contact_phone_max'=>(int)($_POST['contact_phone_max']??2),'contact_email_min'=>(int)($_POST['contact_email_min']??1),'contact_email_max'=>(int)($_POST['contact_email_max']??2),'contact_social_min'=>(int)($_POST['contact_social_min']??0),'contact_social_max'=>(int)($_POST['contact_social_max']??6),'instruments_min'=>(int)($_POST['instruments_min']??0),'instruments_max'=>(int)($_POST['instruments_max']??5),'lessons_min'=>(int)($_POST['lessons_min']??0),'lessons_max'=>(int)($_POST['lessons_max']??5),'gallery_min'=>(int)($_POST['gallery_min']??3),'gallery_max'=>(int)($_POST['gallery_max']??3),'daily_slots_min'=>(int)($_POST['daily_slots_min']??1),'daily_slots_max'=>(int)($_POST['daily_slots_max']??3),'exceptions_min'=>(int)($_POST['exceptions_min']??2),'exceptions_max'=>(int)($_POST['exceptions_max']??4)];
            $rootCount=max(1,min(50,(int)($_POST['manager_count']??10)));$a=$this->tests->seedAcademyManagers($rootCount,$managerOptions);$b=$this->academies->seedSamples($rootCount);$report[]=['title'=>'مدیران، آموزشگاه‌ها و شعب اصلی','items'=>["مدیر ایجادشده: {$a['created']}","مدیر همگام‌شده: {$a['updated']}","آموزشگاه ایجادشده: {$b['created']}","آموزشگاه همگام‌شده: {$b['updated']}","شعب اصلی پردازش‌شده: {$b['branches_created']}","تعداد هدف مشترک: {$rootCount}"]];
            $networkOptions=[];foreach(['branches','teachers','receptionists','employees','managers','students']as$key){$networkOptions[$key.'_min']=(int)($_POST[$key.'_min']??0);$networkOptions[$key.'_max']=(int)($_POST[$key.'_max']??5);}$c=$this->academies->seedBranchNetwork($networkOptions);$report[]=['title'=>'شبکه شعب و اعضا','items'=>["شعب فرعی: {$c['branches']}","پرسنل: {$c['staff']}","هنرجویان: {$c['students']}","قراردادها: {$c['contracts']}","کلاس‌ها: {$c['classrooms']}"]];
            $d=$this->courses->seedCourses($actor,(int)($_POST['courses_min']??10),(int)($_POST['courses_max']??50));$report[]=['title'=>'دوره‌ها','items'=>["ایجاد: {$d['created']}","همگام‌سازی: {$d['updated']}","مجموع: {$d['total']}"]];
            $e=$this->terms->seed($actor,[(int)($_POST['terms_min']??1),(int)($_POST['terms_max']??50)],[(int)($_POST['sessions_min']??4),(int)($_POST['sessions_max']??8)]);$report[]=['title'=>'ترم‌ها و جلسات','items'=>["ترم‌ها: {$e['created']}","جلسات: {$e['sessions']}","رکوردهای حضور و غیاب: {$e['attendance']}"]];
            session()->flash('admin_test_report',$report);
        }catch(\Throwable$e){session()->flash('admin_test_error','اجرای تست یکپارچه ناموفق بود: '.$e->getMessage());}
        return redirect('/analytics/admin-panel#tests');
    }

    private function resetAutoIncrements(): int {
        $pdo=db();
        $columns=$pdo->query("SELECT TABLE_NAME,COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND EXTRA LIKE '%auto_increment%' ORDER BY TABLE_NAME")->fetchAll();
        $reset=0;
        foreach($columns as $column){
            $table=(string)$column['TABLE_NAME'];$key=(string)$column['COLUMN_NAME'];
            if(!preg_match('/^[A-Za-z0-9_]+$/',$table)||!preg_match('/^[A-Za-z0-9_]+$/',$key))continue;
            $next=(int)$pdo->query("SELECT COALESCE(MAX(`{$key}`),0)+1 FROM `{$table}`")->fetchColumn();
            $pdo->exec("ALTER TABLE `{$table}` AUTO_INCREMENT = ".max(1,$next));
            $reset++;
        }
        return $reset;
    }

    public function seedCoursesAndTerms(){
        if(env('APP_ENV','production')!=='local')abort(404);
        try{$actor=(int)auth()->id();$courses=$this->courses->seedCourses($actor,(int)($_POST['courses_min']??10),(int)($_POST['courses_max']??50));$terms=$this->terms->seed($actor,[(int)($_POST['terms_min']??1),(int)($_POST['terms_max']??50)],[(int)($_POST['sessions_min']??4),(int)($_POST['sessions_max']??8)]);session()->flash('admin_test_report',[['title'=>'دوره‌ها، ترم‌ها و جلسات','items'=>["دوره ایجادشده: {$courses['created']}","دوره همگام‌شده: {$courses['updated']}","مجموع دوره‌ها: {$courses['total']}","ترم ایجادشده: {$terms['created']}","جلسه ایجادشده: {$terms['sessions']}","حضور و غیاب ثبت‌شده: {$terms['attendance']}"]]]);}catch(\Throwable$e){session()->flash('admin_test_error','ایجاد دوره‌ها و ترم‌های آزمایشی ناموفق بود: '.$e->getMessage());}return redirect('/analytics/admin-panel#tests');
    }

    public function seedBranchTerms(){if(env('APP_ENV','production')!=='local')abort(404);try{$r=$this->terms->seed((int)auth()->id(),[(int)($_POST['terms_min']??1),(int)($_POST['terms_max']??50)],[(int)($_POST['sessions_min']??4),(int)($_POST['sessions_max']??8)]);session()->flash('admin_test_message',"تست ترم‌ها تکمیل شد: {$r['created']} ترم، {$r['sessions']} جلسه و {$r['attendance']} حضور و غیاب ایجاد شد.");}catch(\Throwable$e){session()->flash('admin_test_error','ایجاد ترم‌های آزمایشی ناموفق بود: '.$e->getMessage());}return redirect('/analytics/admin-panel#tests');}
    public function deleteBranchTerms(){if(env('APP_ENV','production')!=='local')abort(404);try{$r=$this->terms->delete();session()->flash('admin_test_message',"{$r['deleted']} ترم آزمایشی و تمام وابستگی‌های آن حذف شد.");}catch(\Throwable$e){session()->flash('admin_test_error','حذف ترم‌های آزمایشی ناموفق بود: '.$e->getMessage());}return redirect('/analytics/admin-panel#tests');}

    public function seedBranchCourses() {if(env('APP_ENV','production')!=='local')abort(404);try{$r=$this->courses->seedCourses((int)auth()->id(),(int)($_POST['courses_min']??10),(int)($_POST['courses_max']??50));session()->flash('admin_test_message',"تست دوره‌ها تکمیل شد: {$r['created']} ایجاد و {$r['updated']} همگام‌سازی شد.");}catch(\Throwable$e){session()->flash('admin_test_error','ایجاد دوره‌های آزمایشی ناموفق بود: '.$e->getMessage());}return redirect('/analytics/admin-panel#tests');}
    public function deleteBranchCourses() {if(env('APP_ENV','production')!=='local')abort(404);try{$r=$this->courses->deleteSeedCourses((int)auth()->id());session()->flash('admin_test_message',"{$r['deleted']} دوره آزمایشی حذف شد.");}catch(\Throwable$e){session()->flash('admin_test_error','حذف دوره‌های آزمایشی ناموفق بود: '.$e->getMessage());}return redirect('/analytics/admin-panel#tests');}

    public function seedAcademyManagers() {
        if (env('APP_ENV', 'production') !== 'local') abort(404);
        try {
            $result = $this->tests->seedAcademyManagers(max(1,min(50,(int)($_POST['manager_count']??10))),[
                'addresses_min'=>(int)($_POST['addresses_min']??1),'addresses_max'=>(int)($_POST['addresses_max']??3),
                'contact_phone_min'=>(int)($_POST['contact_phone_min']??1),'contact_phone_max'=>(int)($_POST['contact_phone_max']??2),
                'contact_email_min'=>(int)($_POST['contact_email_min']??1),'contact_email_max'=>(int)($_POST['contact_email_max']??2),
                'contact_social_min'=>(int)($_POST['contact_social_min']??0),'contact_social_max'=>(int)($_POST['contact_social_max']??6),
                'instruments_min'=>(int)($_POST['instruments_min']??0),'instruments_max'=>(int)($_POST['instruments_max']??5),
                'lessons_min'=>(int)($_POST['lessons_min']??0),'lessons_max'=>(int)($_POST['lessons_max']??5),
                'gallery_min'=>(int)($_POST['gallery_min']??3),'gallery_max'=>(int)($_POST['gallery_max']??3),
                'daily_slots_min'=>(int)($_POST['daily_slots_min']??1),'daily_slots_max'=>(int)($_POST['daily_slots_max']??3),
                'exceptions_min'=>(int)($_POST['exceptions_min']??2),'exceptions_max'=>(int)($_POST['exceptions_max']??4),
            ]);
            $academyResult=$this->academies->seedSamples((int)$result['total']);
            session()->flash('admin_test_report', [['title'=>'مدیران، آموزشگاه‌ها و شعب اصلی','items'=>["مدیر ایجادشده: {$result['created']}","مدیر همگام‌شده: {$result['updated']}","آموزشگاه ایجادشده: {$academyResult['created']}","آموزشگاه همگام‌شده: {$academyResult['updated']}","شعب اصلی: {$academyResult['branches_created']}","تعداد هدف مشترک: {$result['total']}"]]]);
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
