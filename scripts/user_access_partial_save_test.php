<?php
spl_autoload_register(function (string $class): void {
    $path = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (is_file($path)) require $path;
    elseif (str_starts_with($class, 'Core\\')) {
        $path = __DIR__ . '/../core/' . str_replace('\\', '/', substr($class, 5)) . '.php';
        if (is_file($path)) require $path;
    }
});

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
function db(): PDO { return $GLOBALS['userAccessTestPdo']; }
function locale(): string { return 'fa'; }
function session(): object { return new class { public function get(string $key, mixed $default = null): mixed { return $key === 'suppress_database_notifications' ? true : $default; } }; }
function transaction(callable $callback): mixed {
    db()->beginTransaction();
    try { $result = $callback(); db()->commit(); return $result; }
    catch (Throwable $error) { db()->rollBack(); throw $error; }
}
$GLOBALS['userAccessTestPdo'] = $pdo;
$pdo->exec("CREATE TABLE users (user_id INTEGER PRIMARY KEY, type TEXT, username TEXT, email TEXT, phone TEXT, status TEXT, last_login_at TEXT, deleted_at TEXT);
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
echo "User access: site and academy partial saves, hidden grants, and invalid payload rejection passed.\n";
