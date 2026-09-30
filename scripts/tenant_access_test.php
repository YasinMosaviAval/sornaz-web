<?php
$lockRoot = sys_get_temp_dir().'/sornaz-tenant-'.bin2hex(random_bytes(6));
function storage_path($path = '') { return $GLOBALS['lockRoot'].'/'.$path; }
register_shutdown_function(static function () use ($lockRoot) { foreach (glob($lockRoot.'/payment-locks/*.lock') ?: [] as $f) unlink($f); if (is_dir($lockRoot.'/payment-locks')) rmdir($lockRoot.'/payment-locks'); if (is_dir($lockRoot)) rmdir($lockRoot); });


// Isolated SQLite fixtures; never boots the application or reads production data.
$map = require __DIR__.'/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) {
    if (isset($map[$class])) {
        require_once $map[$class];
    }
});
$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
function db() { return $GLOBALS['pdo']; }
function base_path($path = '') { return dirname(__DIR__).'/'.$path; }
function locale() { return 'fa'; }
function auth() { return new class {
    public function user() { return $GLOBALS['currentUser']; }
    public function id() { return $this->user()['user_id'] ?? null; }
}; }
function check($condition, $message) {
    if (!$condition) { throw new LogicException($message); }
    ++$GLOBALS['checks'];
}
function forbidden(callable $action) {
    try { $action(); } catch (RuntimeException $e) {
        check($e->getCode() === 403, 'Expected authorization denial, got: '.$e->getMessage());
        return;
    }
    throw new LogicException('Unauthorized action succeeded');
}
$checks = 0;
$currentUser = ['user_id' => 10, 'type' => 'academy'];

// Denial must happen before SQL, input validation, filesystem access or DDL.
$operations = [
    [new Modules\Analytics\Services\AdminPostService, ['index' => [[]], 'find' => [1], 'create' => [10, []], 'update' => [10, 1, []], 'trash' => [10, 1], 'restore' => [10, 1], 'destroy' => [10, 1]]],
    [new Modules\Analytics\Services\AdminCommentService, ['index' => [[]], 'update' => [10, 1, []], 'reply' => [10, 1, []], 'delete' => [10, 1]]],
    [new Modules\Analytics\Services\AdminMediaService, ['index' => [[]], 'upload' => [10, [], []], 'update' => [10, 1, []], 'delete' => [10, 1]]],
    [new Modules\Analytics\Services\AdminPostCategoryService, ['index' => [], 'create' => [10, []], 'update' => [10, 1, []], 'delete' => [10, 1]]],
    [new Modules\Analytics\Services\AdminSettingService, ['save' => [10, []]]],
];
foreach ([null, ['user_id' => 10, 'type' => 'academy'], ['user_id' => 11, 'type' => 'branch'], ['user_id' => 12, 'type' => 'human']] as $currentUser) {
    foreach ($operations as [$service, $methods]) {
        foreach ($methods as $method => $args) { forbidden(fn () => $service->$method(...$args)); }
    }
}
$currentUser = ['user_id' => 1, 'type' => 'human'];
Modules\System\Services\SiteAdminAccess::requireCurrentUser(1);
forbidden(fn () => Modules\System\Services\SiteAdminAccess::requireCurrentUser(10));
$currentUser = ['user_id' => 2, 'type' => 'admin'];
Modules\System\Services\SiteAdminAccess::requireCurrentUser(2);

require base_path('Modules/Analytics/Routes/routes.php');
foreach (['posts', 'post-categories', 'comments', 'media'] as $name) {
    $route = Core\router\Router::dispatch('GET', '/analytics/admin-'.$name);
    check(in_array('site-admin', $route['middlewares'], true), 'Global content route lacks admin middleware');
}
$catalog = (new Modules\Analytics\Services\MobilePanelCatalog)->sections();
foreach (['posts', 'post-categories', 'comments', 'media', 'pages', 'page-content', 'settings'] as $key) {
    check($catalog[$key]['access'] === 'admin', 'Mobile content section is not restricted');
}
check($catalog['gallery']['access'] === 'management', 'Academy gallery was unnecessarily disabled');
foreach (['posts' => ['update', 'trash', 'restore', 'delete'], 'post-categories' => ['update', 'delete'], 'comments' => ['update', 'reply', 'delete'], 'media' => ['update', 'delete']] as $section => $actions) {
    foreach ($actions as $action) {
        $route = Core\router\Router::dispatch('POST', '/analytics/admin-'.$section.'/1/'.$action);
        check(in_array('site-admin', $route['middlewares'], true) && in_array('csrf', $route['middlewares'], true), 'Mutation lacks admin or CSRF protection');
    }
}
foreach (['posts', 'post-categories', 'media/upload', 'settings'] as $path) {
    $route = Core\router\Router::dispatch('POST', '/analytics/admin-'.$path);
    check(in_array('site-admin', $route['middlewares'], true), 'Create/settings route lacks admin protection');
}

$pdo->exec("CREATE TABLE users(user_id INTEGER PRIMARY KEY,type TEXT,deleted_at TEXT);
CREATE TABLE academies(academy_id INTEGER PRIMARY KEY,user_id INTEGER,created_by INTEGER,deleted_at TEXT);
CREATE TABLE academy_branches(branch_id INTEGER PRIMARY KEY,academy_id INTEGER,user_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_members(member_id INTEGER PRIMARY KEY,user_id INTEGER,branch_id INTEGER,academy_id INTEGER,status TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_member_roles(member_id INTEGER,role_id INTEGER,deleted_at TEXT);
CREATE TABLE access_system_roles(role_id INTEGER PRIMARY KEY,name TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_member_contracts(member_id INTEGER,type TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_courses(course_id INTEGER PRIMARY KEY,branch_id INTEGER,academy_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_course_terms(term_id INTEGER PRIMARY KEY,course_id INTEGER,status TEXT,approved_at TEXT,approved_by INTEGER,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER);
INSERT INTO users VALUES(1,'admin',NULL),(10,'academy',NULL),(20,'academy',NULL),(11,'branch',NULL),(12,'human',NULL);
INSERT INTO academies VALUES(100,10,10,NULL),(200,20,20,NULL);
INSERT INTO academy_branches VALUES(101,100,11,NULL),(102,100,13,NULL),(201,200,21,NULL);
INSERT INTO academy_branch_courses VALUES(1001,101,NULL,NULL),(1002,102,NULL,NULL),(2001,201,NULL,NULL),(1000,NULL,100,NULL),(2000,NULL,200,NULL);
INSERT INTO academy_branch_course_terms(term_id,course_id,status) VALUES(1,1001,'pending'),(2,2001,'pending'),(3,1000,'pending'),(4,2000,'pending'),(5,1002,'pending');
INSERT INTO academy_branch_members VALUES(12,12,101,100,'inactive',NULL);
INSERT INTO access_system_roles VALUES(7,'branch_manager',NULL);
INSERT INTO academy_branch_member_roles VALUES(12,7,NULL);");
$terms = new Modules\Academy\Services\AcademyTermService;
$before = $pdo->query('SELECT * FROM academy_branch_course_terms')->fetchAll();
foreach ([2, 4, 999] as $id) {
    foreach (['save', 'saveAcademyTerm'] as $method) {
        forbidden(fn () => $terms->$method(10, ['branchId' => 101, 'courseId' => 1001, 'classroomId' => 1], $id));
    }
    forbidden(fn () => $terms->cycleStatus(10, $id));
    forbidden(fn () => $terms->delete(10, $id));
}
forbidden(fn () => $terms->cycleStatus(11, 5)); // Sibling branch.
forbidden(fn () => $terms->cycleStatus(11, 3)); // Academy-wide term.
forbidden(fn () => $terms->cycleStatus(12, 1)); // Inactive manager.
check($before === $pdo->query('SELECT * FROM academy_branch_course_terms')->fetchAll(), 'Denied requests mutated terms');
check($terms->cycleStatus(10, 1)['status'] === 'open', 'Academy owner cannot manage own term');
check($terms->cycleStatus(10, 3)['status'] === 'open', 'Academy owner cannot manage academy-wide term');
check($terms->cycleStatus(11, 1)['status'] === 'ongoing', 'Branch account cannot manage own term');
$pdo->exec("UPDATE academy_branch_members SET status='active' WHERE member_id=12");
check($terms->cycleStatus(12, 1)['status'] === 'finished', 'Active manager cannot manage own term');
$pdo->exec("UPDATE access_system_roles SET name='unrelated_manager' WHERE role_id=7");
forbidden(fn () => $terms->cycleStatus(12, 1));
$pdo->exec("UPDATE access_system_roles SET name='branch_manager' WHERE role_id=7");
forbidden(fn () => $terms->cycleStatus(12, 2));
check($terms->cycleStatus(1, 2)['status'] === 'open', 'Site administrator cannot manage another academy');
$pdo->exec('CREATE TABLE academy_branch_course_term_enrollments(term_id INTEGER); CREATE TABLE academy_branch_course_term_sessions(term_id INTEGER); CREATE TABLE academy_branch_course_term_invoices(term_id INTEGER);');
$terms->delete(10, 3);
check((int) $pdo->query('SELECT deleted_by FROM academy_branch_course_terms WHERE term_id=3')->fetchColumn() === 10, 'Academy-wide term deletion failed');

// Read scopes and existing academy gallery ownership must remain isolated.
$branchScope = new ReflectionMethod($terms, 'branches');
check(array_column($branchScope->invoke($terms, 10), 'branch_id') === [101, 102], 'Academy read scope leaked another academy');
check(array_column($branchScope->invoke($terms, 11), 'branch_id') === [101], 'Branch read scope leaked a sibling');
$pdo->exec("CREATE TABLE media_files(media_file_id INTEGER PRIMARY KEY,user_id INTEGER,collection TEXT,deleted_at TEXT,deleted_by INTEGER,updated_by INTEGER);
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,version INTEGER,deleted_at TEXT);
INSERT INTO media_files VALUES(1,11,'gallery',NULL,NULL,NULL),(2,21,'gallery',NULL,NULL,NULL);");
$gallery = new Modules\Analytics\Services\AdminGalleryService;
try {
    $gallery->delete(11, 2);
    throw new LogicException('Cross-branch gallery deletion accepted');
} catch (RuntimeException $e) {
    check(!$e instanceof PDOException, 'Gallery fixture failed instead of denying access');
    check($pdo->query('SELECT deleted_at FROM media_files WHERE media_file_id=2')->fetchColumn() === null, 'Denied gallery deletion changed data');
}
$gallery->delete(11, 1);
check((int) $pdo->query('SELECT deleted_by FROM media_files WHERE media_file_id=1')->fetchColumn() === 11, 'Own gallery deletion failed');

// The site administrator still has access to site content at the service layer.
$pdo->exec("CREATE TABLE comments(comment_id INTEGER PRIMARY KEY,deleted_at TEXT,deleted_by INTEGER,updated_at TEXT,updated_by INTEGER);
ALTER TABLE translations ADD COLUMN deleted_by INTEGER;
INSERT INTO comments(comment_id) VALUES(1);");
$currentUser = ['user_id' => 1, 'type' => 'admin'];
(new Modules\Analytics\Services\AdminCommentService)->delete(1, 1);
check((int) $pdo->query('SELECT deleted_by FROM comments WHERE comment_id=1')->fetchColumn() === 1, 'Administrator content mutation failed');
$currentUser = ['user_id' => 10, 'type' => 'academy'];
$_SERVER['HTTP_ACCEPT'] = 'application/json';
$middleware = new Modules\System\Middleware\SiteAdminMiddleware;
$response = $middleware->handle(new Core\http\Request, fn () => throw new LogicException('Unauthorized controller reached'));
check((new ReflectionProperty($response, 'status'))->getValue($response) === 403, 'Web middleware did not return 403');
$response = (new Modules\Academy\Controllers\Web\AcademyTermController($terms))->destroy(2);
check((new ReflectionProperty($response, 'status'))->getValue($response) === 403, 'Term controller did not preserve authorization status');
$response = (new Modules\Analytics\Controllers\Web\AdminCommentController(new Modules\Analytics\Services\AdminCommentService))->delete(1);
check((new ReflectionProperty($response, 'status'))->getValue($response) === 403, 'Content controller did not preserve authorization status');

echo "Tenant and site-content access: $checks checks passed.\n";
