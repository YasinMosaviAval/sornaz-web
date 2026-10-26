<?php
// Isolated real SQL; no application bootstrap, gateway or persistent writes.
$map=require __DIR__.'/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($map){if(isset($map[$class]))require_once $map[$class];});
require_once __DIR__.'/../Modules/Academy/Services/AcademyExportAccessService.php';
$pdo=new PDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function db(){return $GLOBALS['pdo'];}
function auth(){return new class {public function user(){return $GLOBALS['actor'];}public function id(){return (int)($GLOBALS['actor']['user_id']??0);}};}
function check($ok,$message){if(!$ok)throw new RuntimeException($message);}
function rejected($call,$code){try{$call();throw new LogicException('Unexpected export grant');}catch(RuntimeException $e){check($e->getCode()===$code,'Wrong rejection code: '.$e->getMessage());}}
$pdo->exec("CREATE TABLE users(user_id INTEGER PRIMARY KEY,type TEXT,deleted_at TEXT);
CREATE TABLE academies(academy_id INTEGER PRIMARY KEY,user_id INTEGER,created_by INTEGER,created_at TEXT,deleted_at TEXT);
CREATE TABLE academy_branches(branch_id INTEGER PRIMARY KEY,academy_id INTEGER,user_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_members(member_id INTEGER PRIMARY KEY,user_id INTEGER,academy_id INTEGER,branch_id INTEGER,status TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_member_roles(member_id INTEGER,role_id INTEGER,deleted_at TEXT);
CREATE TABLE access_system_roles(role_id INTEGER PRIMARY KEY,name TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_member_contracts(member_id INTEGER,type TEXT,deleted_at TEXT);
CREATE TABLE academy_subscription_periods(subscription_period_id INTEGER PRIMARY KEY,academy_id INTEGER,period_start TEXT,period_end TEXT,status TEXT,paid_at TEXT,deleted_at TEXT);
INSERT INTO users VALUES(1,'human',NULL),(10,'academy',NULL),(11,'branch',NULL),(12,'branch',NULL),(13,'human',NULL),(14,'human',NULL),(15,'academy',NULL),(17,'human',NULL),(20,'academy',NULL);
INSERT INTO users VALUES(18,'human',NULL);
INSERT INTO academies VALUES(100,10,10,'2026-09-25 10:00:00',NULL),(200,10,10,'2026-10-01 00:00:00',NULL),(300,20,20,'2026-01-01',NULL),(400,15,15,'2026-01-01',NULL),(500,17,17,NULL,NULL);
INSERT INTO academy_branches VALUES(101,100,11,NULL),(201,200,12,NULL),(301,300,20,NULL);
INSERT INTO academy_branch_members VALUES(1,13,100,101,'active',NULL),(2,14,400,NULL,'active',NULL);
INSERT INTO access_system_roles VALUES(1,'branch_receptionist',NULL);
INSERT INTO academy_branch_member_roles VALUES(1,1,NULL);
INSERT INTO academy_branch_member_contracts VALUES(2,'manager',NULL);
INSERT INTO academy_subscription_periods VALUES(1,100,'2026-09-25','2027-09-25','free',NULL,NULL),(2,100,'2026-10-25','2026-11-25','pending',NULL,NULL),(3,300,'2026-01-01','2027-01-01','paid','2026-01-01',NULL),(4,500,'2026-10-01','2026-11-01','free',NULL,NULL);");
$service=new class extends Modules\Academy\Services\AcademyExportAccessService {public string $date='2026-10-25';protected function today():string{return $this->date;}};
$actor=['user_id'=>10,'type'=>'academy'];
check(!$service->status(10)['allowed'],'An expired academy cannot borrow a second academy trial');
check(!$service->status(10,['academyIds'=>[100]])['allowed'],'Exactly one month ends the free export period');
$_SERVER['REQUEST_URI']='/academy/admin/courses';$_SERVER['REQUEST_METHOD']='GET';
check((new Modules\Academy\Middleware\AcademyPanelMiddleware())->handle(new Core\http\Request(),static fn()=> 'free-panel')==='free-panel','Expired export access must never block normal panel routes');
$service->date='2026-10-24';check($service->status(10,['academyIds'=>[100]])['allowed'],'Exports remain free before month boundary');$service->date='2026-10-25';
check($service->status(10,['academyIds'=>[200]])['allowed'],'The selected new academy retains its own trial');
check(!$service->status(10,['academyIds'=>[100,200]])['allowed'],'A mixed export needs entitlement for every academy');
rejected(fn()=>$service->status(10,['academyIds'=>[300]]),403);
rejected(fn()=>$service->status(10,['branchIds'=>[301]]),403);
rejected(fn()=>$service->status(10,['organizationUserIds'=>[20]]),403);
rejected(fn()=>$service->status(10,['academyIds'=>['invalid']]),422);
rejected(fn()=>$service->status(10,['branchIds'=>'101']),422);
rejected(fn()=>$service->status(11),401);
$pdo->exec("UPDATE academy_subscription_periods SET status='paid' WHERE subscription_period_id=2");
check(!$service->status(10,['academyIds'=>[100]])['allowed'],'An unverified paid marker is not a paid entitlement');
$pdo->exec("UPDATE academy_subscription_periods SET paid_at='2026-10-25' WHERE subscription_period_id=2");
check($service->status(10)['allowed'],'An active paid period enables exports');
check($service->status(10,['organizationUserIds'=>[11]])['allowed'],'Branch organization accounts map to their academy');
$service->date='2026-11-25';check(!$service->status(10,['academyIds'=>[100]])['allowed'],'Paid end date is exclusive');$service->date='2026-10-25';
$pdo->exec("UPDATE academy_subscription_periods SET period_start='2026-10-26' WHERE subscription_period_id=2");
check(!$service->status(10,['academyIds'=>[100]])['allowed'],'Future payment coverage does not activate early');
$pdo->exec("UPDATE academy_subscription_periods SET period_start='2026-10-25',deleted_at='2026-10-25' WHERE subscription_period_id=2");
check(!$service->status(10,['academyIds'=>[100]])['allowed'],'Deleted paid periods cannot unlock exports');
foreach([[11,'branch'],[13,'human'],[14,'human']]as[$id,$type]){$actor=['user_id'=>$id,'type'=>$type];check(!$service->status($id)['allowed'],'Branch/reception/branchless manager must use the parent academy entitlement');}
$actor=['user_id'=>17,'type'=>'human'];check($service->status(17)['allowed'],'Legacy missing creation date uses the recorded free-period start');
$pdo->exec('DELETE FROM academy_subscription_periods WHERE academy_id=500');check(!$service->status(17)['allowed'],'Missing start data must not reset the free month on each request');
$actor=['user_id'=>12,'type'=>'branch'];check($service->status(12)['allowed'],'A branch of a new academy keeps the trial');
$actor=['user_id'=>1,'type'=>'human'];check($service->status(1)['allowed'],'Site administrator exports remain available');
$actor=['user_id'=>18,'type'=>'human'];check($service->status(18)['allowed']&&$service->status(18)['reason']==='personal','Personal accounts without management scope are not charged an academy subscription');
$actor=null;rejected(fn()=>$service->status(0),401);
echo "Export subscription: real SQL scope, month boundary, paid/pending/future/deleted periods, mixed academies, branch/reception/branchless manager, missing dates and authorization passed.\n";
