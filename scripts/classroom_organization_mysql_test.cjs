// Executes PHP from stdin; all database writes are limited to a random fixture.
const { spawnSync } = require('node:child_process');
const source = String.raw`<?php
spl_autoload_register(static function($class){$path=getcwd().'/'.str_replace('\\','/',$class).'.php';if(str_starts_with($class,'Core\\'))$path=getcwd().'/core/'.str_replace('\\','/',substr($class,5)).'.php';if(is_file($path))require_once $path;});
function db():PDO{return $GLOBALS['roomFixture'];}
function locale():string{return 'fa';}
function storage_path($path=''){return sys_get_temp_dir().'/'.$GLOBALS['fixture'].'/'.$path;}
function app():object{return new class{function getLocale(){return 'fa';}};}
function auth():object{return new class{function id(){return null;}function user(){return null;}};}
function session():object{return new class{function get($key,$default=null){return $key==='suppress_database_notifications'?true:$default;}};}
function transaction(callable $fn):mixed{db()->beginTransaction();try{$r=$fn();db()->commit();return $r;}catch(Throwable $e){db()->rollBack();throw $e;}}
function roomCheck($ok,$message){if(!$ok)throw new LogicException($message);++$GLOBALS['checks'];}
function applyRoomMigration(PDO $pdo,string $sql):array{ $result=[];foreach(explode(';',$sql)as $statement){if(trim($statement)==='')continue;$cursor=$pdo->query($statement);if($cursor->columnCount())$result=$cursor->fetchAll(PDO::FETCH_ASSOC);$cursor->closeCursor();}return $result[0]??[]; }
function roomDenied(callable $fn){try{$fn();}catch(RuntimeException $e){roomCheck($e->getCode()===403,'Expected scope denial');return;}throw new LogicException('Unauthorized room operation succeeded');}
$checks=0;$fixture='sornaz_room_fixture_'.bin2hex(random_bytes(6));$created=false;
try{
 $dsn=getenv('SORNAZ_TEST_MYSQL_DSN')?:'mysql:host=127.0.0.1;port=3306;charset=utf8mb4';$user=getenv('SORNAZ_TEST_MYSQL_USER')?:'root';$password=getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'';
 $opts=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC];
 $server=new PDO($dsn,$user,$password,$opts);$server->exec('CREATE DATABASE '.$fixture.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');$created=true;
 $pdo=new Core\database\PrefixedPDO($dsn,$user,$password,$opts);$pdo->exec('USE '.$fixture);$GLOBALS['roomFixture']=$pdo;
 $sql=preg_replace('/^--.*$/m','',file_get_contents(getcwd().'/docs/database/migrations/2026_10_13_classroom_organizations.sql'));
 $result=applyRoomMigration($pdo,$sql);
 roomCheck($result['migration_status']==='NOT_APPLIED'&&str_contains($result['migration_error'],'not found'),'Missing tables produce a diagnostic without a NULL statement');
 if($server->query('SELECT DATABASE()')->fetchColumn()===null){$result=applyRoomMigration($server,$sql);roomCheck($result['migration_status']==='NOT_APPLIED'&&str_contains($result['migration_error'],'Select'),'No selected database produces a diagnostic');}
 $legacyPdo=new PDO($dsn,$user,$password,$opts);$legacyPdo->exec('USE '.$fixture);
 $legacyPdo->exec('CREATE TABLE academy_branch_classrooms(classroom_id INT PRIMARY KEY,branch_id BIGINT UNSIGNED NOT NULL,deleted_at DATETIME NULL) ENGINE=InnoDB');
 $result=applyRoomMigration($legacyPdo,$sql);
 roomCheck($result['migration_status']==='NOT_APPLIED'&&str_contains($result['migration_error'],'academy'),'Missing academy metadata cannot create a NULL prepared statement');
 $legacyPdo->exec('CREATE TABLE academies(academy_id INT UNSIGNED PRIMARY KEY) ENGINE=InnoDB');
 $legacyPdo->exec('INSERT INTO academy_branch_classrooms VALUES(1,101,NULL)');
 $result=applyRoomMigration($legacyPdo,$sql);
 roomCheck($result['migration_status']==='APPLIED'&&$result['classroom_table']==='academy_branch_classrooms','Unprefixed schema is supported');
 roomCheck((int)$legacyPdo->query('SELECT branch_id FROM academy_branch_classrooms WHERE classroom_id=1')->fetchColumn()===101,'Unprefixed branch rows are preserved');
 roomCheck(applyRoomMigration($legacyPdo,$sql)['migration_status']==='ALREADY_APPLIED','Unprefixed migration is repeatable');
 $legacyPdo->exec('ALTER TABLE academy_branch_classrooms MODIFY branch_id BIGINT UNSIGNED NOT NULL');
 roomCheck(applyRoomMigration($legacyPdo,$sql)['migration_status']==='NOT_APPLIED','Partial ownership schema is not reported as applied');
 $legacyPdo->exec('DROP TABLE academy_branch_classrooms');$legacyPdo->exec('DROP TABLE academies');
 $tables=[
 'users'=>'user_id INT PRIMARY KEY,type VARCHAR(30),username VARCHAR(50),deleted_at DATETIME',
 'academies'=>'academy_id INT UNSIGNED PRIMARY KEY,user_id INT,created_by INT,deleted_at DATETIME',
 'academy_branches'=>'branch_id BIGINT UNSIGNED PRIMARY KEY,academy_id INT,user_id INT,deleted_at DATETIME',
 'academy_branch_members'=>'member_id INT PRIMARY KEY,academy_id INT,branch_id BIGINT,user_id INT,status VARCHAR(30),deleted_at DATETIME',
 'academy_branch_member_roles'=>'member_role_id INT PRIMARY KEY,member_id INT,role_id INT,deleted_at DATETIME',
 'access_system_roles'=>'role_id INT PRIMARY KEY,name VARCHAR(50),deleted_at DATETIME',
 'classroom_types'=>'classroom_type_id INT PRIMARY KEY,academy_id INT,branch_id BIGINT,created_by INT,type VARCHAR(50),status VARCHAR(30),approved_at DATETIME,deleted_at DATETIME',
 'academy_branch_classrooms'=>'classroom_id INT AUTO_INCREMENT PRIMARY KEY,branch_id BIGINT UNSIGNED NOT NULL,type_id INT,capacity INT,is_active TINYINT,status VARCHAR(30),created_by INT,updated_by INT,approved_at DATETIME,approved_by INT,deleted_at DATETIME,deleted_by INT,updated_at DATETIME,CONSTRAINT fk_fixture_room_branch FOREIGN KEY(branch_id) REFERENCES p_academy_branches(branch_id)',
 'academy_branch_classroom_assets'=>'classroom_asset_id INT AUTO_INCREMENT PRIMARY KEY,classroom_id INT,quantity INT,created_by INT,updated_by INT,deleted_at DATETIME,deleted_by INT',
 'academy_branch_course_term_sessions'=>'term_session_id INT PRIMARY KEY,classroom_id INT,deleted_at DATETIME',
 'academy_branch_courses'=>'course_id INT PRIMARY KEY,academy_id INT,branch_id BIGINT,lesson_id INT,teacher_capacity INT,student_capacity INT,deleted_at DATETIME',
 'academy_branch_course_terms'=>'term_id INT AUTO_INCREMENT PRIMARY KEY,course_id INT,start_date DATE,end_date DATE,session_count INT,session_period VARCHAR(30),price DECIMAL(12,2),currency_id INT,status VARCHAR(30),created_by INT,updated_by INT,deleted_at DATETIME',
 'academy_branch_course_term_enrollments'=>'enrollment_id INT AUTO_INCREMENT PRIMARY KEY,term_id INT,member_id INT,type VARCHAR(30),status VARCHAR(30),joined_at DATETIME,created_by INT,updated_by INT,deleted_at DATETIME',
 'academy_branch_member_contracts'=>'contract_id INT PRIMARY KEY,member_id INT,type VARCHAR(30),user_lesson_id INT,deleted_at DATETIME',
 'user_lessons'=>'user_lesson_id INT PRIMARY KEY,lesson_id INT,deleted_at DATETIME',
 'user_availabilities'=>'user_availability_id INT PRIMARY KEY,user_id INT,date DATE,day_of_week VARCHAR(20),start_time TIME,end_time TIME,status VARCHAR(30),is_closed TINYINT,is_repeating TINYINT,unavailable_type VARCHAR(30),priority INT,timezone_id INT,deleted_at DATETIME',
 'f_timezone'=>'timezone_id INT PRIMARY KEY,timezone VARCHAR(80)',
 'translations'=>'translation_id INT AUTO_INCREMENT PRIMARY KEY,table_name VARCHAR(80),table_id INT,locale VARCHAR(10),field VARCHAR(40),value TEXT,version INT,created_at DATETIME,updated_at DATETIME,created_by INT,updated_by INT,deleted_at DATETIME,deleted_by INT,approved_at DATETIME,approved_by INT'
 ];foreach($tables as $table=>$columns)$pdo->exec('CREATE TABLE '.$table.' ('.$columns.') ENGINE=InnoDB');
 $pdo->exec("INSERT INTO users VALUES(1,'admin','admin',NULL),(10,'academy','academy',NULL),(20,'academy','other',NULL),(11,'branch','branch',NULL);INSERT INTO academies VALUES(100,10,10,NULL),(200,20,20,NULL);INSERT INTO academy_branches VALUES(101,100,11,NULL),(201,200,21,NULL);INSERT INTO classroom_types VALUES(1,NULL,NULL,1,'music','approved',NOW(),NULL);INSERT INTO academy_branch_classrooms(classroom_id,branch_id,type_id,capacity,status) VALUES(1,101,1,5,'available');");
 roomCheck(!Modules\Academy\Services\ClassroomOrganizationScope::supportsAcademies(),'Legacy schema detected');
 roomCheck(count(Modules\Academy\Services\ClassroomOrganizationScope::rooms([101],[100]))===1,'Legacy room preserved');
 $legacyPdo->exec('CREATE TABLE academy_branch_classrooms(branch_id BIGINT UNSIGNED NOT NULL,deleted_at DATETIME) ENGINE=InnoDB');
 $result=applyRoomMigration($pdo,$sql);
 roomCheck($result['migration_status']==='NOT_APPLIED'&&str_contains($result['migration_error'],'Both'),'Ambiguous table names are refused');
 $legacyPdo->exec('DROP TABLE academy_branch_classrooms');
 roomCheck(applyRoomMigration($pdo,$sql)['migration_status']==='APPLIED','Prefixed migration is applied');
 roomCheck(applyRoomMigration($pdo,$sql)['migration_status']==='ALREADY_APPLIED','Prefixed migration is repeatable');
 $pdo=new Core\database\PrefixedPDO($dsn,$user,$password,$opts);$pdo->exec('USE '.$fixture);$GLOBALS['roomFixture']=$pdo;
 roomCheck(Modules\Academy\Services\ClassroomOrganizationScope::supportsAcademies(),'Migrated schema detected');
 $rollback=file_get_contents(getcwd().'/docs/database/migrations/2026_10_13_classroom_organizations_rollback.sql');
 preg_match('/CREATE PROCEDURE[\s\S]*?END\$\$/',$rollback,$procedure);
 $pdo->exec(substr($procedure[0],0,-2));
 $pdo->query('CALL rollback_classroom_organizations()')->closeCursor();
 roomCheck((int)$pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='p_academy_branch_classrooms' AND COLUMN_NAME='academy_id'")->fetchColumn()===0,'Empty additive schema rollback works');
 foreach(explode(';',$sql)as $statement)if(trim($statement)!=='')$pdo->query($statement)->closeCursor();
 $service=new Modules\Academy\Services\AcademyClassroomService(new Modules\System\Services\AccessControl());
 $data=['organizationUserId'=>10,'typeId'=>1,'name'=>'Synthetic academy room','capacity'=>5,'equipment'=>[]];
 $room=$service->saveRoom(10,$data,false);roomCheck($room['organizationKind']==='academy'&&$room['academyId']===100&&$room['branchId']===0,'Academy room created');
 roomCheck($room['organizationUserId']===10,'Organization user returned');
 $legacy=$data;unset($legacy['organizationUserId']);$legacy['branchId']=101;
 try{$service->saveRoom(10,$legacy,false,$room['id']);throw new LogicException('Legacy form moved academy room');}catch(RuntimeException $e){roomCheck($e->getCode()===409,'Cached legacy form cannot move academy room');}
 roomCheck(Modules\Academy\Services\ClassroomOrganizationScope::find($room['id'],0,100)!==null,'Legacy rejection preserves academy ownership');
 try{$pdo->query('CALL rollback_classroom_organizations()')->closeCursor();throw new LogicException('Rollback discarded academy ownership');}catch(PDOException $e){roomCheck($e->getCode()==='45000','Rollback refuses existing academy rooms');}
 $pdo->exec('DROP PROCEDURE rollback_classroom_organizations');
 roomCheck($service->saveRoom(10,array_replace($data,['name'=>'Edited room']),false,$room['id'])['name']==='Edited room','Room edited');
 roomDenied(fn()=>$service->saveRoom(20,$data,false));roomDenied(fn()=>$service->saveRoom(11,$data,false));roomDenied(fn()=>$service->saveRoom(20,array_replace($data,['organizationUserId'=>20]),false,$room['id']));
 roomCheck(Modules\Academy\Services\ClassroomOrganizationScope::find($room['id'],0,200)===null,'Foreign room excluded from scheduling');
 roomCheck(count(Modules\Academy\Services\ClassroomOrganizationScope::rooms([101]))===1,'Branch scope excludes academy rooms');
 roomCheck($service->saveRoom(11,array_replace($data,['organizationUserId'=>11]),false)['branchId']===101,'Branch contract preserved');
 $pdo->exec("INSERT INTO academy_branch_members VALUES(501,100,NULL,51,'active',NULL),(502,200,NULL,52,'active',NULL);INSERT INTO academy_branch_member_contracts VALUES(1,501,'teacher',1,NULL),(2,502,'teacher',1,NULL);INSERT INTO user_lessons VALUES(1,7,NULL);INSERT INTO academy_branch_courses VALUES(1,100,NULL,7,1,5,NULL);INSERT INTO f_timezone VALUES(1,'Asia/Tehran');INSERT INTO user_availabilities VALUES(1,10,NULL,'monday','09:00','13:00','available',0,1,NULL,0,1,NULL);");
 $terms=new Modules\Academy\Services\AcademyTermService();
 $termData=['name'=>'Synthetic academy term','courseId'=>1,'branchId'=>0,'classroomId'=>$room['id'],'teachers'=>[['id'=>501]],'students'=>[],'sessionCount'=>2,'repeatType'=>'week','status'=>'open','undatedTemplate'=>true,'recurrence'=>['weekday'=>'1'],'currencyId'=>1,'cost'=>100,'installmentCount'=>1,'sessions'=>[['startTime'=>'10:00','endTime'=>'11:00','timezoneId'=>1]]];
 $term=$terms->saveAcademyTerm(10,$termData);
 roomCheck($term['id']>0,'Academy-owned room supports a real undated term');
 roomDenied(fn()=>$terms->saveAcademyTerm(20,$termData));
 roomDenied(fn()=>$terms->saveAcademyTerm(11,$termData));
 try{$terms->saveAcademyTerm(10,array_replace($termData,['teachers'=>[['id'=>502]]]));throw new LogicException('Foreign teacher accepted');}catch(RuntimeException $e){roomCheck(true,'Foreign academy teacher excluded');}
 roomCheck((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_terms')->fetchColumn()===1,'Denied schedules leave no term rows');
 $pdo->exec('INSERT INTO academy_branch_course_term_sessions VALUES(1,'.$room['id'].',NULL)');
 try{$service->saveRoom(10,array_replace($data,['organizationUserId'=>11]),false,$room['id']);throw new LogicException('Recorded room moved');}catch(RuntimeException $e){roomCheck(str_contains($e->getMessage(),'جلسه'),'Recorded room move denied');}
 roomCheck($service->cycleRoomStatus(10,$room['id'],false)['organizationKind']==='academy','Status update works');
 $service->deleteRoom(10,$room['id'],false);roomCheck(Modules\Academy\Services\ClassroomOrganizationScope::find($room['id'],0,100)===null,'Deleted room excluded');
 echo "Classroom organizations: $checks MySQL migration, CRUD and scope checks passed (".$pdo->getAttribute(PDO::ATTR_SERVER_VERSION).").\n";
}catch(Throwable $e){fwrite(fopen('php://stderr','wb'),'Room fixture failed: '.$e->getMessage()."\n");$failed=true;}
finally{if($created)$server->exec('DROP DATABASE '.$fixture);foreach(glob(sys_get_temp_dir().'/'.$fixture.'/payment-locks/*.lock')?:[] as $lock)unlink($lock);if(is_dir(sys_get_temp_dir().'/'.$fixture.'/payment-locks'))rmdir(sys_get_temp_dir().'/'.$fixture.'/payment-locks');if(is_dir(sys_get_temp_dir().'/'.$fixture))rmdir(sys_get_temp_dir().'/'.$fixture);}
exit(!empty($failed)?1:0);
`;
const result = spawnSync('php', [], { input: source, encoding: 'utf8', cwd: require('node:path').resolve(__dirname, '..') });
process.stdout.write(result.stdout || '');
process.stderr.write(result.stderr || '');
if (result.error) console.error(result.error.message);
process.exitCode = result.status ?? 1;
