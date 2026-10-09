<?php
$map = require __DIR__.'/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require_once $map[$class]; });
$pdo = new \Core\database\PrefixedPDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function db() { return $GLOBALS['pdo']; }
function session() { return new class { public function get($key, $default=null) { return $key === 'suppress_database_notifications' ? true : $default; } }; }
function auth() { return new class { public function id() { return 7; } }; }
$pdo->exec("CREATE TABLE media_files(media_file_id INTEGER PRIMARY KEY,user_id INTEGER,deleted_at TEXT,deleted_by INTEGER);
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INTEGER,deleted_at TEXT,deleted_by INTEGER);
CREATE TABLE f_translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INTEGER,deleted_at TEXT,deleted_by INTEGER);
INSERT INTO media_files VALUES(1,7,NULL,NULL),(2,7,NULL,NULL),(3,8,NULL,NULL);
INSERT INTO translations VALUES(1,'media_files',1,NULL,NULL),(2,'media_files',2,NULL,NULL),(3,'media_files',3,NULL,NULL),(4,'users',1,NULL,NULL);
INSERT INTO f_translations VALUES(1,'media_files',1,NULL,NULL),(2,'media_files',2,NULL,NULL);");
\Core\database\DB::table('media_files')->where('user_id',7)->update(['deleted_at'=>'2026-10-06 12:00:00','deleted_by'=>7]);
foreach (['translations','f_translations'] as $store) {
    $count = (int)$pdo->query("SELECT COUNT(*) FROM $store WHERE table_name='media_files' AND table_id IN (1,2) AND deleted_at='2026-10-06 12:00:00' AND deleted_by=7")->fetchColumn();
    if ($count !== 2) throw new RuntimeException('Bulk translation deletion failed');
}
if ((int)$pdo->query('SELECT COUNT(*) FROM translations WHERE deleted_at IS NULL')->fetchColumn() !== 2) throw new RuntimeException('Unrelated translations deleted');
$pdo->beginTransaction();
\Core\database\DB::table('media_files')->where('media_file_id',3)->delete();
$pdo->rollBack();
if ((int)$pdo->query('SELECT COUNT(*) FROM media_files WHERE media_file_id=3')->fetchColumn() !== 1 || $pdo->query('SELECT deleted_at FROM translations WHERE translation_id=3')->fetchColumn() !== null) throw new RuntimeException('Rollback failed');
\Core\database\DB::table('media_files')->where('media_file_id',3)->delete();
if ((int)$pdo->query('SELECT deleted_by FROM translations WHERE translation_id=3')->fetchColumn() !== 7) throw new RuntimeException('Hard deletion actor missing');
echo "Translation deletion: bulk update, both stores, unrelated rows, rollback and hard-delete actor passed.\n";
