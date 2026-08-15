<?php

namespace Modules\System\Services;

use Core\database\DB;
use RuntimeException;

class UserReferralService
{
    public function ensureForUser(int $userId, ?string $invitedByCode = null): array
    {
        $existing=DB::table('user_referrals')->where('user_id',$userId)->whereNull('deleted_at')->first();
        if($existing)return $existing;
        $referrer=null;$code=strtoupper(trim((string)$invitedByCode));
        if($code!==''){$owner=DB::table('user_referrals')->where('invite_code',$code)->where('status','active')->whereNull('deleted_at')->first();if(!$owner)throw new RuntimeException('کد دعوت معتبر نیست.');$referrer=(int)$owner['user_id'];if($referrer===$userId)$referrer=null;}
        $inviteCode=null;for($attempt=0;$attempt<10;$attempt++){$candidate=strtoupper(bin2hex(random_bytes(6)));if(!DB::table('user_referrals')->where('invite_code',$candidate)->first()){$inviteCode=$candidate;break;}}
        if(empty($inviteCode))throw new RuntimeException('ساخت کد دعوت ناموفق بود.');
        DB::table('user_referrals')->insert(['user_id'=>$userId,'invite_code'=>$inviteCode,'referred_by_user_id'=>$referrer,'status'=>'active','created_by'=>$userId,'updated_by'=>$userId]);
        return DB::table('user_referrals')->where('user_id',$userId)->first();
    }

    public function details(int $userId):array
    {
        $row=$this->ensureForUser($userId);$count=DB::table('user_referrals')->where('referred_by_user_id',$userId)->whereNull('deleted_at')->count();
        $base=rtrim((string)env('APP_URL',''),'/');if($base===''){$scheme=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';$base=$scheme.'://'.($_SERVER['HTTP_HOST']??'localhost');}
        return ['code'=>$row['invite_code'],'url'=>$base.'/register?ref='.rawurlencode($row['invite_code']),'invitedUsers'=>(int)$count,'status'=>$row['status']];
    }
}
