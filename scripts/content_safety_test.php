<?php
// Isolated policy tests: no application bootstrap or database connection.
require_once dirname(__DIR__) . '/core/security/ContentSafety.php';

use Core\security\ContentSafety;

$checks = 0;
function check(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) {
        throw new RuntimeException($message);
    }
    ++$checks;
}

foreach (['article', 'comment'] as $profile) {
    foreach ([
        '<script>alert(1)</script><p onclick="alert(1)">safe</p>',
        '<a href="jav&#x61;script:alert(1)">safe</a>',
        '<img src="x" onerror="alert(1)"><svg onload="alert(1)"></svg>',
        '<math><mtext><img src=x onerror=alert(1)></mtext></math>',
        '<a href="data:text/html,test">safe</a><iframe srcdoc="test"></iframe>',
        '<div style="background:url(javascript:alert(1))">safe</div>',
        '<form><input name="location"></form><object data="test"></object>',
    ] as $payload) {
        $safe = ContentSafety::rich($payload, $profile);
        check(!preg_match('/<script|<svg|<math|<iframe|<object|<form|<input|onerror|onclick|onload|javascript:|data:text/i', $safe), 'Unsafe rich output: ' . $safe);
        check(ContentSafety::rich($safe, $profile) === $safe, 'Purification must be stable');
    }
}
check(str_contains(ContentSafety::rich('<p>سلام <strong>World</strong></p>'), '<strong>World</strong>'), 'Legitimate formatting lost');
check(str_contains(ContentSafety::rich('<img src="/assets/image.png" alt="test">'), '/assets/image.png'), 'Article image lost');
check(!str_contains(ContentSafety::rich('<img src="/assets/image.png">', 'comment'), '<img'), 'Comment images allowed');
foreach (['javascript:alert(1)', '//evil.test', '/\\evil.test', "https://safe.test\n.evil.test", 'data:text/html,x'] as $url) {
    check(ContentSafety::url($url) === '', 'Unsafe URL accepted');
}
check(ContentSafety::url('/assets/test.png') === '/assets/test.png', 'Local image rejected');
check(ContentSafety::url('https://example.com/a') === 'https://example.com/a', 'HTTPS rejected');
$json = ContentSafety::json(['title' => '</script><script>alert(1)</script>']);
check(!str_contains($json, '<'), 'Inline script terminator not encoded');
check(json_decode($json, true)['title'] === '</script><script>alert(1)</script>', 'JSON data altered');
check(ContentSafety::text('a < b & c') === 'a < b & c', 'Plain text must remain unchanged');
foreach ([[], new stdClass(), "\xff", str_repeat('a', 20001)] as $value) {
    try {
        ContentSafety::text($value);
        throw new LogicException('Invalid text accepted');
    } catch (RuntimeException $error) {
        check($error->getCode() === 422, 'Wrong validation response');
    }
}
foreach (['<script>alert(1)</script>', str_repeat('a', 3001)] as $value) {
    try {
        ContentSafety::comment($value);
        throw new LogicException('Invalid comment accepted');
    } catch (RuntimeException $error) {
        check($error->getCode() === 422, 'Wrong comment response');
    }
}
// Exercise the actual public read and write boundaries with legacy stored HTML.
$map = require dirname(__DIR__) . '/vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require_once $map[$class]; });
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
function db() { return $GLOBALS['pdo']; }
function session() { return new class { public function get($key, $default = null) { return $key === 'suppress_database_notifications' ? true : $default; } }; }
function transaction($callback) { $pdo = db(); $pdo->beginTransaction(); try { $result = $callback(); $pdo->commit(); return $result; } catch (Throwable $e) { $pdo->rollBack(); throw $e; } }
$pdo->exec("CREATE TABLE posts(post_id INTEGER,status TEXT,visibility TEXT,type TEXT,deleted_at TEXT);
CREATE TABLE comments(comment_id INTEGER,post_id INTEGER,author TEXT,author_email TEXT,author_ip TEXT,has_response INTEGER,agent TEXT,parent INTEGER,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER,approved_at TEXT,approved_by INTEGER,deleted_at TEXT);
CREATE TABLE translations(table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,version INTEGER,created_by INTEGER,updated_by INTEGER,deleted_at TEXT);
INSERT INTO posts VALUES(1,'published','public','post',NULL);");
$service = new Modules\Analytics\Services\PublicCommentService();
$id = $service->store(1, ['content' => '<p onclick="bad()">Hello <strong>world</strong></p>']);
$stored = $pdo->query('SELECT value FROM translations')->fetchColumn();
check(!str_contains($stored, 'onclick') && str_contains($stored, '<strong>'), 'Comment write policy bypassed');
$pdo->exec("UPDATE comments SET approved_at='2026-09-29'");
$pdo->prepare('UPDATE translations SET value=?')->execute(['<p>Legacy</p><script>alert(1)</script>']);
$items = $service->forPost(1, 'en');
check(count($items) === 1 && !str_contains($items[0]['content'], '<script'), 'Legacy comment read policy bypassed');
$pdo->exec('CREATE TABLE f_settings(setting_id INTEGER PRIMARY KEY,variable_name TEXT,page TEXT,table_name TEXT,status TEXT,source TEXT,icon TEXT,value TEXT,created_by INTEGER,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER);
CREATE TABLE f_translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,version INTEGER,created_by INTEGER,updated_by INTEGER,deleted_at TEXT,deleted_by INTEGER)');
$pages = new Modules\Analytics\Services\SitePageContentService();
$valid = ['page' => 'home', 'kind' => 'text', 'key' => 'site.page.home.text.example', 'fa' => 'سلام', 'en' => 'Hello'];
foreach ([['en' => []], ['key' => 'site.page.login.text.example'], ['kind' => 'image', 'key' => 'site.page.home.image.example', 'value' => 'javascript:bad()']] as $invalid) {
    try { $pages->save(1, array_replace($valid, $invalid)); throw new LogicException('Invalid page input accepted'); }
    catch (RuntimeException $e) { check($e->getCode() === 422, 'Wrong page validation response'); }
}
check((int) $pdo->query('SELECT COUNT(*) FROM f_settings')->fetchColumn() === 0, 'Validation wrote partial page content');
$pages->save(1, $valid);
check($pages->content('home', 'en')[0]['value'] === 'Hello', 'Bilingual page content lost');
$pdo->exec("CREATE TRIGGER fail_page_translation BEFORE UPDATE ON f_translations WHEN NEW.locale='en' BEGIN SELECT RAISE(ABORT,'fixture failure'); END;");
try { $pages->save(1, array_replace($valid, ['fa' => 'تغییر', 'en' => 'Changed'])); throw new LogicException('Storage failure accepted'); } catch (PDOException) {}
check($pages->content('home', 'fa')[0]['value'] === 'سلام', 'Failed bilingual save was not rolled back');
echo "Content safety: $checks checks passed.\n";
