<?php

// Isolated SQLite fixture; no application boot or production data.
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function (string $class) use ($map): void {
    if (isset($map[$class])) require_once $map[$class];
});
$pdo = new \Core\database\PrefixedPDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
function db() { return $GLOBALS['pdo']; }
function auth() { return new class { public function id(): int { return 2; } }; }
function request() { return new class { public function input(string $key, mixed $default = null): mixed { return $GLOBALS['requestPayload'][$key] ?? $default; } }; }
$GLOBALS['pdo'] = $pdo;
$pdo->exec('CREATE TABLE users(user_id INTEGER PRIMARY KEY, username TEXT, type TEXT, deleted_at TEXT)');
$pdo->exec('CREATE TABLE academies(academy_id INTEGER PRIMARY KEY, user_id INTEGER, created_by INTEGER, deleted_at TEXT)');
$pdo->exec('CREATE TABLE academy_branches(branch_id INTEGER PRIMARY KEY, academy_id INTEGER, user_id INTEGER, deleted_at TEXT)');
$pdo->exec('CREATE TABLE academy_branch_members(member_id INTEGER PRIMARY KEY, user_id INTEGER, branch_id INTEGER, academy_id INTEGER, deleted_at TEXT)');
$pdo->exec('CREATE TABLE user_availabilities(user_availability_id INTEGER PRIMARY KEY, user_id INTEGER, repeat_period TEXT, day_of_week TEXT, date TEXT, status TEXT, unavailable_type TEXT, deleted_at TEXT, deleted_by INTEGER, updated_by INTEGER)');
$pdo->exec('CREATE TABLE translations(translation_id INTEGER PRIMARY KEY, table_name TEXT, table_id INTEGER, deleted_at TEXT, deleted_by INTEGER, updated_by INTEGER)');
$pdo->exec("INSERT INTO users(user_id,username,type) VALUES(2,'Alice','human'),(3,'Bob','human')");
$pdo->exec("INSERT INTO user_availabilities(user_availability_id,user_id) VALUES(20,2),(30,3)");
$pdo->exec("INSERT INTO translations(translation_id,table_name,table_id) VALUES(201,'user_availabilities',20),(301,'user_availabilities',30)");
$service = new \Modules\Academy\Services\AcademyBranchOfferingService();
$scope = new ReflectionMethod($service, 'scopedOrganizations');
$allowed = new ReflectionMethod($service, 'allowedOrganization');
$mine = $scope->invoke($service, 2);
if (count($mine) !== 1 || $mine[0]['kind'] !== 'personal' || (int) $mine[0]['user_id'] !== 2) {
    throw new LogicException('Personal offering scope must contain only the signed-in user.');
}
if ((int) $allowed->invoke($service, 2, 2)['user_id'] !== 2) {
    throw new LogicException('Own offering was denied.');
}
class PersonalOfferingSpy extends \Modules\Academy\Services\AcademyBranchOfferingService
{
    public array $captured = [];
    public function saveLesson(int $actor, array $data, int $id = 0): array { $this->captured = [$actor, $data, $id]; return ['id' => 1]; }
    public function saveSchedule(int $actor, array $data, int $id = 0): array { $this->captured = [$actor, $data, $id]; return ['ids' => [1]]; }
}
require_once __DIR__ . '/../Modules/Analytics/Controllers/Web/PersonalOfferingController.php';
$spy = new PersonalOfferingSpy();
$controller = new \Modules\Analytics\Controllers\Web\PersonalOfferingController($spy);
$encode = static fn (array $data): string => rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
$GLOBALS['requestPayload'] = ['payload_b64' => $encode(['organization_user_id' => 3, 'lesson_id' => 4])];
$controller->saveLesson();
if ($spy->captured[0] !== 2 || $spy->captured[1]['organization_user_id'] !== 2) throw new LogicException('Forged lesson owner was accepted.');
$GLOBALS['requestPayload'] = ['payload_b64' => $encode(['organizationUserId' => 3, 'branchId' => 3, 'repeatPeriod' => 'هفتگی', 'day' => 'شنبه'])];
$controller->saveSchedule();
if ($spy->captured[0] !== 2 || $spy->captured[1]['organizationUserId'] !== 2 || $spy->captured[1]['branchId'] !== 2) throw new LogicException('Forged schedule owner was accepted.');
$pdo->exec("UPDATE user_availabilities SET repeat_period='week',day_of_week='saturday',status='reserved' WHERE user_availability_id=20");
$spy->captured = [];
$controller->saveSchedule(20);
if ($spy->captured) throw new LogicException('Reserved personal schedule was edited.');
try {
    $service->delete('schedule', 20, 2);
    throw new LogicException('Reserved personal schedule was deleted.');
} catch (RuntimeException $error) {
    if ($error->getMessage() === 'Reserved personal schedule was deleted.') throw $error;
}
$pdo->exec("UPDATE user_availabilities SET status='available' WHERE user_availability_id=20");
try {
    $allowed->invoke($service, 2, 3);
    throw new LogicException('Another user offering was accepted.');
} catch (RuntimeException $error) {
    if ($error->getMessage() === 'Another user offering was accepted.') throw $error;
}
try {
    $service->delete('schedule', 30, 2);
    throw new LogicException('Another user schedule was deleted.');
} catch (RuntimeException $error) {
    if ($error->getMessage() === 'Another user schedule was deleted.') throw $error;
}
if ($pdo->query('SELECT deleted_at FROM user_availabilities WHERE user_availability_id=30')->fetchColumn() !== null) {
    throw new LogicException('Cross-user schedule changed.');
}
$service->delete('schedule', 20, 2);
foreach (['user_availabilities' => 'user_availability_id=20', 'translations' => 'translation_id=201'] as $table => $where) {
    $row = $pdo->query("SELECT deleted_at,deleted_by FROM {$table} WHERE {$where}")->fetch(PDO::FETCH_ASSOC);
    if (!$row['deleted_at'] || (int) $row['deleted_by'] !== 2) throw new LogicException('Own schedule and translation deletion were not synchronized.');
}

require __DIR__ . '/../Modules/Analytics/Routes/routes.php';
foreach (['/analytics/personal-offerings/lessons', '/analytics/personal-offerings/schedules'] as $path) {
    $route = \Core\router\Router::dispatch('POST', $path);
    if (!in_array('auth', $route['middlewares'], true) || !in_array('csrf', $route['middlewares'], true)) {
        throw new LogicException('Personal offering mutation lacks auth or CSRF.');
    }
}
echo "Personal offerings: own scope, forged owner denial, reserved schedule guard, deletion sync, auth and CSRF passed.\n";
