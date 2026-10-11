<?php
require __DIR__ . '/../Modules/CourseMarket/Repositories/CourseRepository.php';
require __DIR__ . '/../Modules/Notation/Services/NotationService.php';

use Modules\CourseMarket\Repositories\CourseRepository;
use Modules\Notation\Services\NotationService;

$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE users (user_id INTEGER, username TEXT, deleted_at TEXT)');
$db->exec('CREATE TABLE music_sheets (id INTEGER, owner_id INTEGER, title TEXT, metadata TEXT, visibility TEXT, version INTEGER, updated_at TEXT, deleted_at TEXT)');
$db->exec('CREATE TABLE music_sheet_bookmarks (sheet_id INTEGER, user_id INTEGER)');
$db->exec("INSERT INTO users VALUES (1, 'composer', NULL)");
$db->exec("INSERT INTO music_sheets VALUES (1, 1, 'Moonlight Sonata', '{\"composer\":\"Beethoven\"}', 'public', 1, '2026-01-01', NULL)");
$db->exec("INSERT INTO music_sheets VALUES (2, 1, 'Prelude', '{\"composer\":\"Bach\"}', 'public', 1, '2026-01-02', NULL)");
$db->exec("INSERT INTO music_sheets VALUES (3, 1, '100% Music', '{}', 'public', 1, '2026-01-03', NULL)");
$service = new NotationService(new CourseRepository($db));
foreach (['Sonata' => 1, 'Bach' => 2, '%' => 3] as $query => $expected) {
    $result = $service->listing(1, 'all', 1, $query);
    if (count($result['items']) !== 1 || $result['items'][0]['id'] !== $expected) {
        throw new Exception("Search failed for {$query}");
    }
}
if (count($service->listing(1, 'all')['items']) !== 3) throw new Exception('Unfiltered listing changed');
try {
    $service->listing(1, 'all', 1, str_repeat('x', 101));
    throw new Exception('Long search text was accepted');
} catch (RuntimeException $error) {
    if ($error->getCode() !== 422) throw $error;
}
echo "Notation search passed.\n";
