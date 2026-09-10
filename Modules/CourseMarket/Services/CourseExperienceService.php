<?php
namespace Modules\CourseMarket\Services;

use Modules\CourseMarket\Repositories\CourseRepository;
use RuntimeException;

/** Public course presentation and authenticated engagement, separate from lesson access. */
class CourseExperienceService
{
    public function __construct(private CourseRepository $r, private CourseService $courses) {}
    public function decorate(array $c, int $actor, string $locale='fa'): array
    {
        $id=(int)$c['id'];
        $meta=json_decode($this->r->one('SELECT metadata FROM creator_course_details WHERE course_id=?',[$id])['metadata']??'{}',true)?:[];
        $c['details']=$meta;unset($c['details']['resources']);
        foreach(['title','description'] as $field)if($locale==='en'&&!empty($meta[$field.'_en']))$c[$field]=$meta[$field.'_en'];
        $c['category']=(string)($meta[$locale==='en'?'category_en':'category']??'');
        $c['duration_seconds']=(int)($meta['duration_seconds']??0);
        $c['lesson_count']=(int)($this->r->one('SELECT COUNT(*) n FROM creator_course_lessons WHERE course_id=? AND deleted_at IS NULL',[$id])['n']??0);
        $c['rating']=$this->r->one('SELECT COALESCE(AVG(score),0) average,COUNT(*) count FROM creator_course_reviews WHERE course_id=?',[$id]);
        $c['students']=(int)($this->r->one("SELECT COUNT(DISTINCT buyer_id) n FROM creator_course_orders WHERE course_id=? AND status='paid'",[$id])['n']??0);
        $u=$this->r->one('SELECT u.user_id id,u.username,sp.display_name,sp.avatar_id,sp.bio FROM users u LEFT JOIN social_profiles sp ON sp.user_id=u.user_id WHERE u.user_id=? AND u.deleted_at IS NULL',[$c['owner_id']])??[];
        $c['author']=['id'=>(int)($u['id']??0),'name'=>(string)(($u['display_name']??'')?:($u['username']??'')),'avatar'=>empty($u['avatar_id'])?null:'/api/sornaz/v1/social/media/'.$u['avatar_id'],'bio'=>$u['bio']??''];
        return $c;
    }
    public function detail(int $actor,int $id,string $locale): array
    {
        $c=$this->decorate($this->courses->detail($actor,$id),$actor,$locale);
        $c['reviews']=$this->r->query('SELECT r.*,u.username author FROM creator_course_reviews r JOIN users u ON u.user_id=r.user_id WHERE r.course_id=? AND r.locale=? ORDER BY r.updated_at DESC LIMIT 100',[$id,$locale]);
        $c['questions']=$this->r->query('SELECT q.*,u.username author FROM creator_course_questions q JOIN users u ON u.user_id=q.user_id WHERE q.course_id=? AND q.locale=? ORDER BY q.id ASC LIMIT 200',[$id,$locale]);
        $c['likes']=(int)($this->r->one('SELECT COUNT(*) n FROM creator_course_reactions WHERE course_id=? AND reaction=1',[$id])['n']??0);
        $c['reaction']=(int)($this->r->one('SELECT reaction FROM creator_course_reactions WHERE course_id=? AND user_id=?',[$id,$actor])['reaction']??0);
        $c['bookmarks']=(int)($this->r->one("SELECT COUNT(*) n FROM social_bookmarks WHERE kind='course' AND target_id=?",[$id])['n']??0);
        $c['schedule']=$actor?$this->r->one('SELECT starts_at FROM creator_course_schedules WHERE course_id=? AND user_id=?',[$id,$actor]):null;
        $metadata=json_decode($this->r->one('SELECT metadata FROM creator_course_details WHERE course_id=?',[$id])['metadata']??'{}',true)?:[];
        $c['resources']=$c['access']?array_values(array_filter($metadata['resources']??[],fn($r)=>empty($r['media_id'])||!$this->r->one('SELECT post_id FROM creator_course_lessons WHERE course_id=? AND deleted_at IS NULL AND JSON_CONTAINS(media_json,?)',[$id,json_encode((int)$r['media_id'])]))):[];
        // Restricted resource metadata is never included in the public details blob.
        foreach($c['resources'] as &$resource){
            if(empty($resource['media_id']))continue;
            $file=$this->r->one('SELECT id,mime,bytes FROM creator_course_media WHERE id=? AND course_id=?',[(int)$resource['media_id'],$id]);
            if(!$file)continue;
            $resource['mime']=$file['mime'];$resource['bytes']=(int)$file['bytes'];
            if(!in_array((int)$file['id'],array_map(fn($f)=>(int)$f['id'],$c['files']??[]),true))$c['files'][]=$file;
        }
        unset($resource);
        unset($c['details']['resources']);
        $catalog=$this->courses->listing($actor,'catalog');
        $c['related']=array_map(fn($row)=>$this->decorate($row,$actor,$locale),array_slice(array_values(array_filter($catalog,fn($r)=>(int)$r['id']!==$id)),0,3));
        $position=array_search($id,array_map(fn($row)=>(int)$row['id'],$catalog),true);
        $c['previous_id']=$position!==false&&$position>0?(int)$catalog[$position-1]['id']:null;
        $c['next_id']=$position!==false&&isset($catalog[$position+1])?(int)$catalog[$position+1]['id']:null;
        $c['rating_distribution']=$this->r->query('SELECT score,COUNT(*) count FROM creator_course_reviews WHERE course_id=? GROUP BY score ORDER BY score DESC',[$id]);
        return $c;
    }
    public function action(int $actor,int $id,string $action,array $data,string $locale): array
    {
        if($actor<1)throw new RuntimeException('Sign in to continue.',401);
        $c=$this->courses->detail($actor,$id);
        $text=trim((string)($data['body']??''));
        if(in_array($action,['review','question','report'],true)&&($text===''||mb_strlen($text)>3000))throw new RuntimeException('Enter between 1 and 3000 characters.',422);
        if($action==='review'){
            if(!$c['access'])throw new RuntimeException('Enroll before reviewing this course.',403);
            $score=filter_var($data['score']??null,FILTER_VALIDATE_INT);$recommend=$data['recommend']??null;
            if($score<1||$score>5||!in_array($recommend,['0','1'],true))throw new RuntimeException('Select a rating and recommendation.',422);
            $this->r->query('INSERT INTO creator_course_reviews(course_id,user_id,score,recommend,body,locale) VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE score=VALUES(score),recommend=VALUES(recommend),body=VALUES(body),locale=VALUES(locale),updated_at=CURRENT_TIMESTAMP',[$id,$actor,$score,$recommend,$text,$locale]);
        }elseif($action==='question'){
            $parent=max(0,(int)($data['parent_id']??0));if($parent&&!$this->r->one('SELECT id FROM creator_course_questions WHERE id=? AND course_id=? AND locale=?',[$parent,$id,$locale]))throw new RuntimeException('Question not found.',404);
            $this->r->insert('creator_course_questions',['course_id'=>$id,'user_id'=>$actor,'parent_id'=>$parent?:null,'body'=>$text,'locale'=>$locale]);
        }elseif($action==='react'){
            $reaction=(int)($data['reaction']??0);if(!in_array($reaction,[-1,0,1],true))throw new RuntimeException('Invalid reaction.',422);
            $this->r->query('INSERT INTO creator_course_reactions(course_id,user_id,reaction) VALUES(?,?,?) ON DUPLICATE KEY UPDATE reaction=VALUES(reaction)',[$id,$actor,$reaction]);
        }elseif($action==='report'){
            $this->r->insert('creator_course_reports',['course_id'=>$id,'user_id'=>$actor,'body'=>$text]);
        }elseif($action==='schedule'){
            $at=strtotime((string)($data['starts_at']??''));if(!$at||$at<time())throw new RuntimeException('Choose a future date and time.',422);
            $this->r->query('INSERT INTO creator_course_schedules(course_id,user_id,starts_at) VALUES(?,?,?) ON DUPLICATE KEY UPDATE starts_at=VALUES(starts_at)',[$id,$actor,date('Y-m-d H:i:s',$at)]);
        }else throw new RuntimeException('Action not found.',404);
        return ['saved'=>true];
    }
    public function saveMetadata(int $actor,int $id,array $data): array
    {
        $this->courses->owned($actor,$id);
        $allowed=['title_en','description_en','category','category_en','duration_seconds','original_price','summary','summary_en','language','level','preview_id','resources'];
        $meta=array_intersect_key($data,array_flip($allowed));
        foreach(['duration_seconds','original_price','preview_id'] as $f)if(isset($meta[$f]))$meta[$f]=max(0,(int)$meta[$f]);
        foreach($meta as $f=>$v)if(!in_array($f,['resources','duration_seconds','original_price','preview_id'],true)&&(!is_string($v)||mb_strlen($v)>20000))throw new RuntimeException('Invalid course metadata.',422);
        if(!empty($meta['preview_id'])){
            $m=$this->r->one('SELECT mime FROM creator_course_media WHERE id=? AND course_id=?',[$meta['preview_id'],$id]);
            if(!$m||!str_starts_with($m['mime'],'video/'))throw new RuntimeException('Select an uploaded course video.',422);
            if($this->r->one('SELECT post_id FROM creator_course_lessons WHERE course_id=? AND deleted_at IS NULL AND JSON_CONTAINS(media_json,?)',[$id,json_encode($meta['preview_id'])]))throw new RuntimeException('A private lesson file cannot be a public preview.',422);
        }
        if(isset($meta['resources'])){
            if(!is_array($meta['resources'])||count($meta['resources'])>100)throw new RuntimeException('Invalid resources.',422);
            foreach($meta['resources'] as $resource){if(!is_array($resource)||empty($resource['title'])||mb_strlen((string)$resource['title'])>180)throw new RuntimeException('Invalid resource title.',422);
                if(!empty($resource['url'])&&!preg_match('~^https://~',(string)$resource['url']))throw new RuntimeException('Use an HTTPS resource URL.',422);
                if(!empty($resource['media_id'])&&!$this->r->one('SELECT id FROM creator_course_media WHERE id=? AND course_id=?',[(int)$resource['media_id'],$id]))throw new RuntimeException('Invalid resource file.',422);
            }
        }
        $this->r->query('INSERT INTO creator_course_details(course_id,metadata) VALUES(?,?) ON DUPLICATE KEY UPDATE metadata=VALUES(metadata)',[$id,json_encode($meta,JSON_UNESCAPED_UNICODE)]);
        $this->r->query('UPDATE creator_courses SET updated_at=CURRENT_TIMESTAMP WHERE id=?',[$id]);return ['saved'=>true];
    }
}
