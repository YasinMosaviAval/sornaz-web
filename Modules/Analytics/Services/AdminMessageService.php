<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

class AdminMessageService
{
    public function index(int $actor): array
    {
        $academyIds=$this->academyIds($actor);
        $siteAdmin=SiteAdminAccess::allows(DB::table('users')->where('user_id',$actor)->first());
        $query=DB::table('user_messages')->where('type','message')->whereNull('deleted_at')->whereRaw('(sender_id = ? OR receiver_user_id = ?)',[$actor,$actor]);
        $rows=$query->orderBy('user_message_id','DESC')->limit(1000)->get();
        $messages=[];foreach($rows as$row){if(!$siteAdmin&&!$this->belongsToAcademies((int)($row['sender_id']??0),(int)($row['receiver_user_id']??0),$academyIds))continue;$messages[]=$this->map($row,$actor);}
        $messageUnread=(int)DB::table('user_messages')->where('receiver_user_id',$actor)->where('type','message')->where('is_read',0)->whereNull('deleted_at')->count();
        $notificationUnread=(int)DB::table('user_messages')->where('receiver_user_id',$actor)->where('type','notification')->where('is_read',0)->whereNull('deleted_at')->count();
        return ['messages'=>$messages,'recipients'=>$this->recipients($actor,$academyIds),'unread'=>['messages'=>$messageUnread,'notifications'=>$notificationUnread],'version'=>$this->version($actor)];
    }

    public function send(int $actor,array $data): int
    {
        $receiver=(int)($data['receiverId']??0);$title=trim((string)($data['title']??''));$body=trim((string)($data['body']??''));
        if(!$receiver||$title===''||$body==='')throw new RuntimeException('گیرنده، عنوان و متن پیام الزامی است.');
        $allowed=array_column($this->recipients($actor,$this->academyIds($actor)),'id');
        if(!in_array($receiver,$allowed,true))throw new RuntimeException('گیرنده انتخاب‌شده عضو قابل پیام‌رسانی این آموزشگاه نیست.');
        $now=date('Y-m-d H:i:s');$id=(int)DB::table('user_messages')->insertGetId(['sender_id'=>$actor,'receiver_user_id'=>$receiver,'type'=>'message','status'=>'published','is_read'=>0,'created_at'=>$now,'created_by'=>$actor,'updated_at'=>$now,'updated_by'=>$actor]);
        $this->saveTexts($id,$title,$body,$actor);return$id;
    }

    public function markRead(int $actor,int $id,bool $read): void
    {
        $row=DB::table('user_messages')->where('user_message_id',$id)->whereNull('deleted_at')->first();
        if(!$row||(int)$row['receiver_user_id']!==$actor)throw new RuntimeException('پیام موردنظر در دسترس نیست.');
        DB::table('user_messages')->where('user_message_id',$id)->update(['is_read'=>$read?1:0,'read_at'=>$read?date('Y-m-d H:i:s'):null,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$actor]);
    }

    public function delete(int $actor,int $id): void
    {
        $row=DB::table('user_messages')->where('user_message_id',$id)->whereNull('deleted_at')->first();
        if(!$row||((int)$row['receiver_user_id']!==$actor&&(int)$row['sender_id']!==$actor))throw new RuntimeException('پیام موردنظر در دسترس نیست.');
        DB::table('user_messages')->where('user_message_id',$id)->update(['deleted_at'=>date('Y-m-d H:i:s'),'deleted_by'=>$actor,'updated_by'=>$actor]);
    }

    private function academyIds(int $actor): array
    {
        $ids=[];foreach(DB::table('academies')->whereNull('deleted_at')->get()as$a)if((int)$a['user_id']===$actor||(int)$a['created_by']===$actor)$ids[]=(int)$a['academy_id'];foreach(DB::table('academy_branches')->where('user_id',$actor)->whereNull('deleted_at')->get()as$b)$ids[]=(int)$b['academy_id'];foreach(DB::table('academy_branch_members')->where('user_id',$actor)->whereNull('deleted_at')->get()as$m)$ids[]=(int)$m['academy_id'];return array_values(array_unique(array_filter($ids)));
    }

    private function recipients(int $actor,array $academyIds): array
    {
        if(!$academyIds&&!SiteAdminAccess::allows(DB::table('users')->where('user_id',$actor)->first()))return[];$users=[];
        $academies=$academyIds?DB::table('academies')->whereIn('academy_id',$academyIds)->whereNull('deleted_at')->get():DB::table('academies')->whereNull('deleted_at')->get();foreach($academies as$a)$users[(int)$a['user_id']]=['id'=>(int)$a['user_id'],'role'=>'مدیر آموزشگاه'];
        $branches=$academyIds?DB::table('academy_branches')->whereIn('academy_id',$academyIds)->whereNull('deleted_at')->get():DB::table('academy_branches')->whereNull('deleted_at')->get();foreach($branches as$b)$users[(int)$b['user_id']]=['id'=>(int)$b['user_id'],'role'=>'کاربر شعبه'];
        $members=$academyIds?DB::table('academy_branch_members')->whereIn('academy_id',$academyIds)->whereNull('deleted_at')->get():DB::table('academy_branch_members')->whereNull('deleted_at')->get();$memberIds=array_map(fn($m)=>(int)$m['member_id'],$members);$students=[];foreach($memberIds?DB::table('academy_branch_course_term_enrollments')->whereIn('member_id',$memberIds)->where('type','student')->whereNull('deleted_at')->get():[]as$x)$students[(int)$x['member_id']]=1;foreach($memberIds?DB::table('academy_branch_course_term_waiting_list')->whereIn('member_id',$memberIds)->whereNull('deleted_at')->get():[]as$x)$students[(int)$x['member_id']]=1;$staff=[];foreach($memberIds?DB::table('academy_branch_member_roles')->join('access_system_roles','access_system_roles.role_id','=','academy_branch_member_roles.role_id')->whereIn('academy_branch_member_roles.member_id',$memberIds)->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get():[]as$r)$staff[(int)$r['member_id']]=(string)$r['name'];foreach($memberIds?DB::table('academy_branch_member_contracts')->whereIn('member_id',$memberIds)->whereNull('deleted_at')->get():[]as$c)if(($c['type']??'')!=='student')$staff[(int)$c['member_id']]=(string)$c['type'];foreach($members as$m){$mid=(int)$m['member_id'];if(isset($students[$mid])&&!isset($staff[$mid]))continue;$users[(int)$m['user_id']]=['id'=>(int)$m['user_id'],'role'=>$this->roleLabel($staff[$mid]??'عضو')];}
        unset($users[$actor]);$ids=array_keys($users);$names=$this->names($ids);foreach($users as$id=>&$u)$u['name']=($names[$id]??('کاربر '.$id)).' - '.$u['role'];unset($u);return array_values($users);
    }

    private function map(array$row,int$actor): array{$id=(int)$row['user_message_id'];$texts=$this->texts($id);$names=$this->names([(int)$row['sender_id'],(int)$row['receiver_user_id']]);$status=(string)($row['status']?:'pending');return['id'=>$id,'title'=>$texts['title']??'بدون عنوان','body'=>$texts['message']??'','sender'=>$names[(int)$row['sender_id']]??'سیستم','receiver'=>$names[(int)$row['receiver_user_id']]??'—','senderId'=>(int)$row['sender_id'],'receiverId'=>(int)$row['receiver_user_id'],'type'=>'پیام','priority'=>'عادی','statusCode'=>$status,'status'=>$this->statusLabel($status),'readStatus'=>$row['is_read']?'خوانده‌شده':'خوانده‌نشده','date'=>date('Y/m/d H:i',strtotime($row['created_at'])),'dateISO'=>date('Y-m-d H:i:s',strtotime($row['created_at'])),'incoming'=>(int)$row['receiver_user_id']===$actor];}
    private function texts(int$id):array{$out=[];foreach(DB::table('translations')->where('table_name','user_messages')->where('table_id',$id)->where('locale',locale())->whereIn('field',['title','message'])->whereNull('deleted_at')->get()as$t)$out[$t['field']]=$t['value'];if(!$out)foreach(DB::table('translations')->where('table_name','user_messages')->where('table_id',$id)->where('locale','fa')->whereIn('field',['title','message'])->whereNull('deleted_at')->get()as$t)$out[$t['field']]=$t['value'];return$out;}
    private function saveTexts(int$id,string$title,string$body,int$actor):void{foreach(['fa','en']as$locale)foreach(['title'=>$title,'message'=>$body]as$field=>$value)DB::table('translations')->insert(['table_name'=>'user_messages','table_id'=>$id,'field'=>$field,'locale'=>$locale,'value'=>$value,'version'=>1,'created_at'=>date('Y-m-d H:i:s'),'created_by'=>$actor,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$actor]);}
    private function names(array$ids):array{$out=[];foreach(array_filter(array_unique($ids))as$id){$u=DB::table('users')->where('user_id',$id)->first();$t=DB::table('translations')->where('table_name','users')->where('table_id',$id)->where('field','full_name')->where('locale',locale())->whereNull('deleted_at')->orderBy('translation_id','DESC')->first();$out[$id]=$t['value']??$u['username']??('کاربر '.$id);}return$out;}
    private function belongsToAcademies(int$a,int$b,array$ids):bool{if(!$ids)return false;return(bool)DB::table('academy_branch_members')->whereIn('academy_id',$ids)->whereIn('user_id',[$a,$b])->whereNull('deleted_at')->first()||(bool)DB::table('academies')->whereIn('academy_id',$ids)->whereIn('user_id',[$a,$b])->whereNull('deleted_at')->first()||(bool)DB::table('academy_branches')->whereIn('academy_id',$ids)->whereIn('user_id',[$a,$b])->whereNull('deleted_at')->first();}
    private function roleLabel(string$r):string{$r=strtolower($r);if(str_contains($r,'teacher'))return'مدرس';if(str_contains($r,'reception'))return'پذیرش';if(str_contains($r,'manager')||str_contains($r,'owner'))return'مدیر';return'کاربر آموزشگاه';}
    private function statusLabel(string$status):string{return['pending'=>'در انتظار','draft'=>'پیش‌نویس','published'=>'منتشر شده','private'=>'خصوصی','trash'=>'زباله‌دان'][$status]??$status;}
    private function version(int$actor):string{$row=DB::table('user_messages')->where('type','message')->whereRaw('(sender_id = ? OR receiver_user_id = ?)',[$actor,$actor])->whereNull('deleted_at')->orderBy('updated_at','DESC')->first();return(string)($row['updated_at']??'');}
}
