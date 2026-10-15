const { spawnSync } = require('node:child_process');
const source = String.raw`<?php
spl_autoload_register(function (string $class): void {
    $path = getcwd() . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($path)) require $path;
    elseif (str_starts_with($class, 'Core\\')) {
        $path = getcwd() . '/core/' . str_replace('\\', '/', substr($class, 5)) . '.php';
        if (is_file($path)) require $path;
    }
});

$mysql = getenv('USER_ACCESS_MYSQL') === '1';
if ($mysql) {
    $testDsn = getenv('SORNAZ_TEST_MYSQL_DSN') ?: 'mysql:host=127.0.0.1;port=3306;charset=utf8mb4';
    $testUser = getenv('SORNAZ_TEST_MYSQL_USER') ?: 'root';
    $testPassword = getenv('SORNAZ_TEST_MYSQL_PASSWORD') ?: '';
    $pdo = new PDO($testDsn, $testUser, $testPassword, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $testDatabase = 'sornaz_access_fixture_' . bin2hex(random_bytes(6));
    $pdo->exec('CREATE DATABASE ' . $testDatabase . ' CHARACTER SET utf8mb4');
    register_shutdown_function(static function () use ($pdo, $testDatabase): void {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $pdo->exec('DROP DATABASE ' . $testDatabase);
    });
    $pdo->exec('USE ' . $testDatabase);
} else {
    $pdo = new PDO('sqlite::memory:');
}
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
function fixtureExec(string $sql): void {
    if ($GLOBALS['mysql']) $sql = str_replace('INTEGER PRIMARY KEY', 'INTEGER AUTO_INCREMENT PRIMARY KEY', $sql);
    db()->exec($sql);
}
function db(): PDO { return $GLOBALS['userAccessTestPdo']; }
function locale(): string { return $GLOBALS['accessTestLocale'] ?? 'fa'; }
function session(): object { return new class { public function get(string $key, mixed $default = null): mixed { return $key === 'suppress_database_notifications' ? true : $default; } }; }
function transaction(callable $callback): mixed {
    db()->beginTransaction();
    try { $result = $callback(); db()->commit(); return $result; }
    catch (Throwable $error) { db()->rollBack(); throw $error; }
}
$GLOBALS['userAccessTestPdo'] = $pdo;
fixtureExec("CREATE TABLE users (user_id INTEGER PRIMARY KEY, type TEXT, username TEXT, email TEXT, phone TEXT, status TEXT, last_login_at TEXT, deleted_at TEXT);
CREATE TABLE academies (academy_id INTEGER PRIMARY KEY, user_id INTEGER, created_by INTEGER, deleted_at TEXT);
CREATE TABLE academy_branches (branch_id INTEGER PRIMARY KEY, academy_id INTEGER, user_id INTEGER, deleted_at TEXT);
CREATE TABLE academy_branch_members (member_id INTEGER PRIMARY KEY, academy_id INTEGER, branch_id INTEGER, user_id INTEGER, deleted_at TEXT);
CREATE TABLE academy_branch_member_roles (member_role_id INTEGER PRIMARY KEY, member_id INTEGER, role_id INTEGER, is_main INTEGER, deleted_at TEXT, deleted_by INTEGER, updated_at TEXT, updated_by INTEGER, approved_at TEXT, approved_by INTEGER, created_at TEXT, created_by INTEGER);
CREATE TABLE academy_branch_member_permissions (academy_branch_member_permission_id INTEGER PRIMARY KEY, member_id INTEGER, permission_id INTEGER, deleted_at TEXT, deleted_by INTEGER, updated_at TEXT, updated_by INTEGER, approved_at TEXT, approved_by INTEGER, created_at TEXT, created_by INTEGER);
CREATE TABLE access_system_roles (role_id INTEGER PRIMARY KEY, name TEXT, scope TEXT, sort_order INTEGER, color TEXT, is_panel_visible INTEGER, deleted_at TEXT);
CREATE TABLE access_system_permissions (permission_id INTEGER PRIMARY KEY, name TEXT, scope TEXT, group_name TEXT, is_panel_visible INTEGER, deleted_at TEXT);
CREATE TABLE access_system_role_permissions (role_permission_id INTEGER PRIMARY KEY, role_id INTEGER, permission_id INTEGER, deleted_at TEXT);
CREATE TABLE translations (translation_id INTEGER PRIMARY KEY, table_name TEXT, table_id INTEGER, locale TEXT, field TEXT, value TEXT, deleted_at TEXT, deleted_by INTEGER);
CREATE TABLE f_translations (translation_id INTEGER PRIMARY KEY, table_name TEXT, table_id INTEGER, locale TEXT, field TEXT, value TEXT, deleted_at TEXT, deleted_by INTEGER);
CREATE TABLE user_roles (user_role_id INTEGER PRIMARY KEY, user_id INTEGER, role_id INTEGER, is_main INTEGER, deleted_at TEXT, deleted_by INTEGER, updated_at TEXT, updated_by INTEGER, approved_at TEXT, approved_by INTEGER, created_at TEXT, created_by INTEGER);
CREATE TABLE user_permissions (user_permission_id INTEGER PRIMARY KEY, user_id INTEGER, permission_id INTEGER, deleted_at TEXT, deleted_by INTEGER, updated_at TEXT, updated_by INTEGER, approved_at TEXT, approved_by INTEGER, created_at TEXT, created_by INTEGER);
INSERT INTO users VALUES (1,'admin','admin',NULL,NULL,'approved',NULL,NULL),(2,'human','member',NULL,NULL,'approved',NULL,NULL),(3,'human','academy_manager',NULL,NULL,'approved',NULL,NULL);
INSERT INTO academies VALUES (100,NULL,3,NULL);
INSERT INTO academy_branch_members VALUES (200,100,NULL,2,NULL);
INSERT INTO access_system_roles VALUES (10,'teacher','academy',0,'#6366f1',1,NULL),(11,'manager','academy',1,'#6366f1',1,NULL),(12,'hidden_role','academy',2,'#6366f1',0,NULL);
INSERT INTO access_system_permissions VALUES (20,'classroom.view','academy','academy',1,NULL),(21,'classroom.edit','academy','academy',1,NULL),(22,'hidden_permission','academy','academy',0,NULL);
INSERT INTO access_system_role_permissions VALUES (1,11,20,NULL),(2,12,22,NULL);
INSERT INTO user_roles (user_id,role_id,is_main) VALUES (2,10,1);
INSERT INTO user_permissions (user_id,permission_id) VALUES (2,20);
INSERT INTO academy_branch_member_roles (member_id,role_id,is_main) VALUES (200,10,1),(200,12,0);
INSERT INTO academy_branch_member_permissions (member_id,permission_id) VALUES (200,20),(200,22);");
$service = new Modules\Analytics\Services\AdminUserAccessService();
$service->save(1, 2, ['roleIds' => [11]]);
$role = (int) $pdo->query('SELECT role_id FROM user_roles WHERE user_id=2 AND deleted_at IS NULL')->fetchColumn();
$permission = (int) $pdo->query('SELECT permission_id FROM user_permissions WHERE user_id=2 AND deleted_at IS NULL')->fetchColumn();
if ($role !== 11 || $permission !== 20) throw new RuntimeException('Role-only save changed permissions.');
$service->save(1, 2, ['permissionIds' => [21]]);
$role = (int) $pdo->query('SELECT role_id FROM user_roles WHERE user_id=2 AND deleted_at IS NULL')->fetchColumn();
$permission = (int) $pdo->query('SELECT permission_id FROM user_permissions WHERE user_id=2 AND deleted_at IS NULL')->fetchColumn();
if ($role !== 11 || $permission !== 21) throw new RuntimeException('Permission-only save changed roles.');
try { $service->save(1, 2, []); throw new RuntimeException('Empty payload accepted.'); }
catch (RuntimeException $error) { if ($error->getMessage() === 'Empty payload accepted.') throw $error; }
$service->save(3, 2, ['roleIds' => [11]]);
$academyRoles = $pdo->query('SELECT role_id FROM academy_branch_member_roles WHERE member_id=200 AND deleted_at IS NULL ORDER BY role_id')->fetchAll(PDO::FETCH_COLUMN);
$academyPermissions = $pdo->query('SELECT permission_id FROM academy_branch_member_permissions WHERE member_id=200 AND deleted_at IS NULL ORDER BY permission_id')->fetchAll(PDO::FETCH_COLUMN);
if (array_map('intval', $academyRoles) !== [11, 12] || array_map('intval', $academyPermissions) !== [20, 22]) throw new RuntimeException('Academy role save altered permissions or hidden role.');
$service->save(3, 2, ['permissionIds' => [21]]);
$academyRoles = $pdo->query('SELECT role_id FROM academy_branch_member_roles WHERE member_id=200 AND deleted_at IS NULL ORDER BY role_id')->fetchAll(PDO::FETCH_COLUMN);
$academyPermissions = $pdo->query('SELECT permission_id FROM academy_branch_member_permissions WHERE member_id=200 AND deleted_at IS NULL ORDER BY permission_id')->fetchAll(PDO::FETCH_COLUMN);
if (array_map('intval', $academyRoles) !== [11, 12] || array_map('intval', $academyPermissions) !== [21, 22]) throw new RuntimeException('Academy permission save altered roles or hidden permission.');
$academyView = $service->index(3, ['perPage' => 20]);
$target = array_values(array_filter($academyView['users'], fn ($user) => $user['id'] === 2))[0] ?? null;
if (!$target || $target['rolePermissionIds'] !== [20, 22] || array_column($target['rolePermissions'], 'id') !== [20]) throw new RuntimeException('Inherited permissions leaked hidden catalog entries or were omitted: ' . json_encode([$target['rolePermissionIds'] ?? null, $target['rolePermissions'] ?? null]));
try { $service->save(3, 2, ['roleIds' => [12]]); throw new RuntimeException('Hidden role accepted.'); }
catch (RuntimeException $error) { if ($error->getMessage() === 'Hidden role accepted.') throw $error; }
$pdo->exec("ALTER TABLE access_system_roles ADD COLUMN type VARCHAR(30) DEFAULT 'academy';
INSERT INTO users VALUES (4,'branch','branch_account',NULL,NULL,'approved',NULL,NULL);
INSERT INTO academy_branches VALUES (101,100,4,NULL);
INSERT INTO access_system_roles (role_id,name,scope,sort_order,color,is_panel_visible,type) VALUES
(13,'academy_branch_teacher','branch',3,'#6366f1',1,'academy'),
(14,'platform_admin','academy',4,'#6366f1',1,'platform');
INSERT INTO f_translations (table_name,table_id,locale,field,value) VALUES
('access_system_roles',10,'fa','title','مدرس فارسی'),
('access_system_roles',10,'en','title','English teacher');");
if (array_column($service->staffRoles(3), 'id') !== [10, 11, 13]) throw new RuntimeException('Academy staff role scope or visibility differs from access policy.');
if (array_column($service->staffRoles(4), 'id') !== [13]) throw new RuntimeException('Branch can select academy roles.');
if (!in_array(12, array_column($service->staffRoles(1), 'id'), true)) throw new RuntimeException('Founder cannot select hidden staff roles.');
if ($service->staffRoles(2) !== [] || $service->staffRoles(999) !== []) throw new RuntimeException('Non-manager can delegate roles.');
if ($service->staffRole(3, 10)['title'] !== 'مدرس فارسی') throw new RuntimeException('Persian staff role translation is missing.');
$GLOBALS['accessTestLocale'] = 'en';
if ($service->staffRole(3, 10)['title'] !== 'English teacher') throw new RuntimeException('English staff role translation is missing.');
foreach ([[3, 12], [4, 10], [3, 14], [2, 13], [3, 999]] as [$actor, $roleId]) {
    try { $service->staffRole($actor, $roleId); throw new LogicException('Forbidden staff role accepted.'); }
    catch (RuntimeException) {}
}
echo "User access: partial saves, staff role scope, visibility, translations and rejected delegation passed.\n";
for ($id = 100; $id < 125; $id++) $pdo->exec("INSERT INTO users (user_id,type,username,status) VALUES ($id,'human','sort_fixture_$id','approved')");
$ascending = $service->index(1, ['page' => 2, 'perPage' => 10, 'sortDirection' => 'asc']);
$descending = $service->index(1, ['page' => 2, 'perPage' => 10, 'sortDirection' => 'desc']);
if (array_column($ascending['users'], 'id') !== range(106, 115)) throw new LogicException('Server ascending sort is not applied before pagination.');
if (array_column($descending['users'], 'id') !== range(114, 105)) throw new LogicException('Server descending sort is not applied before pagination.');
fixtureExec('CREATE TABLE f_settings(setting_id INTEGER PRIMARY KEY,variable_name VARCHAR(200),page VARCHAR(60),table_name VARCHAR(80),status VARCHAR(20),source TEXT,icon TEXT,value TEXT,created_by INTEGER,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER)');
foreach (['version', 'created_by', 'updated_by'] as $column) $pdo->exec('ALTER TABLE f_translations ADD COLUMN ' . $column . ' INTEGER');
function auth(): object { return new class { public function id(): int { return $GLOBALS['guideActor'] ?? 0; } }; }
$GLOBALS['guideActor'] = 1;
$guideService = new Modules\Analytics\Services\SitePageContentService();
$guide = ['page' => 'panel-classrooms', 'fa' => ['title' => 'راهنما', 'body' => 'متن فارسی'], 'en' => ['title' => 'Guide', 'body' => 'English text']];
$guideService->savePanelGuide(1, $guide);
if ($guideService->panelGuides()['classrooms']['body']['en'] !== 'English text') throw new LogicException('Database lost bilingual panel guidance.');
$guideService->savePanelGuide(1, array_replace($guide, ['en' => ['title' => 'Edited', 'body' => 'Changed']]));
if ($guideService->panelGuides()['classrooms']['title']['en'] !== 'Edited' || (int) $pdo->query("SELECT COUNT(*) FROM f_settings WHERE page='panel-classrooms'")->fetchColumn() !== 2) throw new LogicException('Repeated guidance save duplicated configuration rows.');
$GLOBALS['guideActor'] = 2;
try { $guideService->savePanelGuide(2, $guide); throw new LogicException('Non-founder saved guidance.'); } catch (RuntimeException $error) { if ($error->getCode() !== 403) throw $error; }
echo "Panel guidance database: bilingual insert/update, repeated save and founder-only access passed.\n";
`;
const result = spawnSync('php', [], { input: source, encoding: 'utf8', maxBuffer: 4 * 1024 * 1024 });
if (result.stdout) process.stdout.write(result.stdout);
if (result.stderr) process.stderr.write(result.stderr);
if (result.error) throw result.error;
process.exitCode = result.status ?? 1;
