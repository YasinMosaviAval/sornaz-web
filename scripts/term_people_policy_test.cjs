const {spawnSync}=require('node:child_process');
const source=String.raw`<?php
require_once 'Modules/Academy/Services/StaffTeachingLessons.php';
$map=require 'vendor/composer/autoload_classmap.php';spl_autoload_register(static function($class)use($map){if(isset($map[$class]))require_once $map[$class];});
function db(){return $GLOBALS['pdo'];}
function app(){return new class{function getLocale(){return 'fa';}};}
$mysql=getenv('TERM_PEOPLE_MYSQL')==='1';$options=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC];
if($mysql){$dsn=getenv('SORNAZ_TEST_MYSQL_DSN')?:'mysql:host=127.0.0.1;port=3306;charset=utf8mb4';$user=getenv('SORNAZ_TEST_MYSQL_USER')?:'root';$password=getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'';$server=new PDO($dsn,$user,$password,$options);$name='sornaz_term_people_fixture_'.bin2hex(random_bytes(6));$server->exec('CREATE DATABASE '.$name);register_shutdown_function(static function()use($server,$name){$server->exec('DROP DATABASE '.$name);});$pdo=new Core\database\PrefixedPDO($dsn,$user,$password,$options);$pdo->exec('USE '.$name);}else $pdo=new Core\database\PrefixedPDO('sqlite::memory:',null,null,$options);
$pdo->exec('CREATE TABLE academy_branch_members(member_id INTEGER PRIMARY KEY,branch_id INTEGER,academy_id INTEGER,deleted_at TEXT)');
$pdo->exec('CREATE TABLE academy_branch_member_contracts(member_id INTEGER,type VARCHAR(20),user_lesson_id INTEGER,deleted_at TEXT)');
$pdo->exec('CREATE TABLE user_lessons(user_lesson_id INTEGER PRIMARY KEY,lesson_id INTEGER,deleted_at TEXT)');
$pdo->exec('INSERT INTO academy_branch_members VALUES(1,101,100,NULL),(2,NULL,100,NULL),(3,102,100,NULL),(4,NULL,200,NULL),(5,101,100,NULL),(6,101,100,"deleted")');
$pdo->exec("INSERT INTO academy_branch_member_contracts VALUES(1,'teacher',1,NULL),(2,'teacher',1,NULL),(3,'teacher',1,NULL),(4,'teacher',1,NULL),(5,'teacher',2,NULL),(6,'teacher',1,NULL)");
$pdo->exec('INSERT INTO user_lessons VALUES(1,5,NULL),(2,6,NULL)');
$service=new Modules\Academy\Services\AcademyTermService();$validate=new ReflectionMethod($service,'validatePeople');
$check=static function($ids,$branch,$academy)use($validate,$service){return $validate->invoke($service,array_map(static fn($id)=>['id'=>$id],$ids),'teacher',$branch,5,2,$academy);};
if($check([1],101,0)!==[1]||$check([2],0,100)!==[2]||$check([1,2],101,100)!==[1,2])throw new LogicException('Eligible course teachers rejected');
foreach([[[2],101,0],[[3],101,100],[[4],101,100],[[1],0,100],[[5],101,0],[[6],101,0]]as[$ids,$branch,$academy]){try{$check($ids,$branch,$academy);throw new LogicException('Ineligible teacher accepted');}catch(RuntimeException $error){}}
try{$validate->invoke($service,[['id'=>2]],'student',101,0,2,100);throw new LogicException('Student branch scope changed');}catch(RuntimeException $error){}
echo 'Term people policy: branch ownership, academy teachers at branch venues, other-branch/academy isolation, lesson eligibility and deleted members passed ('.($mysql?'mysql':'sqlite').").\n";
`;
const result=spawnSync('php',[],{input:source,encoding:'utf8'});
process.stdout.write(result.stdout||'');process.stderr.write(result.stderr||'');process.exitCode=result.status??1;
