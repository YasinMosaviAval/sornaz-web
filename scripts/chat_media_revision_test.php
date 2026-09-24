<?php
// Isolated SQLite integration checks; never opens the application database.
spl_autoload_register(function($class){$path=__DIR__.'/../'.str_replace('\\','/',$class).'.php';if(is_file($path))require_once $path;});
$pdo=new PDO('sqlite::memory:');$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
$pdo->sqliteCreateFunction('UTC_TIMESTAMP',fn()=>gmdate('Y-m-d H:i:s'));
function db(){global $pdo;return $pdo;}
function locale(){return 'en';}
function session(){return new class { public function get($key,$default=null){return $key==='suppress_database_notifications'?true:$default;} };}
function env($key,$default=null){return $default;}
function transaction($callback){global $pdo;$pdo->beginTransaction();try{$value=$callback();$pdo->commit();return $value;}catch(Throwable $e){$pdo->rollBack();throw $e;}}
$checks=0;
function check($value,$message){global $checks;if(!$value)throw new RuntimeException($message);$checks++;}
function denied($callback){try{$callback();}catch(RuntimeException $e){check(true,'Denied');return;}throw new RuntimeException('Unauthorized operation succeeded');}
$pdo->exec("CREATE TABLE users(user_id INTEGER PRIMARY KEY,username TEXT,type TEXT DEFAULT 'human',avatar_file_id INTEGER,timezone TEXT DEFAULT 'UTC',deleted_at TEXT);
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,deleted_at TEXT);
CREATE TABLE media_files(media_file_id INTEGER PRIMARY KEY,user_id INTEGER,collection TEXT,path TEXT,sort_order INTEGER,deleted_at TEXT);
CREATE TABLE conversations(conversation_id INTEGER PRIMARY KEY,type TEXT,title TEXT,avatar_path TEXT,last_message_id INTEGER,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER);
CREATE TABLE conversation_members(conversation_member_id INTEGER PRIMARY KEY AUTOINCREMENT,conversation_id INTEGER,user_id INTEGER,role TEXT,is_muted INTEGER,joined_at TEXT,last_read_message_id INTEGER DEFAULT 0,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER,left_at TEXT,deleted_at TEXT,deleted_by INTEGER);
CREATE TABLE conversation_messages(conversation_message_id INTEGER PRIMARY KEY AUTOINCREMENT,conversation_id INTEGER,sender_id INTEGER,body TEXT,reply_to_id INTEGER,message_kind TEXT DEFAULT 'text',attachment_path TEXT,attachment_name TEXT,attachment_mime TEXT,attachment_size INTEGER,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER,edited_at TEXT,deleted_at TEXT,deleted_by INTEGER);
CREATE TABLE conversation_message_reactions(conversation_message_reaction_id INTEGER PRIMARY KEY,conversation_message_id INTEGER,user_id INTEGER,reaction TEXT,created_at TEXT,created_by INTEGER);
CREATE TABLE social_posts(id INTEGER PRIMARY KEY AUTOINCREMENT,owner_id INTEGER,kind TEXT,body TEXT,media_id INTEGER,shared_post_id INTEGER,expires_at TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP,deleted_at TEXT);
CREATE TABLE social_media(id INTEGER PRIMARY KEY,owner_id INTEGER,mime TEXT,path TEXT);
CREATE TABLE social_reactions(post_id INTEGER,user_id INTEGER,kind TEXT);
CREATE TABLE social_comments(id INTEGER PRIMARY KEY,post_id INTEGER,user_id INTEGER,body TEXT,parent_id INTEGER,created_at TEXT,deleted_at TEXT);
CREATE TABLE social_story_mentions(story_id INTEGER,user_id INTEGER);
INSERT INTO users(user_id,username) VALUES(1,'Admin'),(2,'Member'),(3,'New member'),(4,'Other member');
INSERT INTO conversations(conversation_id,type,title) VALUES(1,'group','Group'),(2,'direct',NULL);
INSERT INTO conversation_members(conversation_id,user_id,role) VALUES(1,1,'admin'),(1,2,'member'),(2,1,'admin'),(2,4,'member');
INSERT INTO social_media(id,owner_id,mime,path) VALUES(5,1,'image/jpeg','test.jpg');
INSERT INTO social_posts(id,owner_id,kind,body,media_id,expires_at) VALUES(1,1,'story','Expired story',5,'2000-01-01 00:00:00'),(2,1,'post','Original post',5,NULL);");
$chat=new Modules\Analytics\Services\ChatService();
$parent=$chat->send(1,1,'Original message')['id'];
$reply=$chat->send(2,1,'Reply',[],$parent)['id'];
$chat->messages(1,1,0,false);
check((int)$pdo->query('SELECT last_read_message_id FROM conversation_members WHERE conversation_id=1 AND user_id=1')->fetchColumn()===$parent,'Background sync marked a message read');
$rows=$chat->messages(1,1)['messages'];
check((int)$pdo->query('SELECT last_read_message_id FROM conversation_members WHERE conversation_id=1 AND user_id=1')->fetchColumn()===$reply,'Opening conversation failed to mark read');
check($rows[1]['reply']['id']===$parent && $rows[1]['reply']['body']==='Original message','Reply context was lost');
denied(fn()=>$chat->send(1,2,'Cross-conversation reply',[],$parent));
denied(fn()=>$chat->send(4,1,'Not a member'));
$chat->editMessage(1,$parent,'Edited in composer');
check($chat->messages(2,1)['messages'][1]['reply']['body']==='Edited in composer','Quoted message did not update');
$chat->deleteMessage(1,$parent);
check($chat->messages(2,1)['messages'][0]['reply']['body']==='Message deleted','Deleted reply leaked old text');
$chat->addMembers(1,1,[1,2,3]);
check($pdo->query('SELECT role FROM conversation_members WHERE conversation_id=1 AND user_id=1')->fetchColumn()==='admin','Adding an existing admin demoted them');
$rows=$chat->messages(1,1)['messages'];$notice=end($rows);
check($notice['system']===true,'Membership event was not a system message');
denied(fn()=>$chat->send(1,1,'Reply to notice',[],$notice['id']));
$details=$chat->details(1,1);
check(array_column($details['availableUsers'],'id')===[4],'Member picker includes current members');
check($details['canManage'] && !$details['canLeave'] && $details['canDelete'],'Admin permissions');
$details=$chat->details(2,1);
check(!$details['canManage'] && $details['canLeave'] && !$details['canDelete'],'Member permissions');
denied(fn()=>$chat->removeMember(2,1,3));
$chat->send(1,1,"Answer\n/community/stories/1");
$rows=$chat->messages(2,1)['messages'];$ref=end($rows);
check(!$ref['reference']['available'] && !isset($ref['reference']['media']),'Expired story thumbnail leaked to another member');
check($ref['body']==='Answer','Story link was not removed from the message');
$rows=$chat->messages(1,1)['messages'];$ref=end($rows);
check($ref['reference']['available'] && $ref['reference']['owner'] && isset($ref['reference']['media']),'Owner lost access to archived story');
$shared=$chat->send(2,1,'/community/posts/2')['id'];$chat->editMessage(2,$shared,'Edited comment');
$rows=$chat->messages(1,1)['messages'];$ref=end($rows);
check($ref['reference']['kind']==='post' && $ref['reference']['available'] && $ref['body']==='Edited comment','Editing shared post lost its preview');
$repository=new Modules\Social\Repositories\SocialRepository($pdo);
$social=new class($repository) extends Modules\Social\Services\SocialService {
 public function profile(int $actor,int $id):array{return ['id'=>$id,'name'=>'Author '.$id,'username'=>'user'.$id];}
};
check($social->post(1,1)['id']===1,'Owner cannot open expired story');
denied(fn()=>$social->post(2,1));
foreach([1,2] as $actor){$story=$social->storyFromPost($actor,2);check($story['kind']==='story' && (int)$story['owner_id']===$actor && (int)$story['shared_post_id']===2 && (int)$story['media_id']===5,'Post sharing lost author, source or media');check(strtotime($story['expires_at'].' UTC')>time(),'Shared story immediately expired');}
denied(fn()=>$social->storyFromPost(0,2));
denied(fn()=>$social->storyFromPost(1,1));
echo "Chat, offline sync and story sharing: $checks integration checks passed.\n";