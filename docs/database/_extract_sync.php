<?php

function extractCreateAndAlters(string $path, array $tables): array
{
    $wanted = array_flip($tables);
    $fh = fopen($path, 'rb');
    $out = [];
    $in = false;
    $kind = null;
    $cur = '';
    $name = null;

    while (($line = fgets($fh)) !== false) {
        $trim = ltrim($line);
        if (!$in) {
            if (preg_match('/^CREATE TABLE `([^`]+)`/', $trim, $m) && isset($wanted[$m[1]])) {
                $in = true;
                $kind = 'create';
                $name = $m[1];
                $cur = $line;
                if (str_contains($line, ';')) {
                    $out[$name]['create'] = trim($cur);
                    $in = false;
                }
                continue;
            }
            if (preg_match('/^ALTER TABLE `([^`]+)`/', $trim, $m) && isset($wanted[$m[1]])) {
                $in = true;
                $kind = 'alter';
                $name = $m[1];
                $cur = $line;
                if (str_contains($line, ';')) {
                    $out[$name]['alters'][] = trim($cur);
                    $in = false;
                }
                continue;
            }
            continue;
        }
        $cur .= $line;
        if (str_contains($line, ';')) {
            if ($kind === 'create') {
                $out[$name]['create'] = trim($cur);
            } else {
                $out[$name]['alters'][] = trim($cur);
            }
            $in = false;
            $cur = '';
        }
    }
    fclose($fh);
    return $out;
}

function extractInserts(string $path, array $tables): array
{
    $wanted = array_flip($tables);
    $fh = fopen($path, 'rb');
    $out = [];
    $in = false;
    $cur = '';
    $name = null;

    while (($line = fgets($fh)) !== false) {
        $trim = ltrim($line);
        if (!$in) {
            if (preg_match('/^INSERT INTO `([^`]+)`/', $trim, $m) && isset($wanted[$m[1]])) {
                $in = true;
                $name = $m[1];
                $cur = $line;
                if (str_contains($line, ';')) {
                    $out[$name][] = trim($cur);
                    $in = false;
                }
                continue;
            }
            continue;
        }
        $cur .= $line;
        if (str_contains($line, ';')) {
            $out[$name][] = trim($cur);
            $in = false;
            $cur = '';
        }
    }
    fclose($fh);
    return $out;
}

function parseInsertRows(array $inserts): array
{
    $rows = [];
    foreach ($inserts as $sql) {
        if (!preg_match('/INSERT INTO `([^`]+)`\s*(?:\(([^)]+)\))?\s*VALUES\s*(.+);$/s', $sql, $m)) {
            continue;
        }
        $cols = $m[2] !== '' ? array_map(fn($c) => trim($c, " `\n\r\t"), explode(',', $m[2])) : null;
        $valuesSql = $m[3];
        $tuples = splitTuples($valuesSql);
        foreach ($tuples as $tuple) {
            $vals = splitSqlList(trim($tuple, " \n\r\t()"));
            $vals = array_map('trim', $vals);
            if ($cols) {
                $row = [];
                foreach ($cols as $i => $c) {
                    $row[$c] = $vals[$i] ?? null;
                }
                $rows[] = $row;
            } else {
                $rows[] = $vals;
            }
        }
    }
    return $rows;
}

function splitTuples(string $valuesSql): array
{
    $parts = [];
    $cur = '';
    $depth = 0;
    $inStr = false;
    $strChar = '';
    $len = strlen($valuesSql);
    for ($i = 0; $i < $len; $i++) {
        $ch = $valuesSql[$i];
        if ($inStr) {
            $cur .= $ch;
            if ($ch === $strChar) {
                // handle escaped quotes ''
                if ($strChar === "'" && ($i + 1) < $len && $valuesSql[$i + 1] === "'") {
                    $cur .= $valuesSql[++$i];
                    continue;
                }
                $inStr = false;
            }
            continue;
        }
        if ($ch === "'" || $ch === '"' || $ch === '`') {
            $inStr = true;
            $strChar = $ch;
            $cur .= $ch;
            continue;
        }
        if ($ch === '(') {
            $depth++;
            $cur .= $ch;
            continue;
        }
        if ($ch === ')') {
            $depth--;
            $cur .= $ch;
            if ($depth === 0) {
                $parts[] = $cur;
                $cur = '';
            }
            continue;
        }
        if ($depth === 0) {
            continue;
        }
        $cur .= $ch;
    }
    return $parts;
}

function splitSqlList(string $body): array
{
    $parts = [];
    $cur = '';
    $depth = 0;
    $inStr = false;
    $strChar = '';
    $len = strlen($body);
    for ($i = 0; $i < $len; $i++) {
        $ch = $body[$i];
        if ($inStr) {
            $cur .= $ch;
            if ($ch === $strChar) {
                if ($strChar === "'" && ($i + 1) < $len && $body[$i + 1] === "'") {
                    $cur .= $body[++$i];
                    continue;
                }
                $inStr = false;
            }
            continue;
        }
        if ($ch === "'" || $ch === '"' || $ch === '`') {
            $inStr = true;
            $strChar = $ch;
            $cur .= $ch;
            continue;
        }
        if ($ch === '(') {
            $depth++;
            $cur .= $ch;
            continue;
        }
        if ($ch === ')') {
            $depth--;
            $cur .= $ch;
            continue;
        }
        if ($ch === ',' && $depth === 0) {
            $parts[] = $cur;
            $cur = '';
            continue;
        }
        $cur .= $ch;
    }
    if (trim($cur) !== '') {
        $parts[] = $cur;
    }
    return $parts;
}

function unquote($v)
{
    if ($v === null || strtoupper((string)$v) === 'NULL') {
        return null;
    }
    $v = (string)$v;
    if (strlen($v) >= 2 && $v[0] === "'" && substr($v, -1) === "'") {
        return str_replace("''", "'", substr($v, 1, -1));
    }
    return $v;
}

$missingTables = [
    'academy_documents',
    'tracking_ingestion_batches',
    'tracking_user_activity_intervals',
    'tracking_user_consents',
    'tracking_user_content_engagements',
    'user_referrals',
];

$seedTables = [
    'access_system_permissions',
    'access_system_roles',
    'access_system_role_permissions',
    'categories',
    'f_translations',
    'z_settings',
    'posts',
];

$schema = extractCreateAndAlters(__DIR__ . '/sornazco_maindb.sql', $missingTables);
file_put_contents(__DIR__ . '/_offline_missing_tables.json', json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

$webIns = extractInserts(__DIR__ . '/localhost.sql', $seedTables);
$offIns = extractInserts(__DIR__ . '/sornazco_maindb.sql', $seedTables);

$summary = [];
foreach ($seedTables as $t) {
    $w = parseInsertRows($webIns[$t] ?? []);
    $o = parseInsertRows($offIns[$t] ?? []);
    $summary[$t] = [
        'web_rows' => count($w),
        'off_rows' => count($o),
    ];

    // write compact dumps for later comparison
    file_put_contents(__DIR__ . "/_rows_web_{$t}.json", json_encode($w, JSON_UNESCAPED_UNICODE));
    file_put_contents(__DIR__ . "/_rows_off_{$t}.json", json_encode($o, JSON_UNESCAPED_UNICODE));
}

echo json_encode($summary, JSON_PRETTY_PRINT), "\n";
echo "schema tables extracted: ", implode(',', array_keys($schema)), "\n";
