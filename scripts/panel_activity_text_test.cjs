const { spawnSync } = require('node:child_process');
const source=String.raw`<?php
$map=require 'vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($map){if(isset($map[$class]))require_once $map[$class];});
require_once 'core/database/ActivityText.php';
require_once 'core/database/NotificationMetadata.php';
require_once 'core/database/DatabaseChangeNotifier.php';
require_once 'Modules/Analytics/Services/AdminNotificationService.php';
require_once 'Modules/Analytics/Services/PanelTableRenderer.php';
function db(){return $GLOBALS['pdo'];} function locale(){return $GLOBALS['language']??'fa';}
function auth(){return new class {function id(){return $GLOBALS['actor']??2;}function user(){return ['user_id'=>$this->id(),'type'=>'human'];}};}
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function component($name,$data=[]){extract($data);require 'Modules/Analytics/Resources/Views/sections/'.$name.'.php';}
function check($v,$m){if(!$v)throw new LogicException($m);$GLOBALS['checks']++;}
$checks=0;$pdo=new Core\database\PrefixedPDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
if(getenv('NOTIFICATION_METADATA_MYSQL')==='1' && $pdo->getAttribute(PDO::ATTR_DRIVER_NAME)!=='mysql')throw new LogicException('Expected MySQL fixture');
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
check($notices[0]['audience']==='Sender name','Personal audience resolves to the actual recipient');
check($notices[0]['priorityCode']==='low','Routine discount creation has low priority');
check(Core\database\NotificationMetadata::priority('financial_system_payments','insert')==='high','Payments are high priority');
check(Core\database\NotificationMetadata::priority('user_roles','insert')==='high','Access changes are high priority');
check(Core\database\NotificationMetadata::priority('academy_branch_bookings','insert')==='normal','Scheduling is normal priority');
check(Core\database\NotificationMetadata::priority('lessons','delete')==='high','Deletion is high priority');
check(Core\database\NotificationMetadata::operation('', 'lessons', 'Removed lesson')==='delete','Legacy descriptions recover real operations');
check($pdo->query("SELECT value FROM translations WHERE field='event_operation' LIMIT 1")->fetchColumn()==='insert','Operation snapshot stored');
check($pdo->query("SELECT value FROM translations WHERE field='priority' LIMIT 1")->fetchColumn()==='low','Priority snapshot stored');
check(Core\database\ActivityText::describe('f_media_files','insert',['collection'=>'cover'],'fa')['title']==='ایجاد عکس کاور','Physical media tables resolve to specific cover actions');
check(Core\database\ActivityText::describe('users','insert',['type'=>'branch'],'en')['title']==='Created branch account','Branch account creation stays explicit');
check(Core\database\ActivityText::describe('users','update',['avatar_file_id'=>9],'en')['title']==='Updated profile photo','Profile photo changes are explicit');
check(Core\database\ActivityText::action('user_sessions.insert','en')==='Signed in to the site','Session creation describes sign-in');
check(Core\database\ActivityText::point(['action'=>'media_files.insert','metadata'=>json_encode(['activity'=>['en'=>['title'=>'Created cover photo']]])],'en')==='Created cover photo','Points preserve historical media context');
$pdo->exec("INSERT INTO user_messages(user_message_id,sender_id,receiver_user_id,type,status,related_entity_type,related_entity_id,is_read,created_at) VALUES(100,2,2,'notification','published','users',2,0,'2026-10-22 18:00:00')");
$legacyTexts=['fa'=>['title'=>'ویرایش اطلاعات در users','message'=>'UPDATE users SET avatar_file_id=9, updated_by=2'], 'en'=>['title'=>'Data update in users','message'=>'UPDATE users SET avatar_file_id=9, updated_by=2']];
$insertLegacy=$pdo->prepare("INSERT INTO translations(table_name,table_id,field,locale,value) VALUES('user_messages',100,?,?,?)");
foreach($legacyTexts as $lang=>$fields)foreach($fields as $field=>$value)$insertLegacy->execute([$field,$lang,$value]);
foreach(['fa','en'] as $lang){
 $GLOBALS['language']=$lang;$GLOBALS['actor']=2;
 $publicNotice=$service->all(2)[0];
 check(!str_contains($publicNotice['body'],'avatar_file_id')&&!str_contains($publicNotice['body'],'UPDATE users')&&!str_contains($publicNotice['body'],'updated_by'),'Ordinary recipients never receive database descriptions');
 check(str_contains($publicNotice['body'],'2026/10/22 18:00'),'Public description includes the original event time');
 check($publicNotice['entityType']===''&&$publicNotice['entityId']===null,'Technical identifiers omitted from ordinary API response');
 check($publicNotice['title']===$publicNotice['activityTitle'],'Table action retains the specific notification title');
 $GLOBALS['actor']=1;$adminNotice=$service->all(2)[0];
 check(str_contains($adminNotice['body'],'UPDATE users SET avatar_file_id=9, updated_by=2')&&str_contains($adminNotice['body'],'2026/10/22 18:00'),'Site admin receives readable history and original technical details');
 check($adminNotice['entityType']==='users','Site admin retains technical identifiers');
}
$GLOBALS['actor']=2;$GLOBALS['language']='fa';
$pdo->exec('DELETE FROM translations WHERE table_id=100');$pdo->exec('DELETE FROM user_messages WHERE user_message_id=100');
$GLOBALS['language']='en';check($service->all(2)[0]['title']==='Created 2% discount for educational terms','English activity description');
$legacy=Core\database\ActivityText::legacy($pdo,['related_entity_type'=>'financial_system_discounts'],'Data insert in financial_system_discounts','old','en');
check($legacy['title']==='Created discount for educational terms','Old descriptions are readable without inventing historical values');
check(Core\database\ActivityText::legacy($pdo,[],'A custom notice','Custom body','en')['message']==='Custom body','Manual notices preserved');
check(Core\database\ActivityText::describe('unknown_private_table','insert',['password'=>'SECRET'],'en')['title']==='Created account activity','Unknown identifiers never exposed');
check(Core\database\ActivityText::action('user_lessons.insert','en')==='Created lesson and learning level','Point action uses activity names');
$pdo->beginTransaction();Core\database\DatabaseChangeNotifier::record($pdo,'lessons','insert',['created_by'=>2],2);$pdo->rollBack();check(count($service->all(2))===1,'Notification rolls back with transaction');
$pdo->exec('CREATE TABLE user_points(user_point_id INTEGER PRIMARY KEY,user_id INTEGER,type TEXT,points INTEGER,action TEXT,reference_type TEXT,reference_id INTEGER,rule_id INTEGER,award_key TEXT,metadata TEXT,created_at TEXT,updated_at TEXT,approved_at TEXT,approved_by INTEGER,created_by INTEGER,updated_by INTEGER,deleted_at TEXT)');
$pdo->exec("INSERT INTO user_points VALUES(10,2,'professional',12,'user_lessons.insert','PRIVATE_REF',99,1,'PRIVATE_KEY','PRIVATE_METADATA','2026-10-01','2026-10-02',NULL,1,1,1,NULL),(11,3,'general',999,'manual',NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)");
if(getenv('ACTIVITY_SPECIALIZED_POINT'))$pdo->exec("INSERT INTO user_points(user_point_id,user_id,type,points,action) VALUES(12,2,'specialized',25,'manual')");
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
check(!str_contains($noticeMember,'filterNotificationStatus')&&!str_contains($noticeMember,'filterNotificationAudience')&&!str_contains($noticeMember,'notifSortIcon-branchName')&&!str_contains($noticeMember,'notifSortIcon-status'),'Member notification filters and administrative columns hidden');
$GLOBALS['actor']=1;ob_start();component('notifications',['canCreateNotifications'=>true]);$noticeAdmin=ob_get_clean();check(str_contains($noticeAdmin,'onclick="openAddNotificationModal()"'),'Admin create button retained');
check(str_contains($noticeAdmin,'filterNotificationStatus')&&str_contains($noticeAdmin,'filterNotificationAudience')&&str_contains($noticeAdmin,'notifSortIcon-status'),'Admin notification filters retained');
if(getenv('ACTIVITY_VIEWS'))echo json_encode(['points'=>$member,'adminPoints'=>$admin,'notifications'=>$noticeMember],JSON_UNESCAPED_UNICODE);else echo "Panel activity: $checks notification, recipient, legacy, bilingual and point view checks passed.\n";
`;
let input=source;
if(process.env.NOTIFICATION_METADATA_MYSQL==='1'){
 const marker="$checks=0;$pdo=new Core\\database\\PrefixedPDO('sqlite::memory:',null,null,";
 const setup=String.raw`$checks=0;
$dsn=getenv('SORNAZ_TEST_MYSQL_DSN')?:'mysql:host=127.0.0.1;port=3306;charset=utf8mb4';
$user=getenv('SORNAZ_TEST_MYSQL_USER')?:'root';$password=getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'';
$server=new PDO($dsn,$user,$password,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$fixture='sornaz_notice_fixture_'.bin2hex(random_bytes(6));$server->exec('CREATE DATABASE '.$fixture.' CHARACTER SET utf8mb4');
register_shutdown_function(static function()use($server,$fixture){$server->exec('DROP DATABASE '.$fixture);});
$pdo=new Core\database\PrefixedPDO($dsn,$user,$password,`;
 if(!input.includes(marker))throw Error('MySQL fixture initialization marker missing');
 input=input.replace(marker,setup).replace("$pdo->exec('CREATE TABLE users", "$pdo->exec('USE '.$fixture);\n$pdo->exec('CREATE TABLE users").replaceAll('AUTOINCREMENT','AUTO_INCREMENT').replaceAll('created_at TEXT DEFAULT CURRENT_TIMESTAMP','created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
}
const result=spawnSync('php',[],{input,encoding:'utf8'});process.stdout.write(result.stdout||'');process.stderr.write(result.stderr||'');process.exitCode=result.status??1;
