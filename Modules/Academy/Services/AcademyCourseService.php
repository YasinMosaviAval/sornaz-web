<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

class AcademyCourseService
{
    public function bootstrap(int $actor): array
    {
        $this->ensureSchema();
        $branches = $this->branches($actor);
        $branchIds = array_column($branches, 'branch_id');
        $levels = DB::table('levels')->whereNull('deleted_at')->get();
        $courses = $branchIds ? DB::table('academy_branch_courses')->whereIn('branch_id', $branchIds)->whereNull('deleted_at')->get() : [];
        $lessonRows = [];
        foreach ($branches as $branch) {
            $rows = DB::table('user_lessons')->where('user_id', (int)$branch['user_id'])->whereNull('deleted_at')->get();
            foreach ($rows as $row) $lessonRows[] = $row + ['branch_id' => (int)$branch['branch_id']];
        }
        $branchNames = $this->translations('academy_branches', $branchIds, ['name']);
        $levelIds = array_map(fn($r)=>(int)$r['level_id'], $levels);
        $levelTexts = $this->translations('levels', $levelIds, ['title','summary','description']);
        $lessonIds = array_values(array_unique(array_map(fn($r)=>(int)$r['lesson_id'], $lessonRows)));
        $lessonTexts = $this->translations('lessons', $lessonIds, ['title']);
        $courseIds = array_map(fn($r)=>(int)$r['course_id'], $courses);
        $courseTexts = $this->translations('academy_branch_courses', $courseIds, ['title','summary','description','teacher']);
        $branchMap=[]; foreach($branches as $b){$id=(int)$b['branch_id'];$branchMap[$id]=$branchNames[$id]['name']??('شعبه '.$id);}
        $levelMap=[];$levelItems=[];foreach($levels as $l){$id=(int)$l['level_id'];$levelMap[$id]=$levelTexts[$id]['title']??('سطح '.$id);$levelItems[]=['id'=>$id,'name'=>$levelMap[$id],'summary'=>$levelTexts[$id]['summary']??'','description'=>$levelTexts[$id]['description']??''];}
        $lessonItems=[];foreach($lessonRows as $l){$lessonItems[]=['id'=>(int)$l['lesson_id'],'branchId'=>(int)$l['branch_id'],'name'=>$lessonTexts[(int)$l['lesson_id']]['title']??('درس '.$l['lesson_id'])];}
        $status=['pending'=>'در انتظار','open'=>'باز','ongoing'=>'در حال برگزاری','finished'=>'پایان‌یافته'];
        return [
            'branches'=>array_map(fn($b)=>['id'=>(int)$b['branch_id'],'name'=>$branchMap[(int)$b['branch_id']]],$branches),
            'levels'=>$levelItems,
            'lessons'=>$lessonItems,
            'courses'=>array_map(function($c)use($branchMap,$levelMap,$lessonTexts,$courseTexts,$status){$id=(int)$c['course_id'];$lessonId=(int)($c['lesson_id']??0);return ['id'=>$id,'name'=>$courseTexts[$id]['title']??('دوره '.$id),'summary'=>$courseTexts[$id]['summary']??'','description'=>$courseTexts[$id]['description']??'','teacher'=>$courseTexts[$id]['teacher']??'','level_id'=>(int)$c['level_id'],'level'=>$levelMap[(int)$c['level_id']]??'—','branchId'=>(int)$c['branch_id'],'branchName'=>$branchMap[(int)$c['branch_id']]??'—','lesson_id'=>$lessonId,'instrument'=>$lessonTexts[$lessonId]['title']??'—','capacity'=>(int)$c['capacity'],'enrolled'=>0,'status'=>$status[$c['status']]??'در انتظار','status_code'=>$c['status']];},$courses),
        ];
    }

    public function saveCourse(int $actor,array $data,int $id=0): array
    {
        $this->ensureSchema();$branch=$this->allowedBranch($actor,(int)($data['branchId']??0));
        $lessonId=(int)($data['lesson_id']??0);$lesson=DB::table('user_lessons')->where('user_id',(int)$branch['user_id'])->where('lesson_id',$lessonId)->whereNull('deleted_at')->first();
        if(!$lesson)throw new RuntimeException('درس انتخاب‌شده متعلق به شعبه نیست.');
        $levelId=(int)($data['level_id']??0);if(!DB::table('levels')->where('level_id',$levelId)->whereNull('deleted_at')->first())throw new RuntimeException('سطح دوره معتبر نیست.');
        $title=trim((string)($data['name']??''));if($title==='')throw new RuntimeException('نام دوره الزامی است.');
        $statuses=['در انتظار'=>'pending','باز'=>'open','در حال برگزاری'=>'ongoing','پایان‌یافته'=>'finished','pending'=>'pending','open'=>'open','ongoing'=>'ongoing','finished'=>'finished'];
        $values=['branch_id'=>(int)$branch['branch_id'],'lesson_id'=>$lessonId,'level_id'=>$levelId,'capacity'=>max(1,(int)($data['capacity']??1)),'status'=>$statuses[$data['status']??'pending']??'pending','updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];
        return transaction(function()use($actor,$data,$id,$values,$title){if($id){$row=DB::table('academy_branch_courses')->where('course_id',$id)->whereNull('deleted_at')->first();if(!$row)throw new RuntimeException('دوره یافت نشد.');$this->allowedBranch($actor,(int)$row['branch_id']);DB::table('academy_branch_courses')->where('course_id',$id)->update($values);}else$id=DB::table('academy_branch_courses')->insertGetId(['created_at'=>date('Y-m-d H:i:s'),'created_by'=>$actor]+$values);$this->setTranslations('academy_branch_courses',$id,['title'=>$title,'summary'=>trim((string)($data['summary']??'')),'description'=>trim((string)($data['description']??'')),'teacher'=>trim((string)($data['teacher']??''))],$actor);return ['id'=>$id];});
    }

    public function saveLevel(int $actor,array $data,int $id=0): array
    {
        $title=trim((string)($data['name']??$data['title']??''));if($title==='')throw new RuntimeException('عنوان سطح الزامی است.');$now=date('Y-m-d H:i:s');
        return transaction(function()use($actor,$data,$id,$title,$now){$values=['type'=>'learning','sort_order'=>max(1,(int)($data['sort_order']??999)),'updated_at'=>$now,'updated_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];if($id){if(!DB::table('levels')->where('level_id',$id)->whereNull('deleted_at')->first())throw new RuntimeException('سطح یافت نشد.');DB::table('levels')->where('level_id',$id)->update($values);}else$id=DB::table('levels')->insertGetId(['created_at'=>$now,'created_by'=>$actor]+$values);$this->setTranslations('levels',$id,['title'=>$title,'summary'=>trim((string)($data['summary']??'')),'description'=>trim((string)($data['description']??''))],$actor);return ['id'=>$id];});
    }

    public function deleteCourse(int $actor,int $id): void {$row=DB::table('academy_branch_courses')->where('course_id',$id)->whereNull('deleted_at')->first();if(!$row)throw new RuntimeException('دوره یافت نشد.');$this->allowedBranch($actor,(int)$row['branch_id']);$this->softDelete('academy_branch_courses','course_id',$id,$actor);}
    public function deleteLevel(int $actor,int $id): void {if(DB::table('academy_branch_courses')->where('level_id',$id)->whereNull('deleted_at')->count())throw new RuntimeException('این سطح توسط دوره فعال استفاده می‌شود.');$this->softDelete('levels','level_id',$id,$actor);}

    public function seedCourses(int $actor): array
    {
        $this->ensureSchema();$created=0;$updated=0;foreach(DB::table('academy_branches')->whereNull('deleted_at')->get() as $bi=>$branch){$lessons=DB::table('user_lessons')->where('user_id',(int)$branch['user_id'])->whereNull('deleted_at')->get();$levels=DB::table('levels')->whereNull('deleted_at')->get();if(!$levels)continue;if(!$lessons){$catalog=DB::table('lessons')->whereNull('deleted_at')->first();if(!$catalog)throw new RuntimeException('برای ساخت دوره، حداقل یک درس عمومی لازم است.');$uid=(int)$branch['user_id'];$lessonRowId=DB::table('user_lessons')->insertGetId(['user_id'=>$uid,'lesson_id'=>(int)$catalog['lesson_id'],'level_id'=>(int)$levels[0]['level_id'],'start_date'=>'1400-01-01','is_primary'=>1,'created_by'=>$actor,'updated_by'=>$actor]);$this->setTranslations('user_lessons',$lessonRowId,['code'=>'test-course-prerequisite','summary'=>'درس پایه تست دوره‌ها','description'=>'این درس فقط برای تکمیل پیش‌نیاز تست دوره‌های شعب ایجاد شده است.'],$actor);$this->setTranslations('user_lessons',$lessonRowId,['code'=>'test-course-prerequisite','summary'=>'Course test prerequisite lesson','description'=>'This lesson exists only as a prerequisite for the branch course fixture test.'],$actor,'en');$lessons=DB::table('user_lessons')->where('user_lesson_id',$lessonRowId)->get();}$count=10+(($bi*17)%41);for($i=1;$i<=$count;$i++){$lesson=$lessons[($i+$bi)%count($lessons)];$level=$levels[($i*3+$bi)%count($levels)];$marker='test-course-b'.(int)$branch['branch_id'].'-'.$i;$tr=DB::table('translations')->where('table_name','academy_branch_courses')->where('field','code')->where('locale','fa')->where('value',$marker)->first();$values=['branch_id'=>(int)$branch['branch_id'],'lesson_id'=>(int)$lesson['lesson_id'],'level_id'=>(int)$level['level_id'],'capacity'=>8+(($i+$bi)%23),'status'=>['pending','open','ongoing','finished'][($i+$bi)%4],'updated_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];if($tr){$id=(int)$tr['table_id'];DB::table('academy_branch_courses')->where('course_id',$id)->update($values);$updated++;}else{$id=DB::table('academy_branch_courses')->insertGetId(['created_by'=>$actor]+$values);$created++;}$lessonTitle=$this->translation('lessons',(int)$lesson['lesson_id'],'title')?:'موسیقی';$this->setTranslations('academy_branch_courses',$id,['code'=>$marker,'title'=>'دوره '.$lessonTitle.' '.($i),'summary'=>'دوره آموزشی '.$lessonTitle.' در سطح انتخاب‌شده.','description'=>'این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.'],$actor);$this->setTranslations('academy_branch_courses',$id,['code'=>$marker,'title'=>'Music course '.$i,'summary'=>'A branch music course at the selected learning level.','description'=>'This fixture course is based on the lessons offered by its branch.'],$actor,'en');}}return ['created'=>$created,'updated'=>$updated,'total'=>DB::table('translations')->where('table_name','academy_branch_courses')->where('field','code')->whereRaw("value LIKE 'test-course-b%'")->whereNull('deleted_at')->count()];
    }
    public function deleteSeedCourses(int $actor): array {$rows=DB::table('translations')->where('table_name','academy_branch_courses')->where('field','code')->whereRaw("value LIKE 'test-course-b%'")->get();$ids=array_values(array_unique(array_map(fn($r)=>(int)$r['table_id'],$rows)));if($ids){DB::table('translations')->where('table_name','academy_branch_courses')->whereIn('table_id',$ids)->delete();DB::table('academy_branch_courses')->whereIn('course_id',$ids)->delete();}$pr=DB::table('translations')->where('table_name','user_lessons')->where('field','code')->where('value','test-course-prerequisite')->get();$pids=array_values(array_unique(array_map(fn($r)=>(int)$r['table_id'],$pr)));if($pids){DB::table('translations')->where('table_name','user_lessons')->whereIn('table_id',$pids)->delete();DB::table('user_lessons')->whereIn('user_lesson_id',$pids)->delete();}return ['deleted'=>count($ids)];}

    private function branches(int $actor): array {$user=DB::table('users')->where('user_id',$actor)->whereNull('deleted_at')->first();if(!$user)throw new RuntimeException('کاربر معتبر نیست.');if(SiteAdminAccess::allows($user))return DB::table('academy_branches')->whereNull('deleted_at')->get();if(($user['type']??'')==='branch')return DB::table('academy_branches')->where('user_id',$actor)->whereNull('deleted_at')->get();$academies=array_merge(DB::table('academies')->where('user_id',$actor)->whereNull('deleted_at')->get(),DB::table('academies')->where('created_by',$actor)->whereNull('deleted_at')->get());$ids=array_values(array_unique(array_map(fn($a)=>(int)$a['academy_id'],$academies)));return $ids?DB::table('academy_branches')->whereIn('academy_id',$ids)->whereNull('deleted_at')->get():[];}
    private function allowedBranch(int $actor,int $id): array {foreach($this->branches($actor) as $b)if((int)$b['branch_id']===$id)return$b;throw new RuntimeException('شما به این شعبه دسترسی ندارید.');}
    private function translations(string $table,array $ids,array $fields): array {if(!$ids)return[];$map=[];foreach(DB::table('translations')->where('table_name',$table)->whereIn('table_id',array_values(array_unique($ids)))->whereIn('field',$fields)->where('locale','fa')->whereNull('deleted_at')->get() as $r)$map[(int)$r['table_id']][$r['field']]=$r['value'];return$map;}
    private function translation(string $table,int $id,string $field): string {$r=DB::table('translations')->where('table_name',$table)->where('table_id',$id)->where('field',$field)->where('locale','fa')->whereNull('deleted_at')->first();return(string)($r['value']??'');}
    private function setTranslations(string $table,int $id,array $values,int $actor,string $locale='fa'): void {foreach($values as $field=>$value){$r=DB::table('translations')->where('table_name',$table)->where('table_id',$id)->where('field',$field)->where('locale',$locale)->first();$v=['value'=>$value,'version'=>1,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];if($r)DB::table('translations')->where('translation_id',(int)$r['translation_id'])->update($v);else DB::table('translations')->insert(['table_name'=>$table,'table_id'=>$id,'field'=>$field,'locale'=>$locale,'created_by'=>$actor]+$v);}}
    private function softDelete(string $table,string $key,int $id,int $actor): void {$now=date('Y-m-d H:i:s');DB::table($table)->where($key,$id)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$actor,'updated_by'=>$actor]);DB::table('translations')->where('table_name',$table)->where('table_id',$id)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$actor,'updated_by'=>$actor]);}
    private function ensureSchema(): void {$q=db()->query("SHOW COLUMNS FROM academy_branch_courses LIKE 'lesson_id'");if(!$q->fetch())db()->exec("ALTER TABLE academy_branch_courses ADD COLUMN lesson_id BIGINT UNSIGNED NULL AFTER branch_id, ADD INDEX idx_course_lesson (lesson_id)");}
}
