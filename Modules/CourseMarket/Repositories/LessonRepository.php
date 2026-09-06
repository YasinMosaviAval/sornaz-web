<?php
namespace Modules\CourseMarket\Repositories;
use RuntimeException;

class LessonRepository
{
    public function __construct(private CourseRepository $r) {}

    // The caller holds the course row lock and transaction.
    public function sync(int $course,int $actor,array $chapters): array
    {
        $rows=$this->r->query('SELECT l.*,p.password FROM creator_course_lessons l JOIN posts p ON p.post_id=l.post_id WHERE l.course_id=? AND l.deleted_at IS NULL',[$course]);
        $map=array_column($rows,null,'post_id');$used=[];
        foreach($chapters as &$chapter){
            foreach($chapter['lessons'] as &$lesson){
                $id=(int)($lesson['post_id']??0);
                if($id&&(!isset($map[$id])||isset($used[$id])))throw new RuntimeException('شناسه درس متعلق به این دوره نیست یا تکراری است.',422);
                $old=$map[$id]??null;$password=(string)($lesson['password']??'');
                $changed=$password!==''||!empty($lesson['clear_password']);
                $required=$password!==''?1:(!empty($lesson['clear_password'])?0:(int)($old['requires_password']??0));
                $hash=(!$old||$changed)?password_hash($password!==''?$password:bin2hex(random_bytes(32)),PASSWORD_DEFAULT):$old['password'];
                if(!$id){
                    $id=$this->r->insert('posts',['author_id'=>$actor,'name'=>$lesson['title'],'type'=>'post','status'=>'private','visibility'=>'private','password'=>$hash,'slug'=>'course-lesson-'.bin2hex(random_bytes(12)),'created_by'=>$actor,'updated_by'=>$actor]);
                    $this->r->insert('creator_course_lessons',['post_id'=>$id,'course_id'=>$course,'media_json'=>json_encode($lesson['media'],JSON_THROW_ON_ERROR),'requires_password'=>$required]);
                }else{
                    $this->r->query("UPDATE posts SET name=?,status='private',visibility='private',password=?,updated_at=CURRENT_TIMESTAMP,updated_by=? WHERE post_id=? AND author_id=?",[$lesson['title'],$hash,$actor,$id,$actor]);
                    $this->r->query('UPDATE creator_course_lessons SET media_json=?,requires_password=?,password_revision=password_revision+? WHERE post_id=? AND course_id=?',[json_encode($lesson['media'],JSON_THROW_ON_ERROR),$required,$changed?1:0,$id,$course]);
                }
                foreach(['title'=>$lesson['title'],'content'=>$lesson['text']] as $field=>$value){
                    $t=$this->r->one("SELECT translation_id FROM translations WHERE table_name='posts' AND table_id=? AND locale='fa' AND field=?",[$id,$field]);
                    if($t)$this->r->query('UPDATE translations SET value=?,version=COALESCE(version,0)+1,updated_by=?,updated_at=CURRENT_TIMESTAMP,deleted_at=NULL WHERE translation_id=?',[$value,$actor,$t['translation_id']]);
                    else $this->r->insert('translations',['table_name'=>'posts','table_id'=>$id,'locale'=>'fa','field'=>$field,'value'=>$value,'version'=>1,'created_by'=>$actor,'updated_by'=>$actor]);
                }
                $used[$id]=true;$lesson=['post_id'=>$id];
            }
            unset($lesson);
        }
        unset($chapter);
        foreach($map as $id=>$old)if(!isset($used[$id])){
            $this->r->query('UPDATE creator_course_lessons SET deleted_at=CURRENT_TIMESTAMP WHERE post_id=?',[$id]);
            $this->r->query("UPDATE posts SET status='trash',visibility='private',deleted_at=CURRENT_TIMESTAMP,deleted_by=? WHERE post_id=?",[$actor,$id]);
        }
        return $chapters;
    }

    public function hydrate(int $course,array $chapters): array
    {
        $rows=$this->r->query("SELECT l.post_id,l.media_json,l.requires_password,p.name FROM creator_course_lessons l JOIN posts p ON p.post_id=l.post_id WHERE l.course_id=? AND l.deleted_at IS NULL AND p.deleted_at IS NULL AND p.visibility='private' AND p.status='private'",[$course]);
        $map=array_column($rows,null,'post_id');
        $rows=$this->r->query("SELECT t.table_id,t.field,t.value FROM translations t JOIN creator_course_lessons l ON l.post_id=t.table_id WHERE l.course_id=? AND t.table_name='posts' AND t.locale='fa' AND t.field IN ('title','content') AND t.deleted_at IS NULL",[$course]);
        $texts=[];foreach($rows as $t)$texts[$t['table_id']][$t['field']]=$t['value'];
        foreach($chapters as &$chapter){$lessons=[];
            foreach($chapter['lessons'] as $lesson){$id=(int)($lesson['post_id']??0);
                if(!$id){$lessons[]=$lesson;continue;}
                if(!isset($map[$id]))continue;$row=$map[$id];
                $lessons[]=['post_id'=>$id,'title'=>$texts[$id]['title']??$row['name'],'text'=>$texts[$id]['content']??'','media'=>json_decode($row['media_json'],true)?:[],'has_password'=>(bool)$row['requires_password']];
            }$chapter['lessons']=$lessons;
        }
        return $chapters;
    }
    public function unlocked(int $actor,int $post): bool
    {
        return (bool)$this->r->one('SELECT 1 FROM creator_course_lessons l JOIN creator_lesson_access a ON a.post_id=l.post_id AND a.password_revision=l.password_revision WHERE l.post_id=? AND a.user_id=? AND l.deleted_at IS NULL',[$post,$actor]);
    }
    public function unlock(int $actor,int $course,int $post,string $password): void
    {
        $l=$this->r->one("SELECT l.*,p.password FROM creator_course_lessons l JOIN posts p ON p.post_id=l.post_id WHERE l.course_id=? AND l.post_id=? AND l.deleted_at IS NULL AND p.deleted_at IS NULL AND p.status='private' AND p.visibility='private'",[$course,$post]);
        if(!$l)throw new RuntimeException('درس پیدا نشد.',404);
        if(!$l['requires_password']||!password_verify($password,$l['password']))throw new RuntimeException('رمز درس صحیح نیست.',422);
        $this->r->query('INSERT INTO creator_lesson_access(user_id,post_id,password_revision) VALUES(?,?,?) ON DUPLICATE KEY UPDATE password_revision=VALUES(password_revision)',[$actor,$post,$l['password_revision']]);
    }
}
