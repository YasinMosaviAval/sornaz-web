const assert=require('node:assert/strict');
const {spawnSync}=require('node:child_process');
const fs=require('node:fs'),os=require('node:os'),path=require('node:path');
const php=String.raw`<?php
$root=getcwd();$map=require $root.'/vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($map,$root){if(isset($map[$class]))require_once $map[$class];else{$file=$root.'/'.str_replace(chr(92),'/',$class).'.php';if(is_file($file))require_once $file;}});
function db(){return $GLOBALS['pdo'];}
function auth(){return new class{function id(){return $GLOBALS['actor'];}function user(){return Core\database\DB::table('users')->where('user_id',$this->id())->first();}};}
function locale(){return 'fa';}
function env($key,$default=null){return $default;}
function session(){return new class{function get($key,$default=null){return $key==='suppress_database_notifications'?true:$default;}};}
function storage_path($path){return getenv('SORNAZ_ACHIEVEMENT_FIXTURE').'/'.$path;}
function ok($condition,$message){if(!$condition)throw new Exception($message);}
function denied($work,$message){try{$work();}catch(RuntimeException $e){ok($e->getCode()===403,$message.' wrong status');return;}throw new Exception($message);}
$pdo=new PDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);$actor=1;
$pdo->exec("CREATE TABLE users(user_id INTEGER PRIMARY KEY,username TEXT,type TEXT,deleted_at TEXT);
CREATE TABLE academies(academy_id INT,user_id INT,deleted_at TEXT);
CREATE TABLE academy_branches(branch_id INT,academy_id INT,user_id INT,deleted_at TEXT);
CREATE TABLE academy_branch_members(member_id INT,user_id INT,academy_id INT,branch_id INT,status TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_member_roles(member_id INT,role_id INT,deleted_at TEXT);
CREATE TABLE access_system_roles(role_id INT,name TEXT,deleted_at TEXT);
CREATE TABLE verification_levels(verification_level_id INT,code TEXT,priority INT,deleted_at TEXT);
CREATE TABLE user_addresses(address_id INT,user_id INT,deleted_at TEXT);
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INT,field TEXT,locale TEXT,value TEXT,version INT,created_at TEXT,created_by INT,updated_at TEXT,updated_by INT,deleted_at TEXT,deleted_by INT);
INSERT INTO users VALUES(1,'founder','admin',NULL),(10,'academy A','academy',NULL),(11,'branch A1','branch',NULL),(12,'branch A2','branch',NULL),(20,'academy B','academy',NULL),(21,'branch B1','branch',NULL),(22,'branch B2','branch',NULL),(30,'person','human',NULL),(31,'manager','human',NULL),(32,'inactive','human',NULL);
INSERT INTO academies VALUES(100,10,NULL),(200,20,NULL);
INSERT INTO academy_branches VALUES(101,100,11,NULL),(102,100,12,NULL),(201,200,21,NULL),(202,200,22,NULL);
INSERT INTO academy_branch_members VALUES(1,31,100,NULL,'active',NULL),(2,31,200,201,'active',NULL),(3,32,100,NULL,'inactive',NULL);
INSERT INTO access_system_roles VALUES(1,'academy_manager',NULL),(2,'branch_manager',NULL);
INSERT INTO academy_branch_member_roles VALUES(1,1,NULL),(2,2,NULL),(3,1,NULL);
INSERT INTO verification_levels VALUES(1,'verified',1,NULL);");
$configs=['awards'=>['user_awards','user_award_id','date TEXT'],'badges'=>['user_badges','user_badge_id','verification_level_id INT,granted_at TEXT,expires_at TEXT,granted_by INT,status TEXT'],'educations'=>['user_educations','user_education_id','start_date TEXT,end_date TEXT'],'certificates'=>['user_certificates','user_certificate_id','issue_date TEXT,expire_date TEXT,certificate_url TEXT,file_path TEXT'],'experiences'=>['user_experiences','user_experience_id','start_date TEXT,end_date TEXT,address_id INT'],'events'=>['user_events','user_event_id','event_date TEXT,event_type TEXT,address_id INT'],'publications'=>['user_publications','user_publication_id','published_date TEXT,title TEXT,publisher TEXT,url TEXT,is_peer_reviewed INT']];
foreach($configs as [$table,$key,$columns]){
 $pdo->exec("CREATE TABLE $table($key INTEGER PRIMARY KEY,user_id INT,$columns,created_at TEXT,created_by INT,updated_at TEXT,updated_by INT,deleted_at TEXT,deleted_by INT,approved_at TEXT,approved_by INT)");
 foreach([10,11,12,20,21,22,30]as$owner)$pdo->exec("INSERT INTO $table($key,user_id,approved_at)VALUES($owner,$owner,'2030-01-01')");
}
$pdo->exec("UPDATE user_badges SET verification_level_id=1,status='active';CREATE TABLE user_polls(user_poll_id INTEGER PRIMARY KEY,owner_id INT,expires_at TEXT,status TEXT,type TEXT,approved_at TEXT,updated_at TEXT,deleted_at TEXT);INSERT INTO user_polls VALUES(1,10,NULL,'active','single','2030-01-01',NULL,NULL),(2,30,NULL,'active','single','2030-01-01',NULL,NULL);CREATE TABLE user_poll_options(user_poll_option_id INT,poll_id INT,sort_order INT,votes_count INT,deleted_at TEXT);CREATE TABLE user_poll_votes(user_poll_vote_id INTEGER PRIMARY KEY,poll_id INT,option_id INT,user_id INT,created_at TEXT,created_by INT,updated_at TEXT,updated_by INT,approved_at TEXT,approved_by INT,deleted_at TEXT,deleted_by INT);");
$pdo->exec(str_replace('CREATE TABLE translations','CREATE TABLE f_translations',$pdo->query("SELECT sql FROM sqlite_master WHERE name='translations'")->fetchColumn()));
$service=new Modules\Analytics\Services\AdminProfileContentService;
$pdo->exec("ALTER TABLE user_polls ADD COLUMN votes_count INTEGER DEFAULT 0;INSERT INTO user_poll_options VALUES(1,2,1,0,NULL),(2,2,2,0,NULL)");
foreach([10=>[10,11,12],11=>[11],12=>[12],30=>[30],31=>[10,11,12,21],32=>[]]as$user=>$expected){
 $actor=$user;$data=$service->index($actor,'badges');$ids=array_column($data['items'],'user_id');sort($ids);sort($expected);ok($ids===$expected,'Wrong badge scope for '.$actor.':'.json_encode($ids));
 ok($data['canCreate']===false,'Non-admin badge creation enabled');foreach($data['items']as$item)ok(!$item['canManage']&&!$item['canChangeStatus'],'Badge mutation exposed');
 denied(fn()=>$service->save($actor,'badges',null,['user_id'=>$actor,'verification_level_id'=>1]),'Badge self-grant');
 denied(fn()=>$service->delete($actor,'badges',11),'Badge deletion');
 denied(fn()=>$service->cycleStatus($actor,'badges',11),'Badge status change');
 denied(fn()=>$service->save($actor,'badges',11,['user_id'=>11,'verification_level_id'=>1]),'Badge update');
}
$actor=30;
foreach(['awards'=>['title'=>'Award'],'educations'=>['degree'=>'Degree'],'certificates'=>['title'=>'Certificate'],'experiences'=>['title'=>'Experience'],'events'=>['title'=>'Event'],'publications'=>['title'=>'Publication','content'=>'Text']]as$entity=>$fields){
 $id=$service->save($actor,$entity,null,['user_id'=>30]+$fields);ok($id>0,'Own record save failed: '.$entity);
 $rows=$service->index($actor,$entity)['items'];ok(in_array($id,array_column($rows,'id'),true),'Own record not listed');
 denied(fn()=>$service->save($actor,$entity,null,['user_id'=>11]+$fields),'Cross-user write: '.$entity);
}
denied(fn()=>$service->vote(30,1,[1]),'Cross-organization poll vote');
$service->vote(30,2,[1]);$service->vote(30,2,[2]);
ok((int)$pdo->query('SELECT votes_count FROM user_polls WHERE user_poll_id=2')->fetchColumn()===1,'Repeat voting inflated total');
ok((int)$pdo->query('SELECT option_id FROM user_poll_votes WHERE poll_id=2 AND deleted_at IS NULL')->fetchColumn()===2,'Vote replacement failed');
ok((int)$pdo->query('SELECT votes_count FROM user_poll_options WHERE user_poll_option_id=1')->fetchColumn()===0,'Previous option count retained');
$actor=1;$id=$service->save(1,'badges',null,['user_id'=>30,'verification_level_id'=>1,'status'=>'active']);ok($id>0,'Administrator cannot grant personal badge');ok($service->index(1,'badges')['canCreate'],'Admin creation missing');
$controller=new Modules\Analytics\Controllers\Web\AdminProfileContentController($service);$actor=30;$response=$controller->index('educations');ok((new ReflectionProperty($response,'status'))->getValue($response)===200,'Ordinary controller access blocked');$_POST=['user_id'=>30,'verification_level_id'=>1];$response=$controller->store('badges');ok((new ReflectionProperty($response,'status'))->getValue($response)===403,'Controller self-grant not forbidden');
$actor=0;$response=$controller->index('educations');ok((new ReflectionProperty($response,'status'))->getValue($response)===401,'Guest controller access allowed');
echo "Achievements: real SQLite CRUD, personal/academy/branch scopes, mixed-organization manager, inactive role, badge assignment and controller authorization passed.\n";
`;
const fixture=fs.mkdtempSync(path.join(os.tmpdir(),'sornaz-achievements-'));
try{
 const result=spawnSync('php',[],{input:php,encoding:'utf8',cwd:path.resolve(__dirname,'..'),env:{...process.env,SORNAZ_ACHIEVEMENT_FIXTURE:fixture}});
 if(result.stdout)process.stdout.write(result.stdout);if(result.stderr)process.stderr.write(result.stderr);assert.equal(result.status,0);
}finally{
 if(!path.resolve(fixture).startsWith(path.resolve(os.tmpdir())+path.sep)||!path.basename(fixture).startsWith('sornaz-achievements-'))throw Error('Unsafe cleanup');
 fs.rmSync(fixture,{recursive:true,force:true,maxRetries:6,retryDelay:100});
}
