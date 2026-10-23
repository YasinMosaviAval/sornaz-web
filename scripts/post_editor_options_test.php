<?php
// Isolated SQLite fixture; never opens the application database.
require_once __DIR__ . '/../Modules/System/Services/SiteAdminAccess.php';
require_once __DIR__ . '/../Modules/Analytics/Services/AdminPostService.php';
$actor = ['user_id' => 1, 'type' => 'human'];
function auth(): object { return new class { public function user(): ?array { return $GLOBALS['actor']; } }; }
function db(): PDO { return $GLOBALS['fixture']; }
$fixture = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$fixture->exec("CREATE TABLE posts(post_id INTEGER,slug TEXT,type TEXT,status TEXT,deleted_at TEXT);
CREATE TABLE translations(translation_id INTEGER,table_name TEXT,table_id INTEGER,locale TEXT,field TEXT,value TEXT,deleted_at TEXT);
INSERT INTO posts VALUES(1,'first','post','published',NULL),(2,'draft','post','draft',NULL),(3,'page','page','published',NULL),(4,'trash','post','trash',NULL),(5,'deleted','post','published','2026-01-01');
INSERT INTO translations VALUES(1,'posts',1,'fa','title','Old',NULL),(2,'posts',1,'fa','title','Latest <title>',NULL),(3,'posts',1,'fa','content','PRIVATE CONTENT',NULL),(4,'posts',1,'fa','title','Deleted','2026-01-01');");
$service = new Modules\Analytics\Services\AdminPostService();
$options = $service->index(['editorOptions' => '1']);
if ($options !== ['posts' => [['id' => 2, 'title' => 'draft'], ['id' => 1, 'title' => 'Latest <title>']]]) throw new RuntimeException('Incorrect lightweight options or private content leak');
foreach ([null, ['user_id' => 8, 'type' => 'human'], ['user_id' => 9, 'type' => 'academy']] as $denied) {
    $actor = $denied;
    try { $service->index(['editorOptions' => '1']); throw new LogicException('Unauthorized editor options'); }
    catch (RuntimeException $error) { if ($error->getCode() !== 403) throw $error; }
}
echo "Post editor options: latest titles, draft support, lightweight fields, excluded pages/trash/deleted rows and authorization passed (SQLite fixture).\n";
