<?php
namespace Modules\Social\Services;

use Modules\Social\Repositories\SocialRepository;
use RuntimeException;

class SocialService
{
    public function __construct(private SocialRepository $r) {}
    public function user(int $id): array
    {
        $user = $this->r->one('SELECT user_id,username FROM users WHERE user_id=? AND deleted_at IS NULL', [$id]);
        if (!$user) throw new RuntimeException('کاربر پیدا نشد.',404);
        return $user;
    }
    public function profile(int $actor, int $id): array
    {
        $user = $this->user($id);
        $profile = $this->r->one('SELECT * FROM social_profiles WHERE user_id=?',[$id]) ?: [];
        $count = fn($sql,$args)=>(int)($this->r->one($sql,$args)['n']??0);
        $settings=json_decode($this->r->one('SELECT settings_json FROM social_account_settings WHERE user_id=?',[$id])['settings_json']??'{}',true)?:[];
        return ['links'=>array_intersect_key($settings,array_flip(['website','instagram','youtube'])),'id'=>$id,'username'=>$user['username'],'name'=>($profile['display_name']??'') ?: $user['username'],
            'bio'=>$profile['bio']??'','avatar'=>$this->url($profile['avatar_id']??null),'cover'=>$this->url($profile['cover_id']??null),
            'followers'=>$count('SELECT COUNT(*) n FROM social_follows WHERE following_id=?',[$id]),
            'following'=>$count('SELECT COUNT(*) n FROM social_follows WHERE follower_id=?',[$id]),
            'posts'=>$count("SELECT COUNT(*) n FROM social_posts WHERE owner_id=? AND kind='post' AND deleted_at IS NULL",[$id]),
            'courses'=>$count("SELECT COUNT(*) n FROM creator_courses WHERE owner_id=? AND status='published'",[$id]),
            'isFollowing'=>(bool)$this->r->one('SELECT 1 FROM social_follows WHERE follower_id=? AND following_id=?',[$actor,$id]),'isMe'=>$actor===$id];
    }
    public function updateProfile(int $actor,array $data): array
    {
        $name=$this->text($data['name']??'',180,true); $bio=$this->text($data['bio']??'',3000);
        $current=$this->r->one('SELECT * FROM social_profiles WHERE user_id=?',[$actor])?:[];
        $ids=[];
        foreach(['avatar','cover'] as $key){
            $id=(int)($data[$key.'_id']??$current[$key.'_id']??0);
            if($id){$media=$this->ownMedia($actor,$id);if(!str_starts_with($media['mime'],'image/'))throw new RuntimeException('تصویر معتبر انتخاب کنید.',422);}
            $ids[]=$id?:null;
        }
        $this->r->query('INSERT INTO social_profiles(user_id,display_name,bio,avatar_id,cover_id) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE display_name=VALUES(display_name),bio=VALUES(bio),avatar_id=VALUES(avatar_id),cover_id=VALUES(cover_id)',[$actor,$name,$bio,...$ids]);
        return $this->profile($actor,$actor);
    }
    public function people(int $actor,string $search,int $id=0,string $kind=''): array
    {
        $params=[]; $where='u.deleted_at IS NULL';
        if($id){$this->user($id);$where.=$kind==='followers'?' AND EXISTS(SELECT 1 FROM social_follows f WHERE f.follower_id=u.user_id AND f.following_id=?)':' AND EXISTS(SELECT 1 FROM social_follows f WHERE f.following_id=u.user_id AND f.follower_id=?)';$params[]=$id;}
        if(trim($search)!==''){$where.=' AND u.username LIKE ?';$params[]='%'.mb_substr(trim($search),0,80).'%';}
        return array_map(fn($u)=>$this->profile($actor,(int)$u['user_id']),$this->r->query("SELECT u.user_id FROM users u WHERE $where ORDER BY u.user_id DESC LIMIT 50",$params));
    }
    public function follow(int $actor,int $id,bool $follow): array
    {
        $this->user($id); if($actor===$id)throw new RuntimeException('نمی‌توانید خودتان را دنبال کنید.',422);
        $this->r->transaction(function()use($actor,$id,$follow){
            $this->r->one('SELECT user_id FROM users WHERE user_id=? FOR UPDATE',[$actor]);
            $exists=$this->r->one('SELECT 1 FROM social_follows WHERE follower_id=? AND following_id=?',[$actor,$id]);
            if($follow&&!$exists){$this->r->query('INSERT INTO social_follows(follower_id,following_id) VALUES(?,?)',[$actor,$id]);$this->notify($id,$actor,'follow',$actor,'شما را دنبال کرد.');}
            if(!$follow)$this->r->query('DELETE FROM social_follows WHERE follower_id=? AND following_id=?',[$actor,$id]);
        });
        return $this->profile($actor,$id);
    }
    public function posts(int $actor,string $kind='post',int $owner=0,int $before=0,bool $saved=false): array
    {
        $where="p.deleted_at IS NULL AND (p.expires_at IS NULL OR p.expires_at>UTC_TIMESTAMP()) AND p.kind=?"; $params=[$kind==='story'?'story':'post'];
        if($owner){$where.=' AND p.owner_id=?';$params[]=$owner;}
        if($before){$where.=' AND p.id<?';$params[]=$before;}
        if($saved){$where.=" AND EXISTS(SELECT 1 FROM social_reactions r WHERE r.post_id=p.id AND r.user_id=? AND r.kind='save')";$params[]=$actor;}
        $rows=$this->r->query("SELECT p.*,m.mime FROM social_posts p LEFT JOIN social_media m ON m.id=p.media_id WHERE $where ORDER BY p.id DESC LIMIT 30",$params);
        return array_map(fn($p)=>$this->postData($actor,$p),$rows);
    }
    public function post(int $actor,int $id): array
    {
        $p=$this->r->one('SELECT p.*,m.mime FROM social_posts p LEFT JOIN social_media m ON m.id=p.media_id WHERE p.id=? AND p.deleted_at IS NULL AND (p.expires_at IS NULL OR p.expires_at>UTC_TIMESTAMP())',[$id]);
        if(!$p)throw new RuntimeException('محتوا پیدا نشد یا منقضی شده است.',404);
        return $this->postData($actor,$p);
    }
    private function postData(int $actor,array $p): array
    {
        $id=(int)$p['id'];
        $p['author']=$this->profile($actor,(int)$p['owner_id']);$p['media']=$this->url($p['media_id']);
        $p['liked']=(bool)$this->r->one("SELECT 1 FROM social_reactions WHERE user_id=? AND post_id=? AND kind='like'",[$actor,$id]);
        $p['saved']=(bool)$this->r->one("SELECT 1 FROM social_reactions WHERE user_id=? AND post_id=? AND kind='save'",[$actor,$id]);
        $p['likes']=(int)$this->r->one("SELECT COUNT(*) n FROM social_reactions WHERE post_id=? AND kind='like'",[$id])['n'];
        return $p;
    }
    public function publish(int $actor,array $data): array
    {
        $kind=$data['kind']??'post';if(!in_array($kind,['post','story'],true))throw new RuntimeException('نوع محتوا معتبر نیست.',422);
        $body=$this->text($data['body']??'',10000);$mid=(int)($data['media_id']??0);
        if($mid)$this->ownMedia($actor,$mid);
        if(($body===''&&!$mid)||($kind==='story'&&!$mid))throw new RuntimeException('برای استوری فایل و برای پست متن یا فایل اضافه کنید.',422);
        $id=$this->r->insert('social_posts',['owner_id'=>$actor,'body'=>$body,'kind'=>$kind,'media_id'=>$mid?:null,'expires_at'=>$kind==='story'?gmdate('Y-m-d H:i:s',time()+86400):null]);
        return $this->post($actor,$id);
    }
    public function remove(int $actor,int $id): array
    {
        $post=$this->post($actor,$id);if((int)$post['owner_id']!==$actor)throw new RuntimeException('اجازه حذف این محتوا را ندارید.',403);
        $this->r->query('UPDATE social_posts SET deleted_at=UTC_TIMESTAMP() WHERE id=? AND owner_id=?',[$id,$actor]);return [];
    }
    public function react(int $actor,int $id,string $kind,bool $active): array
    {
        if(!in_array($kind,['like','save'],true))throw new RuntimeException('عملیات نامعتبر است.',422);
        $p=$this->post($actor,$id);
        $this->r->transaction(function()use($actor,$id,$kind,$active,$p){
            $this->r->one('SELECT id FROM social_posts WHERE id=? FOR UPDATE',[$id]);
            $exists=$this->r->one('SELECT 1 FROM social_reactions WHERE user_id=? AND post_id=? AND kind=?',[$actor,$id,$kind]);
            if($active&&!$exists){$this->r->query('INSERT INTO social_reactions(user_id,post_id,kind) VALUES(?,?,?)',[$actor,$id,$kind]);if($kind==='like')$this->notify((int)$p['owner_id'],$actor,'like',$id,'پست شما را پسندید.');}
            if(!$active)$this->r->query('DELETE FROM social_reactions WHERE user_id=? AND post_id=? AND kind=?',[$actor,$id,$kind]);
        });return $this->post($actor,$id);
    }
    public function notifications(int $actor): array
    {
        return array_map(function($n)use($actor){$n['actor']=$this->profile($actor,(int)$n['actor_id']);return $n;},$this->r->query('SELECT * FROM social_notifications WHERE user_id=? ORDER BY id DESC LIMIT 100',[$actor]));
    }
    public function read(int $actor,int $id): array { $this->r->query('UPDATE social_notifications SET read_at=UTC_TIMESTAMP() WHERE id=? AND user_id=?',[$id,$actor]);return []; }
    public function notify(int $user,int $actor,string $kind,int $target,string $body): void
    {
        $settings=json_decode($this->r->one('SELECT settings_json FROM social_account_settings WHERE user_id=?',[$user])['settings_json']??'{}',true)?:[];
        if(($settings[$kind]??true)===false)return;
        if($user!==$actor)$this->r->insert('social_notifications',['user_id'=>$user,'actor_id'=>$actor,'kind'=>$kind,'target_id'=>$target,'body'=>$body]);
    }
    public function upload(int $actor,array $file): array
    {
        if(($file['error']??4)!==UPLOAD_ERR_OK||!is_uploaded_file($file['tmp_name']??''))throw new RuntimeException('آپلود کامل نشد. حجم فایل را بررسی کنید.',422);
        $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);$size=filesize($file['tmp_name']);
        $types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','video/mp4'=>'mp4','video/webm'=>'webm'];
        if(!isset($types[$mime])||!$size||$size>(str_starts_with($mime,'image/')?10:100)*1024*1024)throw new RuntimeException('تصویر تا ۱۰ مگابایت و ویدیو MP4 یا WebM تا ۱۰۰ مگابایت مجاز است.',422);
        if(str_starts_with($mime,'image/')&&!getimagesize($file['tmp_name']))throw new RuntimeException('تصویر معتبر نیست.',422);
        $name=bin2hex(random_bytes(24)).'.'.$types[$mime];$path=base_path('storage/social-media/'.$name);
        return $this->r->transaction(function()use($actor,$size,$mime,$name,$path,$file){
            $this->r->one('SELECT user_id FROM users WHERE user_id=? FOR UPDATE',[$actor]);
            $total=$this->r->one('SELECT COALESCE(SUM(bytes),0) n FROM social_media WHERE owner_id=?',[$actor]);
            if((int)$total['n']+$size>2*1024*1024*1024)throw new RuntimeException('فضای ۲ گیگابایتی حساب پر شده است.',422);
            if(!move_uploaded_file($file['tmp_name'],$path))throw new RuntimeException('ذخیره فایل انجام نشد.',500);
            try{$id=$this->r->insert('social_media',['owner_id'=>$actor,'filename'=>$name,'mime'=>$mime,'bytes'=>$size]);}catch(\Throwable $e){unlink($path);throw $e;}
            return ['id'=>$id,'url'=>$this->url($id),'mime'=>$mime];
        });
    }
    public function media(int $actor,int $id): array
    {
        $m=$this->r->one('SELECT * FROM social_media WHERE id=?',[$id]);if(!$m)throw new RuntimeException('فایل پیدا نشد.',404);
        if((int)$m['owner_id']!==$actor&&!$this->r->one('SELECT 1 FROM social_profiles WHERE avatar_id=? OR cover_id=?',[$id,$id])&&!$this->r->one('SELECT 1 FROM social_posts WHERE media_id=? AND deleted_at IS NULL AND (expires_at IS NULL OR expires_at>UTC_TIMESTAMP())',[$id]))throw new RuntimeException('محتوا در دسترس نیست.',404);
        return $m;
    }
    private function ownMedia(int $actor,int $id): array
    {
        $m=$this->r->one('SELECT * FROM social_media WHERE id=? AND owner_id=?',[$id,$actor]);if(!$m)throw new RuntimeException('فایل متعلق به شما نیست.',403);return $m;
    }
    private function url(mixed $id): ?string { return $id?'/api/sornaz/v1/social/media/'.(int)$id:null; }
    private function text(mixed $text,int $max,bool $required=false): string
    {
        if(!is_string($text)||mb_strlen(trim($text))>$max||($required&&trim($text)===''))throw new RuntimeException('عنوان یا متن معتبر وارد کنید.',422);return trim($text);
    }
}
