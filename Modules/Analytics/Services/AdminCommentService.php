<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class AdminCommentService
{
    private string $table;
    private string $key;
    private bool $directContent;

    public function __construct()
    {
        $hasCurrent = (bool)db()->query("SHOW TABLES LIKE 'user_comments'")->fetchColumn();
        $this->table = $hasCurrent ? 'user_comments' : 'z_user_comments';
        $this->key = $hasCurrent ? 'id' : 'user_comment_id';
        $this->directContent = $hasCurrent;
    }

    public function index(array $filters): array
    {
        $page=max(1,(int)($filters['page']??1));$per=in_array((int)($filters['perPage']??20),[10,20,30,50,100],true)?(int)$filters['perPage']:20;
        $where=['c.deleted_at IS NULL'];$bind=[];
        if(!empty($filters['status'])){$where[]='c.status=?';$bind[]=$filters['status'];}
        if(!empty($filters['post'])){$where[]='c.post_id=?';$bind[]=(int)$filters['post'];}
        if(!empty($filters['search'])){$q='%'.trim((string)$filters['search']).'%';$content=$this->directContent?'c.content LIKE ?':"EXISTS(SELECT 1 FROM translations t WHERE t.table_name='{$this->table}' AND t.table_id=c.{$this->key} AND t.field='content' AND t.deleted_at IS NULL AND t.value LIKE ?)";$where[]="(c.guest_name LIKE ? OR c.guest_email LIKE ? OR $content)";array_push($bind,$q,$q,$q);}
        $w=implode(' AND ',$where);$total=$this->query("SELECT COUNT(*) FROM {$this->table} c WHERE $w",$bind,true);$offset=($page-1)*$per;
        $rows=$this->query("SELECT c.*,u.username FROM {$this->table} c LEFT JOIN users u ON u.user_id=c.user_id WHERE $w ORDER BY c.{$this->key} DESC LIMIT $offset,$per",$bind);
        $items=array_map(fn($r)=>$this->map($r),$rows);$counts=[];foreach($this->query("SELECT status,COUNT(*) total FROM {$this->table} WHERE deleted_at IS NULL GROUP BY status")as$r)$counts[$r['status']]=(int)$r['total'];
        $posts=[];foreach(DB::table('posts')->whereNull('deleted_at')->orderBy('post_id','DESC')->get()as$p){$title=DB::table('translations')->where('table_name','posts')->where('table_id',(int)$p['post_id'])->where('locale','fa')->where('field','title')->whereNull('deleted_at')->first();$posts[]=['id'=>(int)$p['post_id'],'title'=>$title['value']??('نوشته '.$p['post_id'])];}
        return['comments'=>$items,'posts'=>$posts,'total'=>(int)$total,'page'=>$page,'perPage'=>$per,'statusCounts'=>$counts];
    }

    public function update(int$actor,int$id,array$data):void
    {
        $row=$this->find($id);$status=in_array($data['status']??'',['pending','approved','rejected'],true)?$data['status']:$row['status'];$content=trim((string)($data['content']??$this->content($row)));
        if($content==='')throw new RuntimeException('متن دیدگاه الزامی است.');$values=['status'=>$status,'updated_at'=>date('Y-m-d H:i:s')];if(array_key_exists('updated_by',$row))$values['updated_by']=$actor;if($status==='approved'){$values['approved_at']=date('Y-m-d H:i:s');$values['approved_by']=$actor;}if($this->directContent)$values['content']=$content;
        DB::table($this->table)->where($this->key,$id)->update($values);if(!$this->directContent)$this->setContent($id,$content,$actor);
    }

    public function reply(int$actor,int$id,array$data):int
    {
        $parent=$this->find($id);$content=trim((string)($data['content']??''));if($content==='')throw new RuntimeException('متن پاسخ الزامی است.');$values=['post_id'=>(int)$parent['post_id'],'user_id'=>$actor,'parent_id'=>$id,'status'=>'approved','approved_at'=>date('Y-m-d H:i:s'),'approved_by'=>$actor,'created_at'=>date('Y-m-d H:i:s')];if($this->directContent)$values['content']=$content;else{$values['created_by']=$actor;$values['updated_by']=$actor;}$newId=(int)DB::table($this->table)->insertGetId($values);if(!$this->directContent)$this->setContent($newId,$content,$actor);return$newId;
    }

    public function delete(int$actor,int$id):void
    {
        $row=$this->find($id);$now=date('Y-m-d H:i:s');$values=['deleted_at'=>$now];if(array_key_exists('deleted_by',$row))$values['deleted_by']=$actor;DB::table($this->table)->where($this->key,$id)->update($values);if(!$this->directContent)DB::table('translations')->where('table_name',$this->table)->where('table_id',$id)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$actor]);
    }

    private function find(int$id):array{$row=DB::table($this->table)->where($this->key,$id)->whereNull('deleted_at')->first();if(!$row)throw new RuntimeException('دیدگاه یافت نشد.');return$row;}
    private function map(array$r):array{$id=(int)$r[$this->key];$post=DB::table('translations')->where('table_name','posts')->where('table_id',(int)$r['post_id'])->where('locale','fa')->where('field','title')->whereNull('deleted_at')->first();$userName='';if(!empty($r['user_id'])){$tr=DB::table('translations')->where('table_name','users')->where('table_id',(int)$r['user_id'])->where('locale','fa')->where('field','full_name')->whereNull('deleted_at')->first();$userName=$tr['value']??$r['username']??'';}return['id'=>$id,'postId'=>(int)$r['post_id'],'postTitle'=>$post['value']??('نوشته '.$r['post_id']),'author'=>$userName?:($r['guest_name']??'مهمان'),'email'=>$r['guest_email']??'','content'=>$this->content($r),'parentId'=>$r['parent_id']?(int)$r['parent_id']:null,'status'=>$r['status']?:'pending','ip'=>$r['ip']??'','createdAt'=>$r['created_at']??null];}
    private function content(array$r):string{if($this->directContent)return(string)($r['content']??'');$tr=DB::table('translations')->where('table_name',$this->table)->where('table_id',(int)$r[$this->key])->where('field','content')->whereNull('deleted_at')->first();return(string)($tr['value']??'');}
    private function setContent(int$id,string$content,int$actor):void{$tr=DB::table('translations')->where('table_name',$this->table)->where('table_id',$id)->where('locale','fa')->where('field','content')->first();$v=['value'=>$content,'version'=>1,'updated_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];if($tr)DB::table('translations')->where('translation_id',(int)$tr['translation_id'])->update($v);else DB::table('translations')->insert(['table_name'=>$this->table,'table_id'=>$id,'locale'=>'fa','field'=>'content','created_by'=>$actor]+$v);}
    private function query(string$sql,array$bind=[],bool$scalar=false):mixed{$s=db()->prepare($sql);$s->execute($bind);return$scalar?$s->fetchColumn():$s->fetchAll();}
}
