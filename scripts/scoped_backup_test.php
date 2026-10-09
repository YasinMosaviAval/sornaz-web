<?php
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require_once $map[$class]; });
class BackupFixture extends \Core\database\PrefixedPDO
{
    public function query(string $query, ?int $fetchMode = null, mixed ...$args): PDOStatement|false
    {
        if (str_contains($query, 'information_schema.tables')) {
            $query = "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name";
        }
        return parent::query($query);
    }
}
$pdo = new BackupFixture('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$root = sys_get_temp_dir() . '/sornaz-backup-' . bin2hex(random_bytes(6));
function db() { return $GLOBALS['pdo']; }
function base_path($path = '') { return $GLOBALS['root'] . ($path === '' ? '' : DIRECTORY_SEPARATOR . $path); }
function storage_path($path = '') { return base_path('storage/' . $path); }
function session() { return new class { public function get($key, $default = null) { return $key === 'suppress_database_notifications' ? true : $default; } }; }
function check($ok, $label) { if (!$ok) throw new LogicException($label); }
register_shutdown_function(static function () use ($root) {
    if (!is_dir($root)) return;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($it as $file) { $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname()); }
    rmdir($root);
});
$pdo->exec("CREATE TABLE users(user_id INTEGER,type TEXT,password TEXT,deleted_at TEXT);
INSERT INTO users VALUES(7,'academy','DO_NOT_EXPORT',NULL),(1,'admin','ADMIN_SECRET',NULL);
CREATE TABLE academies(academy_id INTEGER,user_id INTEGER,created_by INTEGER,deleted_at TEXT); INSERT INTO academies VALUES(10,7,1,NULL),(20,8,1,NULL);
CREATE TABLE academy_branches(branch_id INTEGER,academy_id INTEGER,user_id INTEGER); INSERT INTO academy_branches VALUES(100,10,70),(200,20,80);
CREATE TABLE academy_branch_members(member_id INTEGER,branch_id INTEGER,user_id INTEGER); INSERT INTO academy_branch_members VALUES(1,100,1),(2,200,1);
CREATE TABLE academy_branch_courses(course_id INTEGER,branch_id INTEGER);
CREATE TABLE academy_branch_course_terms(term_id INTEGER,course_id INTEGER);
CREATE TABLE academy_branch_course_term_sessions(term_session_id INTEGER,term_id INTEGER,booking_id INTEGER);
CREATE TABLE academy_branch_course_term_invoices(term_invoice_id INTEGER,term_id INTEGER);
CREATE TABLE academy_branch_classrooms(classroom_id INTEGER,branch_id INTEGER);
CREATE TABLE translations(translation_id INTEGER,table_name TEXT,table_id INTEGER,value TEXT);
CREATE TABLE auth_tokens(user_id INTEGER,token TEXT); INSERT INTO auth_tokens VALUES(1,'SECRET_TOKEN');");
$fields = ['user_id','disk','directory','filename','extension','mime_type','type','collection','path','original_filename','fileable_type','fileable_id','size','checksum','visibility','created_by','updated_by','deleted_at'];
$pdo->exec('CREATE TABLE media_files(media_file_id INTEGER PRIMARY KEY,' . implode(',', array_map(fn ($f) => "$f TEXT", $fields)) . ')');
$service = new Modules\Analytics\Services\AcademyScopedBackupService();
$result = $service->create(1);
$json = file_get_contents(base_path('storage/backups/users/1/' . $result['filename']));
$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR)['records'];
check(!str_contains($json, 'DO_NOT_EXPORT') && !str_contains($json, 'ADMIN_SECRET') && !str_contains($json, 'SECRET_TOKEN'), 'Credentials exported');
check(count($data['users']) === 1 && (int)$data['users'][0]['user_id'] === 1, 'Current user boundary failed');
check($service->find(1, (int) $result['id'])['filename'] === $result['filename'], 'Own export cannot be downloaded');
try { $service->create(7); throw new LogicException('Non-admin created export'); } catch (RuntimeException $e) { check($e->getCode() === 403, 'Wrong create denial'); }
try { $service->find(7, (int) $result['id']); throw new LogicException('Non-admin downloaded export'); } catch (RuntimeException $e) { check($e->getCode() === 403, 'Wrong download denial'); }
echo "User export: admin-only access, own data, secrets exclusion and no-academy checks passed.\n";
