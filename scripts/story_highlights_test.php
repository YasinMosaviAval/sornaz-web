<?php
spl_autoload_register(function($class){$p=dirname(__DIR__).'/'.str_replace('\\','/',$class).'.php';if(is_file($p))require_once $p;});
$db=new PDO('sqlite::memory:');$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$db->sqliteCreateFunction('UTC_TIMESTAMP',fn()=>gmdate('Y-m-d H:i:s'));
$db->exec("CREATE TABLE users(user_id INTEGER PRIMARY KEY,username TEXT,avatar_file_id INTEGER,type TEXT,register_method TEXT,deleted_at TEXT);
CREATE TABLE social_profiles(user_id INTEGER,avatar_id INTEGER,cover_id INTEGER);
CREATE TABLE social_media(id INTEGER PRIMARY KEY,owner_id INTEGER,mime TEXT,filename TEXT);
CREATE TABLE social_posts(id INTEGER PRIMARY KEY AUTOINCREMENT,owner_id INTEGER,body TEXT,kind TEXT,media_id INTEGER,created_at TEXT DEFAULT CURRENT_TIMESTAMP,expires_at TEXT,deleted_at TEXT);
CREATE TABLE social_story_mentions(story_id INTEGER,user_id INTEGER);
CREATE TABLE social_reactions(user_id INTEGER,post_id INTEGER,kind TEXT);
CREATE TABLE social_highlights(id INTEGER PRIMARY KEY AUTOINCREMENT,owner_id INTEGER,title TEXT,cover_id INTEGER);
CREATE TABLE social_highlight_stories(highlight_id INTEGER,story_id INTEGER);
INSERT INTO users VALUES(1,'one',NULL,'human','email',NULL),(2,'two',NULL,'human','phone',NULL),(3,'internal',NULL,'human','academy',NULL),(4,'academy',NULL,'academy','email',NULL),(5,'branch',NULL,'branch','phone',NULL);
INSERT INTO social_media VALUES(1,1,'image/png','one.png'),(2,2,'image/png','two.png'),(3,1,'video/mp4','three.mp4');");
$r=new class($db) extends \Modules\Social\Repositories\SocialRepository{public function query(string $sql,array $params=[]):array{return parent::query(str_replace(' FOR UPDATE','',$sql),$params);}};
$s=new class($r) extends \Modules\Social\Services\SocialService {public function profile(int $actor,int $id):array{$u=$this->user($id);return ['id'=>$id,'username'=>$u['username']];}};
function check($condition,$message){if(!$condition)throw new RuntimeException($message);}
function denied(callable $callback,int $code){try{$callback();throw new RuntimeException('Expected rejection');}catch(RuntimeException $e){check($e->getCode()===$code,'Unexpected rejection: '.$e->getMessage());}}
check(array_column($s->people(1,''),'id')===[5,4,2,1],'Discovery must omit internally created academy users');
$story=$s->publish(1,['kind'=>'story','body'=>'Caption','media_id'=>1,'mention_ids'=>'[2]']);
check($story['body']==='Caption'&&$story['mentions'][0]['id']===2,'Caption and clickable mention persisted');
$id=(int)$story['id'];$db->exec("UPDATE social_posts SET expires_at='2000-01-01' WHERE id=$id");
denied(fn()=>$s->media(2,1),404);
$highlight=$s->saveHighlight(1,['title'=>'Music','cover_id'=>1,'story_ids'=>json_encode([$id])]);$hid=(int)$highlight['id'];
check(count($s->highlights(2,1))===1,'Highlight is visible on owner profile');
check($s->highlightStories(2,$hid)[0]['id']===$id,'Highlight keeps expired story available');
check($s->media(2,1)['id']===1,'Highlighted media stays readable');
denied(fn()=>$s->saveHighlight(2,['id'=>$hid,'title'=>'Bad','cover_id'=>2,'story_ids'=>json_encode([$id])]),403);
denied(fn()=>$s->saveHighlight(2,['title'=>'Bad','cover_id'=>2,'story_ids'=>json_encode([$id])]),403);
denied(fn()=>$s->saveHighlight(1,['title'=>'Bad','cover_id'=>3,'story_ids'=>json_encode([$id])]),422);
denied(fn()=>$s->deleteHighlight(2,$hid),403);
check(count($s->storyArchive(1))===1&&count($s->storyArchive(2))===0,'Archive is owner scoped');
$s->saveHighlight(1,['id'=>$hid,'title'=>'Renamed','cover_id'=>1,'story_ids'=>json_encode([$id])]);check($s->highlights(2,1)[0]['title']==='Renamed','Highlight title updates');
$s->deleteHighlight(1,$hid);check(count($s->highlights(2,1))===0,'Highlight deleted');denied(fn()=>$s->media(2,1),404);
echo "Story highlights, mentions, discovery and access checks passed.\n";
