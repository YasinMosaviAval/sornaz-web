<?php
namespace Modules\Analytics\Services { class ChatService {} }
namespace {
require __DIR__.'/../Modules/CourseMarket/Repositories/CourseRepository.php';
require __DIR__.'/../Modules/Social/Repositories/SocialRepository.php';
require __DIR__.'/../Modules/Social/Services/SocialService.php';
require __DIR__.'/../Modules/Social/Services/SocialInteractionService.php';
$db=new PDO('sqlite::memory:');$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$db->sqliteCreateFunction('UTC_TIMESTAMP',fn()=>gmdate('Y-m-d H:i:s'));
$db->exec('CREATE TABLE social_comments(id INTEGER PRIMARY KEY AUTOINCREMENT,post_id INTEGER,user_id INTEGER,parent_id INTEGER,body TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP,deleted_at TEXT);
CREATE TABLE social_comment_likes(comment_id INTEGER,user_id INTEGER,PRIMARY KEY(comment_id,user_id));');
$repository=new class($db) extends \Modules\Social\Repositories\SocialRepository {
 public function query(string $sql,array $params=[]):array{return parent::query(str_replace('INSERT IGNORE','INSERT OR IGNORE',$sql),$params);}
};
$social=new class($repository) extends \Modules\Social\Services\SocialService {
 public function post(int $actor,int $id):array {if(!in_array($id,[1,2],true))throw new RuntimeException('Not found',404);return ['id'=>$id,'kind'=>'post','owner_id'=>1];}
 public function profile(int $actor,int $id):array{return ['id'=>$id,'username'=>'member'.$id];}
 public function notify(int $user,int $actor,string $kind,int $target,string $body):void{}
};
$service=new \Modules\Social\Services\SocialInteractionService($repository,$social,new \Modules\Analytics\Services\ChatService);
$checks=0;
function check($value,$message){global $checks;if(!$value)throw new RuntimeException($message);$checks++;}
function denied($callback,$status){try{$callback();}catch(RuntimeException $e){check($e->getCode()===$status,'Wrong error status');return;}throw new RuntimeException('Unauthorized operation succeeded');}
$root=$service->comment(2,1,'Original comment');
$reply=$service->comment(3,1,'Reply',(int)$root['id']);
check($reply['parent_id']===$root['id'],'Reply lost parent');
check($reply['parent_body']==='Original comment','Reply context missing');
denied(fn()=>$service->comment(3,2,'Wrong post',(int)$root['id']),404);
denied(fn()=>$service->comment(0,1,'Guest'),401);
denied(fn()=>$service->likeComment(0,1,(int)$root['id'],true),401);
$service->likeComment(3,1,(int)$root['id'],true);$service->likeComment(3,1,(int)$root['id'],true);
$rows=$service->comments(3,1);
check($rows[1]['likes']===1,'Repeated like counted twice');
check($rows[1]['liked']===true,'Actor like state missing');
check($service->comments(2,1)[1]['liked']===false,'Like leaked across accounts');
check($service->likeComment(3,1,(int)$root['id'],false)['likes']===0,'Unlike failed');
denied(fn()=>$service->likeComment(3,2,(int)$root['id'],true),404);
denied(fn()=>$service->deleteComment(3,1,(int)$root['id']),403);
$service->deleteComment(1,1,(int)$root['id']);
denied(fn()=>$service->comment(3,1,'Deleted parent',(int)$root['id']),404);
denied(fn()=>$service->likeComment(3,1,(int)$root['id'],true),404);
$rows=$service->comments(3,1);check(count($rows)===1,'Deleting parent lost a reply');
check($rows[0]['parent_body']===null,'Deleted parent text leaked');
for($i=0;$i<31;$i++)$service->comment(2,1,'Comment '.$i);
$first=$service->comments(2,1);check(count($first)===30,'Page limit');
check(count($service->comments(2,1,(int)end($first)['id']))===2,'Cursor pagination');
echo "Comment threads: $checks checks passed in an isolated in-memory database.\n";
}
