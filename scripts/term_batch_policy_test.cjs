const {spawnSync}=require('node:child_process');
const source=String.raw`<?php
$map=require 'vendor/composer/autoload_classmap.php';spl_autoload_register(static function($class)use($map){if(isset($map[$class]))require_once $map[$class];else{$path=str_replace('\\','/',$class).'.php';if(is_file($path))require_once $path;}});
function db(){return $GLOBALS['pdo'];}function locale(){return 'fa';}function app(){return new class{function getLocale(){return 'fa';}};}
function session(){return new class{function get($key,$default=null){return $key==='suppress_database_notifications'?true:$default;}};}
function transaction($work){$own=!db()->inTransaction();if($own)db()->beginTransaction();try{$result=$work();if($own)db()->commit();return $result;}catch(Throwable $e){if($own&&db()->inTransaction())db()->rollBack();throw $e;}}
$root=sys_get_temp_dir().'/sornaz-batch-'.bin2hex(random_bytes(6));function storage_path($path=''){return $GLOBALS['root'].'/'.$path;}
register_shutdown_function(static function()use($root){foreach(glob($root.'/payment-locks/*.lock')?:[]as$file)unlink($file);if(is_dir($root.'/payment-locks'))rmdir($root.'/payment-locks');if(is_dir($root))rmdir($root);});
$checks=0;function check($ok,$label){if(!$ok)throw new LogicException($label);$GLOBALS['checks']++;}function denied($work){try{$work();}catch(RuntimeException $e){$GLOBALS['checks']++;return;}throw new LogicException('Invalid batch accepted');}
$opts=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC];$mysql=getenv('TERM_BATCH_MYSQL')==='1';
if($mysql){$dsn=getenv('SORNAZ_TEST_MYSQL_DSN')?:'mysql:host=127.0.0.1;port=3306;charset=utf8mb4';$user=getenv('SORNAZ_TEST_MYSQL_USER')?:'root';$pass=getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'';$server=new PDO($dsn,$user,$pass,$opts);$fixture='sornaz_term_batch_fixture_'.bin2hex(random_bytes(6));$server->exec('CREATE DATABASE '.$fixture.' CHARACTER SET utf8mb4');register_shutdown_function(static function()use($server,$fixture){$server->exec('DROP DATABASE '.$fixture);});$pdo=new Core\database\PrefixedPDO($dsn,$user,$pass,$opts);$pdo->exec('USE '.$fixture);}else $pdo=new Core\database\PrefixedPDO('sqlite::memory:',null,null,$opts);
$auto=$mysql?'INTEGER AUTO_INCREMENT PRIMARY KEY':'INTEGER PRIMARY KEY AUTOINCREMENT';
$tables=[
'users'=>'user_id INTEGER PRIMARY KEY,type VARCHAR(30),deleted_at TEXT',
'academies'=>'academy_id INTEGER PRIMARY KEY,user_id INTEGER,created_by INTEGER,deleted_at TEXT',
'academy_branches'=>'branch_id INTEGER PRIMARY KEY,academy_id INTEGER,user_id INTEGER,deleted_at TEXT',
'academy_branch_members'=>'member_id INTEGER PRIMARY KEY,user_id INTEGER,academy_id INTEGER,branch_id INTEGER,status VARCHAR(20),deleted_at TEXT',
'access_system_roles'=>'role_id INTEGER PRIMARY KEY,name VARCHAR(100),deleted_at TEXT',
'academy_branch_member_roles'=>'member_id INTEGER,role_id INTEGER,deleted_at TEXT',
'academy_branch_member_contracts'=>'member_id INTEGER,type VARCHAR(30),user_lesson_id INTEGER,deleted_at TEXT',
'user_lessons'=>'user_lesson_id INTEGER PRIMARY KEY,lesson_id INTEGER,deleted_at TEXT',
'academy_branch_courses'=>'course_id INTEGER PRIMARY KEY,academy_id INTEGER,branch_id INTEGER,lesson_id INTEGER,teacher_capacity INTEGER,student_capacity INTEGER,deleted_at TEXT',
'academy_branch_classrooms'=>'classroom_id INTEGER PRIMARY KEY,academy_id INTEGER,branch_id INTEGER,deleted_at TEXT',
'financial_system_currency'=>'currency_id INTEGER PRIMARY KEY,code VARCHAR(10)',
'f_timezone'=>'timezone_id INTEGER PRIMARY KEY,timezone VARCHAR(100)',
'user_availabilities'=>"user_availability_id $auto,user_id INTEGER,date DATE,day_of_week VARCHAR(20),is_repeating INTEGER,unavailable_type VARCHAR(20),start_time TIME,end_time TIME,status VARCHAR(20),is_closed INTEGER,timezone_id INTEGER,priority INTEGER,deleted_at TEXT",
'academy_branch_course_terms'=>"term_id $auto,course_id INTEGER,start_date DATE,end_date DATE,session_count INTEGER,session_period VARCHAR(30),price DECIMAL(14,2),currency_id INTEGER,status VARCHAR(30),created_by INTEGER,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER",
'academy_branch_course_term_enrollments'=>"enrollment_id $auto,term_id INTEGER,member_id INTEGER,type VARCHAR(20),status VARCHAR(20),joined_at TEXT,created_by INTEGER,updated_by INTEGER,deleted_at TEXT",
'academy_branch_course_term_sessions'=>"term_session_id $auto,term_id INTEGER,deleted_at TEXT",
'translations'=>"translation_id $auto,table_name VARCHAR(100),table_id INTEGER,field VARCHAR(100),locale VARCHAR(10),value TEXT,version INTEGER,created_by INTEGER,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER",
];
foreach($tables as$table=>$columns)$pdo->exec('CREATE TABLE '.$table.'('.$columns.')'.($mysql?' ENGINE=InnoDB':''));
$pdo->exec("INSERT INTO users VALUES(10,'academy',NULL),(11,'branch',NULL),(20,'academy',NULL),(30,'human',NULL)");
$pdo->exec('INSERT INTO academies VALUES(100,10,10,NULL),(200,20,20,NULL)');$pdo->exec('INSERT INTO academy_branches VALUES(101,100,11,NULL)');
$pdo->exec("INSERT INTO academy_branch_members VALUES(1,1000,100,NULL,'active',NULL),(2,1001,100,101,'active',NULL),(3,2000,200,NULL,'active',NULL),(300,30,100,NULL,'active',NULL)");
$pdo->exec("INSERT INTO access_system_roles VALUES(1,'academy_receptionist',NULL)");$pdo->exec('INSERT INTO academy_branch_member_roles VALUES(300,1,NULL)');
$pdo->exec("INSERT INTO academy_branch_member_contracts VALUES(1,'teacher',1,NULL),(2,'teacher',1,NULL),(3,'teacher',1,NULL)");$pdo->exec('INSERT INTO user_lessons VALUES(1,5,NULL)');
$pdo->exec('INSERT INTO academy_branch_courses VALUES(500,100,NULL,5,1,1,NULL),(501,100,101,5,1,1,NULL),(600,200,NULL,5,1,1,NULL)');
$pdo->exec('INSERT INTO academy_branch_classrooms VALUES(1001,100,NULL,NULL),(1002,100,101,NULL),(2001,200,NULL,NULL)');
$pdo->exec("INSERT INTO financial_system_currency VALUES(1,'IRR')");$pdo->exec("INSERT INTO f_timezone VALUES(1,'Asia/Tehran')");
$pdo->exec("INSERT INTO user_availabilities(user_id,day_of_week,is_repeating,start_time,end_time,status,is_closed,timezone_id,priority) VALUES(10,'saturday',1,'09:00:00','19:00:00','available',0,1,1)");
$service=new Modules\Academy\Services\AcademyTermService();$data=['name'=>'General term','organizationUserId'=>10,'courseId'=>500,'classroomId'=>0,'currencyId'=>1,'cost'=>'100000','sessionCount'=>8,'repeatType'=>'week','weekday'=>'6','timezoneId'=>1,'durationMinutes'=>30,'rangeStart'=>'10:00','rangeEnd'=>'18:00','requestKey'=>str_repeat('a',32),'teachers'=>[['id'=>1]],'summary'=>'Summary','description'=>'Description'];
$result=$service->createBatch(10,$data);check($result['count']===16&&count($result['ids'])===16,'Exactly 16 independent terms');
$rows=$pdo->query('SELECT * FROM academy_branch_course_terms ORDER BY term_id')->fetchAll();check(count($rows)===16&&count(array_filter($rows,fn($r)=>$r['status']==='open'&&$r['start_date']===null&&(int)$r['session_count']===8&&(float)$r['price']===100000.0))===16,'Open undated terms retain session count and per-term cost');
check((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_enrollments')->fetchColumn()===16,'Teacher attached to each term');check((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_sessions')->fetchColumn()===0,'Empty terms create no dated sessions');
$drafts=array_map(fn($r)=>json_decode($r['value'],true),$pdo->query("SELECT value FROM translations WHERE field='schedule_draft' ORDER BY table_id")->fetchAll());check($drafts[0]['startTime']==='10:00'&&$drafts[15]['startTime']==='17:30'&&$drafts[15]['endTime']==='18:00'&&$drafts[0]['classroomId']===0,'Complete half-hour range and optional room persisted');
check($service->createBatch(10,$data)===$result,'Request replay returns same IDs');check((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_terms')->fetchColumn()===16,'Replay creates no duplicate rows');
denied(fn()=>$service->createBatch(10,array_replace($data,['cost'=>'200000'])));
foreach([['rangeEnd'=>'17:45'],['durationMinutes'=>0],['courseId'=>600],['organizationUserId'=>20],['teachers'=>[['id'=>3]]],['classroomId'=>2001]]as$bad)denied(fn()=>$service->createBatch(10,array_replace($data,$bad,['requestKey'=>str_repeat('b',32)])));
$counts=fn()=>array_map(fn($table)=>(int)$pdo->query('SELECT COUNT(*) FROM '.$table)->fetchColumn(),['academy_branch_course_terms','academy_branch_course_term_enrollments','translations']);$before=$counts();denied(fn()=>$service->createBatch(10,array_replace($data,['requestKey'=>str_repeat('c',32),'rangeEnd'=>'20:00'])));check($counts()===$before,'Late invalid slot rolls back every term, teacher and translation');
$pending=$service->createBatch(30,array_replace($data,['requestKey'=>str_repeat('d',32),'status'=>'ongoing']));check($pending['count']===16&&(int)$pdo->query("SELECT COUNT(*) FROM academy_branch_course_terms WHERE status='pending'")->fetchColumn()===16,'Receptionist cannot override pending status');
$id=$result['ids'][0];denied(fn()=>$service->assignDraftClassroom(20,$id,2001));$service->assignDraftClassroom(10,$id,1001);$stored=json_decode($pdo->query("SELECT value FROM translations WHERE field='schedule_draft' AND table_id=".$id)->fetchColumn(),true);check($stored['classroomId']===1001,'Room can be assigned later');denied(fn()=>$service->assignDraftClassroom(10,$id,1002));
echo "Term batch: $checks atomicity, 16-row creation, retries, role/status, tenant and optional classroom checks passed (".($mysql?'mysql':'sqlite').").\n";
`;
module.exports=source;
if(require.main===module){const result=spawnSync('php',[],{input:source,encoding:'utf8'});process.stdout.write(result.stdout||'');process.stderr.write(result.stderr||'');process.exitCode=result.status??1;}
