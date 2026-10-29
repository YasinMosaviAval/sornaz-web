<?php
// Isolated SQL integration tests. Never loads application database credentials.
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
ob_start();$root=dirname(__DIR__);$map=require $root.'/vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($root,$map){$file=$map[$class]??$root.'/'.str_replace('\\','/',$class).'.php';if(is_file($file))require_once $file;});
function base_path($p=''){return $GLOBALS['root'].'/'.ltrim($p,'/');}
function storage_path($p=''){return sys_get_temp_dir().'/sornaz-music-integration/'.ltrim($p,'/');}
function db(){return $GLOBALS['pdo'];}
function locale(){return 'fa';}
function config($k,$d=null){return $k==='app.key'?str_repeat('fixture-key-',4):$d;}
function env($k,$d=null){return match($k){'ZARINPAL_MERCHANT_ID'=>'fixture-merchant','APP_URL'=>'https://fixture.test','ZARINPAL_SANDBOX'=>true,default=>$d};}
use Core\database\PrefixedPDO;
use Modules\Education\Services\MusicPracticeStore;
use Modules\Education\Services\MusicPracticeCatalog;
use Modules\Education\Services\MusicPracticeAccess;
use Modules\Education\Services\MusicPracticeResults;
use Modules\Education\Services\MusicPracticePlans;
use Modules\Education\Services\MusicPracticePayment;
use Modules\Education\Services\MusicPracticeSeeder;
$checks=0;
function check(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);$GLOBALS['checks']++;}
function denied(int $code,callable $work):void{try{$work();}catch(RuntimeException $e){check($e->getCode()===$code,'Unexpected denial '.$e->getCode().': '.$e->getMessage());return;}throw new RuntimeException('Missing denial '.$code);}
function schema(PrefixedPDO $pdo,bool $mysql):void{
    $auto=$mysql?'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY':'INTEGER PRIMARY KEY AUTOINCREMENT';
    $tables=[
        "f_users(user_id $auto,username VARCHAR(100),password VARCHAR(255),status VARCHAR(20),deleted_at DATETIME NULL)",
        "f_access_system_permissions(permission_id $auto,name VARCHAR(100),resource VARCHAR(100),action VARCHAR(100),type VARCHAR(30),group_name VARCHAR(40),scope VARCHAR(40),risk_level INT,approval VARCHAR(30),created_at DATETIME,created_by BIGINT,updated_at DATETIME,updated_by BIGINT,approved_at DATETIME,approved_by BIGINT,deleted_at DATETIME NULL)",
        'f_user_permissions(user_id BIGINT,permission_id BIGINT,deleted_at DATETIME NULL,expires_at DATETIME NULL)',
        'f_user_roles(user_id BIGINT,role_id BIGINT,deleted_at DATETIME NULL,expires_at DATETIME NULL)',
        'f_access_system_role_permissions(role_id BIGINT,permission_id BIGINT,deleted_at DATETIME NULL)',
        'f_access_system_roles(role_id BIGINT PRIMARY KEY,deleted_at DATETIME NULL)',
        "f_translations(translation_id $auto,table_name VARCHAR(100),table_id BIGINT,locale VARCHAR(10),field VARCHAR(100),value TEXT,version INT,created_at DATETIME,created_by BIGINT,updated_at DATETIME,updated_by BIGINT,approved_at DATETIME NULL,approved_by BIGINT NULL,deleted_at DATETIME NULL)",
        'f_auth_remember_tokens(token_hash VARCHAR(64) PRIMARY KEY,user_id BIGINT,expires_at BIGINT)',
        'p_academy_branch_members(member_id BIGINT PRIMARY KEY,user_id BIGINT,deleted_at DATETIME NULL)',
        'p_academy_branch_course_terms(term_id BIGINT PRIMARY KEY,deleted_at DATETIME NULL)',
        'p_academy_branch_course_term_enrollments(member_id BIGINT,term_id BIGINT,type VARCHAR(20),status VARCHAR(20),deleted_at DATETIME NULL)',
        'f_practice_sessions(user_id BIGINT,tool_key VARCHAR(50),curriculum_version INT,music VARCHAR(20),level_number INT,status VARCHAR(30))',
        'f_practice_legacy_progress(user_id BIGINT,tool_key VARCHAR(50),curriculum_version INT,music VARCHAR(20),level_number INT)',
    ];
    foreach($tables as$table)$pdo->exec('CREATE TABLE '.$table.($mysql?' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4':''));
    $sql=preg_replace('/^--.*$/m','',file_get_contents(base_path('docs/database/migrations/2026_10_29_music_practice_catalog.sql')));
    foreach(explode(';',$sql)as$s){$s=trim($s);if(!$s)continue;
        if(!$mysql&&str_starts_with($s,'CREATE TABLE')){
            $s=str_replace('BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY','INTEGER PRIMARY KEY AUTOINCREMENT',$s);
            $s=str_replace('CHARACTER SET ascii COLLATE ascii_bin','',$s);
            $s=preg_replace('/,\s*UNIQUE KEY \w+\(([^)]+)\)/',', UNIQUE($1)',$s);
            $s=preg_replace('/,\s*KEY \w+\([^)]+\)/','',$s);$s=preg_replace('/\) ENGINE=InnoDB.*$/s',')',$s);
        }$pdo->exec($s);
    }
    foreach(explode(';',$sql)as$s)if(str_starts_with(trim($s),'INSERT'))$pdo->exec($s);
}
class MusicFixtureGateway extends Modules\System\Services\ZarinpalGateway{
    public function request(string $action,array $payload):array{return match($action){'request'=>['data'=>['code'=>100,'authority'=>'fixture-'.substr(hash('sha256',$payload['callback_url']),0,24)]],'verify'=>['data'=>['code'=>100,'ref_id'=>'ref-'.$payload['authority']]],default=>['data'=>['code'=>100,'status'=>'IN_BANK']]};}
}
$admin=null;$database=null;
try{
    $mysql=getenv('SORNAZ_MUSIC_MYSQL')==='1';
    if($mysql){
        $dsn=getenv('SORNAZ_TEST_MYSQL_DSN')?:'mysql:host=127.0.0.1;port=3306;charset=utf8mb4';
        if(!preg_match('/^mysql:host=(127\.0\.0\.1|localhost)(;|$)/D',$dsn))throw new RuntimeException('Only local test MySQL is allowed');
        $user=getenv('SORNAZ_TEST_MYSQL_USER')?:'root';$password=getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'';
        $admin=new PDO($dsn,$user,$password,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);$database='sornaz_music_fixture_'.bin2hex(random_bytes(8));
        $admin->exec('CREATE DATABASE `'.$database.'` CHARACTER SET utf8mb4');
        $pdo=new PrefixedPDO(preg_replace('/;dbname=[^;]*/','',$dsn).';dbname='.$database,$user,$password,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    }else{$pdo=new PrefixedPDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);$pdo->sqliteCreateFunction('NOW',fn()=>gmdate('Y-m-d H:i:s'));$pdo->exec('PRAGMA foreign_keys=ON');}
    schema($pdo,$mysql);$store=new MusicPracticeStore($pdo);$catalog=new MusicPracticeCatalog($store);$access=new MusicPracticeAccess($store);$results=new MusicPracticeResults($store);$plans=new MusicPracticePlans($store);
    check((int)$store->one('SELECT COUNT(*) n FROM f_access_system_permissions')['n']===3,'Permission migration duplicated');check((int)$store->one('SELECT COUNT(*) n FROM f_translations')['n']===6,'Permission translations duplicated');
    foreach([1=>'founder',2=>'teacher',3=>'student',4=>'other',5=>'publisher']as$id=>$name)$store->execute("INSERT INTO f_users(user_id,username,password,status) VALUES(?,?,?,'active')",[$id,$name,'fixture-password-hash']);
    check(!$store->allows(2,'create'),'Default permissions must be founder only');$store->execute('INSERT INTO f_user_permissions(user_id,permission_id) SELECT 2,permission_id FROM f_access_system_permissions');
    $store->execute("INSERT INTO f_user_permissions(user_id,permission_id) SELECT 5,permission_id FROM f_access_system_permissions WHERE name='music_practice.publish'");
    check($store->allows(2,'publish')&&!$store->allows(3,'publish'),'Permission grant boundary');
    (new MusicPracticeSeeder())->seed($store);(new MusicPracticeSeeder())->seed($store);
    check((int)$store->one('SELECT COUNT(*) n FROM f_music_practice_exams')['n']===224,'Expected 224 tests');check((int)$store->one("SELECT COUNT(*) n FROM f_music_practice_exams WHERE legacy_key LIKE 'iranian:%'")['n']===180,'Expected 180 Iranian tests');check(count($catalog->listing(0)['nodes'])===10,'Seed hierarchy');
    check(!$store->allows(5,'edit')&&$store->allows(5,'publish'),'Independent publication permission');$ownedNode=$catalog->saveNode(1,['slug'=>'publication-fixture','titleFa'=>'انتشار','titleEn'=>'Publication']);$store->execute('UPDATE f_music_practice_nodes SET created_by=5 WHERE id=?',[$ownedNode['id']]);$catalog->publish(5,['kind'=>'node','id'=>$ownedNode['id'],'version'=>1,'published'=>true]);
    check((int)$store->one('SELECT published FROM f_music_practice_nodes WHERE id=?',[$ownedNode['id']])['published']===1,'Publisher without edit');denied(403,fn()=>$catalog->publish(5,['kind'=>'node','id'=>1,'version'=>1,'published'=>false]));
    denied(403,fn()=>$catalog->people(3,'st'));check($catalog->people(2,'st')['users']===[['id'=>3,'username'=>'student']],'Named recipient search');check($catalog->people(2,'%_')['users']===[],'Wildcard escaping');denied(422,fn()=>$catalog->people(2,[]));
    $nodes=array_column($catalog->listing(1,true)['nodes'],null,'slug');$rhythm=$nodes['rhythm']['id'];denied(422,fn()=>$catalog->saveNode(1,['parentId'=>$nodes['iranian']['id'],'slug'=>'deep','titleFa'=>'عمق','titleEn'=>'Depth']));denied(403,fn()=>$catalog->saveNode(3,['slug'=>'forbidden']));
    $definition=['nodeId'=>$rhythm,'titleFa'=>'آزمون نمونه','titleEn'=>'Example test','position'=>0,'published'=>true,'audience'=>'users','userIds'=>[3],'stageCount'=>3,'requiredWins'=>1,'config'=>['kind'=>'choice','questions'=>[['promptFa'=>'پرسش','promptEn'=>'Question','choices'=>['A','B'],'correct'=>0]]]];
    denied(422,fn()=>$catalog->saveExam(2,array_replace($definition,['config'=>'malformed'])));denied(422,fn()=>$catalog->saveExam(2,array_replace($definition,['userIds'=>[999999]])));
    $id=$catalog->saveExam(2,$definition)['id'];$exam=$store->one('SELECT * FROM f_music_practice_exams WHERE id=?',[$id]);check($access->permitted(3,$exam)&&!$access->permitted(4,$exam),'Selected audience boundary');check(!in_array($id,array_column($catalog->listing(4)['exams'],'id'),true),'Private metadata excluded');check(array_column($catalog->listing(2,true)['exams'],null,'id')[$id]['users']===[['id'=>3,'username'=>'student']],'Named selected users');
    denied(403,fn()=>$catalog->saveExam(4,array_replace($definition,['id'=>$id,'version'=>1])));denied(409,fn()=>$catalog->saveExam(2,array_replace($definition,['id'=>$id,'version'=>8])));
    $permit=$access->permit(3,$exam);$at=gmdate('Y-m-d\TH:i:s\Z');
    for($stage=1;$stage<=3;$stage++){
        $payload=['examId'=>$id,'version'=>1,'stage'=>$stage,'runId'=>str_pad(dechex($stage),32,'0',STR_PAD_LEFT),'closed'=>false,'answers'=>[['sequence'=>1,'elapsedMs'=>100,'answeredAt'=>$at,'question'=>0,'selected'=>0]]]+$permit;
        if($stage===1){$wrong=$payload;$wrong['stage']=3;$wrong['runId']=str_repeat('e',32);denied(409,fn()=>$results->store(3,$wrong));denied(403,fn()=>$results->store(4,$payload));}
        check($results->store(3,$payload)['status']==='passed','Stage persistence');check($results->store(3,$payload)['acceptedThrough']===1,'Replay idempotence');check(array_column($results->progress(3)['progress'],null,'examId')[$id]['stars']===(int)floor($stage*5/3),'Proportional stars');$wrong=$payload;$wrong['answers'][0]['selected']=1;denied(409,fn()=>$results->store(3,$wrong));
    }
    check(array_column($results->progress(3)['progress'],null,'examId')[$id]['stars']===5,'Five star completion');check($results->progress(4)['recent']===[],'Private results');$catalog->saveExam(2,array_replace($definition,['id'=>$id,'version'=>1,'stageCount'=>2]));
    check((int)$store->one('SELECT COUNT(*) n FROM f_music_practice_versions WHERE exam_id=?',[$id])['n']===2,'Immutable snapshots');$revised=array_column($results->progress(3)['progress'],null,'examId')[$id];check($revised['version']===2&&$revised['stars']===0,'No false inherited completion');check((int)$store->one('SELECT COUNT(*) n FROM f_music_practice_answers')['n']===3,'Old answers preserved');
    $store->execute('INSERT INTO p_academy_branch_members(member_id,user_id) VALUES(2,2),(3,3)');$store->execute('INSERT INTO p_academy_branch_course_terms(term_id) VALUES(8)');$store->execute("INSERT INTO p_academy_branch_course_term_enrollments(member_id,term_id,type,status) VALUES(2,8,'teacher','active'),(3,8,'student','active')");$studentExam=array_replace($exam,['audience'=>'students']);check($access->permitted(3,$studentExam)&&!$access->permitted(4,$studentExam),'Teacher student scope');$store->execute('UPDATE p_academy_branch_course_terms SET deleted_at=? WHERE term_id=8',[$at]);check(!$access->permitted(3,$studentExam),'Deleted term access');
    $paidId=$catalog->saveExam(2,array_replace($definition,['audience'=>'subscribers','userIds'=>[]]))['id'];check((int)$store->one('SELECT teacher_bps FROM f_music_practice_exams WHERE id=?',[$paidId])['teacher_bps']===8000,'Default 80/20');$offer=$plans->propose(2,['examId'=>$paidId,'title'=>'Individual','amount'=>12001,'days'=>0]);denied(403,fn()=>$plans->approve(2,['id'=>$offer['id'],'version'=>1,'status'=>'approved']));$plans->approve(1,['id'=>$offer['id'],'version'=>1,'status'=>'approved']);$payment=new MusicPracticePayment($store,new MusicFixtureGateway());
    denied(409,fn()=>$payment->start(3,$offer['id'],str_repeat('a',32),1));$payment->start(3,$offer['id'],str_repeat('a',32),2);$order=$store->one('SELECT * FROM f_music_practice_orders WHERE buyer_id=3');$plans->share(1,['id'=>$paidId,'teacherBps'=>9000,'shareVersion'=>1]);$payment->callback($order['token'],$order['authority'],'OK');$payment->callback($order['token'],$order['authority'],'OK');
    check((int)$store->one('SELECT COUNT(*) n FROM f_music_practice_revenue')['n']===2,'Exactly once ledger');check((int)$store->one("SELECT amount FROM f_music_practice_revenue WHERE beneficiary='teacher'")['amount']===9600,'Frozen order share');check((int)$store->one('SELECT SUM(amount) n FROM f_music_practice_revenue')['n']===12001,'Revenue conservation');$paidExam=$store->one('SELECT * FROM f_music_practice_exams WHERE id=?',[$paidId]);check($access->permitted(3,$paidExam)&&!$access->permitted(4,$paidExam),'Individual isolation');check($store->one('SELECT expires_at FROM f_music_practice_orders WHERE id=?',[$order['id']])['expires_at']===null,'Permanent purchase');
    denied(403,fn()=>$plans->share(2,['id'=>$paidId,'teacherBps'=>0,'shareVersion'=>2]));denied(409,fn()=>$plans->share(1,['id'=>$paidId,'teacherBps'=>0,'shareVersion'=>1]));check((int)$store->one('SELECT version FROM f_music_practice_exams WHERE id=?',[$paidId])['version']===1,'Share preserves progress');$vip=$plans->propose(2,['title'=>'VIP','amount'=>30001,'days'=>30]);$plans->approve(1,['id'=>$vip['id'],'version'=>1,'status'=>'approved']);$payment->start(4,$vip['id'],str_repeat('b',32),2);$order=$store->one('SELECT * FROM f_music_practice_orders WHERE buyer_id=4');$payment->callback($order['token'],$order['authority'],'OK');
    check($access->permitted(4,$exam)&&$access->permitted(4,$paidExam),'VIP scope');check(!$access->permitted(4,array_replace($paidExam,['published'=>0])),'VIP excludes drafts');$store->execute("UPDATE f_music_practice_orders SET expires_at='2000-01-01 00:00:00' WHERE buyer_id=4");check(!$access->permitted(4,$paidExam),'VIP expiry');
    $auth=new Modules\System\Services\MobileAuthTokenService(new Modules\System\Repositories\UserRepository());$tokens=[];foreach([1,2,3,4,5]as$actor)$tokens[$actor]=$auth->issue($store->one('SELECT * FROM f_users WHERE user_id=?',[$actor]));$_SERVER['HTTP_AUTHORIZATION']='Bearer '.$tokens[3];check($auth->userFromRequest()['user_id']==3,'Real signed bearer');$_SERVER['HTTP_AUTHORIZATION']='Bearer invalid';check($auth->userFromRequest()===null,'Invalid bearer');
    foreach($argv as$arg)if(str_starts_with($arg,'--export-browser=')){
        if($mysql)throw new RuntimeException('Export requires SQLite');$target=realpath(substr($arg,17));if(!$target||dirname($target)!==realpath(sys_get_temp_dir())||!str_starts_with(basename($target),'sornaz-music-browser-'))throw new RuntimeException('Invalid export target');
        foreach(['f_music_practice_revenue','f_music_practice_orders','f_music_practice_plans','f_music_practice_answers','f_music_practice_runs','f_music_practice_grants']as$table)$pdo->exec('DELETE FROM '.$table);$pdo->exec('DELETE FROM f_music_practice_versions WHERE exam_id>224');$pdo->exec('DELETE FROM f_music_practice_exams WHERE id>224');$pdo->exec('DELETE FROM f_music_practice_nodes WHERE id>10');$pdo->exec('VACUUM INTO '.$pdo->quote($target.'/database.sqlite'));file_put_contents($target.'/tokens.json',json_encode($tokens,JSON_THROW_ON_ERROR));
    }
    ob_end_clean();echo "Music practice SQL integration: $checks checks passed (".($mysql?'MySQL':'SQLite').").\n";
}finally{if($admin&&is_string($database)&&preg_match('/^sornaz_music_fixture_[a-f0-9]{16}$/D',$database))$admin->exec('DROP DATABASE `'.$database.'`');}
