<?php
require __DIR__.'/../Modules/CourseMarket/Repositories/CourseRepository.php';
require __DIR__.'/../Modules/Social/Repositories/SocialRepository.php';
require __DIR__.'/../Modules/Social/Services/SocialService.php';
$db=new PDO('sqlite::memory:');$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$db->sqliteCreateFunction('UTC_TIMESTAMP',fn()=>gmdate('Y-m-d H:i:s'));
$db->exec('CREATE TABLE social_posts(id INTEGER PRIMARY KEY,owner_id INTEGER,kind TEXT,media_id INTEGER,deleted_at TEXT,expires_at TEXT);
CREATE TABLE social_media(id INTEGER PRIMARY KEY,mime TEXT);
CREATE TABLE social_follows(follower_id INTEGER,following_id INTEGER);
CREATE TABLE social_reactions(user_id INTEGER,post_id INTEGER,kind TEXT);
CREATE TABLE social_story_mentions(story_id INTEGER,user_id INTEGER);
CREATE TABLE social_comments(id INTEGER,post_id INTEGER,user_id INTEGER,parent_id INTEGER,body TEXT,created_at TEXT,deleted_at TEXT);
CREATE TABLE users(user_id INTEGER,username TEXT);
INSERT INTO social_follows VALUES(7,2);
INSERT INTO social_posts VALUES(1,1,"story",NULL,NULL,NULL),(2,2,"story",NULL,NULL,NULL),(3,3,"story",NULL,NULL,NULL),(4,7,"story",NULL,NULL,NULL),(5,2,"story",NULL,NULL,"2000-01-01"),(6,2,"story",NULL,"2020-01-01",NULL),(7,3,"post",NULL,NULL,NULL);');
$service=new class(new \Modules\Social\Repositories\SocialRepository($db)) extends \Modules\Social\Services\SocialService {
    public function profile(int $actor,int $id):array{return ['id'=>$id];}
};
function ids($rows){return array_map('intval',array_column($rows,'id'));}
function check($actual,$expected,$message){if($actual!==$expected)throw new RuntimeException($message.' '.json_encode($actual));}
check(ids($service->posts(7,'story')),[2,1],'Stage must show followed authors and administrator only');
check(ids($service->posts(0,'story')),[1],'Guest must not receive every author');
check(ids($service->posts(7,'story',3)),[3],'Profile story lookup must remain available');
check(ids($service->posts(7,'post')),[7],'Ordinary feed posts must remain available');
check(ids($service->posts(7,'story',0,2)),[1],'Story pagination must preserve filtering');
$db->exec('DELETE FROM social_follows');
check(ids($service->posts(7,'story')),[1],'Unfollowing must remove author stories');
echo "Story feed: followed users, administrator, guest, expiry, deletion, profile lookup and pagination passed.\n";
