<?php
// Isolated API fixture; no production account, filesystem upload, or payment calls.
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function (string $class) use ($map): void { if (isset($map[$class])) require_once $map[$class]; });
require_once __DIR__ . '/../Modules/Analytics/Services/PersonalPanelDataService.php';
require_once __DIR__ . '/../Modules/Analytics/Controllers/Web/PersonalPanelDataController.php';
$GLOBALS['pdo'] = new \Core\database\PrefixedPDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
function db() { return $GLOBALS['pdo']; }
function locale() { return 'fa'; }
function auth() { return new class { public function id(): int { return 2; } }; }
function request() { return new class { public function input(string $key, mixed $default = null): mixed { return $GLOBALS['inputs'][$key] ?? $default; } }; }
function session() { return new class { public function get(string $key, mixed $default = null): mixed { return $key === 'suppress_database_notifications' ? true : $default; } }; }
function transaction(callable $work) { db()->beginTransaction(); try { $result = $work(); db()->commit(); return $result; } catch (Throwable $error) { db()->rollBack(); throw $error; } }
$pdo = db();
$pdo->exec("CREATE TABLE z_settings(setting_id INTEGER PRIMARY KEY,`key` TEXT,value TEXT,deleted_at TEXT);
CREATE TABLE z_user_settings(user_setting_id INTEGER PRIMARY KEY,user_id INTEGER,`key` TEXT,value TEXT,type TEXT,visibility TEXT,created_by INTEGER,updated_by INTEGER,deleted_at TEXT);
CREATE TABLE media_files(media_file_id INTEGER PRIMARY KEY,user_id INTEGER,deleted_at TEXT);
INSERT INTO media_files VALUES(7,3,NULL);");
$controller = new \Modules\Analytics\Controllers\Web\PersonalPanelDataController(new \Modules\Analytics\Services\PersonalPanelDataService(), new \Modules\Analytics\Services\AdminGalleryService(), new \Modules\Analytics\Services\AdminSettingService());
$payload = static fn (array $data): array => ['payload_b64' => base64_encode(json_encode($data))];
$GLOBALS['inputs'] = $payload(['language' => 'en', 'fontWeight' => 3]);
$saved = $controller->saveSettings();
$rows = $pdo->query("SELECT `key`,value FROM z_user_settings WHERE user_id=2 ORDER BY `key`")->fetchAll(PDO::FETCH_KEY_PAIR);
if ($rows !== ['panel_fontWeight' => '3', 'panel_language' => 'en']) throw new LogicException('Personal settings were not saved for the current user.');
$GLOBALS['inputs'] = $payload(['colorTheme' => 'invalid']);
$controller->saveSettings();
if ((int) $pdo->query('SELECT COUNT(*) FROM z_user_settings')->fetchColumn() !== 2) throw new LogicException('Invalid preference was persisted.');
$controller->deleteGallery(7);
if ($pdo->query('SELECT deleted_at FROM media_files WHERE media_file_id=7')->fetchColumn() !== null) throw new LogicException('Cross-user gallery item was deleted.');
echo "Personal panel API: current-user settings and foreign media denial passed.\n";
