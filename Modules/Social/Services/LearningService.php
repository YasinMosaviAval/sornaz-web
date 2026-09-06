<?php
namespace Modules\Social\Services;

use Modules\Social\Repositories\SocialRepository;
use Modules\CourseMarket\Services\CourseService;
use RuntimeException;

class LearningService
{
    public function __construct(private SocialRepository $r, private CourseService $courses, private SocialService $social) {}

    public function home(int $actor): array
    {
        $courses=$this->courses->listing($actor,'catalog');
        foreach($courses as &$c){
            $c['lesson_count']=(int)($this->r->one('SELECT COUNT(*) n FROM creator_course_lessons WHERE course_id=? AND deleted_at IS NULL',[$c['id']])['n']??0);
            $c['author']=$this->social->profile($actor,(int)$c['owner_id']);
        }
        unset($c);
        $authors=[];foreach($courses as $c)$authors[$c['owner_id']]=$c['author'];
        return ['courses'=>$courses,'authors'=>array_values($authors)];
    }

    public function bookmarks(int $actor): array
    {
        $rows=$this->r->query('SELECT kind,target_id FROM social_bookmarks WHERE user_id=? ORDER BY created_at DESC',[$actor]);
        $result=['courses'=>[],'authors'=>[]];
        foreach($rows as $row){
            try{
                if($row['kind']==='author')$result['authors'][]=$this->social->profile($actor,(int)$row['target_id']);
                else { $c=$this->courses->detail($actor,(int)$row['target_id']);unset($c['curriculum'],$c['files']);$result['courses'][]=$c; }
            }catch(RuntimeException $e){if(!in_array($e->getCode(),[403,404],true))throw $e;}
        }
        return $result;
    }

    public function bookmark(int $actor,string $kind,int $id,bool $active): array
    {
        if(!in_array($kind,['course','author'],true)||$id<1)throw new RuntimeException('نشان معتبر نیست.',422);
        if($active){
            if($kind==='course')$this->courses->detail($actor,$id);else $this->social->user($id);
            $this->r->query('INSERT IGNORE INTO social_bookmarks(user_id,kind,target_id) VALUES(?,?,?)',[$actor,$kind,$id]);
        }else $this->r->query('DELETE FROM social_bookmarks WHERE user_id=? AND kind=? AND target_id=?',[$actor,$kind,$id]);
        return ['saved'=>$active];
    }

    public function progress(int $actor,int $courseId,int $postId,bool $complete): array
    {
        $course=$this->courses->detail($actor,$courseId);
        if(!$course['access'])throw new RuntimeException('ابتدا دوره را تهیه کنید.',403);
        $found=false;
        foreach($course['curriculum'] as $chapter)foreach($chapter['lessons'] as $lesson){
            if((int)$lesson['post_id']===$postId){$found=true;if($lesson['locked'])throw new RuntimeException('ابتدا رمز درس را وارد کنید.',403);}
        }
        if(!$found)throw new RuntimeException('درس پیدا نشد.',404);
        if($complete)$this->r->query('INSERT IGNORE INTO social_lesson_progress(user_id,course_id,post_id) VALUES(?,?,?)',[$actor,$courseId,$postId]);
        else $this->r->query('DELETE FROM social_lesson_progress WHERE user_id=? AND course_id=? AND post_id=?',[$actor,$courseId,$postId]);
        return ['completed'=>$complete];
    }

    public function dashboard(int $actor): array
    {
        $courses=$this->courses->listing($actor,'library');
        foreach($courses as &$c){
            $lessons=$this->r->query('SELECT post_id FROM creator_course_lessons WHERE course_id=? AND deleted_at IS NULL',[$c['id']]);
            $done=$this->r->query('SELECT p.post_id FROM social_lesson_progress p INNER JOIN creator_course_lessons l ON l.post_id=p.post_id AND l.course_id=p.course_id AND l.deleted_at IS NULL WHERE p.user_id=? AND p.course_id=?',[$actor,$c['id']]);
            $c['lesson_count']=count($lessons);$c['completed_ids']=array_map('intval',array_column($done,'post_id'));
            $c['completed']=count($done);$c['progress']=count($lessons)>0?(int)floor(count($done)*100/count($lessons)):0;
        }
        unset($c);
        return ['profile'=>$this->social->profile($actor,$actor),'courses'=>$courses,
            'completed_courses'=>count(array_filter($courses,fn($c)=>$c['progress']===100)),
            'completed_lessons'=>array_sum(array_column($courses,'completed'))];
    }

    public function settings(int $actor): array
    {
        return array_replace(['follow'=>true,'like'=>true,'message'=>true,'website'=>'','instagram'=>'','youtube'=>''],json_decode($this->r->one('SELECT settings_json FROM social_account_settings WHERE user_id=?',[$actor])['settings_json']??'{}',true)?:[]);
    }
    public function updateSettings(int $actor,array $data): array
    {
        return $this->r->transaction(function()use($actor,$data){
            $this->r->one('SELECT user_id FROM users WHERE user_id=? FOR UPDATE',[$actor]);
            $settings=$this->settings($actor);
            foreach(['follow','like','message'] as $key)if(isset($data[$key]))$settings[$key]=$data[$key]==='1';
            foreach(['website','instagram','youtube'] as $key)if(isset($data[$key])){
                $url=trim((string)$data[$key]);
                if($url!==''&&(strlen($url)>500||!filter_var($url,FILTER_VALIDATE_URL)||strtolower(parse_url($url,PHP_URL_SCHEME)??'')!=='https'))throw new RuntimeException('لینک باید آدرس معتبر https باشد.',422);
                $settings[$key]=$url;
            }
            $this->r->query('INSERT INTO social_account_settings(user_id,settings_json) VALUES(?,?) ON DUPLICATE KEY UPDATE settings_json=VALUES(settings_json)',[$actor,json_encode($settings,JSON_UNESCAPED_UNICODE)]);
            return $settings;
        });
    }
}
