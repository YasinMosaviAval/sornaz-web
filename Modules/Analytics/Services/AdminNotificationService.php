<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class AdminNotificationService {
    public function all(int $recipientId): array {
        $rows = DB::table('user_messages')->where('receiver_user_id',$recipientId)->where('type','notification')->whereNull('deleted_at')->orderBy('user_message_id', 'DESC')->limit(1000)->get();
        return array_map(fn(array $row) => $this->map($row), $rows);
    }

    public function create(int $actor, array $data): int {
        $title = trim((string)($data['title'] ?? ''));
        $message = trim((string)($data['body'] ?? ''));
        $audience = trim((string)($data['audience'] ?? 'همه')) ?: 'همه';
        if ($title === '' || $message === '') throw new RuntimeException('عنوان و متن اعلان الزامی است.');
        if (!in_array($audience, ['همه','هنرجویان','اساتید','والدین','پرسنل'], true)) throw new RuntimeException('مخاطب انتخاب‌شده معتبر نیست.');
        $context=$this->creatorContext($actor);
        $status=$context['receptionist']?'pending':(!empty($data['asDraft'])?'draft':($context['canPublish']?'published':'pending'));
        $recipients=$this->recipients($context,$audience);
        if(!$recipients) throw new RuntimeException('هیچ کاربری برای مخاطب انتخاب‌شده یافت نشد.');
        $pdo=db();$ownsTransaction=!$pdo->inTransaction();if($ownsTransaction)$pdo->beginTransaction();
        try{$first=0;$now=date('Y-m-d H:i:s');foreach($recipients as$receiver){$id=(int)DB::table('user_messages')->insertGetId([
            'sender_id'=>$actor,'receiver_user_id'=>$receiver,'type'=>'notification','status'=>$status,'related_entity_type'=>'admin_notification','is_read'=>0,
            'approved_at'=>$status==='published'?$now:null,'approved_by'=>$status==='published'?$actor:null,'created_by'=>$actor,'updated_by'=>$actor,
        ]);if(!$first)$first=$id;foreach(['fa','en']as$locale)foreach(['title'=>$title,'message'=>$message,'audience'=>$audience]as$field=>$value)DB::table('translations')->insert(['table_name'=>'user_messages','table_id'=>$id,'field'=>$field,'locale'=>$locale,'value'=>$value,'version'=>1,'created_by'=>$actor,'updated_by'=>$actor]);}if($ownsTransaction)$pdo->commit();return$first;}catch(\Throwable$e){if($ownsTransaction&&$pdo->inTransaction())$pdo->rollBack();throw$e;}
    }

    public function setStatus(int $id, string $status, int $actor): void {
        $row = DB::table('user_messages')->where('user_message_id', $id)->where('type','notification')->whereNull('deleted_at')->first();
        if (!$row) throw new RuntimeException('اعلان یافت نشد.');
        if ($status === 'expired') {
            DB::table('user_messages')->where('user_message_id', $id)->update(['status'=>'trash','updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$actor]);
            return;
        }
        DB::table('user_messages')->where('user_message_id', $id)->update(['status'=>'published','approved_at'=>date('Y-m-d H:i:s'),'approved_by'=>$actor,'is_read'=>0,'updated_by'=>$actor]);
    }

    public function delete(int $id, int $actor): void {
        $this->setStatus($id, 'expired', $actor);
    }

    public function markRead(int $id, int $actor): void {
        $row=DB::table('user_messages')->where('user_message_id',$id)->where('receiver_user_id',$actor)->where('type','notification')->whereNull('deleted_at')->first();
        if(!$row) throw new RuntimeException('اعلان یافت نشد.');
        DB::table('user_messages')->where('user_message_id',$id)->update(['is_read'=>1,'read_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$actor]);
    }

    private function map(array $row): array {
        $texts=[];
        foreach (DB::table('translations')->where('table_name','user_messages')->where('table_id',(int)$row['user_message_id'])->where('locale',locale())->whereIn('field',['title','message','audience'])->whereNull('deleted_at')->get() as $translation) $texts[(string)$translation['field']]=(string)$translation['value'];
        $recipient=DB::table('users')->where('user_id',(int)($row['receiver_user_id']??0))->whereNull('deleted_at')->first();
        $operation = str_replace('database_', '', (string)($row['related_entity_type']??''));
        $priority = $operation === 'delete' ? 'بالا' : ($operation === 'update' ? 'متوسط' : 'کم');
        $status=(string)($row['status']?:'pending');
        return [
            'id'=>(int)$row['user_message_id'], 'title'=>$texts['title']??'', 'body'=>$texts['message']??'', 'recipientId'=>(int)($row['receiver_user_id']??0), 'recipientUsername'=>$recipient['username']??'—',
            'branchId'=>0, 'branchName'=>'سیستم', 'audience'=>$texts['audience']??'همه', 'priority'=>$priority,
            'statusCode'=>$status, 'status'=>$this->statusLabel($status), 'readStatus'=>$row['is_read']?'خوانده‌شده':'خوانده‌نشده',
            'date'=>date('Y/m/d', strtotime($row['created_at'])), 'dateISO'=>date('Y-m-d', strtotime($row['created_at'])),
            'source'=>'سیستم', 'type'=>$row['type'], 'entityType'=>$row['related_entity_type'], 'entityId'=>$row['related_entity_id'],
        ];
    }
    private function statusLabel(string $status): string { return ['pending'=>'در انتظار','draft'=>'پیش‌نویس','published'=>'منتشر شده','private'=>'خصوصی','trash'=>'زباله‌دان'][$status] ?? $status; }

    private function creatorContext(int $actor): array {
        $user=DB::table('users')->where('user_id',$actor)->whereNull('deleted_at')->first();if(!$user)throw new RuntimeException('کاربر معتبر نیست.');
        $academyIds=[];$branchIds=[];$canPublish=false;$isReceptionist=false;$type=(string)($user['type']??'');$academyWide=$type==='academy';
        foreach(DB::table('academies')->whereNull('deleted_at')->get()as$a)if((int)$a['user_id']===$actor||(int)$a['created_by']===$actor){$academyIds[]=(int)$a['academy_id'];$canPublish=true;$academyWide=true;}
        foreach(DB::table('academy_branches')->where('user_id',$actor)->whereNull('deleted_at')->get()as$b){$branchIds[]=(int)$b['branch_id'];$canPublish=true;}
        $members=DB::table('academy_branch_members')->where('user_id',$actor)->whereNull('deleted_at')->get();$memberIds=array_map(fn($m)=>(int)$m['member_id'],$members);
        $roles=$memberIds?DB::table('academy_branch_member_roles')->join('access_system_roles','access_system_roles.role_id','=','academy_branch_member_roles.role_id')->whereIn('academy_branch_member_roles.member_id',$memberIds)->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get():[];
        $roleByMember=[];foreach($roles as$r)$roleByMember[(int)$r['member_id']][]=strtolower((string)$r['name']);
        foreach($members as$m){$names=$roleByMember[(int)$m['member_id']]??[];foreach($names as$name){if(str_contains($name,'reception')){$isReceptionist=true;if(str_contains($name,'branch')&&$m['branch_id'])$branchIds[]=(int)$m['branch_id'];else{$academyIds[]=(int)$m['academy_id'];$academyWide=true;}}if((str_contains($name,'manager')||str_contains($name,'owner'))&&!str_contains($name,'reception')){$canPublish=true;if(str_contains($name,'branch')&&$m['branch_id'])$branchIds[]=(int)$m['branch_id'];else{$academyIds[]=(int)$m['academy_id'];$academyWide=true;}}}}
        if(in_array($type,['academy','branch'],true))$canPublish=true;
        $academyIds=array_values(array_unique(array_filter($academyIds)));$branchIds=array_values(array_unique(array_filter($branchIds)));
        if($academyIds&&!$branchIds)foreach(DB::table('academy_branches')->whereIn('academy_id',$academyIds)->whereNull('deleted_at')->get()as$b)$branchIds[]=(int)$b['branch_id'];
        if(!$academyIds&&$branchIds)foreach(DB::table('academy_branches')->whereIn('branch_id',$branchIds)->whereNull('deleted_at')->get()as$b)$academyIds[]=(int)$b['academy_id'];
        if(!$academyIds&&!$branchIds)throw new RuntimeException('محدوده آموزشگاه یا شعبه برای ایجاد اعلان مشخص نیست.');
        return['academyIds'=>array_values(array_unique($academyIds)),'branchIds'=>array_values(array_unique($branchIds)),'academyWide'=>$academyWide,'canPublish'=>$canPublish,'receptionist'=>$isReceptionist&&!$canPublish];
    }

    private function recipients(array $context,string $audience): array {
        $academyWide=(bool)$context['academyWide'];$academyIds=$context['academyIds'];$branchIds=$context['branchIds'];
        $members=$academyWide?DB::table('academy_branch_members')->whereIn('academy_id',$academyIds)->whereNull('deleted_at')->get():DB::table('academy_branch_members')->whereIn('branch_id',$branchIds)->whereNull('deleted_at')->get();
        $memberIds=array_map(fn($m)=>(int)$m['member_id'],$members);$memberUsers=[];foreach($members as$m)$memberUsers[(int)$m['member_id']]=(int)$m['user_id'];$ids=[];
        if($audience==='همه'){foreach($members as$m)$ids[]=(int)$m['user_id'];$academies=DB::table('academies')->whereIn('academy_id',$academyIds)->whereNull('deleted_at')->get();foreach($academies as$a)$ids[]=(int)$a['user_id'];$branches=DB::table('academy_branches')->whereIn('branch_id',$branchIds)->whereNull('deleted_at')->get();foreach($branches as$b)$ids[]=(int)$b['user_id'];}
        elseif($audience==='هنرجویان'){foreach($memberIds?DB::table('academy_branch_course_term_enrollments')->whereIn('member_id',$memberIds)->where('type','student')->whereNull('deleted_at')->get():[]as$e)$ids[]=$memberUsers[(int)$e['member_id']]??0;foreach($memberIds?DB::table('academy_branch_course_term_waiting_list')->whereIn('member_id',$memberIds)->whereNull('deleted_at')->get():[]as$w)$ids[]=$memberUsers[(int)$w['member_id']]??0;}
        elseif($audience==='اساتید'){foreach($memberIds?DB::table('academy_branch_course_term_enrollments')->whereIn('member_id',$memberIds)->where('type','teacher')->whereNull('deleted_at')->get():[]as$e)$ids[]=$memberUsers[(int)$e['member_id']]??0;foreach($memberIds?DB::table('academy_branch_member_roles')->join('access_system_roles','access_system_roles.role_id','=','academy_branch_member_roles.role_id')->whereIn('academy_branch_member_roles.member_id',$memberIds)->whereRaw("LOWER(access_system_roles.name) LIKE '%teacher%'")->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get():[]as$r)$ids[]=$memberUsers[(int)$r['member_id']]??0;}
        elseif($audience==='والدین'){foreach($members as$m)if(!empty($m['parent_user_id']))$ids[]=(int)$m['parent_user_id'];}
        else{foreach($memberIds?DB::table('academy_branch_member_roles')->whereIn('member_id',$memberIds)->whereNull('deleted_at')->get():[]as$r)$ids[]=$memberUsers[(int)$r['member_id']]??0;foreach($memberIds?DB::table('academy_branch_member_contracts')->whereIn('member_id',$memberIds)->whereNull('deleted_at')->get():[]as$c)if(($c['type']??'')!=='student')$ids[]=$memberUsers[(int)$c['member_id']]??0;$academies=DB::table('academies')->whereIn('academy_id',$academyIds)->whereNull('deleted_at')->get();foreach($academies as$a)$ids[]=(int)$a['user_id'];$branches=DB::table('academy_branches')->whereIn('branch_id',$branchIds)->whereNull('deleted_at')->get();foreach($branches as$b)$ids[]=(int)$b['user_id'];}
        return array_values(array_unique(array_filter($ids,fn($id)=>$id>0&&(bool)DB::table('users')->where('user_id',$id)->whereNull('deleted_at')->first())));
    }
}
