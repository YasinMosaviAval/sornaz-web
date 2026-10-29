const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const net = require('node:net');
const {spawn} = require('node:child_process');

// Real multipart uploads and downloads against isolated SQLite data and files.
const routerSource = String.raw`<?php
$fixture=getenv('SORNAZ_SESSION_FIXTURE'); $root=getenv('SORNAZ_SESSION_PROJECT');
if(PHP_SAPI!=='cli-server'||!$fixture||!str_starts_with(basename($fixture),'sornaz-session-http-')||!$root){http_response_code(500);exit;}
if(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)==='/health'){http_response_code(204);exit;}
$map=require $root.'/vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($map,$root){if(isset($map[$class]))require_once $map[$class];else{$p=$root.'/'.str_replace(chr(92),'/',$class).'.php';if(is_file($p))require_once $p;}});
function base_path($p=''){return $GLOBALS['root'].($p?'/'.$p:'');}
function storage_path($p=''){return $GLOBALS['fixture'].'/locks'.($p?'/'.$p:'');}
function env($k,$v=null){return $k==='SORNAZ_SESSION_FILES_ROOT'?(($_SERVER['HTTP_X_FIXTURE_ROOT']??'')==='public'?base_path('storage/session-files-unsafe-fixture'):($GLOBALS['fixture'].'/private')): $v;}
function auth(){return new class{function id(){return (int)($_SESSION['_auth_user']??$_SERVER['HTTP_X_FIXTURE_ACTOR']??0);}function check(){return $this->id()>0;}function user(){return ['user_id'=>$this->id(),'type'=>'user'];}};}
function session(){static $s;return $s??=new class{function get($k,$v=null){return $k==='suppress_database_notifications'?true:($k==='_csrf_token'?'fixture-csrf':$v);}};}
function app(){static $app;return $app??=new class{public $locale='en';function container(){return new Core\container\Container;}function getLocale(){return $this->locale;}function setLocale($locale){$this->locale=$locale;}};}
function transaction($work){$p=db();$p->beginTransaction();try{$v=$work();if(($_SERVER['HTTP_X_FIXTURE_FAIL']??'')==='commit')throw new Exception('synthetic commit failure');$p->commit();return $v;}catch(Throwable $e){if($p->inTransaction())$p->rollBack();throw $e;}}
function db(){static $p;if($p)return $p;$p=new PDO('sqlite:'.$GLOBALS['fixture'].'/fixture.sqlite',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$p->sqliteCreateFunction('CONCAT',fn(...$parts)=>implode('',$parts));$p->sqliteCreateFunction('IF',fn($test,$yes,$no)=>$test?$yes:$no);
if($p->query("SELECT count(*) FROM sqlite_master WHERE name='users'")->fetchColumn())return $p;
$p->exec("CREATE TABLE users(user_id INTEGER PRIMARY KEY,type TEXT,status TEXT,deleted_at TEXT);
CREATE TABLE academies(academy_id INTEGER PRIMARY KEY,user_id INT,deleted_at TEXT,created_by INT);
CREATE TABLE academy_branches(branch_id INTEGER PRIMARY KEY,academy_id INT,user_id INT,deleted_at TEXT);
CREATE TABLE academy_branch_courses(course_id INTEGER PRIMARY KEY,academy_id INT,branch_id INT,deleted_at TEXT);
CREATE TABLE academy_branch_course_terms(term_id INTEGER PRIMARY KEY,course_id INT,deleted_at TEXT);
CREATE TABLE academy_branch_bookings(booking_id INTEGER PRIMARY KEY,deleted_at TEXT);
CREATE TABLE academy_branch_course_term_sessions(term_session_id INTEGER PRIMARY KEY,term_id INT,booking_id INT,deleted_at TEXT);
CREATE TABLE academy_branch_members(member_id INTEGER PRIMARY KEY,user_id INT,academy_id INT,branch_id INT,status TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_member_roles(member_id INT,role_id INT,deleted_at TEXT);
CREATE TABLE access_system_roles(role_id INTEGER PRIMARY KEY,name TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_member_contracts(member_id INT,type TEXT,start_date TEXT,end_date TEXT,status TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_course_term_enrollments(member_id INT,term_id INT,type TEXT,status TEXT,deleted_at TEXT);
CREATE TABLE media_files(media_file_id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INT,disk TEXT,directory TEXT,filename TEXT,extension TEXT,mime_type TEXT,type TEXT,collection TEXT,path TEXT,original_filename TEXT,fileable_type TEXT,fileable_id INT,sort_order INT,size INT,checksum TEXT,visibility TEXT,created_at TEXT,created_by INT,updated_at TEXT,updated_by INT,deleted_at TEXT,deleted_by INT);
INSERT INTO users VALUES(1,'admin','active',NULL),(10,'academy','active',NULL),(11,'branch','active',NULL),(12,'branch','active',NULL),(20,'academy','active',NULL),(30,'user','active',NULL),(31,'user','active',NULL),(32,'user','active',NULL),(33,'user','active',NULL),(34,'user','active',NULL),(35,'user','active',NULL),(36,'user','active',NULL),(37,'user','active',NULL),(38,'user','active',NULL),(39,'user','inactive',NULL),(40,'user','active',NULL);
INSERT INTO academies VALUES(100,10,NULL,10),(200,20,NULL,20);
INSERT INTO academy_branches VALUES(101,100,11,NULL),(102,100,12,NULL);
INSERT INTO academy_branch_courses VALUES(1,100,101,NULL),(2,100,102,NULL),(3,100,NULL,NULL),(4,200,NULL,NULL);
INSERT INTO academy_branch_course_terms VALUES(1,1,NULL),(2,2,NULL),(3,3,NULL),(4,4,NULL);
INSERT INTO academy_branch_bookings VALUES(1,NULL),(2,NULL),(3,NULL),(4,NULL);
INSERT INTO academy_branch_course_term_sessions VALUES(1,1,1,NULL),(2,2,2,NULL),(3,3,3,NULL),(4,4,4,NULL);
INSERT INTO academy_branch_members VALUES(30,30,100,101,'active',NULL),(31,31,100,101,'active',NULL),(32,32,100,102,'active',NULL),(33,33,100,101,'active',NULL),(34,34,100,NULL,'active',NULL),(35,35,100,101,'active',NULL),(36,36,100,101,'inactive',NULL),(37,37,100,101,'active',NULL),(38,38,100,101,'active',NULL),(39,39,100,101,'active',NULL),(40,40,100,101,'active',NULL),(110,11,100,NULL,'active',NULL);
INSERT INTO access_system_roles VALUES(1,'academy_manager',NULL),(2,'academy_branch_receptionist',NULL);
INSERT INTO academy_branch_member_roles VALUES(33,2,NULL),(34,1,NULL),(110,1,NULL);
INSERT INTO academy_branch_member_contracts VALUES(35,'manager','2020-01-01','2020-12-31','active',NULL),(40,'receptionist','2020-01-01','2099-01-01','active',NULL);
INSERT INTO academy_branch_course_term_enrollments VALUES(30,1,'teacher','active',NULL),(31,1,'student','active',NULL),(32,2,'student','active',NULL),(35,1,'teacher','pending',NULL),(36,1,'student','active',NULL),(37,1,'student','pending',NULL),(38,2,'teacher','active',NULL),(39,1,'student','active',NULL);
INSERT INTO users VALUES(41,'user','active',NULL);
INSERT INTO academy_branch_members VALUES(41,41,100,101,'active',NULL);
INSERT INTO academy_branch_course_term_enrollments VALUES(41,1,'teacher','active',NULL);
ALTER TABLE academy_branch_course_terms ADD COLUMN status TEXT DEFAULT 'active';
ALTER TABLE academy_branch_course_terms ADD COLUMN start_date TEXT DEFAULT '2030-01-01';
ALTER TABLE academy_branch_course_terms ADD COLUMN end_date TEXT DEFAULT '2030-02-01';
ALTER TABLE academy_branch_course_term_sessions ADD COLUMN classroom_id INT;
ALTER TABLE academy_branch_bookings ADD COLUMN requested_date TEXT DEFAULT '2030-01-01';
ALTER TABLE academy_branch_bookings ADD COLUMN start_time TEXT DEFAULT '12:00:00';
ALTER TABLE academy_branch_bookings ADD COLUMN end_time TEXT DEFAULT '13:00:00';
CREATE TABLE academy_branch_classrooms(classroom_id INT,deleted_at TEXT);
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INT,field TEXT,locale TEXT,value TEXT,deleted_at TEXT,deleted_by INT);");return $p;}
function locale(){return 'en';}
if(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)==='/fixture-learning'){(new Modules\Analytics\Controllers\Api\MobileLearningController())->index()->send();exit;}
require $root.'/Modules/Academy/Routes/web.php';
if(preg_match('~^/fixture-mobile/(session-files|session-file-upload|session-file-download|session-file-delete)$~',parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),$mobile)){
 $_SESSION=['sentinel'=>'browser'];$before=$_SESSION;
 $tokens=new class extends Modules\System\Services\MobileAuthTokenService{function __construct(){}function userFromRequest():?array{if(!preg_match('/^Bearer fixture-([1-9][0-9]*)$/D',$_SERVER['HTTP_AUTHORIZATION']??'',$m))return null;$q=db()->prepare('SELECT * FROM users WHERE user_id=?');$q->execute([(int)$m[1]]);$user=$q->fetch();return $user?($user+['password'=>'fixture-only']):null;}};
 $panel=new Modules\Analytics\Controllers\Api\MobilePanelController($tokens,new Modules\Analytics\Services\MobilePanelAccess,new Modules\Analytics\Services\MobilePanelCatalog);
 $response=$panel->execute('schedules',$mobile[1]);if($_SESSION!==$before){http_response_code(500);exit('Bearer identity leaked');}$response->send();exit;
}
$route=Core\router\Router::dispatch($_SERVER['REQUEST_METHOD'],parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));
if(!$route||$route['action'][0]!==Modules\Academy\Controllers\Web\AcademySessionFileController::class){http_response_code(404);exit;}
$next=static function()use($route){preg_match('~/sessions/(\d+)/files(?:/(\d+)/[^/]+)?$~',parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),$m);return (new Modules\Academy\Controllers\Web\AcademySessionFileController(new Modules\Academy\Services\AcademySessionFileService()))->{$route['action'][1]}((int)$m[1],(int)($m[2]??0));};
foreach(array_reverse($route['middlewares'])as $middleware){$previous=$next;$next=static fn()=> (new ($middleware==='auth'?Core\middleware\AuthMiddleware::class:Core\csrf\CsrfMiddleware::class))->handle(new Core\http\Request(),$previous);}
$next()->send();
`;

async function run(){
 const fixture=fs.mkdtempSync(path.join(os.tmpdir(),'sornaz-session-http-'));
 const router=path.join(fixture,'router.php');fs.writeFileSync(router,routerSource);
 const server=net.createServer();await new Promise(r=>server.listen(0,'127.0.0.1',r));const port=server.address().port;await new Promise(r=>server.close(r));
 const child=spawn('php',['-d','display_errors=0','-d',`upload_tmp_dir=${fixture}`,'-d','upload_max_filesize=2M','-d','post_max_size=3M','-S',`127.0.0.1:${port}`,router],{cwd:path.resolve(__dirname,'..'),windowsHide:true,stdio:['ignore','ignore','pipe'],env:{...process.env,SORNAZ_SESSION_FIXTURE:fixture,SORNAZ_SESSION_PROJECT:path.resolve(__dirname,'..')}});
 let stderr='';child.stderr.on('data',c=>stderr+=c);
 const request=(url,actor=0,options={})=>fetch(`http://127.0.0.1:${port}`+url,{...options,headers:{Accept:'application/json','X-Fixture-Actor':String(actor),...options.headers}});
 const upload=(actor,id=1,name='lesson.txt',content='Synthetic session exercise',headers={})=>{const body=new FormData();body.append('file',new Blob([content],{type:'text/plain'}),name);return request(`/academy/sessions/${id}/files`,actor,{method:'POST',body,headers:{'X-CSRF-TOKEN':'fixture-csrf',...headers}});};
 const data=async r=>(await r.json()).data.data;
 const stored=()=>fs.existsSync(path.join(fixture,'private'))?fs.readdirSync(path.join(fixture,'private'),{recursive:true}).filter(p=>/\.[a-z0-9]+$/.test(p)).length:0;
 try{
  let ready=false;for(let i=0;i<70;i++){try{if((await request('/health')).status===204){ready=true;break;}}catch{}await new Promise(r=>setTimeout(r,50));}assert.ok(ready,stderr);
  assert.equal((await request('/academy/sessions/1/files')).status,401);
  for(const actor of [12,20,32,35,36,37,38,39])assert.equal((await request('/academy/sessions/1/files',actor)).status,403,`deny actor ${actor}`);
  for(const [actor,id]of [[11,2],[11,3],[11,4],[12,1],[33,2],[33,3],[40,2],[10,4]])assert.equal((await upload(actor,id)).status,403,`scope ${actor}/${id}`);
  assert.equal(stored(),0,'Denied uploads must not create files');
  assert.equal((await upload(31)).status,403,'Enrolled students cannot upload');
  assert.equal((await upload(30,1,'lesson.txt','exercise',{'X-CSRF-TOKEN':'wrong'})).status,419);
  const student=await data(await request('/academy/sessions/1/files',31));assert.equal(student.canUpload,false);assert.equal(student.canDownload,true);
  for(const actor of [1,10,11,30,33,34,40])assert.equal((await data(await request('/academy/sessions/1/files',actor))).canUpload,true);
  const first=await upload(30);assert.equal(first.status,200,await first.clone().text());const file=(await data(first)).file;assert.equal(stored(),1);
  assert.equal((await data(await request('/academy/sessions/1/files',41))).files[0].canDelete,false,'Another assigned teacher may not delete this upload');
  assert.equal((await upload(30,1,'private.txt','Unsafe root fixture',{'X-Fixture-Root':'public'})).status,503,'Public storage roots must be rejected');
  const repeated=await data(await upload(30));assert.equal(repeated.duplicate,true);assert.equal(repeated.file.id,file.id);assert.equal(stored(),1);
  const download=await request(file.downloadUrl,31);assert.equal(download.status,200);assert.equal(await download.text(),'Synthetic session exercise');assert.match(download.headers.get('content-disposition'),/^attachment/);assert.match(download.headers.get('cache-control'),/no-store/);assert.equal(download.headers.get('x-content-type-options'),'nosniff');
  assert.equal((await request(`/academy/sessions/2/files/${file.id}/download`,12)).status,404,'Files cannot be rebound to another authorized session');
  assert.equal((await request(file.downloadUrl,32)).status,403);
  assert.equal((await upload(30,1,'bad.php.txt')).status,422);
  assert.equal((await upload(30,1,'large.txt','x'.repeat(2*1024*1024+1))).status,413);
  const before=stored();const failed=await upload(30,1,'rollback.txt','Rollback synthetic content',{'X-Fixture-Fail':'commit'});assert.equal(failed.status,500);assert.equal(stored(),before,'Rollback must remove its new file');
  const del=(actor)=>request(`/academy/sessions/1/files/${file.id}/delete`,actor,{method:'POST',headers:{'X-CSRF-TOKEN':'fixture-csrf'}});
  assert.equal((await del(31)).status,403);assert.equal((await del(41)).status,403);assert.equal((await del(33)).status,200);assert.equal((await request(file.downloadUrl,31)).status,404);
  assert.equal((await data(await request('/academy/sessions/1/files',31))).files.length,0);
  const mobile=(operation,actor,query='id=1',options={})=>request(`/fixture-mobile/${operation}?${query}`,0,{...options,headers:{Authorization:`Bearer fixture-${actor}`,...options.headers}});
  assert.equal((await data(await mobile('session-files',31))).canUpload,false);
  assert.equal((await mobile('session-files',32)).status,403);
  assert.equal((await mobile('session-files',31,'id=../1')).status,422);
  assert.equal((await request('/fixture-mobile/session-files?id=1')).status,401);
  const mobileBody=new FormData();mobileBody.append('payload_b64',Buffer.from('{}').toString('base64'));mobileBody.append('file',new Blob(['Mobile private exercise']),'mobile.txt');
  const mobileUpload=await mobile('session-file-upload',30,'id=1',{method:'POST',body:mobileBody});assert.equal(mobileUpload.status,200,await mobileUpload.clone().text());const mobileFile=(await data(mobileUpload)).file;
  const mobileDownload=await mobile('session-file-download',31,`id=1&file=${mobileFile.id}`);assert.equal(mobileDownload.status,200);assert.equal(await mobileDownload.text(),'Mobile private exercise');
  assert.equal((await mobile('session-file-download',32,`id=1&file=${mobileFile.id}`)).status,403);
  for(const actor of [30,31,32,35,36,37]){
   const learning=(await (await request('/fixture-learning',actor)).json()).data;
   const active=[30,31,32].includes(actor);
   assert.equal(learning.classes.length,active?1:0,'Only active enrollments and memberships expose sessions');
   if(active){assert.equal(learning.classes[0].id,actor===32?2:1);assert.equal(learning.classes[0].sessionId,learning.classes[0].term_session_id);assert.deepEqual(learning.terms[0].sessions.map(s=>s.id),[learning.classes[0].id]);}
   else{assert.equal(learning.terms.length,1,'Pending terms remain visible');assert.deepEqual(learning.terms[0].sessions,[]);}
  }
  console.log('Session files HTTP passed: real multipart, private streaming, CSRF, active enrollment, scoped management, repeat uploads, MIME/size, rollback and deletion.');
 }finally{
  if(child.exitCode===null){const done=new Promise(r=>child.once('exit',r));child.kill();await done;}
  const resolved=path.resolve(fixture);if(!resolved.startsWith(path.resolve(os.tmpdir())+path.sep)||!path.basename(resolved).startsWith('sornaz-session-http-'))throw Error('Unsafe cleanup');
  fs.rmSync(resolved,{recursive:true,force:true,maxRetries:6,retryDelay:100});
 }
}
if(require.main===module)run().catch(e=>{console.error(e);process.exitCode=1;});
module.exports={run};
