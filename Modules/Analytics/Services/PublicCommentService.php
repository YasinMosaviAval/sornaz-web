<?php
namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class PublicCommentService
{
    public function forPost(int $postId): array
    {
        $rows=DB::table('comments')->where('post_id',$postId)->whereNull('deleted_at')->whereNotNull('approved_at')->orderBy('created_at','ASC')->get();
        $items=[];foreach($rows as $row){$translation=DB::table('translations')->where('table_name','comments')->where('table_id',(int)$row['comment_id'])->where('field','content')->where('locale','fa')->whereNull('deleted_at')->first();if(!$translation)continue;$content=(string)$translation['value'];$content=preg_replace('/<\/?(?:b|strong)(?:\s[^>]*)?>/i','',$content);$items[]=['id'=>(int)$row['comment_id'],'author'=>(string)($row['author']?:'کاربر'),'content'=>$content,'created_at'=>$row['created_at'],'parent'=>(int)($row['parent']??0)];}return $items;
    }

    public function store(int $postId,array $data,?int $userId=null): int
    {
        $content=trim((string)($data['content']??''));if($content==='')throw new RuntimeException('متن نظر الزامی است.');
        $user=$userId?DB::table('users')->where('user_id',$userId)->whereNull('deleted_at')->first():null;
        $authorInput=trim((string)($data['author']??''));
        $author=$authorInput!==''?$authorInput:($user['username']??null);
        $email=trim((string)($data['author_email']??($user['email']??'')));
        if($email!==''&&!filter_var($email,FILTER_VALIDATE_EMAIL))throw new RuntimeException('ایمیل معتبر نیست.');
        $now=date('Y-m-d H:i:s');
        $id=(int)(db()->query('SELECT COALESCE(MAX(comment_id), 0) + 1 FROM comments')->fetchColumn());
        DB::table('comments')->insert(['comment_id'=>$id,'post_id'=>$postId,'author'=>$author,'author_email'=>$email?:null,'author_ip'=>$_SERVER['REMOTE_ADDR']??null,'has_response'=>0,'agent'=>$_SERVER['HTTP_USER_AGENT']??null,'parent'=>null,'created_at'=>$now,'created_by'=>$userId,'updated_at'=>$now,'updated_by'=>$userId,'approved_at'=>null,'approved_by'=>null]);
        DB::table('translations')->insert(['table_name'=>'comments','table_id'=>$id,'field'=>'content','locale'=>'fa','value'=>$content,'version'=>1,'created_by'=>$userId,'updated_by'=>$userId]);return $id;
    }
}
