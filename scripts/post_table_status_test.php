<?php
// Real SQL against an isolated in-memory SQLite database. Only MySQL's schema probe is adapted.
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require_once $map[$class]; });
class PostTableFixturePDO extends PDO {
    public function prepare(string $query, array $options = []): PDOStatement|false {
        if ($query === "SHOW COLUMNS FROM posts LIKE 'type'") $query = "SELECT 'varchar(30)' AS Type";
        return parent::prepare($query, $options);
    }
}
$pdo = new PostTableFixturePDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$pdo->sqliteCreateFunction('CONCAT', static fn (...$parts) => implode('', $parts));
$actor = ['user_id'=>1,'type'=>'human'];
$language = 'en';
function db() { return $GLOBALS['pdo']; }
function locale() { return $GLOBALS['language']; }
function auth() { return new class { public function user() { return $GLOBALS['actor']; } }; }
function session() { return new class { public function get($key, $default = null) { return $key === 'suppress_database_notifications' ? true : $default; } }; }
function verify($ok, $message) { if (!$ok) throw new RuntimeException($message); }
$pdo->exec("CREATE TABLE posts(post_id INTEGER PRIMARY KEY,author_id INTEGER,slug TEXT,categories TEXT,cover_media_id INTEGER,type TEXT,status TEXT,visibility TEXT,published_at TEXT,updated_at TEXT,updated_by INTEGER,deleted_at TEXT);
CREATE TABLE users(user_id INTEGER PRIMARY KEY,username TEXT);
CREATE TABLE categories(category_id INTEGER PRIMARY KEY,name TEXT,deleted_at TEXT);
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INTEGER,locale TEXT,field TEXT,value TEXT,deleted_at TEXT);
INSERT INTO users VALUES(1,'admin');
INSERT INTO posts VALUES(1,1,'post','7, 8,9,10',NULL,'post','draft','public',NULL,NULL,NULL,NULL),(2,1,'deleted','',NULL,'post','draft','public',NULL,NULL,NULL,'2026-01-01');
INSERT INTO categories VALUES(7,'Fallback',NULL),(8,'Name fallback',NULL),(9,'Deleted','2026-01-01');
INSERT INTO translations VALUES(1,'categories',7,'fa','title','FA title',NULL),(2,'categories',7,'en','title','Old',NULL),(3,'categories',7,'en','title','Latest <title>',NULL),(4,'posts',1,'fa','title','Keep title',NULL);");
$service = new Modules\Analytics\Services\AdminPostService();
$post = $service->index(['perPage'=>10])['posts'][0];
verify($post['categories'] === '7, 8,9,10' && $post['category_names'] === ['Latest <title>', 'Name fallback'], 'Names must resolve latest translations without changing stored IDs');
$language='fa';
verify($service->index(['perPage'=>10])['posts'][0]['category_names'] === ['FA title', 'Name fallback'], 'Persian fallback failed');
$service->status(1,1,'published');
$row=$pdo->query('SELECT * FROM posts WHERE post_id=1')->fetch();
verify($row['status']==='published' && $row['published_at'] && $row['updated_by']===1, 'Publication status and audit fields were not saved');
verify($row['categories']==='7, 8,9,10' && $pdo->query("SELECT value FROM translations WHERE table_name='posts'")->fetchColumn()==='Keep title', 'Status update overwrote content');
$service->status(1,1,'published');
verify($pdo->query('SELECT published_at FROM posts WHERE post_id=1')->fetchColumn()===$row['published_at'], 'Repeated publication changed publication date');
foreach ([[1,1,'invalid'],[1,2,'published'],[9,1,'published']] as $args) {
    try { $service->status(...$args); throw new LogicException('Invalid status mutation accepted'); }
    catch (RuntimeException $error) { verify(!($error instanceof LogicException), 'Mutation unexpectedly succeeded'); }
}
$actor=['user_id'=>9,'type'=>'academy'];
try { $service->status(9,1,'draft'); throw new LogicException('Unauthorized mutation accepted'); }
catch (RuntimeException $error) { verify($error->getCode()===403, 'Expected access denial'); }
echo "Post tables: latest localized category names, stored-ID preservation, status-only updates, repeated requests, invalid/deleted rows and authorization passed (SQLite).\n";
