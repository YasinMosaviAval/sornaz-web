<?php
// Actual SQL, controller decoding and middleware against an isolated SQLite database.
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require_once $map[$class]; });
class PostMetadataFixturePDO extends PDO
{
    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        if ($query === "SHOW COLUMNS FROM posts LIKE 'type'") $query = "SELECT 'varchar(30)' AS Type";
        return parent::prepare($query, $options);
    }
}
$pdo = new PostMetadataFixturePDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$actor = ['user_id' => 1, 'type' => 'human'];
function db(): PDO { return $GLOBALS['pdo']; }
function auth(): object { return new class { public function user(): ?array { return $GLOBALS['actor']; } public function id(): int { return (int) ($GLOBALS['actor']['user_id'] ?? 0); } }; }
function request(): Core\http\Request { return new Core\http\Request(); }
function session(): object { return new class { public function get($key, $default = null) { return $key === '_csrf_token' ? 'fixture' : ($key === 'suppress_database_notifications' ? true : $default); } }; }
function app(): object { return new class { public function container(): object { return new class { public function make($class): object { return new $class(); } }; } }; }
function transaction(callable $callback): mixed
{
    db()->beginTransaction();
    try { $result = $callback(); db()->commit(); return $result; }
    catch (Throwable $error) { db()->rollBack(); throw $error; }
}
function verify($ok, $message): void { if (!$ok) throw new RuntimeException($message); }
function postRow(): array { return db()->query('SELECT * FROM posts WHERE post_id=1')->fetch(); }
function texts(): array { return db()->query('SELECT * FROM translations ORDER BY translation_id')->fetchAll(); }
function rejected(callable $callback, ?int $code = null): void
{
    try { $callback(); throw new LogicException('Mutation unexpectedly succeeded'); }
    catch (RuntimeException $error) { verify(!$error instanceof LogicException && ($code === null || $error->getCode() === $code), 'Expected mutation rejection'); }
}
$pdo->exec("CREATE TABLE posts(post_id INTEGER PRIMARY KEY,author_id INTEGER,created_by INTEGER,categories TEXT,cover TEXT,cover_media_id INTEGER,slug TEXT,published_at TEXT,type TEXT,status TEXT,visibility TEXT,visibility_user_id INTEGER,password TEXT,guid TEXT,related_posts_id TEXT,views_count INTEGER,comment_count INTEGER,updated_by INTEGER,updated_at TEXT,deleted_at TEXT);
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY AUTOINCREMENT,table_name TEXT,table_id INTEGER,locale TEXT,field TEXT,value TEXT,version INTEGER DEFAULT 1,created_by INTEGER,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER);
INSERT INTO posts VALUES(1,21,22,'3','/old.webp',7,'first','2026-10-20 11:12:00','post','draft','private',23,'secret','/first','8',99,17,1,NULL,NULL),(2,1,1,'','',NULL,'taken',NULL,'post','draft','public',NULL,NULL,NULL,NULL,0,0,1,NULL,NULL),(3,1,1,'','',NULL,'deleted',NULL,'post','draft','public',NULL,NULL,NULL,NULL,0,0,1,NULL,'2026-10-20');");
$insert = $pdo->prepare("INSERT INTO translations(table_name,table_id,locale,field,value,created_by) VALUES('posts',1,?,?,?,1)");
foreach (['fa', 'en'] as $locale) foreach (['title' => 'Original '.$locale, 'brief' => "  Excerpt\n".$locale, 'description' => " Description & < >\n".$locale, 'content' => "<p class='keep' data-text=\"value\">  Original &amp; body\n".$locale."</p>"] as $field => $value) $insert->execute([$locale, $field, $value]);
$service = new Modules\Analytics\Services\AdminPostService();
$before = texts(); $original = postRow();
$service->updateMetadata(1, 1, [
    'title' => 'New title', 'slug' => 'new-slug', 'categories' => '3,4', 'cover' => '/new.webp', 'cover_media_id' => 8,
    'published_at' => '2026-10-27T08:15', 'type' => 'music_theory', 'status' => 'pending', 'visibility' => 'followers',
    'password' => 'new password', 'guid' => '/new', 'related_posts_id' => '8,9',
    'content' => '<p>ERASE</p>', 'summary' => 'ERASE', 'description' => 'ERASE', 'brief' => 'ERASE',
    'visibility_user_id' => 100, 'author_id' => 100, 'created_by' => 100, 'views_count' => 0, 'comment_count' => 0, 'deleted_at' => '2026-01-01',
]);
$after = texts();
verify(count($after) === count($before), 'Metadata update created unrelated translations');
foreach ($before as $index => $translation) {
    if ($translation['locale'] === 'fa' && $translation['field'] === 'title') {
        verify($after[$index]['value'] === 'New title' && $after[$index]['version'] === 2, 'Title was not updated through translation storage');
    } else verify($after[$index] === $translation, 'Metadata update changed article, excerpt, description or another language');
}
$row = postRow();
foreach (['slug' => 'new-slug', 'categories' => '3,4', 'cover' => '/new.webp', 'cover_media_id' => 8, 'published_at' => '2026-10-27 08:15:00', 'type' => 'music_theory', 'status' => 'pending', 'visibility' => 'followers', 'password' => 'new password', 'guid' => '/new', 'related_posts_id' => '8,9'] as $key => $value) verify($row[$key] === $value, 'Editable metadata not saved: '.$key);
foreach (['visibility_user_id', 'author_id', 'created_by', 'views_count', 'comment_count', 'deleted_at'] as $key) verify($row[$key] === $original[$key], 'Hidden or readonly field changed: '.$key);
verify($row['updated_by'] === 1 && $row['updated_at'], 'Audit fields missing');
// A content edit completed after opening settings survives an old client snapshot.
$pdo->exec("UPDATE translations SET value='Concurrent article',version=version+1 WHERE field='content' AND locale='fa'");
$concurrent = texts();
$service->updateMetadata(1, 1, ['visibility' => 'premium', 'content' => $before[3]['value']]);
verify(texts() === $concurrent, 'An older metadata snapshot replaced concurrent article changes');
$expected = $row; $expected['visibility'] = 'premium'; $expected['updated_at'] = postRow()['updated_at'];
verify(postRow() === $expected, 'Omitted metadata was normalized or cleared');
rejected(fn () => $service->updateMetadata(1, 1, ['slug' => 'taken']));
verify(postRow() === $expected && texts() === $concurrent, 'Rejected duplicate slug changed data');
rejected(fn () => $service->updateMetadata(1, 1, ['title' => '   ']));
rejected(fn () => $service->updateMetadata(1, 3, ['title' => 'Deleted']));
rejected(fn () => $service->updateMetadata(8, 1, ['title' => 'Spoof actor']), 403);
$actor = null; rejected(fn () => $service->updateMetadata(1, 1, ['title' => 'Anonymous']), 403);
$actor = ['user_id' => 8, 'type' => 'human']; rejected(fn () => $service->updateMetadata(8, 1, ['title' => 'Member']), 403);
$actor = ['user_id' => 1, 'type' => 'human'];
// The latest displayed title changes without modifying older translation history.
$insert->execute(['fa', 'title', 'Most recent title']);
$latestId = (int) $pdo->lastInsertId();
$olderTitle = $pdo->query("SELECT value FROM translations WHERE field='title' AND locale='fa' ORDER BY translation_id LIMIT 1")->fetchColumn();
$service->updateMetadata(1, 1, ['title' => 'Latest saved']);
verify($pdo->query('SELECT value FROM translations WHERE translation_id='.$latestId)->fetchColumn() === 'Latest saved', 'Latest active title was not changed');
verify($pdo->query("SELECT value FROM translations WHERE field='title' AND locale='fa' ORDER BY translation_id LIMIT 1")->fetchColumn() === $olderTitle, 'Older title history changed');
$pdo->exec("CREATE TRIGGER reject_title BEFORE UPDATE OF value ON translations WHEN NEW.value='reject-title' BEGIN SELECT RAISE(ABORT,'fixture title failure'); END");
$beforeFailure = postRow(); $textsBeforeFailure = texts();
rejected(fn () => $service->updateMetadata(1, 1, ['title' => 'reject-title', 'slug' => 'must-rollback']));
verify(postRow() === $beforeFailure && texts() === $textsBeforeFailure, 'Metadata and title updates were not atomic');
$pdo->exec('DROP TRIGGER reject_title');
// Exercise route registration, payload decoder, site-admin middleware and real CSRF.
require __DIR__ . '/../Modules/Analytics/Routes/routes.php';
$route = Core\router\Router::dispatch('POST', '/analytics/admin-posts/1/metadata');
verify($route && $route['middlewares'] === ['site-admin', 'csrf'], 'Metadata route is missing protected middleware');
$_SERVER = ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/analytics/admin-posts/1/metadata', 'SCRIPT_NAME' => '/index.php', 'HTTP_ACCEPT' => 'application/json'];
$controller = new Modules\Analytics\Controllers\Web\AdminPostController($service);
$dispatch = static function ($token, $payload = ['title' => 'Controller title']): int {
    $_POST = ['_token' => $token]; $_REQUEST = ['payload_b64' => rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=')];
    $controller = $GLOBALS['controller'];
    $response = (new Modules\System\Middleware\SiteAdminMiddleware())->handle(request(), fn ($request) =>
        (new Core\csrf\CsrfMiddleware())->handle($request, fn () => $controller->metadata(1)));
    ob_start(); $response->send(); $json = json_decode(ob_get_clean(), true);
    return (int) $json['status'];
};
$securityBefore = postRow(); $textsSecurityBefore = texts();
verify($dispatch('bad') === 419, 'Bad CSRF token accepted');
verify(postRow() === $securityBefore && texts() === $textsSecurityBefore, 'Rejected CSRF changed data');
$actor = ['user_id' => 8, 'type' => 'human'];
verify($dispatch('fixture') === 403, 'Member bypassed site-admin middleware');
verify(postRow() === $securityBefore && texts() === $textsSecurityBefore, 'Rejected member changed data');
$actor = ['user_id' => 1, 'type' => 'human'];
verify($dispatch('fixture') === 200, 'Protected controller route rejected valid payload');
verify($pdo->query('SELECT value FROM translations WHERE translation_id='.$latestId)->fetchColumn() === 'Controller title', 'Controller did not decode payload');
// Existing full update intentionally still writes its article fields.
$service->update(1, 1, ['title' => 'Full edit', 'slug' => 'full-edit', 'content' => '<p>Full article</p>', 'summary' => 'Full excerpt', 'description' => 'Full description']);
foreach (['content' => '<p>Full article</p>', 'brief' => 'Full excerpt', 'description' => 'Full description'] as $field => $value) verify($pdo->query("SELECT value FROM translations WHERE locale='fa' AND field=".$pdo->quote($field))->fetchColumn() === $value, 'Full editor behavior changed');
echo "Post metadata: real SQLite/controller/CSRF, title and settings, exact article preservation, concurrent content, latest title history, hidden fields, denied access, atomic rollback and full-editor compatibility passed.\n";
