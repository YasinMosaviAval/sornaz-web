<?php
namespace Modules\Academy\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

final class NationalHolidayService
{
    public function index(int $actor): array
    {
        $siteAdmin=SiteAdminAccess::allows(DB::table('users')->where('user_id',$actor)->whereNull('deleted_at')->first());
        $academies=$this->manageableAcademies($actor,$siteAdmin);
        $academyIds=array_column($academies,'academy_id');
        $settings=[];
        if($academyIds)foreach(DB::table('academy_national_holiday_settings')->whereIn('academy_id',$academyIds)->get() as $row)$settings[(int)$row['academy_id']]=(bool)$row['allow_classes_on_national_holidays'];
        $holidays=DB::table('user_availabilities')->where('unavailable_type','national_holiday')->whereNull('user_id')->whereNull('deleted_at')->orderBy('date','DESC')->get();
        return [
            'isSiteAdmin'=>$siteAdmin,
            'holidays'=>array_map(fn($row)=>['id'=>(int)$row['user_availability_id'],'date'=>$row['date'],'title'=>$this->text((int)$row['user_availability_id'],'summary'),'description'=>$this->text((int)$row['user_availability_id'],'description'),'status'=>$row['status']==='available'?'active':'inactive'],$holidays),
            'academies'=>array_map(fn($row)=>['id'=>(int)$row['academy_id'],'name'=>$this->academyName((int)$row['academy_id']),'allowClasses'=>(bool)($settings[(int)$row['academy_id']]??false)],$academies),
        ];
    }

    public function saveHoliday(int $actor,array $data,int $id=0): array
    {
        $this->requireSiteAdmin($actor);
        $date=trim((string)($data['date']??''));$title=trim((string)($data['title']??''));
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)||!strtotime($date))throw new RuntimeException('تاریخ تعطیل رسمی معتبر نیست.');
        if($title==='')throw new RuntimeException('عنوان تعطیل رسمی الزامی است.');
        $duplicate=DB::table('user_availabilities')->where('unavailable_type','national_holiday')->whereNull('user_id')->where('date',$date)->whereNull('deleted_at')->first();
        if($duplicate&&(!$id||(int)$duplicate['user_availability_id']!==$id))throw new RuntimeException('برای این تاریخ قبلاً تعطیل رسمی ثبت شده است.');
        $now=date('Y-m-d H:i:s');$values=['user_id'=>null,'member_id'=>null,'date'=>$date,'day_of_week'=>null,'start_time'=>null,'end_time'=>null,'timezone_id'=>1,'status'=>($data['status']??'active')==='inactive'?'unavailable':'available','unavailable_type'=>'national_holiday','is_repeating'=>0,'repeat_period'=>'none','is_closed'=>1,'priority'=>0,'updated_at'=>$now,'updated_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];
        if($id){if(!DB::table('user_availabilities')->where('user_availability_id',$id)->where('unavailable_type','national_holiday')->whereNull('user_id')->whereNull('deleted_at')->first())throw new RuntimeException('تعطیل رسمی یافت نشد.');DB::table('user_availabilities')->where('user_availability_id',$id)->update($values);}
        else $id=DB::table('user_availabilities')->insertGetId(['created_at'=>$now,'created_by'=>$actor]+$values);
        $this->setText($id,'summary',$title,$actor);$this->setText($id,'description',trim((string)($data['description']??'')),$actor);
        $this->syncConflictNotifications($actor,$id);
        return ['id'=>$id];
    }

    public function deleteHoliday(int $actor,int $id): void
    {
        $this->requireSiteAdmin($actor);$now=date('Y-m-d H:i:s');
        DB::table('user_availabilities')->where('user_availability_id',$id)->where('unavailable_type','national_holiday')->whereNull('user_id')->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$actor,'updated_at'=>$now,'updated_by'=>$actor]);$this->syncConflictNotifications($actor,$id);
    }

    public function toggleHolidayStatus(int $actor,int $id):array
    {
        $this->requireSiteAdmin($actor);$holiday=DB::table('user_availabilities')->where('user_availability_id',$id)->where('unavailable_type','national_holiday')->whereNull('user_id')->whereNull('deleted_at')->first();if(!$holiday)throw new RuntimeException('تعطیل رسمی یافت نشد.');$active=($holiday['status']??'available')==='available';DB::table('user_availabilities')->where('user_availability_id',$id)->update(['status'=>$active?'unavailable':'available','updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$actor]);$this->syncConflictNotifications($actor,$id);return['id'=>$id,'status'=>$active?'inactive':'active'];
    }

    public function saveAcademySetting(int $actor,int $academyId,bool $allow): array
    {
        $siteAdmin=SiteAdminAccess::allows(DB::table('users')->where('user_id',$actor)->whereNull('deleted_at')->first());$allowed=false;
        foreach($this->manageableAcademies($actor,$siteAdmin) as $academy)if((int)$academy['academy_id']===$academyId){$allowed=true;break;}
        if(!$allowed)throw new RuntimeException('اجازه تغییر تنظیم تعطیلات رسمی این آموزشگاه را ندارید.');
        $now=date('Y-m-d H:i:s');$existing=DB::table('academy_national_holiday_settings')->where('academy_id',$academyId)->first();
        $values=['allow_classes_on_national_holidays'=>$allow?1:0,'updated_at'=>$now,'updated_by'=>$actor];
        if($existing)DB::table('academy_national_holiday_settings')->where('academy_id',$academyId)->update($values);
        else DB::table('academy_national_holiday_settings')->insert(['academy_id'=>$academyId,'created_at'=>$now,'created_by'=>$actor]+$values);
        foreach(DB::table('user_availabilities')->where('unavailable_type','national_holiday')->whereNull('user_id')->where('status','available')->whereNull('deleted_at')->get()as$holiday)$this->syncConflictNotifications($actor,(int)$holiday['user_availability_id']);
        return ['academyId'=>$academyId,'allowClasses'=>$allow];
    }

    private function syncConflictNotifications(int$actor,int$holidayId):void
    {
        $now=date('Y-m-d H:i:s');DB::table('user_messages')->where('related_entity_type','national_holiday_conflict')->where('related_entity_id',$holidayId)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$actor,'updated_at'=>$now,'updated_by'=>$actor]);
        $holiday=DB::table('user_availabilities')->where('user_availability_id',$holidayId)->where('unavailable_type','national_holiday')->whereNull('user_id')->where('status','available')->whereNull('deleted_at')->first();if(!$holiday)return;$holiday['holiday_date']=$holiday['date'];$holiday['title']=$this->text($holidayId,'summary');
        $sql="SELECT s.term_session_id,s.term_id,br.branch_id,br.academy_id,br.user_id branch_user_id,a.user_id academy_user_id,COALESCE((SELECT value FROM translations WHERE table_name='academy_branch_course_terms' AND table_id=s.term_id AND field='title' AND locale='fa' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('ترم ',s.term_id)) term_name FROM academy_branch_course_term_sessions s JOIN academy_branch_bookings b ON b.booking_id=s.booking_id JOIN academy_branch_course_terms t ON t.term_id=s.term_id JOIN academy_branch_courses c ON c.course_id=t.course_id LEFT JOIN academy_branch_classrooms room ON room.classroom_id=s.classroom_id AND room.deleted_at IS NULL JOIN academy_branches br ON br.branch_id=COALESCE(c.branch_id,room.branch_id) JOIN academies a ON a.academy_id=br.academy_id LEFT JOIN academy_national_holiday_settings hs ON hs.academy_id=a.academy_id WHERE b.requested_date=? AND s.deleted_at IS NULL AND b.deleted_at IS NULL AND t.deleted_at IS NULL AND c.deleted_at IS NULL AND br.deleted_at IS NULL AND a.deleted_at IS NULL AND b.status NOT IN ('canceled','rejected') AND COALESCE(hs.allow_classes_on_national_holidays,0)=0";
        $statement=db()->prepare($sql);$statement->execute([$holiday['holiday_date']]);
        foreach($statement->fetchAll()as$conflict){$recipients=[(int)$conflict['academy_user_id'],(int)$conflict['branch_user_id']];$members=DB::table('academy_branch_members')->join('academy_branch_member_roles','academy_branch_member_roles.member_id','=','academy_branch_members.member_id')->join('access_system_roles','access_system_roles.role_id','=','academy_branch_member_roles.role_id')->where('academy_branch_members.academy_id',(int)$conflict['academy_id'])->whereRaw("(academy_branch_members.branch_id IS NULL OR academy_branch_members.branch_id = ?)",[(int)$conflict['branch_id']])->whereRaw("(LOWER(access_system_roles.name) LIKE '%manager%' OR LOWER(access_system_roles.name) LIKE '%owner%' OR LOWER(access_system_roles.name) LIKE '%reception%')")->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get();foreach($members as$member)$recipients[]=(int)$member['user_id'];$recipients=array_values(array_unique(array_filter($recipients)));
            $title='تداخل جلسه با تعطیل رسمی '.$holiday['title'];$message='جلسه #'.$conflict['term_session_id'].' از '.$conflict['term_name'].' در تاریخ '.$holiday['holiday_date'].' با تعطیل رسمی «'.$holiday['title'].'» تداخل دارد. جلسه به‌صورت خودکار لغو نشده و در صورت نیاز باید از بخش لغو جلسه، تاریخ جبرانی ثبت شود.';
            foreach($recipients as$receiver){if(!DB::table('users')->where('user_id',$receiver)->whereNull('deleted_at')->first())continue;$messageId=DB::table('user_messages')->insertGetId(['sender_id'=>$actor,'receiver_user_id'=>$receiver,'type'=>'notification','status'=>'published','related_entity_type'=>'national_holiday_conflict','related_entity_id'=>$holidayId,'is_read'=>0,'approved_at'=>$now,'approved_by'=>$actor,'created_at'=>$now,'created_by'=>$actor,'updated_at'=>$now,'updated_by'=>$actor]);foreach(['fa','en']as$locale)foreach(['title'=>$title,'message'=>$message,'audience'=>'پرسنل']as$field=>$value)DB::table('translations')->insert(['table_name'=>'user_messages','table_id'=>$messageId,'field'=>$field,'locale'=>$locale,'value'=>$value,'version'=>1,'created_by'=>$actor,'updated_by'=>$actor]);}
        }
    }

    private function manageableAcademies(int $actor,bool $siteAdmin): array
    {
        if($siteAdmin)return DB::table('academies')->whereNull('deleted_at')->orderBy('academy_id')->get();
        $rows=array_merge(DB::table('academies')->where('user_id',$actor)->whereNull('deleted_at')->get(),DB::table('academies')->where('created_by',$actor)->whereNull('deleted_at')->get());
        $managed=DB::table('academy_branch_members')->join('academy_branch_member_roles','academy_branch_member_roles.member_id','=','academy_branch_members.member_id')->join('access_system_roles','access_system_roles.role_id','=','academy_branch_member_roles.role_id')->where('academy_branch_members.user_id',$actor)->whereRaw("(access_system_roles.name LIKE '%academy%manager%' OR access_system_roles.name LIKE '%academy%owner%')")->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get();
        foreach($managed as $row)if($row['academy_id']!==null){$academy=DB::table('academies')->where('academy_id',(int)$row['academy_id'])->whereNull('deleted_at')->first();if($academy)$rows[]=$academy;}
        $unique=[];foreach($rows as $row)$unique[(int)$row['academy_id']]=$row;return array_values($unique);
    }

    private function academyName(int $id): string
    {
        $translation=DB::table('translations')->where('table_name','academies')->where('table_id',$id)->where('field','title')->where('locale',locale())->whereNull('deleted_at')->orderBy('translation_id','DESC')->first();
        return (string)($translation['value']??('آموزشگاه '.$id));
    }

    private function text(int$id,string$field):string{$row=DB::table('translations')->where('table_name','user_availabilities')->where('table_id',$id)->where('field',$field)->where('locale',locale())->whereNull('deleted_at')->orderBy('translation_id','DESC')->first();if(!$row&&locale()!=='fa')$row=DB::table('translations')->where('table_name','user_availabilities')->where('table_id',$id)->where('field',$field)->where('locale','fa')->whereNull('deleted_at')->orderBy('translation_id','DESC')->first();return(string)($row['value']??'');}
    private function setText(int$id,string$field,string$value,int$actor):void{$row=DB::table('translations')->where('table_name','user_availabilities')->where('table_id',$id)->where('field',$field)->where('locale','fa')->first();$values=['value'=>$value,'version'=>1,'updated_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];if($row)DB::table('translations')->where('translation_id',(int)$row['translation_id'])->update($values);else DB::table('translations')->insert(['table_name'=>'user_availabilities','table_id'=>$id,'field'=>$field,'locale'=>'fa','created_by'=>$actor]+$values);}

    private function requireSiteAdmin(int $actor): void
    {
        if(!SiteAdminAccess::allows(DB::table('users')->where('user_id',$actor)->whereNull('deleted_at')->first()))throw new RuntimeException('فقط مدیر سایت اجازه مدیریت تعطیلات رسمی کشور را دارد.');
    }
}
