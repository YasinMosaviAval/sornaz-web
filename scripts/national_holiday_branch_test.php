<?php
$root = dirname(__DIR__);
$map = require $root . '/vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map, $root) {
    if (isset($map[$class])) require_once $map[$class];
    elseif (str_starts_with($class, 'Modules\\')) require_once $root . '/' . str_replace('\\', '/', $class) . '.php';
});
$pdo = new Core\database\PrefixedPDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
function db() { return $GLOBALS['pdo']; }
function locale() { return 'fa'; }
function session() { return new class { function get($key, $default = null) { return $key === 'suppress_database_notifications' ? true : $default; } }; }
function check($condition, $message) { if (!$condition) throw new LogicException($message); }
$pdo->exec('CREATE TABLE users(user_id INTEGER PRIMARY KEY,type TEXT,deleted_at TEXT);
CREATE TABLE academies(academy_id INTEGER PRIMARY KEY,user_id INTEGER,created_by INTEGER,deleted_at TEXT);
CREATE TABLE academy_branches(branch_id INTEGER PRIMARY KEY,academy_id INTEGER,user_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_members(member_id INTEGER PRIMARY KEY,user_id INTEGER,academy_id INTEGER,branch_id INTEGER,status TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_member_roles(member_id INTEGER,role_id INTEGER,deleted_at TEXT);
CREATE TABLE access_system_roles(role_id INTEGER,name TEXT,deleted_at TEXT);
CREATE TABLE academy_national_holiday_settings(academy_id INTEGER PRIMARY KEY,allow_classes_on_national_holidays INTEGER,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER);
CREATE TABLE z_user_settings(user_setting_id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER,`key` TEXT,value TEXT,type TEXT,visibility TEXT,created_by INTEGER,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER);
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,deleted_at TEXT);
CREATE TABLE user_availabilities(user_availability_id INTEGER PRIMARY KEY,user_id INTEGER,member_id INTEGER,date TEXT,start_time TEXT,end_time TEXT,timezone_id INTEGER,status TEXT,unavailable_type TEXT,deleted_at TEXT);
CREATE TABLE f_timezone(timezone_id INTEGER,timezone TEXT,deleted_at TEXT);');
$pdo->exec("INSERT INTO users VALUES(10,'academy',NULL),(11,'branch',NULL),(12,'branch',NULL),(20,'academy',NULL);
INSERT INTO academies VALUES(100,10,10,NULL),(200,20,20,NULL);
INSERT INTO academy_branches VALUES(101,100,11,NULL),(102,100,12,NULL),(201,200,21,NULL);
INSERT INTO academy_national_holiday_settings(academy_id,allow_classes_on_national_holidays) VALUES(100,1),(200,0);
INSERT INTO translations VALUES(1,'academies',100,'title','fa','آموزشگاه نمونه',NULL),(2,'academy_branches',101,'name','fa','شعبه مرکزی',NULL);");
$service = new Modules\Academy\Services\NationalHolidayService();
check(Modules\Academy\Services\NationalHolidayPolicy::allows(100, 101), 'Branch must inherit academy default');
$service->saveAcademySetting(10, 100, false, 101);
check(!Modules\Academy\Services\NationalHolidayPolicy::allows(100, 101), 'Branch override ignored');
check(Modules\Academy\Services\NationalHolidayPolicy::allows(100, 102), 'Sibling branch was changed');
$service->saveAcademySetting(11, 100, true, 101);
$service->saveAcademySetting(11, 100, true, 101);
check((int) $pdo->query('SELECT COUNT(*) FROM z_user_settings')->fetchColumn() === 1, 'Repeated save duplicated the branch setting');
try { $service->saveAcademySetting(11, 100, false, 102); throw new LogicException('Sibling write accepted'); } catch (RuntimeException $error) { check($error->getCode() === 403, 'Wrong denied branch error'); }
try { $service->saveOrganizationSettings(10, [['academyId'=>100,'branchId'=>101,'allowClasses'=>false], ['academyId'=>200,'branchId'=>201,'allowClasses'=>true]]); throw new LogicException('Foreign bulk write accepted'); } catch (RuntimeException $error) { check($error->getCode() === 403, 'Wrong foreign organization error'); }
check(Modules\Academy\Services\NationalHolidayPolicy::allows(100, 101), 'Failed bulk write was not rolled back');
$pdo->exec("INSERT INTO users VALUES(30,'person',NULL); INSERT INTO access_system_roles VALUES(1,'academy_branch_manager',NULL); INSERT INTO academy_branch_members VALUES(301,30,100,101,'active',NULL); INSERT INTO academy_branch_member_roles VALUES(301,1,NULL);");
check(count($service->index(30)['organizations']) === 1, 'Branch manager gained academy-wide settings');
try { $service->saveAcademySetting(30, 100, false, 102); throw new LogicException('Branch manager changed sibling settings'); } catch (RuntimeException $error) { check($error->getCode() === 403, 'Wrong branch manager denial'); }
$organizations = $service->index(10)['organizations'];
check(count($organizations) === 3 && $organizations[1]['name'] === 'شعبه مرکزی', 'Academy/branches or translated branch name missing');
check(count($service->index(11)['organizations']) === 1, 'Branch actor sees sibling settings');
$pdo->exec("INSERT INTO user_availabilities VALUES(1,1000,301,'2026-11-01','09:00','10:00',1,'available','vacation',NULL); INSERT INTO f_timezone VALUES(1,'Asia/Tehran',NULL);");
$leaves = new class extends Modules\Academy\Services\AcademyBranchService {
    public function memberSchedules(int $actor, bool $siteAdmin = false): array { return ['members'=>[['id'=>301,'user_id'=>1000,'organizationUserId'=>11,'branchId'=>101,'branch'=>'نام جایگزین','name'=>'کارمند']], 'staff_catalog'=>['organizations'=>[['user_id'=>11,'branch_id'=>101,'name'=>'شعبه مرکزی']]]]; }
};
check($leaves->availabilityExceptions(10)['exceptions'][0]['branchName'] === 'شعبه مرکزی', 'Leave organization label lost');
echo "National holidays: inherited policy, branch override, ownership, atomic rollback and leave organization passed.\n";
