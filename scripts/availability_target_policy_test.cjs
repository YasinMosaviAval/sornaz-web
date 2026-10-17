const {spawnSync} = require('node:child_process');
// PHP fixtures are passed through stdin; no application database or generated PHP file.
const source = String.raw`<?php
$map = require 'vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require_once $map[$class]; });
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
$mysql = getenv('SCHEDULING_MYSQL') === '1';
if ($mysql) {
 $dsn = getenv('SORNAZ_TEST_MYSQL_DSN') ?: 'mysql:host=127.0.0.1;port=3306;charset=utf8mb4';
 $user = getenv('SORNAZ_TEST_MYSQL_USER') ?: 'root'; $password = getenv('SORNAZ_TEST_MYSQL_PASSWORD') ?: '';
 $server = new PDO($dsn, $user, $password, $options);
 $fixture = 'sornaz_schedule_fixture_' . bin2hex(random_bytes(6));
 $server->exec('CREATE DATABASE ' . $fixture . ' CHARACTER SET utf8mb4');
 register_shutdown_function(static function () use ($server, $fixture) { $server->exec('DROP DATABASE ' . $fixture); });
 $pdo = new Core\database\PrefixedPDO($dsn, $user, $password, $options); $pdo->exec('USE ' . $fixture);
} else $pdo = new Core\database\PrefixedPDO('sqlite::memory:', null, null, $options);
function db() { return $GLOBALS['pdo']; }
function locale() { return 'fa'; }
function app() { return new class { function getLocale() { return locale(); } }; }
function auth() { return new class { function id() { return 10; } function user() { return ['user_id'=>10,'type'=>'academy']; } }; }
function session() { return new class { function get($key, $default = null) { return $key === 'suppress_database_notifications' ? true : $default; } }; }
function transaction($callback) { db()->beginTransaction(); try { $result = $callback(); db()->commit(); return $result; } catch (Throwable $e) { db()->rollBack(); throw $e; } }
$schema = 'CREATE TABLE users(user_id INTEGER PRIMARY KEY,type TEXT,deleted_at TEXT);
 INSERT INTO users VALUES(10,"academy",NULL);
 CREATE TABLE f_timezone(timezone_id INTEGER PRIMARY KEY,timezone TEXT,deleted_at TEXT);
 INSERT INTO f_timezone VALUES(1,"Asia/Tehran",NULL);
 CREATE TABLE translations(translation_id INTEGER PRIMARY KEY AUTOINCREMENT,table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,version INTEGER,created_at TEXT,updated_at TEXT,created_by INTEGER,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER);
 CREATE TABLE user_availabilities(user_availability_id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER,member_id INTEGER,date TEXT,day_of_week INTEGER,start_time TEXT,end_time TEXT,timezone_id INTEGER,status TEXT,unavailable_type TEXT,is_repeating INTEGER,repeat_period TEXT,is_closed INTEGER,priority INTEGER,approved_at TEXT,approved_by INTEGER,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER);';
foreach (explode(';', $schema) as $statement) if (trim($statement) !== '') $pdo->exec($mysql ? str_replace('INTEGER PRIMARY KEY AUTOINCREMENT', 'INTEGER AUTO_INCREMENT PRIMARY KEY', $statement) : $statement);
$service = new class extends Modules\Academy\Services\AcademyBranchService {
 public function availabilityExceptions(int $actor, bool $siteAdmin = false): array {
  $rows = db()->query('SELECT user_availability_id AS id,member_id AS membershipId FROM user_availabilities WHERE user_id IN (10,1000) AND deleted_at IS NULL')->fetchAll();
  foreach ($rows as &$row) $row['readOnly'] = false;
  unset($row);
  return ['members'=>[['id'=>100,'user_id'=>1000,'organizationUserId'=>10]],'staff_catalog'=>['organizations'=>[['user_id'=>10]]],'exceptions'=>$actor === 10 ? $rows : []];
 }
};
$base = ['organizationUserId'=>10,'date'=>'2026-11-01','timezone'=>'Asia/Tehran','allDay'=>true];
$holiday = $service->saveAvailabilityException(10, $base + ['targetType'=>'organization','type'=>'closed']);
$leave = $service->saveAvailabilityException(10, $base + ['targetType'=>'member','membershipId'=>100,'type'=>'vacation']);
$rows = $pdo->query('SELECT * FROM user_availabilities ORDER BY user_availability_id')->fetchAll();
if (count($rows)!==2 || $rows[0]['member_id']!==null || (int)$rows[0]['user_id']!==10 || (int)$rows[1]['member_id']!==100) throw new LogicException('Incorrect target persisted');
foreach ([['organization','vacation',0,10],['member','holiday',100,10],['member','closed',100,10],['member','vacation',100,20],['member','vacation',200,10],['organization','closed',0,20]] as [$target,$type,$member,$organization]) {
 try { $service->saveAvailabilityException(10, array_replace($base,['targetType'=>$target,'type'=>$type,'membershipId'=>$member,'organizationUserId'=>$organization])); throw new LogicException('Invalid target or type accepted'); }
 catch (RuntimeException $e) { /* expected rejection */ }
}
foreach (['organization'=>$holiday['ids'][0], 'member'=>$leave['ids'][0]] as $target => $id) {
 $scoped = $service->targetAvailabilityExceptions(10,$target);
 if (array_map('intval',array_column($scoped['exceptions'],'id')) !== [(int)$id]) throw new LogicException('Mixed targets leaked');
 $service->targetAvailabilityExceptions(10,$target,(int)$id);
 foreach ([['organization' === $target ? 'member' : 'organization',10],[$target,20]] as [$badTarget,$actor]) {
  try { $service->targetAvailabilityExceptions($actor,$badTarget,(int)$id); throw new LogicException('Foreign target mutation accepted'); }
  catch (RuntimeException $e) { if ($e->getCode() !== 403) throw $e; }
 }
}
if ((int)$pdo->query('SELECT COUNT(*) FROM user_availabilities')->fetchColumn()!==2) throw new LogicException('Rejected request wrote data');
$controller = new Modules\Academy\Controllers\Web\AcademyBranchController($service);
$request = static function ($target,$id,$operation,$data) use ($controller) {
 $_POST = ['payload_b64'=>base64_encode(json_encode($data))];
 $response = $controller->mutateTargetAvailabilityException($target,$id,$operation);
 return (new ReflectionProperty($response,'data'))->getValue($response);
};
foreach (['organization'=>['targetType'=>'member','membershipId'=>100,'type'=>'closed'], 'member'=>['targetType'=>'organization','membershipId'=>100,'type'=>'vacation']] as $target=>$values) {
 $result = $request($target,0,'create',$base+$values);
 if (!$result['success']) throw new LogicException('Scoped controller write failed');
 $id = (int)$result['data']['ids'][0];
 $row = $pdo->query('SELECT * FROM user_availabilities WHERE user_availability_id='.$id)->fetch();
 if (($target==='organization') !== ($row['member_id']===null)) throw new LogicException('Request overrode fixed route target');
}
foreach ([['organization',(int)$leave['ids'][0],'update'],['member',(int)$holiday['ids'][0],'delete'],['organization',(int)$holiday['ids'][0],'create'],['wrong',0,'create']] as [$target,$id,$operation]) {
 if ($request($target,$id,$operation,$base+['type'=>'closed'])['success']) throw new LogicException('Invalid scoped controller action succeeded');
}
if ((int)$pdo->query('SELECT COUNT(*) FROM user_availabilities WHERE deleted_at IS NULL')->fetchColumn()!==4) throw new LogicException('Denied controller action changed rows');
echo 'Availability target policy: separate lists, target mutation guard, real writes, type and membership limits passed (' . ($mysql ? 'MySQL' : 'SQLite') . " with controlled catalog).\n";
`;
const result = spawnSync('php', [], {input:source, encoding:'utf8'});
process.stdout.write(result.stdout || '');
process.stderr.write(result.stderr || '');
process.exitCode = result.status ?? 1;
