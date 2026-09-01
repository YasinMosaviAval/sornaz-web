<?php
namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class PublicCommentService
{
    public function forPost(int $postId, string $locale): array
    {
        $locale=in_array($locale,['fa','en'],true)?$locale:'fa';
        $rows=DB::table('comments')->where('post_id',$postId)->whereNull('deleted_at')->whereNotNull('approved_at')->orderBy('created_at','ASC')->get();
        $items=[];foreach($rows as $row){$translation=DB::table('translations')->where('table_name','comments')->where('table_id',(int)$row['comment_id'])->where('field','content')->where('locale',$locale)->whereNull('deleted_at')->first();if(!$translation)continue;$content=(string)$translation['value'];$content=preg_replace('/<\/?(?:b|strong)(?:\s[^>]*)?>/i','',$content);$items[]=['id'=>(int)$row['comment_id'],'author'=>(string)($row['author']?:($locale==='en'?'User':'کاربر')),'content'=>$content,'locale'=>$locale,'created_at'=>$row['created_at'],'parent'=>(int)($row['parent']??0)];}return $this->threaded($items);
    }

    public function store(int $postId,array $data,?int $userId=null,string $locale='fa'): int
    {
        $locale=in_array($locale,['fa','en'],true)?$locale:'fa';
        $content=trim((string)($data['content']??''));if($content==='')throw new RuntimeException('متن نظر الزامی است.');
        $locale=$this->detectContentLocale($content,$locale);
        $user=$userId?DB::table('users')->where('user_id',$userId)->whereNull('deleted_at')->first():null;
        $authorInput=trim((string)($data['author']??''));
        $author=$authorInput!==''?$authorInput:($user['username']??null);
        $email=trim((string)($data['author_email']??($user['email']??'')));
        if($email!==''&&!filter_var($email,FILTER_VALIDATE_EMAIL))throw new RuntimeException('ایمیل معتبر نیست.');
        $parent=(int)($data['parent']??0);
        if($parent>0){$parentRow=DB::table('comments')->where('comment_id',$parent)->where('post_id',$postId)->whereNull('deleted_at')->whereNotNull('approved_at')->first();if(!$parentRow)throw new RuntimeException('نظری که به آن پاسخ می‌دهید موجود نیست.');}
        $now=date('Y-m-d H:i:s');
        $id=(int)(db()->query('SELECT COALESCE(MAX(comment_id), 0) + 1 FROM comments')->fetchColumn());
        DB::table('comments')->insert(['comment_id'=>$id,'post_id'=>$postId,'author'=>$author,'author_email'=>$email?:null,'author_ip'=>$_SERVER['REMOTE_ADDR']??null,'has_response'=>0,'agent'=>$_SERVER['HTTP_USER_AGENT']??null,'parent'=>$parent?:null,'created_at'=>$now,'created_by'=>$userId,'updated_at'=>$now,'updated_by'=>$userId,'approved_at'=>null,'approved_by'=>null]);
        DB::table('translations')->insert(['table_name'=>'comments','table_id'=>$id,'field'=>'content','locale'=>$locale,'value'=>$content,'version'=>1,'created_by'=>$userId,'updated_by'=>$userId]);if($parent)DB::table('comments')->where('comment_id',$parent)->update(['has_response'=>1,'updated_at'=>$now]);if($userId)UserPointService::recordPublicAction(db(),$userId,$parent?'public.comment.reply':'public.comment.submit','comment',$id);return $id;
    }

    private function detectContentLocale(string $content,string $fallback):string
    {
        $plain=html_entity_decode(strip_tags($content),ENT_QUOTES|ENT_HTML5,'UTF-8');
        preg_match_all('/[\x{0600}-\x{06FF}]/u',$plain,$persian);
        preg_match_all('/[A-Za-z]/',$plain,$english);
        $persianCount=count($persian[0]);$englishCount=count($english[0]);
        if($persianCount>$englishCount)return'fa';
        if($englishCount>$persianCount)return'en';
        return$fallback;
    }

    private function threaded(array $items):array
    {
        $children=[];foreach($items as$item)$children[(int)$item['parent']][]=$item;$result=[];$walk=function(int$parent,int$depth)use(&$walk,&$children,&$result){foreach($children[$parent]??[]as$item){$item['depth']=min($depth,3);$result[]=$item;$walk((int)$item['id'],$depth+1);}};$walk(0,0);return$result;
    }
}
