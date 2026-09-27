<?php
// Isolated database and authentication fixtures; does not boot the application.
require __DIR__.'/../vendor/composer/ClassLoader.php';
$loader = new Composer\Autoload\ClassLoader();
$loader->addClassMap(require __DIR__.'/../vendor/composer/autoload_classmap.php');
$loader->register();
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
function db() { global $pdo; return $pdo; }
function auth() { return new class { public function id() { return $GLOBALS['actor'] ?? null; } }; }
$pdo->exec('CREATE TABLE f_settings(setting_id INTEGER,variable_name TEXT,table_name TEXT,deleted_at TEXT);
CREATE TABLE f_translations(table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,deleted_at TEXT)');
$settings=$pdo->prepare('INSERT INTO f_settings VALUES(?,?,?,?)');
$translations=$pdo->prepare("INSERT INTO f_translations VALUES('f_settings',?,'value',?,?,NULL)");
$key='admin.ui.'.substr(sha1('داشبورد'),0,16);
foreach ([[1,$key,'admin_ui',null],[2,'site.inline.example','site_ui',null],[3,'private.setting','security',null],[4,'site.inline.removed','site_ui','2026-01-01']] as $row) {
    $settings->execute($row);
    $translations->execute([$row[0],'fa','عنوان جدید']);
    $translations->execute([$row[0],'en','Updated title']);
}
$items=(new Modules\Analytics\Services\AdminTestDataService())->inlineTranslationCatalog(true);
if (array_column($items,'key') !== [$key,'site.inline.example']) throw new RuntimeException('Public catalog leaked unrelated/deleted settings');
if ($items[0]['aliases'] !== ['داشبورد','Dashboard']) throw new RuntimeException('Legacy source labels were lost');
foreach ([null,2,7] as $actor) {
    ob_start(); require __DIR__.'/../Modules/Analytics/Resources/Views/sections/guides.php';
    if (trim(ob_get_clean()) !== '') throw new RuntimeException('Guides rendered for another user');
    ob_start(); require __DIR__.'/../Modules/Analytics/Resources/Views/sections/panel-help-template.php';
    if (trim(ob_get_clean()) !== '') throw new RuntimeException('Help template rendered for another user');
}
$actor=1;
ob_start(); require __DIR__.'/../Modules/Analytics/Resources/Views/sections/guides.php';
if (!str_contains(ob_get_clean(),'id="guides"')) throw new RuntimeException('Founder lost guides');
echo "Public translations and guide visibility checks passed.\n";
