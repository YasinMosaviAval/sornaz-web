<?php
namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class PublicRatingService
{
    private const TARGETS=['post'=>['posts','post_id'],'comment'=>['comments','comment_id'],'user'=>['users','user_id'],'academy'=>['academies','academy_id'],'branch'=>['academy_branches','branch_id']];

    public function summary(string $type,int $id,?int $viewerId=null):array
    {
        $this->target($type,$id);
        $this->ensureTable();
        $rows=DB::table('public_ratings')->where('target_type',$type)->where('target_id',$id)->whereNull('deleted_at')->get();
        $count=count($rows);$sum=array_sum(array_map(fn($row)=>(int)$row['score'],$rows));$mine=null;
        foreach($rows as$row)if($viewerId&&(int)$row['user_id']===$viewerId)$mine=(int)$row['score'];
        return['targetType'=>$type,'targetId'=>$id,'average'=>$count?round($sum/$count,1):0,'count'=>$count,'userScore'=>$mine];
    }

    public function rate(string $type,int $id,int $score,int $userId):array
    {
        if($score<1||$score>5)throw new RuntimeException('امتیاز باید بین ۱ تا ۵ باشد.');
        $target=$this->target($type,$id);
        if($type==='user'&&$id===$userId)throw new RuntimeException('نمی‌توانید به پروفایل خودتان امتیاز بدهید.');
        if(in_array($type,['academy','branch'],true)&&(int)($target['user_id']??0)===$userId)throw new RuntimeException('نمی‌توانید به مجموعه خودتان امتیاز بدهید.');
        $this->ensureTable();$now=date('Y-m-d H:i:s');$existing=DB::table('public_ratings')->where('target_type',$type)->where('target_id',$id)->where('user_id',$userId)->first();
        if($existing)DB::table('public_ratings')->where('public_rating_id',(int)$existing['public_rating_id'])->update(['score'=>$score,'updated_at'=>$now,'updated_by'=>$userId,'deleted_at'=>null,'deleted_by'=>null]);
        else DB::table('public_ratings')->insert(['target_type'=>$type,'target_id'=>$id,'user_id'=>$userId,'score'=>$score,'created_at'=>$now,'created_by'=>$userId,'updated_at'=>$now,'updated_by'=>$userId]);
        if(in_array($type,['post','comment'],true))UserPointService::recordPublicAction(db(),$userId,$type==='post'?'public.article.rate':'public.comment.rate','public_'.$type,$id);
        return$this->summary($type,$id,$userId);
    }

    public function branchesForAcademy(int $academyId,string $locale):array
    {
        $rows=DB::table('academy_branches')->where('academy_id',$academyId)->whereNull('deleted_at')->get();$out=[];
        foreach($rows as$row){$id=(int)$row['branch_id'];$tr=DB::table('translations')->where('table_name','academy_branches')->where('table_id',$id)->where('field','name')->where('locale',$locale)->whereNull('deleted_at')->first();if(!$tr&&$locale!=='fa')$tr=DB::table('translations')->where('table_name','academy_branches')->where('table_id',$id)->where('field','name')->where('locale','fa')->whereNull('deleted_at')->first();$out[]=['id'=>$id,'name'=>(string)($tr['value']??('شعبه '.$id))];}return$out;
    }

    private function target(string $type,int $id):array
    {
        if(!isset(self::TARGETS[$type])||$id<1)throw new RuntimeException('هدف امتیازدهی معتبر نیست.');[$table,$key]=self::TARGETS[$type];$query=DB::table($table)->where($key,$id)->whereNull('deleted_at');if($type==='post')$query->where('status','published');if($type==='comment')$query->whereNotNull('approved_at');$row=$query->first();if(!$row)throw new RuntimeException('مورد انتخاب‌شده یافت نشد.');return$row;
    }

    private function ensureTable():void
    {
        db()->exec("CREATE TABLE IF NOT EXISTS public_ratings (public_rating_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,target_type VARCHAR(30) NOT NULL,target_id BIGINT UNSIGNED NOT NULL,user_id BIGINT UNSIGNED NOT NULL,score TINYINT UNSIGNED NOT NULL,created_at DATETIME NULL,created_by BIGINT UNSIGNED NULL,updated_at DATETIME NULL,updated_by BIGINT UNSIGNED NULL,deleted_at DATETIME NULL,deleted_by BIGINT UNSIGNED NULL,UNIQUE KEY uq_public_rating_user_target(target_type,target_id,user_id),INDEX idx_public_rating_target(target_type,target_id,deleted_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
}
