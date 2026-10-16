const { spawnSync } = require('node:child_process');
const source=String.raw`<?php
$map=require 'vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($map){if(isset($map[$class]))require_once $map[$class];});
require_once 'core/database/ActivityText.php';
require_once 'core/database/DatabaseChangeNotifier.php';
require_once 'Modules/Analytics/Services/AdminNotificationService.php';
require_once 'Modules/Analytics/Services/PanelTableRenderer.php';
function db(){return $GLOBALS['pdo'];} function locale(){return $GLOBALS['language']??'fa';}
function auth(){return new class {function id(){return $GLOBALS['actor']??2;}function user(){return ['user_id'=>$this->id(),'type'=>'human'];}};}
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function component($name,$data=[]){extract($data);require 'Modules/Analytics/Resources/Views/sections/'.$name.'.php';}
function check($v,$m){if(!$v)throw new LogicException($m);$GLOBALS['checks']++;}
$checks=0;$pdo=new Core\database\PrefixedPDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$pdo->exec('CREATE TABLE users(user_id INTEGER PRIMARY KEY,username TEXT,deleted_at TEXT)');
$pdo->exec('CREATE TABLE user_messages(user_message_id INTEGER PRIMARY KEY AUTOINCREMENT,sender_id INTEGER,receiver_user_id INTEGER,type TEXT,status TEXT,related_entity_type TEXT,related_entity_id INTEGER,is_read INTEGER,created_by INTEGER,updated_by INTEGER,created_at TEXT DEFAULT CURRENT_TIMESTAMP,deleted_at TEXT)');
$pdo->exec('CREATE TABLE translations(translation_id INTEGER PRIMARY KEY AUTOINCREMENT,table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,version INTEGER,created_by INTEGER,updated_by INTEGER,deleted_at TEXT)');
$pdo->exec("INSERT INTO users VALUES(2,'sender',NULL),(3,'other',NULL)");
$pdo->exec("INSERT INTO translations(table_name,table_id,field,locale,value) VALUES('users',2,'full_name','fa','Sender name')");
Core\database\DatabaseChangeNotifier::record($pdo,'financial_system_discounts','insert',['type'=>'percentage','value'=>2,'created_by'=>2,'password'=>'SECRET'],5);
$service=new Modules\Analytics\Services\AdminNotificationService();
$notices=$service->all(2);check(count($notices)===1,'Recipient notification recorded');
check(str_contains($notices[0]['title'],'2'),'Discount value appears in notification');
check($notices[0]['branchName']==='Sender name','Actual sender displayed');
check(!str_contains(json_encode($notices[0]['title'].$notices[0]['body']),'financial_system')&&!str_contains($notices[0]['body'],'SECRET'),'No technical fields or secrets');
check($service->all(3)===[],'Recipient isolation');
$GLOBALS['language']='en';check($service->all(2)[0]['title']==='Created 2% discount for educational terms','English activity description');
$legacy=Core\database\ActivityText::legacy($pdo,['related_entity_type'=>'financial_system_discounts'],'Data insert in financial_system_discounts','old','en');
check($legacy['title']==='Created discount for educational terms','Old descriptions are readable without inventing historical values');
check(Core\database\ActivityText::legacy($pdo,[],'A custom notice','Custom body','en')['message']==='Custom body','Manual notices preserved');
check(Core\database\ActivityText::describe('unknown_private_table','insert',['password'=>'SECRET'],'en')['title']==='Created account activity','Unknown identifiers never exposed');
check(Core\database\ActivityText::action('user_lessons.insert','en')==='Created lesson and learning level','Point action uses activity names');
$pdo->beginTransaction();Core\database\DatabaseChangeNotifier::record($pdo,'lessons','insert',['created_by'=>2],2);$pdo->rollBack();check(count($service->all(2))===1,'Notification rolls back with transaction');
$pdo->exec('CREATE TABLE user_points(user_point_id INTEGER PRIMARY KEY,user_id INTEGER,type TEXT,points INTEGER,action TEXT,reference_type TEXT,reference_id INTEGER,rule_id INTEGER,award_key TEXT,metadata TEXT,created_at TEXT,updated_at TEXT,approved_at TEXT,approved_by INTEGER,created_by INTEGER,updated_by INTEGER,deleted_at TEXT)');
$pdo->exec("INSERT INTO user_points VALUES(10,2,'professional',12,'user_lessons.insert','PRIVATE_REF',99,1,'PRIVATE_KEY','PRIVATE_METADATA','2026-10-01','2026-10-02',NULL,1,1,1,NULL),(11,3,'general',999,'manual',NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)");
if (getenv('ACTIVITY_POINT_COUNT') !== false) {
 $count=max(0,min(30,(int)getenv('ACTIVITY_POINT_COUNT')));
 if (!$count) $pdo->exec('DELETE FROM user_points WHERE user_id=2');
 for($i=1;$i<$count;$i++) $pdo->exec("INSERT INTO user_points(user_point_id,user_id,type,points,action,updated_at) VALUES(".(100+$i).",2,'general',5,'manual','2026-10-02')");
}
$GLOBALS['language']='fa';ob_start();component('points');$member=ob_get_clean();
check(!str_contains($member,'pointSortIcon-source')&&!str_contains($member,'pointSortIcon-branchName'),'Technical rule columns hidden from members');
check(!str_contains($member,'PRIVATE_')&&!str_contains($member,'999'),'Private fields and other user points absent');
check(str_contains($member,'id="myPointsTable"')&&str_contains($member,'data-no-identity-column'),'Shared personal table with explicit row numbering');
$GLOBALS['actor']=1;ob_start();component('points');$admin=ob_get_clean();check(str_contains($admin,'pointSortIcon-source')&&str_contains($admin,'pointSortIcon-branchName'),'Admin rule columns retained');
$GLOBALS['actor']=2;ob_start();component('notifications',['canCreateNotifications'=>true]);$noticeMember=ob_get_clean();check(!str_contains($noticeMember,'onclick="openAddNotificationModal()"'),'Members cannot see create button');
$GLOBALS['actor']=1;ob_start();component('notifications',['canCreateNotifications'=>true]);$noticeAdmin=ob_get_clean();check(str_contains($noticeAdmin,'onclick="openAddNotificationModal()"'),'Admin create button retained');
if(getenv('ACTIVITY_VIEWS'))echo json_encode(['points'=>$member,'adminPoints'=>$admin,'notifications'=>$noticeMember],JSON_UNESCAPED_UNICODE);else echo "Panel activity: $checks notification, recipient, legacy, bilingual and point view checks passed.\n";
`;
const result=spawnSync('php',[],{input:source,encoding:'utf8'});process.stdout.write(result.stdout||'');process.stderr.write(result.stderr||'');process.exitCode=result.status??1;
