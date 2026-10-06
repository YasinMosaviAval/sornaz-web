<?php
// Optional integration suite; creates/deletes only its random test database.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../core/database/TableNames.php';
require_once __DIR__ . '/../core/database/PrefixedPDO.php';
$dsn = getenv('SORNAZ_TEST_MYSQL_DSN');
if (!$dsn) { fwrite(STDERR, "Set isolated-test SORNAZ_TEST_MYSQL_DSN/USER/PASSWORD.\n"); exit(2); }
$fixture = 'sornaz_prefix_schema_test_' . bin2hex(random_bytes(6));
$pdo = null;
$created = false;
$failed = false;
try {
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
    $pdo = new PDO($dsn, getenv('SORNAZ_TEST_MYSQL_USER') ?: '', getenv('SORNAZ_TEST_MYSQL_PASSWORD') ?: '', $options);
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'mysql') { throw new LogicException('MySQL required'); }
    $pdo->exec("CREATE DATABASE `$fixture` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $created = true;
    $pdo->exec("USE `$fixture`");
    // Required external content dependency, synthetic and without application data.
    $pdo->exec('CREATE TABLE f_posts(post_id INT UNSIGNED PRIMARY KEY) ENGINE=InnoDB');
    $schemas = ['Modules/Social/schema.sql', 'Modules/Social/profile-schema.sql', 'Modules/Social/comments-schema.sql', 'Modules/Notation/schema.sql', 'Modules/CourseMarket/schema.sql', 'Modules/CourseMarket/course-experience-schema.sql'];
    foreach ($schemas as $schema) { $pdo->exec(file_get_contents(__DIR__ . '/../' . $schema)); }
    $names = $pdo->query('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE()')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($names as $name) {
        if (!preg_match('/^[fp]_/', $name)) { throw new LogicException('Unprefixed install table'); }
    }
    $fks = $pdo->query('SELECT REFERENCED_TABLE_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($fks as $target) { if (!in_array($target, $names, true)) { throw new LogicException('Missing FK target'); } }
    foreach ($schemas as $schema) { $pdo->exec(file_get_contents(__DIR__ . '/../' . $schema)); }
    $runtime = new \Core\database\PrefixedPDO($dsn, getenv('SORNAZ_TEST_MYSQL_USER') ?: '', getenv('SORNAZ_TEST_MYSQL_PASSWORD') ?: '', $options);
    $runtime->exec("USE `$fixture`");
    $runtime->exec("INSERT INTO social_profiles(user_id,display_name) VALUES(101,'prefix-fixture')");
    $query = $runtime->prepare('SELECT display_name FROM social_profiles WHERE user_id=?');
    $query->execute([101]);
    if ($query->fetchColumn() !== 'prefix-fixture') { throw new LogicException('Installed schema unreachable through runtime mapping'); }
    echo 'MySQL install schemas: six files, ', count($names), ' physical tables, ', count($fks), " FK columns, repeated installation and runtime bindings passed.\n";
} catch (Throwable $error) {
    fwrite(STDERR, 'Isolated schema test failed (' . get_class($error) . "). Application data untouched.\n");
    $failed = true;
} finally {
    if ($pdo && $created) { $pdo->exec("DROP DATABASE `$fixture`"); }
}
exit($failed ? 1 : 0);
