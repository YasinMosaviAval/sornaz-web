<?php
// Isolated SQLite behaviour checks, never connects to the application database.
require_once __DIR__ . '/../core/database/TableNames.php';
require_once __DIR__ . '/../core/database/PrefixedPDO.php';
use Core\database\TableNames;
use Core\database\PrefixedPDO;

function prefixCheck(bool $ok, string $label): void
{
    if (!$ok) { throw new LogicException($label); }
}
$mapping = TableNames::mapping();
prefixCheck(count($mapping) === count(array_unique($mapping)), 'Duplicate physical names');
foreach ($mapping as $logical => $physical) {
    prefixCheck(strlen($physical) <= 64 && preg_match('/^[fp]_[a-z0-9_]+$/', $physical), 'Invalid physical name');
    prefixCheck(TableNames::logical($physical) === $logical, 'Reverse mapping failed');
    prefixCheck(TableNames::physical($physical) === $physical, 'Double prefix');
}
prefixCheck(TableNames::physical('users') === 'f_users', 'Users category');
prefixCheck(TableNames::physical('academies') === 'p_academies', 'Academies category');
prefixCheck(TableNames::physical('f_settings') === 'f_settings', 'Existing f prefix');
prefixCheck(TableNames::physical('z_user_settings') === 'f_user_settings', 'Legacy z prefix');
prefixCheck(TableNames::physical('settings') === 'p_settings', 'Settings collision');
prefixCheck(TableNames::physical('translations') === 'p_translations', 'Translations collision');
$forward = file_get_contents(__DIR__ . '/../docs/database/migrations/2026_10_04_table_prefixes.sql');
$reverse = file_get_contents(__DIR__ . '/../docs/database/migrations/2026_10_04_table_prefixes_rollback.sql');
foreach ($mapping as $logical => $physical) {
    if ($logical === $physical) { continue; }
    if ($logical === 'mobile_contact_challenges') {
        $newTable = file_get_contents(__DIR__ . '/../docs/database/migrations/2026_10_09_mobile_contact_challenges.sql');
        prefixCheck(str_contains($newTable, "CREATE TABLE IF NOT EXISTS `$physical`"), 'Mobile contact migration differs from runtime map');
        continue;
    }
    prefixCheck(str_contains($forward, "`$logical` TO `$physical`"), 'Forward migration differs from runtime map');
    prefixCheck(str_contains($reverse, "`$physical` TO `$logical`"), 'Reverse migration differs from runtime map');
}
$cases = [
    'SELECT users.user_id FROM users' => 'SELECT f_users.user_id FROM f_users',
    'SELECT users.user_id FROM users AS users' => 'SELECT users.user_id FROM f_users AS users',
    'SELECT u.user_id FROM `users` u JOIN academies a ON a.user_id=u.user_id' => 'SELECT u.user_id FROM `f_users` u JOIN p_academies a ON a.user_id=u.user_id',
    'SELECT users.user_id FROM test.users' => 'SELECT f_users.user_id FROM test.f_users',
    "SELECT 'FROM users', \"JOIN academies\" FROM users -- JOIN academies\n" => "SELECT 'FROM users', \"JOIN academies\" FROM f_users -- JOIN academies\n",
    "SELECT * FROM translations WHERE table_name='users'" => "SELECT * FROM p_translations WHERE table_name='users'",
    'SELECT * FROM users u, academies a WHERE a.user_id=u.user_id' => 'SELECT * FROM f_users u, p_academies a WHERE a.user_id=u.user_id',
    'SHOW COLUMNS FROM `users`' => 'SHOW COLUMNS FROM `f_users`',
    'CREATE INDEX idx ON users(user_id)' => 'CREATE INDEX idx ON f_users(user_id)',
    'SELECT * FROM f_users' => 'SELECT * FROM f_users',
    'SELECT * FROM users; SELECT user_id, users FROM unrelated' => 'SELECT * FROM f_users; SELECT user_id, users FROM unrelated',
];
foreach ($cases as $before => $after) { prefixCheck(TableNames::sql($before) === $after, 'SQL mapping: ' . $before); }
$pdo = new PrefixedPDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE TABLE users(user_id INTEGER PRIMARY KEY, academy_id INTEGER, username TEXT);
CREATE TABLE academies(academy_id INTEGER PRIMARY KEY, user_id INTEGER);
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY, table_name TEXT, table_id INTEGER, value TEXT);
CREATE TABLE z_user_settings(user_id INTEGER, value TEXT);');
$insert = $pdo->prepare('INSERT INTO users(user_id,academy_id,username) VALUES(?,?,?)');
$insert->execute([1,10,'users']);
$insert->execute([2,20,'other']);
$pdo->exec('INSERT INTO academies VALUES(10,1),(20,2)');
$statement = $pdo->prepare('INSERT INTO translations VALUES(?,?,?,?)');
$statement->execute([1,'users',1,'JOIN users']);
$statement = $pdo->prepare('SELECT u.username FROM users u JOIN academies a ON a.user_id=u.user_id WHERE a.academy_id=? AND u.user_id=?');
$statement->execute([10,1]);
prefixCheck($statement->fetchColumn() === 'users', 'Owned join failed');
$statement->execute([20,1]);
prefixCheck($statement->fetchColumn() === false, 'Cross-tenant query admitted');
prefixCheck($pdo->query("SELECT value FROM translations WHERE table_name='users' AND table_id=1")->fetchColumn() === 'JOIN users', 'Stored logical names changed');
$pdo->beginTransaction();
$pdo->exec("UPDATE users SET username='rolled-back' WHERE user_id=1");
$pdo->rollBack();
prefixCheck($pdo->query('SELECT users.username FROM users WHERE user_id=1')->fetchColumn() === 'users', 'Rollback changed');
$pdo->exec("INSERT INTO z_user_settings VALUES(1,'private')");
prefixCheck($pdo->query('SELECT value FROM z_user_settings WHERE user_id=1')->fetchColumn() === 'private', 'User settings unavailable');
$names = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
prefixCheck(in_array('f_users', $names, true) && !in_array('users', $names, true), 'Logical tables created');
echo "Table prefixes: SQL, native bindings, tenant isolation, logical values and rollback passed.\n";
