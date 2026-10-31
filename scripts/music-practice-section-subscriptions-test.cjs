const {execFileSync}=require('node:child_process');
const fs=require('node:fs'),os=require('node:os'),path=require('node:path');
const php=String.raw`<?php
$root=getenv('SORNAZ_TEST_ROOT');
spl_autoload_register(function($class)use($root){$file=$root.'/'.str_replace('\\','/',$class).'.php';if(is_file($file))require_once $file;});
function base_path($p=''){return $GLOBALS['root'].'/'.$p;}
function storage_path($p=''){return getenv('SORNAZ_TEST_STORAGE').'/'.$p;}
function config($k,$d=null){return $k==='app.key'?str_repeat('fixture-key-',4):$d;}
function env($k,$d=null){return match($k){'ZARINPAL_MERCHANT_ID'=>'fixture','APP_URL'=>'https://fixture.test','ZARINPAL_SANDBOX'=>true,default=>$d};}
function locale(){return 'fa';}
function db(){return $GLOBALS['pdo'];}
use Core\database\PrefixedPDO;
use Modules\Education\Services\MusicPracticeStore;
use Modules\Education\Services\MusicPracticeCatalog;
use Modules\Education\Services\MusicPracticePlans;
use Modules\Education\Services\MusicPracticePayment;
use Modules\Education\Services\MusicPracticeAccess;
class ScopeStore extends MusicPracticeStore{public function allows(int $actor,string $action):bool{return in_array($actor,[1,2,5],true);}}
class ScopeGateway extends Modules\System\Services\ZarinpalGateway{public function request(string $action,array $payload):array{return match($action){'request'=>['data'=>['code'=>100,'authority'=>'fixture-'.substr(hash('sha256',$payload['callback_url']),0,24)]],'verify'=>['data'=>['code'=>100,'ref_id'=>'ref-'.$payload['authority']]],default=>['data'=>['code'=>100,'status'=>'IN_BANK']]};}}
function check($value,$message){if(!$value)throw new RuntimeException($message);$GLOBALS['checks']++;}
function denied($code,$work){try{$work();}catch(RuntimeException $e){check($e->getCode()===$code,'Unexpected rejection: '.$e->getCode().' '.$e->getMessage());return;}throw new RuntimeException('Expected rejection');}
$checks=0;$mysql=getenv('SORNAZ_SCOPE_MYSQL')==='1';
if($mysql){
 $admin=new PDO('mysql:host=127.0.0.1;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $database='sornaz_scope_fixture_'.bin2hex(random_bytes(8));$admin->exec('CREATE DATABASE '.$database.' CHARACTER SET utf8mb4');
 register_shutdown_function(function()use($admin,$database){if(preg_match('/^sornaz_scope_fixture_[a-f0-9]{16}$/D',$database))$admin->exec('DROP DATABASE '.$database);});
 $pdo=new PrefixedPDO('mysql:host=127.0.0.1;dbname='.$database.';charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
}else{$pdo=new PrefixedPDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}
$auto=$mysql?'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY':'INTEGER PRIMARY KEY AUTOINCREMENT';
$pdo->exec('CREATE TABLE f_translations(translation_id '.$auto.',table_name VARCHAR(100),table_id BIGINT,locale VARCHAR(10),field VARCHAR(100),value TEXT,version INT,created_at DATETIME,created_by BIGINT,updated_at DATETIME,updated_by BIGINT,approved_at DATETIME,approved_by BIGINT,deleted_at DATETIME)');
$pdo->exec('CREATE TABLE f_users(user_id INTEGER PRIMARY KEY,username VARCHAR(100),deleted_at DATETIME)');
foreach([1,2,3,4,5] as $id)$pdo->exec("INSERT INTO f_users VALUES($id,'fixture-$id',NULL)");
$sql=preg_replace('/^--.*$/m','',file_get_contents(base_path('docs/database/migrations/2026_10_29_music_practice_catalog.sql')));
foreach(explode(';',$sql)as$s){$s=trim($s);if(!str_starts_with($s,'CREATE TABLE'))continue;
 if(!$mysql){$s=str_replace(['BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY','CHARACTER SET ascii COLLATE ascii_bin'],['INTEGER PRIMARY KEY AUTOINCREMENT',''],$s);$s=preg_replace('/,\s*UNIQUE KEY \w+\(([^)]+)\)/',', UNIQUE($1)',$s);$s=preg_replace('/,\s*KEY \w+\([^)]+\)/','',$s);$s=preg_replace('/\) ENGINE=InnoDB.*$/s',')',$s);} $pdo->exec($s);
}
// Exercise the old schema first, then upgrade it using the actual MariaDB file.
$pdo->exec('ALTER TABLE f_music_practice_plans DROP COLUMN node_id');$pdo->exec('ALTER TABLE f_music_practice_orders DROP COLUMN node_id');
$store=new ScopeStore($pdo,true);$catalog=new MusicPracticeCatalog($store);
$nodeInput=['titleFa'=>'بخش','titleEn'=>'Section','published'=>true];
$a=$catalog->saveNode(1,$nodeInput+['slug'=>'section-a'])['id'];$b=$catalog->saveNode(1,$nodeInput+['slug'=>'section-b'])['id'];
$child=$catalog->saveNode(1,$nodeInput+['slug'=>'child','parentId'=>$a])['id'];$leaf=$catalog->saveNode(1,$nodeInput+['slug'=>'leaf','parentId'=>$child])['id'];
$testInput=['titleFa'=>'آزمون','titleEn'=>'Test','published'=>true,'audience'=>'subscribers','stageCount'=>1,'requiredWins'=>1,'config'=>['kind'=>'choice','questions'=>[['promptFa'=>'پرسش','promptEn'=>'Question','choices'=>['A','B'],'correct'=>0]]]];
$ea=$catalog->saveExam(2,$testInput+['nodeId'=>$a])['id'];$deep=$catalog->saveExam(2,$testInput+['nodeId'=>$leaf])['id'];$eb=$catalog->saveExam(2,$testInput+['nodeId'=>$b])['id'];$other=$catalog->saveExam(5,$testInput+['nodeId'=>$a])['id'];
$plans=new MusicPracticePlans($store);$gateway=new ScopeGateway();$payment=new MusicPracticePayment($store,$gateway);
$proposal=['title'=>'اشتراک','amount'=>12001,'days'=>90];
denied(503,fn()=>$plans->propose(2,$proposal+['nodeId'=>$a]));
$legacy=$plans->propose(2,$proposal)['id'];$plans->approve(1,['id'=>$legacy,'version'=>1,'status'=>'approved']);
$payment->start(4,$legacy,str_repeat('a',32),2);$old=$store->one('SELECT * FROM f_music_practice_orders WHERE buyer_id=4');$payment->callback($old['token'],$old['authority'],'OK');
if($mysql){$migration=file_get_contents(base_path('docs/database/migrations/2026_10_30_music_practice_section_subscriptions.mariadb.sql'));$migration=preg_replace('/^DELIMITER .*\R/m','',$migration);$migration=str_replace('END$$','END',$migration);for($i=0;$i<2;$i++){$q=$pdo->query($migration);do{if($q->columnCount())$q->fetchAll();}while($q->nextRowset());$q->closeCursor();}}
else{$pdo->exec('ALTER TABLE f_music_practice_plans ADD COLUMN node_id BIGINT NULL');$pdo->exec('ALTER TABLE f_music_practice_orders ADD COLUMN node_id BIGINT NULL');}
$store=new ScopeStore($pdo,true);$plans=new MusicPracticePlans($store);$payment=new MusicPracticePayment($store,$gateway);$access=new MusicPracticeAccess($store);
$exam=fn($id)=>$store->one('SELECT * FROM f_music_practice_exams WHERE id=?',[$id]);
check($access->permitted(4,$exam($ea))&&$access->permitted(4,$exam($eb)),'Legacy teacher-wide purchase changed');
denied(403,fn()=>$plans->propose(4,$proposal+['nodeId'=>$a]));check($plans->propose(2,$proposal+['nodeId'=>$child])['id']>0,'Depth-two subscriptions rejected');denied(422,fn()=>$plans->propose(2,$proposal+['nodeId'=>$a,'examId'=>$ea]));
denied(422,fn()=>$plans->propose(2,array_replace($proposal,['nodeId'=>$a,'days'=>60])));
$scoped=$plans->propose(2,$proposal+['nodeId'=>$a])['id'];$plans->approve(1,['id'=>$scoped,'version'=>1,'status'=>'approved']);
check((int)$store->one('SELECT node_id FROM f_music_practice_plans WHERE id=?',[$scoped])['node_id']===$a,'Proposal scope not saved');
check(!in_array($scoped,array_column($plans->listing(3)['plans'],'id'),true),'Old clients received a scoped offer without a target exam');
check(in_array($scoped,array_column($plans->listing(3,$ea)['plans'],'id'),true),'Matching section offer was hidden');
check(!in_array($scoped,array_column($plans->listing(3,$eb)['plans'],'id'),true),'Another section offer was shown for this exam');
$payment->start(3,$scoped,str_repeat('b',32),2);$orderA=$store->one('SELECT * FROM f_music_practice_orders WHERE buyer_id=3');
check((int)$orderA['node_id']===$a,'Checkout scope not frozen');
check(!$access->permitted(3,$exam($ea)),'Pending payment granted access');
$plans->propose(2,$proposal+['id'=>$scoped,'version'=>2,'nodeId'=>$b]);$plans->approve(1,['id'=>$scoped,'version'=>3,'status'=>'approved']);
$payment->start(3,$scoped,str_repeat('c',32),4);$orderB=$store->one('SELECT * FROM f_music_practice_orders WHERE buyer_id=3 AND node_id=?',[$b]);
check((int)$orderB['id']!==(int)$orderA['id'],'Different sections reused the same pending checkout');
$payment->callback($orderA['token'],$orderA['authority'],'OK');$payment->callback($orderA['token'],$orderA['authority'],'OK');
check($access->permitted(3,$exam($ea))&&$access->permitted(3,$exam($deep)),'Root and depth-three tests not granted');
check(!$access->permitted(3,$exam($eb))&&!$access->permitted(3,$exam($other)),'Cross-section or cross-teacher access leaked');
check((int)$store->one('SELECT COUNT(*) total FROM f_music_practice_revenue')['total']===4,'Repeated callback duplicated revenue');
check((int)$store->one("SELECT amount FROM f_music_practice_revenue WHERE order_id=? AND beneficiary='teacher'",[$orderA['id']])['amount']===9600,'Frozen revenue share changed');
$payment->callback($orderB['token'],$orderB['authority'],'OK');
$paidA=$store->one('SELECT * FROM f_music_practice_orders WHERE id=?',[$orderA['id']]);$paidB=$store->one('SELECT * FROM f_music_practice_orders WHERE id=?',[$orderB['id']]);
check($paidA['starts_at']===$paidB['starts_at']||abs(strtotime($paidA['starts_at'])-strtotime($paidB['starts_at']))<5,'Section renewal waited behind another section');
check($access->permitted(3,$exam($eb)),'Second purchased section not granted');
check((int)$store->one('SELECT COUNT(*) total FROM f_music_practice_orders')['total']===3,'Migration or callbacks duplicated purchases');

if($mysql){
 $pdo->exec('CREATE TABLE f_access_system_permissions(permission_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),resource VARCHAR(100),action VARCHAR(100),type VARCHAR(100),group_name VARCHAR(100),scope VARCHAR(100),risk_level INT,approval VARCHAR(100),created_at DATETIME,created_by BIGINT,updated_at DATETIME,updated_by BIGINT,approved_at DATETIME,approved_by BIGINT,deleted_at DATETIME)');
 $archive=preg_replace('/^--.*$/m','',file_get_contents(base_path('docs/database/migrations/2026_10_30_music_practice_level_archive.mariadb.sql')));
 for($i=0;$i<2;$i++)foreach(explode(';',$archive)as$statement)if(trim($statement)!=='')$pdo->exec($statement);
 check((int)$store->one('SELECT COUNT(*) total FROM f_access_system_permissions')['total']===1,'Archive permission migration was not idempotent');
}
// A leaf purchase covers that whole test, never a sibling or another teacher.
$leafPlan=$plans->propose(2,$proposal+['nodeId'=>$leaf])['id'];$plans->approve(1,['id'=>$leafPlan,'version'=>1,'status'=>'approved']);
$payment->start(5,$leafPlan,str_repeat('d',32),2);$leafOrder=$store->one('SELECT * FROM f_music_practice_orders WHERE buyer_id=5');$payment->callback($leafOrder['token'],$leafOrder['authority'],'OK');
check($access->permitted(5,$exam($deep))&&!$access->permitted(5,$exam($ea))&&!$access->permitted(5,$exam($eb)),'Leaf subscription leaked into another section');
denied(422,fn()=>$plans->propose(2,$proposal+['examId'=>$ea]));
$catalog=new MusicPracticeCatalog($store);
$catalog->deleteExam(2,['id'=>$deep,'version'=>1]);$catalog->deleteExam(2,['id'=>$deep,'version'=>1]);
check(!$access->permitted(5,$exam($deep)),'Archived level retained online access');
check($store->one('SELECT exam_id FROM f_music_practice_versions WHERE exam_id=?',[$deep])!==null,'Archive erased definition history');
denied(403,fn()=>$catalog->deleteExam(5,['id'=>$ea,'version'=>1]));
denied(409,fn()=>$catalog->deleteNode(1,['id'=>$a,'version'=>1]));
$catalog->deleteNode(1,['id'=>$leaf,'version'=>1]);$catalog->deleteNode(1,['id'=>$leaf,'version'=>1]);
check(!in_array($leaf,array_column($catalog->listing(1,true)['nodes'],'id'),true),'Archived section stayed visible');
check($store->one('SELECT id FROM f_music_practice_orders WHERE id=?',[$leafOrder['id']])!==null,'Archive erased paid purchase');

echo 'Section subscriptions: '.$checks.' checks passed ('.($mysql?'MariaDB':'SQLite').').'.PHP_EOL;
`;
const directory=fs.mkdtempSync(path.join(os.tmpdir(),'sornaz-scope-test-'));
try{
 process.stdout.write(execFileSync('php',[],{input:php,encoding:'utf8',cwd:path.resolve(__dirname,'..'),env:{...process.env,SORNAZ_TEST_ROOT:path.resolve(__dirname,'..'),SORNAZ_TEST_STORAGE:directory,SORNAZ_SCOPE_MYSQL:process.argv.includes('--mysql')?'1':'0'}}));
}finally{
 if(path.dirname(directory)!==path.resolve(os.tmpdir())||!path.basename(directory).startsWith('sornaz-scope-test-'))throw Error('Unsafe fixture cleanup');
 fs.rmSync(directory,{recursive:true,force:true});
}
