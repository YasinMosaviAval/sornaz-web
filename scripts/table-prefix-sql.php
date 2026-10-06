<?php
// Converts static SQL before a CLI/phpMyAdmin import; never executes SQL.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../core/database/TableNames.php';
$input = $argv[1] ?? '';
$output = $argv[2] ?? '';
if (strtolower(pathinfo($input, PATHINFO_EXTENSION)) !== 'sql' || strtolower(pathinfo($output, PATHINFO_EXTENSION)) !== 'sql' || !is_file($input) || file_exists($output)) {
    fwrite(STDERR, "Usage: php scripts/table-prefix-sql.php input.sql new-output.sql (output must not exist).\n"); exit(1);
}
$sql = file_get_contents($input);
if ($sql === false || preg_match('/\b(?:PREPARE|EXECUTE|information_schema)\b/i', $sql)) {
    fwrite(STDERR, "Dynamic SQL or metadata queries require a reviewed physical-name migration. No output written.\n"); exit(1);
}
$converted = \Core\database\TableNames::sql($sql);
if (file_put_contents($output, $converted, LOCK_EX) === false) { fwrite(STDERR, "Unable to write output.\n"); exit(1); }
echo "Static SQL converted; no database connection or execution.\n";
