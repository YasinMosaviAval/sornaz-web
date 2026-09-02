<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

final class UserMergeService
{
    public function data(int $actor): array
    {
        $user=$this->user($actor);$admin=SiteAdminAccess::allows($user);
        $sql="SELECT um.user_merge_id id,um.from_user_id sourceUserId,um.to_user_id targetUserId,um.member_id memberId,um.status,um.reason,um.admin_note adminNote,um.created_at createdAt,um.approved_at decidedAt,COALESCE(a.branch_id,0) branchId,COALESCE(a.academy_id,0) academyId FROM user_merges um LEFT JOIN academy_branch_members a ON a.member_id=um.member_id WHERE um.deleted_at IS NULL";
        $bindings=[];if(!$admin){$sql.=' AND um.to_user_id=?';$bindings[]=$actor;}$sql.=" ORDER BY FIELD(um.status,'pending','approved','rejected','cancelled'),um.user_merge_id DESC";
        $q=db()->prepare($sql);$q->execute($bindings);
        return ['eligible'=>$this->eligible($user),'isSiteAdmin'=>$admin,'requests'=>$q->fetchAll()];
    }

    public function request(int $actor,array $data): void
    {
        $target=$this->user($actor);if(!$this->eligible($target))throw new RuntimeException('فقط حساب حقیقی کامل و دارای سابقه ورود می‌تواند درخواست ادغام ثبت کند.');
        $sourceId=(int)($data['userId']??0);$memberId=(int)($data['memberId']??0);if($sourceId<1||$memberId<1)throw new RuntimeException('شناسه کاربر و شماره عضویت الزامی است.');
        if($sourceId===$actor)throw new RuntimeException('حساب فعلی را نمی‌توان با خودش ادغام کرد.');
        $source=$this->user($sourceId);if(($source['type']??'')!=='human')throw new RuntimeException('شناسه واردشده متعلق به شخص حقیقی نیست.');
        if($this->eligible($source))throw new RuntimeException('شناسه واردشده متعلق به یک حساب کامل و فعال است و قابل ادغام نیست.');
        $member=DB::table('academy_branch_members')->where('member_id',$memberId)->where('user_id',$sourceId)->whereNull('deleted_at')->first();if(!$member)throw new RuntimeException('ترکیب شناسه کاربر و شماره عضویت معتبر نیست.');
        if(DB::table('user_merges')->where('member_id',$memberId)->where('status','pending')->whereNull('deleted_at')->first())throw new RuntimeException('برای این عضویت قبلاً درخواست در انتظار ثبت شده است.');
        $now=date('Y-m-d H:i:s');DB::table('user_merges')->insert(['from_user_id'=>$sourceId,'to_user_id'=>$actor,'member_id'=>$memberId,'status'=>'pending','reason'=>trim((string)($data['reason']??''))?:null,'created_at'=>$now,'created_by'=>$actor,'updated_at'=>$now,'updated_by'=>$actor]);
    }

    public function cancel(int $actor,int $id): void
    {
        $row=DB::table('user_merges')->where('user_merge_id',$id)->where('to_user_id',$actor)->where('status','pending')->whereNull('deleted_at')->first();if(!$row)throw new RuntimeException('درخواست قابل لغو نیست.');
        DB::table('user_merges')->where('user_merge_id',$id)->update(['status'=>'cancelled','updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$actor]);
    }

    public function decide(int $actor,int $id,array $data): void
    {
        if(!SiteAdminAccess::allows($this->user($actor)))throw new RuntimeException('فقط مدیر سایت اجازه بررسی درخواست را دارد.');
        $decision=(string)($data['decision']??'');if(!in_array($decision,['approved','rejected'],true))throw new RuntimeException('تصمیم معتبر نیست.');
        transaction(function()use($actor,$id,$data,$decision){
            $row=DB::table('user_merges')->where('user_merge_id',$id)->where('status','pending')->whereNull('deleted_at')->first();if(!$row)throw new RuntimeException('درخواست قبلاً بررسی شده یا وجود ندارد.');
            $member=DB::table('academy_branch_members')->where('member_id',(int)$row['member_id'])->where('user_id',(int)$row['from_user_id'])->whereNull('deleted_at')->first();if(!$member)throw new RuntimeException('مالکیت عضویت از زمان ثبت درخواست تغییر کرده است.');
            $now=date('Y-m-d H:i:s');if($decision==='approved')DB::table('academy_branch_members')->where('user_id',(int)$row['from_user_id'])->whereNull('deleted_at')->update(['user_id'=>(int)$row['to_user_id'],'updated_at'=>$now,'updated_by'=>$actor]);
            DB::table('user_merges')->where('user_merge_id',$id)->update(['status'=>$decision,'admin_note'=>trim((string)($data['note']??''))?:null,'merged_at'=>$decision==='approved'?$now:null,'merged_by'=>$decision==='approved'?$actor:null,'approved_at'=>$now,'approved_by'=>$actor,'updated_at'=>$now,'updated_by'=>$actor]);
        });
    }

    private function user(int $id): array{$u=DB::table('users')->where('user_id',$id)->whereNull('deleted_at')->first();if(!$u)throw new RuntimeException('کاربر معتبر نیست.');return$u;}
    private function eligible(array $u): bool{return($u['type']??'')==='human'&&!empty($u['password'])&&!empty($u['last_login_at'])&&!empty($u['last_login_ip']);}
}
