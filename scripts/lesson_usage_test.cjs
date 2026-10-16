const { spawnSync } = require('node:child_process');
const source = String.raw`<?php
$map=require 'vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($map){if(isset($map[$class]))require_once $map[$class];});
require_once 'Modules/Academy/Services/LessonUsageService.php';
require_once 'Modules/Academy/Services/LessonInUseException.php';
function db(){return $GLOBALS['pdo'];} function locale(){return 'fa';}
function transaction(callable $callback){$p=db();$own=!$p->inTransaction();if($own)$p->beginTransaction();try{$r=$callback();if($own)$p->commit();return $r;}catch(Throwable $e){if($own&&$p->inTransaction())$p->rollBack();throw $e;}}
function check($ok,$message){if(!$ok)throw new LogicException($message);$GLOBALS['checks']++;}
function denied(callable $f){try{$f();}catch(RuntimeException $e){$GLOBALS['checks']++;return $e;}throw new LogicException('Unexpected permission or deletion success');}
$checks=0;$fixture=null;$server=null;$workerFile=null;
try{
$mysql=getenv('LESSON_USAGE_MYSQL')==='1';$opts=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC];
if($mysql){$dsn=getenv('SORNAZ_TEST_MYSQL_DSN')?:'mysql:host=127.0.0.1;port=3306;charset=utf8mb4';$user=getenv('SORNAZ_TEST_MYSQL_USER')?:'root';$pass=getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'';$server=new PDO($dsn,$user,$pass,$opts);$fixture='sornaz_lesson_fixture_'.bin2hex(random_bytes(6));$server->exec('CREATE DATABASE '.$fixture.' CHARACTER SET utf8mb4');$pdo=new Core\database\PrefixedPDO($dsn,$user,$pass,$opts);$pdo->exec('USE '.$fixture);}else $pdo=new Core\database\PrefixedPDO('sqlite::memory:',null,null,$opts);
$auto=$mysql?'INTEGER AUTO_INCREMENT PRIMARY KEY':'INTEGER PRIMARY KEY AUTOINCREMENT';
$tables=[
'users'=>'user_id INTEGER PRIMARY KEY,username TEXT,type TEXT,deleted_at TEXT',
'academies'=>'academy_id INTEGER PRIMARY KEY,user_id INTEGER,created_by INTEGER,deleted_at TEXT',
'academy_branches'=>'branch_id INTEGER PRIMARY KEY,academy_id INTEGER,user_id INTEGER,deleted_at TEXT',
'academy_branch_members'=>'member_id INTEGER PRIMARY KEY,user_id INTEGER,academy_id INTEGER,branch_id INTEGER,status TEXT,deleted_at TEXT',
'access_system_roles'=>'role_id INTEGER PRIMARY KEY,name TEXT,deleted_at TEXT',
'academy_branch_member_roles'=>'member_role_id INTEGER PRIMARY KEY,member_id INTEGER,role_id INTEGER,deleted_at TEXT',
'academy_branch_member_contracts'=>'member_contract_id INTEGER PRIMARY KEY,member_id INTEGER,type TEXT,user_lesson_id INTEGER,deleted_at TEXT',
'academy_branch_courses'=>'course_id INTEGER PRIMARY KEY,lesson_id INTEGER,academy_id INTEGER,branch_id INTEGER,deleted_at TEXT',
'lessons'=>'lesson_id INTEGER PRIMARY KEY,deleted_at TEXT',
'levels'=>'level_id INTEGER PRIMARY KEY,type TEXT,is_active INTEGER,sort_order INTEGER,deleted_at TEXT',
'user_lessons'=>'user_lesson_id INTEGER PRIMARY KEY,user_id INTEGER,lesson_id INTEGER,level_id INTEGER,start_date TEXT,status TEXT,is_primary INTEGER DEFAULT 0,approved_at TEXT,approved_by INTEGER,deleted_at TEXT,deleted_by INTEGER,updated_at TEXT,updated_by INTEGER',
'translations'=>"translation_id $auto,table_name VARCHAR(190),table_id INTEGER,field VARCHAR(190),locale VARCHAR(10),value TEXT,version INTEGER,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER",
'instruments'=>'instrument_id INTEGER PRIMARY KEY,deleted_at TEXT',
'user_instruments'=>'user_instrument_id INTEGER PRIMARY KEY,user_id INTEGER,deleted_at TEXT',
'f_timezone'=>'timezone_id INTEGER PRIMARY KEY,timezone TEXT,status TEXT,sort_order INTEGER,deleted_at TEXT',
'user_availabilities'=>'user_availability_id INTEGER PRIMARY KEY,user_id INTEGER,unavailable_type TEXT,deleted_at TEXT'];
foreach($tables as $name=>$columns)$pdo->exec("CREATE TABLE $name($columns)".($mysql?' ENGINE=InnoDB':''));
$pdo->exec("INSERT INTO users VALUES(1,'founder','human',NULL),(10,'academy-a','academy',NULL),(11,'branch-a','branch',NULL),(12,'branch-b','branch',NULL),(20,'other','academy',NULL),(21,'other-branch','branch',NULL),(30,'manager','human',NULL),(31,'branch-manager','human',NULL),(32,'receptionist','human',NULL),(33,'inactive-manager','human',NULL),(90,'teacher-a','human',NULL),(91,'teacher-academy','human',NULL),(92,'teacher-other','human',NULL)");
$pdo->exec('INSERT INTO academies VALUES(100,10,30,NULL),(200,20,20,NULL)');
$pdo->exec('INSERT INTO academy_branches VALUES(101,100,11,NULL),(102,100,12,NULL),(201,200,21,NULL)');
$pdo->exec("INSERT INTO academy_branch_members VALUES(300,30,100,NULL,'active',NULL),(301,31,100,101,'active',NULL),(302,32,100,NULL,'active',NULL),(303,33,100,NULL,'inactive',NULL),(900,90,100,101,'active',NULL),(901,91,100,NULL,'active',NULL),(902,92,200,201,'active',NULL)");
$pdo->exec("INSERT INTO access_system_roles VALUES(1,'academy_manager',NULL),(2,'branch_manager',NULL),(3,'academy_receptionist',NULL)");
$pdo->exec('INSERT INTO academy_branch_member_roles VALUES(1,300,1,NULL),(2,301,2,NULL),(3,302,3,NULL),(4,303,1,NULL)');
$pdo->exec('INSERT INTO lessons VALUES(1,NULL),(2,NULL),(3,NULL)');$pdo->exec("INSERT INTO levels VALUES(1,'learning',1,1,NULL)");
foreach([[1001,10,1],[1002,10,2],[1101,11,1],[1102,11,3],[1201,12,2],[2001,20,1],[9001,90,1],[9002,91,1],[9003,92,1]]as[$id,$owner,$lesson])$pdo->exec("INSERT INTO user_lessons(user_lesson_id,user_id,lesson_id,level_id,start_date,status) VALUES($id,$owner,$lesson,1,'2026-10-01','active')");
$pdo->exec('INSERT INTO academy_branch_courses VALUES(501,1,100,NULL,NULL),(502,1,100,101,NULL),(503,1,NULL,102,NULL),(599,1,200,201,NULL)');
$pdo->exec("INSERT INTO academy_branch_member_contracts VALUES(701,900,'teacher',9001,NULL),(702,901,'teacher',9002,NULL),(799,902,'teacher',9003,NULL)");
$insert=$pdo->prepare("INSERT INTO translations(table_name,table_id,field,locale,value) VALUES(?,?,?,'fa',?)");foreach([['academy_branch_courses',501,'title','Academy course'],['academy_branch_member_contracts',701,'title','Teacher contract'],['users',90,'full_name','Teacher A']]as$row)$insert->execute($row);
$service=new Modules\Academy\Services\AcademyBranchOfferingService();$teaching = new Modules\Academy\Services\LessonUsageService();
check($teaching->teachingLevelIds(12,1)===[1], 'Branch inherits academy teaching levels');
check($teaching->teachingLevelIds(20,3)===[], 'Other academy cannot use branch lesson levels');
$pdo->beginTransaction();
$pdo->exec("UPDATE user_lessons SET level_id=2 WHERE user_lesson_id=1101");
check($teaching->teachingLevelIds(11,1)===[2], 'Branch offering overrides academy levels');
$pdo->exec("INSERT INTO user_lessons(user_lesson_id,user_id,lesson_id,level_id,status) VALUES(1103,11,1,3,'active'),(1104,11,1,4,'pending')");
check($teaching->teachingLevelIds(11,1)===[2,3], 'Only active levels for selected organization and lesson');
$pdo->rollBack();
$teaching->validateTeachingLevel(12,1,1);
denied(fn()=>$teaching->validateTeachingLevel(12,1,2));
denied(fn()=>$teaching->validateTeachingLevel(20,3,1));
denied(fn()=>$teaching->validateTeachingLevel(12,0,1));
$policy=new Modules\Academy\Services\LessonUsageService();
$usage=$service->lessonUsage(10,1001);
check(array_column($usage['courses'],'id')===[501,502,503],'Academy courses include descendants and legacy rows, not foreign academy');
check(array_column($usage['contracts'],'id')===[701,702],'Teacher personal lessons match organization dependencies');
check($usage['contracts'][0]['teacher']==='Teacher A','Teacher full name included');
check(array_column($service->lessonUsage(11,1101)['contracts'],'id')===[701],'Branch excludes sibling and academy contracts');
denied(fn()=>$service->lessonUsage(11,1001));denied(fn()=>$service->lessonUsage(20,1001));
foreach([1,10,30,32]as$actor)check(denied(fn()=>$service->delete('lesson',1001,$actor))instanceof Modules\Academy\Services\LessonInUseException,'Used lesson cannot be deleted');
$data=['organization_user_id'=>10,'lesson_id'=>1,'level_id'=>1,'start_date'=>'2026-10-01','summary'=>'updated','status'=>'active'];
foreach([32,31,11,33]as$actor)denied(fn()=>$service->saveLesson($actor,$data,1001));
check($service->saveLesson(30,$data,1001)['id']===1001,'Academy manager edits linked lesson');$data['organization_user_id']=11;
check($service->saveLesson(10,$data,1101)['id']===1101,'Academy account edits branch lesson');check($service->saveLesson(31,$data,1101)['id']===1101,'Branch manager edits own branch lesson');
$data['lesson_id']=2;denied(fn()=>$service->saveLesson(10,$data,1101));
$rows=$service->all(32)['lessons'];$row=array_values(array_filter($rows,fn($r)=>$r['id']===1001))[0];check(!$row['canEdit']&&!$row['canDelete']&&$row['canInspectDependencies'],'Receptionist has dependency view without edit/delete');
$rows=$service->all(11)['lessons'];$row=array_values(array_filter($rows,fn($r)=>$r['id']===1001))[0];check(!$row['canEdit']&&!$row['canDelete']&&!$row['canInspectDependencies'],'Inherited academy lesson is read-only for branch');
check($policy->available(12,1)['user_id']==10,'Branch inherits academy lesson');denied(fn()=>$policy->available(12,3));
$options=$policy->offeredRows([['user_id'=>12]]);check(in_array(1,array_column($options,'lesson_id'),true)&&!in_array(3,array_column($options,'lesson_id'),true),'Catalog inherits parent, not sibling');
$before=$service->lessonsRealtimeVersion(32)['version'];
$pdo->exec('INSERT INTO academy_branch_courses VALUES(504,2,100,102,NULL)');denied(fn()=>$service->delete('lesson',1002,10));
check($before!==$service->lessonsRealtimeVersion(32)['version'],'Realtime version refreshes actions when a course starts using a lesson');
$service->delete('lesson',1102,30);check((bool)$pdo->query('SELECT deleted_at FROM user_lessons WHERE user_lesson_id=1102')->fetchColumn(),'Academy manager deletes unused branch lesson');denied(fn()=>$service->delete('lesson',1102,30));
if($mysql){
 $workerFile=tempnam(sys_get_temp_dir(),'sornaz_lesson_worker_');
 file_put_contents($workerFile, <<<'LESSONWORKER'
<?php
$input=json_decode(stream_get_contents(STDIN),true,512,JSON_THROW_ON_ERROR);
if(!preg_match('/^sornaz_lesson_fixture_[a-f0-9]{12}$/D',$input['database']??''))exit(2);
$map=require getcwd().'/vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($map){if(isset($map[$class]))require_once $map[$class];});
require_once getcwd().'/Modules/Academy/Services/LessonUsageService.php';
function db(){return $GLOBALS['pdo'];}
$pdo=new Core\database\PrefixedPDO(getenv('SORNAZ_TEST_MYSQL_DSN')?:'mysql:host=127.0.0.1;port=3306;charset=utf8mb4',getenv('SORNAZ_TEST_MYSQL_USER')?:'root',getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$pdo->exec('USE '.$input['database']);$pdo->beginTransaction();
try{
 if($input['mode']==='delete-first'){echo "READY\n";flush();}
 (new Modules\Academy\Services\LessonUsageService())->available(12,2,true);
 if($input['mode']==='insert-first'){echo "LOCKED\n";flush();usleep(700000);}
 $pdo->exec('INSERT INTO academy_branch_courses VALUES(505,2,100,102,NULL)');$pdo->commit();echo "INSERTED\n";
}catch(RuntimeException $error){if($pdo->inTransaction())$pdo->rollBack();if($input['mode']!=='delete-first')throw $error;echo "DENIED\n";}
LESSONWORKER
 );
 $pdo->exec("UPDATE academy_branch_courses SET deleted_at='2026-10-01' WHERE course_id=504");
 $spawn=function($mode)use($fixture,$workerFile){$process=proc_open([PHP_BINARY,$workerFile],[['pipe','r'],['pipe','w'],['pipe','w']],$pipes);if(!is_resource($process))throw new RuntimeException('Cannot start worker');fwrite($pipes[0],json_encode(['database'=>$fixture,'mode'=>$mode]));fclose($pipes[0]);check(trim(fgets($pipes[1]))===($mode==='insert-first'?'LOCKED':'READY'),'Worker synchronized');return[$process,$pipes];};
 [$process,$pipes]=$spawn('insert-first');check(denied(fn()=>$service->delete('lesson',1002,10))instanceof Modules\Academy\Services\LessonInUseException,'Delete sees concurrent committed dependency after lock');check(trim(stream_get_contents($pipes[1]))==='INSERTED','Concurrent course committed');fclose($pipes[1]);fclose($pipes[2]);check(proc_close($process)===0,'Worker exited');
 $pdo->exec("UPDATE academy_branch_courses SET deleted_at='2026-10-01' WHERE course_id=505");$pdo->beginTransaction();$service->delete('lesson',1002,10);[$process,$pipes]=$spawn('delete-first');$pdo->commit();check(trim(stream_get_contents($pipes[1]))==='DENIED','Course checks current lesson after delete commits');fclose($pipes[1]);fclose($pipes[2]);check(proc_close($process)===0,'Worker exited');
}
echo "Lesson usage: $checks access, dependencies, edits, inheritance and deletion checks passed (".$pdo->getAttribute(PDO::ATTR_DRIVER_NAME).").\n";
}finally{if($fixture!==null&&$server!==null)$server->exec('DROP DATABASE '.$fixture);if($workerFile!==null&&is_file($workerFile))unlink($workerFile);}
`;
const result = spawnSync('php', [], { input: source, encoding: 'utf8', cwd: require('node:path').resolve(__dirname, '..'), timeout: 60000 });
process.stdout.write(result.stdout || ''); process.stderr.write(result.stderr || '');
if (result.error) console.error(result.error.message);
process.exitCode = result.status ?? 1;
