<?php
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require_once $map[$class]; });
$pdo = new \Core\database\PrefixedPDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
function db() { return $GLOBALS['pdo']; }
function locale() { return 'fa'; }
$pdo->exec("CREATE TABLE users(user_id INTEGER PRIMARY KEY, username TEXT, type TEXT, deleted_at TEXT);
INSERT INTO users VALUES(2,'recipient','human',NULL),(3,'sender','human',NULL),(4,'other','human',NULL);
CREATE TABLE academies(academy_id INTEGER,user_id INTEGER,created_by INTEGER,deleted_at TEXT);
CREATE TABLE academy_branches(branch_id INTEGER,academy_id INTEGER,user_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_members(member_id INTEGER,academy_id INTEGER,branch_id INTEGER,user_id INTEGER,deleted_at TEXT);
CREATE TABLE user_messages(user_message_id INTEGER PRIMARY KEY,sender_id INTEGER,receiver_user_id INTEGER,type TEXT,status TEXT,is_read INTEGER,related_entity_type TEXT,related_entity_id INTEGER,created_at TEXT,updated_at TEXT,deleted_at TEXT);
INSERT INTO user_messages VALUES(10,3,2,'message','published',0,NULL,NULL,'2026-10-07 10:00:00','2026-10-07 10:00:00',NULL),(11,3,4,'message','published',0,NULL,NULL,'2026-10-07 10:00:00','2026-10-07 10:00:00',NULL),(12,3,2,'notification','published',0,NULL,NULL,'2026-10-07 10:00:00','2026-10-07 10:00:00',NULL),(13,3,4,'notification','published',0,NULL,NULL,'2026-10-07 10:00:00','2026-10-07 10:00:00',NULL);
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,deleted_at TEXT);");
$messages = (new \Modules\Analytics\Services\AdminMessageService())->index(2);
if (array_column($messages['messages'], 'id') !== [10] || $messages['unread']['messages'] !== 1 || $messages['unread']['notifications'] !== 1) {
    throw new LogicException('Personal message boundary failed.');
}
$notifications = (new \Modules\Analytics\Services\AdminNotificationService())->all(2);
if (array_column($notifications, 'id') !== [12]) {
    throw new LogicException('Personal notification boundary failed.');
}
echo "Panel inbox: personal message and notification boundaries passed.\n";
