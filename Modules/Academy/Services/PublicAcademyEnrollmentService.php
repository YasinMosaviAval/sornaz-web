<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use RuntimeException;

class PublicAcademyEnrollmentService
{
    public function formData(int $academyId, int $userId, string $locale): array
    {
        $academy=$this->academy($academyId);$locale=$locale==='en'?'en':'fa';
        $rows=DB::table('academy_branch_course_terms')
            ->join('academy_branch_courses','academy_branch_courses.course_id','=','academy_branch_course_terms.course_id')
            ->leftJoin('academy_branches','academy_branches.branch_id','=','academy_branch_courses.branch_id')
            ->select('academy_branch_course_terms.*','academy_branch_courses.lesson_id','academy_branch_courses.branch_id','academy_branch_courses.course_id','academy_branch_courses.academy_id','academy_branches.user_id as branch_user_id')
            ->where('academy_branch_courses.academy_id',$academyId)->whereIn('academy_branch_course_terms.status',['open','ongoing'])
            ->whereNull('academy_branch_course_terms.deleted_at')->whereNull('academy_branch_courses.deleted_at')->orderBy('academy_branch_course_terms.term_id','DESC')->get();
        $terms=[];
        foreach($rows as$row){
            $termId=(int)$row['term_id'];$courseId=(int)$row['course_id'];$lessonId=(int)$row['lesson_id'];
            $courseTitle=$this->tr('academy_branch_courses',$courseId,'title',$locale)?:$this->tr('lessons',$lessonId,'title',$locale);
            $termTitle=$this->tr('academy_branch_course_terms',$termId,'title',$locale);
            $organizationUserId=(int)($row['branch_user_id']?:$academy['user_id']);
            $levelIds=array_values(array_unique(array_map('intval',array_column(DB::table('user_lessons')->select('level_id')->where('user_id',$organizationUserId)->where('lesson_id',$lessonId)->where('status','active')->whereNull('deleted_at')->get(),'level_id'))));
            $levels=[];foreach($levelIds as$levelId){if(!$levelId)continue;$level=DB::table('levels')->where('level_id',$levelId)->where('type','learning')->where('is_active',1)->whereNull('deleted_at')->first();if($level)$levels[]=['id'=>$levelId,'title'=>$this->tr('levels',$levelId,'title',$locale)?:($locale==='en'?'Level '.$levelId:'سطح '.$levelId)];}
            $terms[]=['id'=>$termId,'title'=>trim(($courseTitle?:($locale==='en'?'Course '.$courseId:'دوره '.$courseId)).($termTitle?' — '.$termTitle:'')),'levels'=>$levels];
        }
        $user=DB::table('users')->where('user_id',$userId)->whereNull('deleted_at')->first();
        return['academy'=>['id'=>$academyId,'title'=>$this->tr('academies',$academyId,'title',$locale)?:($locale==='en'?'Academy '.$academyId:'آموزشگاه '.$academyId)],'terms'=>$terms,'needsPhone'=>(string)($user['register_method']??'')!=='phone'];
    }

    public function joinWaitingList(int $academyId,int $userId,int $termId,int $levelId,string $phone='',string $note='',string $locale='fa'): array
    {
        return transaction(function()use($academyId,$userId,$termId,$levelId,$phone,$note,$locale){
            $data=$this->formData($academyId,$userId,$locale);$term=null;foreach($data['terms']as$item)if($item['id']===$termId){$term=$item;break;}
            if(!$term)throw new RuntimeException($locale==='en'?'The selected term is not available.':'ترم انتخاب‌شده باز یا در حال برگزاری نیست.');
            if(!in_array($levelId,array_column($term['levels'],'id'),true))throw new RuntimeException($locale==='en'?'The selected level is not available for this term.':'سطح انتخاب‌شده برای این ترم ارائه نمی‌شود.');
            $user=DB::table('users')->where('user_id',$userId)->whereNull('deleted_at')->first();if(!$user)throw new RuntimeException('User not found.');
            if($data['needsPhone']){$phone=$this->digits($phone);if(!preg_match('/^09\d{9}$/',$phone))throw new RuntimeException($locale==='en'?'Enter a valid mobile number.':'شماره موبایل معتبر وارد کنید.');if(empty($user['phone']))DB::table('users')->where('user_id',$userId)->update(['phone'=>$phone,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$userId]);}
            $row=DB::table('academy_branch_course_terms')->join('academy_branch_courses','academy_branch_courses.course_id','=','academy_branch_course_terms.course_id')->where('academy_branch_course_terms.term_id',$termId)->first();
            $branchId=$row['branch_id']!==null?(int)$row['branch_id']:null;$now=date('Y-m-d H:i:s');
            $memberQuery=DB::table('academy_branch_members')->where('academy_id',$academyId)->where('user_id',$userId)->whereNull('deleted_at');if($branchId)$memberQuery->where('branch_id',$branchId);$member=$memberQuery->first();
            if($member)$memberId=(int)$member['member_id'];else$memberId=DB::table('academy_branch_members')->insertGetId(['academy_id'=>$academyId,'branch_id'=>$branchId,'user_id'=>$userId,'status'=>'pending','joined_at'=>date('Y-m-d'),'created_at'=>$now,'created_by'=>$userId,'updated_at'=>$now,'updated_by'=>$userId]);
            $role=DB::table('access_system_roles')->where('type','academy')->whereRaw("name LIKE '%student%'")->whereNull('deleted_at')->orderBy('role_id')->first();
            if($role&&!DB::table('academy_branch_member_roles')->where('member_id',$memberId)->where('role_id',(int)$role['role_id'])->whereNull('deleted_at')->first())DB::table('academy_branch_member_roles')->insert(['member_id'=>$memberId,'role_id'=>(int)$role['role_id'],'is_main'=>1,'created_at'=>$now,'created_by'=>$userId,'updated_at'=>$now,'updated_by'=>$userId]);
            $exists=DB::table('academy_branch_course_term_waiting_list')->where('term_id',$termId)->where('member_id',$memberId)->whereNull('deleted_at')->first();if($exists)throw new RuntimeException($locale==='en'?'You are already on the waiting list for this term.':'شما قبلاً در فهرست انتظار این ترم ثبت شده‌اید.');
            $lesson=DB::table('user_lessons')->where('user_id',$userId)->where('lesson_id',(int)$row['lesson_id'])->whereNull('deleted_at')->first();$lessonValues=['level_id'=>$levelId,'status'=>'active','updated_at'=>$now,'updated_by'=>$userId];if($lesson)DB::table('user_lessons')->where('user_lesson_id',(int)$lesson['user_lesson_id'])->update($lessonValues);else DB::table('user_lessons')->insert(['user_id'=>$userId,'lesson_id'=>(int)$row['lesson_id'],'level_id'=>$levelId,'start_date'=>date('Y-m-d'),'status'=>'active','is_primary'=>0,'created_at'=>$now,'created_by'=>$userId,'updated_at'=>$now,'updated_by'=>$userId]);
            $waitingId=DB::table('academy_branch_course_term_waiting_list')->insertGetId(['term_id'=>$termId,'member_id'=>$memberId,'created_at'=>$now,'created_by'=>$userId,'updated_at'=>$now,'updated_by'=>$userId]);
            if(trim($note)!=='')DB::table('translations')->insert(['table_name'=>'academy_branch_course_term_waiting_list','table_id'=>$waitingId,'field'=>'description','locale'=>$locale==='en'?'en':'fa','value'=>trim($note),'created_at'=>$now,'created_by'=>$userId,'updated_at'=>$now,'updated_by'=>$userId]);
            return['id'=>$waitingId];
        });
    }

    private function academy(int$id):array{$row=DB::table('academies')->where('academy_id',$id)->whereNull('deleted_at')->first();if(!$row)throw new RuntimeException('Academy not found.');return$row;}
    private function tr(string$table,int$id,string$field,string$locale):string{$r=DB::table('translations')->where('table_name',$table)->where('table_id',$id)->where('field',$field)->where('locale',$locale)->whereNull('deleted_at')->orderBy('translation_id','DESC')->first();return(string)($r['value']??'');}
    private function digits(string$v):string{return preg_replace('/\D+/','',strtr($v,['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9']));}
}
