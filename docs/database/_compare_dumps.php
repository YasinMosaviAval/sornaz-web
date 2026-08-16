<?php
/**
 * Compare two phpMyAdmin SQL dumps (schema only) and print a report.
 */

function parseDump(string $path): array
{
    $fh = fopen($path, 'rb');
    if (!$fh) {
        throw new RuntimeException("Cannot open $path");
    }

    $tables = [];
    $views = [];
    $triggers = [];
    $routines = [];
    $currentTable = null;
    $buf = '';
    $inCreate = false;
    $inAlter = false;
    $inView = false;
    $inTrigger = false;
    $inRoutine = false;
    $insertCounts = [];
    $dbName = null;

    while (($line = fgets($fh)) !== false) {
        if ($dbName === null && preg_match('/Database:\s+`([^`]+)`/', $line, $m)) {
            $dbName = $m[1];
        }

        $trim = ltrim($line);

        // Skip comments except we already captured db name
        if (!$inCreate && !$inAlter && !$inView && !$inTrigger && !$inRoutine) {
            if (str_starts_with($trim, 'CREATE TABLE')) {
                $inCreate = true;
                $buf = $line;
                if (preg_match('/CREATE TABLE `([^`]+)`/', $line, $m)) {
                    $currentTable = $m[1];
                }
                if (str_contains($line, ';')) {
                    $tables[$currentTable]['create'] = trim($buf);
                    $tables[$currentTable]['columns'] = parseColumns($buf);
                    $inCreate = false;
                    $buf = '';
                }
                continue;
            }
            if (str_starts_with($trim, 'ALTER TABLE')) {
                $inAlter = true;
                $buf = $line;
                if (preg_match('/ALTER TABLE `([^`]+)`/', $line, $m)) {
                    $currentTable = $m[1];
                }
                if (str_contains($line, ';')) {
                    $tables[$currentTable]['alters'][] = trim($buf);
                    $inAlter = false;
                    $buf = '';
                }
                continue;
            }
            if (str_starts_with($trim, 'CREATE') && str_contains($trim, 'VIEW')) {
                $inView = true;
                $buf = $line;
                if (preg_match('/VIEW `([^`]+)`/', $line, $m)) {
                    $views[$m[1]] = true;
                }
                if (str_contains($line, ';')) {
                    $views[array_key_last($views)] = trim($buf);
                    $inView = false;
                    $buf = '';
                }
                continue;
            }
            if (str_starts_with($trim, 'CREATE TRIGGER') || str_starts_with($trim, 'CREATE DEFINER') && str_contains($trim, 'TRIGGER')) {
                $inTrigger = true;
                $buf = $line;
                if (preg_match('/TRIGGER `([^`]+)`/', $line, $m)) {
                    $triggers[$m[1]] = true;
                }
                if (str_contains($line, ';')) {
                    $triggers[array_key_last($triggers) ?: '_'] = trim($buf);
                    $inTrigger = false;
                    $buf = '';
                }
                continue;
            }
            if (preg_match('/^CREATE (DEFINER=.+ )?(PROCEDURE|FUNCTION) /i', $trim)) {
                $inRoutine = true;
                $buf = $line;
                if (preg_match('/(PROCEDURE|FUNCTION) `([^`]+)`/i', $line, $m)) {
                    $routines[$m[2]] = $m[1];
                }
                continue;
            }
            if (str_starts_with($trim, 'INSERT INTO')) {
                if (preg_match('/INSERT INTO `([^`]+)`/', $trim, $m)) {
                    $insertCounts[$m[1]] = ($insertCounts[$m[1]] ?? 0) + 1;
                }
            }
            continue;
        }

        $buf .= $line;

        if ($inCreate && str_contains($line, ';')) {
            $tables[$currentTable]['create'] = trim($buf);
            $tables[$currentTable]['columns'] = parseColumns($buf);
            $inCreate = false;
            $buf = '';
            continue;
        }
        if ($inAlter && str_contains($line, ';')) {
            $tables[$currentTable]['alters'][] = trim($buf);
            $inAlter = false;
            $buf = '';
            continue;
        }
        if ($inView && str_contains($line, ';')) {
            $name = null;
            if (preg_match('/VIEW `([^`]+)`/', $buf, $m)) {
                $name = $m[1];
            }
            if ($name) {
                $views[$name] = trim($buf);
            }
            $inView = false;
            $buf = '';
            continue;
        }
        if ($inTrigger && (str_contains($line, ';') || str_starts_with(ltrim($line), 'DELIMITER'))) {
            $name = null;
            if (preg_match('/TRIGGER `([^`]+)`/', $buf, $m)) {
                $name = $m[1];
            }
            if ($name) {
                $triggers[$name] = trim($buf);
            }
            $inTrigger = false;
            $buf = '';
            continue;
        }
        if ($inRoutine && preg_match('/^END\s*;;?\s*$/i', trim($line))) {
            $inRoutine = false;
            $buf = '';
            continue;
        }
    }
    fclose($fh);

    foreach ($tables as $name => &$info) {
        $info['indexes'] = parseIndexes($info['alters'] ?? []);
        $info['auto_increment'] = parseAutoIncrement($info['alters'] ?? []);
        $info['engine'] = parseEngine($info['create'] ?? '');
        $info['insert_batches'] = $insertCounts[$name] ?? 0;
    }
    unset($info);

    return [
        'db' => $dbName,
        'tables' => $tables,
        'views' => $views,
        'triggers' => $triggers,
        'routines' => $routines,
        'insert_counts' => $insertCounts,
    ];
}

function parseColumns(string $create): array
{
    $cols = [];
    // Extract body between first ( and last )
    $start = strpos($create, '(');
    $end = strrpos($create, ')');
    if ($start === false || $end === false) {
        return $cols;
    }
    $body = substr($create, $start + 1, $end - $start - 1);
    $parts = splitSqlList($body);
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '') {
            continue;
        }
        // skip table-level constraints inside CREATE (rare in phpMyAdmin)
        if (preg_match('/^(PRIMARY KEY|UNIQUE KEY|KEY|CONSTRAINT|FULLTEXT|SPATIAL|INDEX|CHECK)\b/i', $part)) {
            $cols['_inline_indexes'][] = $part;
            continue;
        }
        if (preg_match('/^`([^`]+)`\s+(.+)$/s', $part, $m)) {
            $cols[$m[1]] = normalizeColDef($m[2]);
        }
    }
    return $cols;
}

function normalizeColDef(string $def): string
{
    $def = preg_replace('/\s+/', ' ', trim($def));
    $def = str_replace('CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci', '', $def);
    $def = preg_replace('/\s+/', ' ', trim($def));
    return $def;
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
            if ($ch === $strChar && ($i === 0 || $body[$i - 1] !== '\\')) {
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

function parseIndexes(array $alters): array
{
    $idx = [
        'primary' => null,
        'keys' => [],
        'uniques' => [],
        'fulltext' => [],
        'fks' => [],
    ];
    foreach ($alters as $sql) {
        // strip ALTER TABLE `x`
        $sql = preg_replace('/^ALTER TABLE `[^`]+`\s*/i', '', $sql);
        $sql = rtrim($sql, ';');
        $parts = splitSqlList($sql);
        foreach ($parts as $part) {
            $part = trim($part);
            if (preg_match('/^ADD PRIMARY KEY\s*\((.+)\)/i', $part, $m)) {
                $idx['primary'] = normalizeKeyCols($m[1]);
            } elseif (preg_match('/^ADD UNIQUE KEY `([^`]+)`\s*\((.+)\)/i', $part, $m)) {
                $idx['uniques'][$m[1]] = normalizeKeyCols($m[2]);
            } elseif (preg_match('/^ADD (?:KEY|INDEX) `([^`]+)`\s*\((.+)\)/i', $part, $m)) {
                $idx['keys'][$m[1]] = normalizeKeyCols($m[2]);
            } elseif (preg_match('/^ADD FULLTEXT(?: KEY)? `([^`]+)`\s*\((.+)\)/i', $part, $m)) {
                $idx['fulltext'][$m[1]] = normalizeKeyCols($m[2]);
            } elseif (preg_match('/^ADD CONSTRAINT `([^`]+)` FOREIGN KEY\s*\((.+)\)\s*REFERENCES `([^`]+)`\s*\((.+)\)(.*)$/i', $part, $m)) {
                $idx['fks'][$m[1]] = [
                    'cols' => normalizeKeyCols($m[2]),
                    'ref_table' => $m[3],
                    'ref_cols' => normalizeKeyCols($m[4]),
                    'rest' => trim(preg_replace('/\s+/', ' ', $m[5])),
                ];
            }
        }
    }
    return $idx;
}

function normalizeKeyCols(string $cols): string
{
    $cols = preg_replace('/\s+/', '', $cols);
    return $cols;
}

function parseAutoIncrement(array $alters): ?int
{
    foreach ($alters as $sql) {
        if (preg_match('/AUTO_INCREMENT=(\d+)/i', $sql, $m)) {
            return (int)$m[1];
        }
    }
    return null;
}

function parseEngine(string $create): string
{
    if (preg_match('/ENGINE=(\w+)/i', $create, $m)) {
        return $m[1];
    }
    return '';
}

function colTypeOnly(string $def): string
{
    // first token(s) until DEFAULT/NOT/NULL/AUTO/COMMENT/ON/COLLATE/CHARACTER
    if (preg_match('/^((?:tiny|small|medium|big)?int(?:\(\d+\))?(?:\s+UNSIGNED)?|decimal\(\d+,\d+\)|float(?:\(\d+,\d+\))?|double(?:\(\d+,\d+\))?|datetime(?:\(\d+\))?|timestamp(?:\(\d+\))?|date|time(?:\(\d+\))?|year|char\(\d+\)|varchar\(\d+\)|tinytext|text|mediumtext|longtext|tinyblob|blob|mediumblob|longblob|json|enum\([^)]+\)|set\([^)]+\))/i', $def, $m)) {
        return strtolower($m[1]);
    }
    return strtolower(explode(' ', $def)[0]);
}

$web = parseDump(__DIR__ . '/localhost.sql');
$off = parseDump(__DIR__ . '/sornazco_maindb.sql');

$report = [];
$report[] = "WEBSITE DB: " . ($web['db'] ?? '?') . " tables=" . count($web['tables']) . " views=" . count($web['views']);
$report[] = "OFFLINE  DB: " . ($off['db'] ?? '?') . " tables=" . count($off['tables']) . " views=" . count($off['views']);
$report[] = "";

$webTables = array_keys($web['tables']);
$offTables = array_keys($off['tables']);
sort($webTables);
sort($offTables);

$onlyWeb = array_values(array_diff($webTables, $offTables));
$onlyOff = array_values(array_diff($offTables, $webTables));
$both = array_values(array_intersect($webTables, $offTables));

$report[] = "=== TABLES ONLY IN WEBSITE (localhost) ===";
$report[] = $onlyWeb ? implode("\n", $onlyWeb) : "(none)";
$report[] = "";
$report[] = "=== TABLES ONLY IN OFFLINE (sornazco) ===";
$report[] = $onlyOff ? implode("\n", $onlyOff) : "(none)";
$report[] = "";

$colDiffs = [];
$idxDiffs = [];
$engineDiffs = [];

foreach ($both as $t) {
    $wc = $web['tables'][$t]['columns'] ?? [];
    $oc = $off['tables'][$t]['columns'] ?? [];
    unset($wc['_inline_indexes'], $oc['_inline_indexes']);

    $onlyW = array_diff(array_keys($wc), array_keys($oc));
    $onlyO = array_diff(array_keys($oc), array_keys($wc));
    $changed = [];
    foreach (array_intersect(array_keys($wc), array_keys($oc)) as $c) {
        if ($wc[$c] !== $oc[$c]) {
            $changed[$c] = ['web' => $wc[$c], 'off' => $oc[$c]];
        }
    }
    if ($onlyW || $onlyO || $changed) {
        $colDiffs[$t] = ['only_web' => array_values($onlyW), 'only_off' => array_values($onlyO), 'changed' => $changed];
    }

    $wi = $web['tables'][$t]['indexes'] ?? [];
    $oi = $off['tables'][$t]['indexes'] ?? [];
    $id = [];
    if (($wi['primary'] ?? null) !== ($oi['primary'] ?? null)) {
        $id['primary'] = ['web' => $wi['primary'] ?? null, 'off' => $oi['primary'] ?? null];
    }
    foreach (['keys', 'uniques', 'fulltext'] as $kind) {
        $wk = $wi[$kind] ?? [];
        $ok = $oi[$kind] ?? [];
        $onlyWk = array_diff_key($wk, $ok);
        $onlyOk = array_diff_key($ok, $wk);
        $chgK = [];
        foreach (array_intersect_key($wk, $ok) as $n => $def) {
            if ($wk[$n] !== $ok[$n]) {
                $chgK[$n] = ['web' => $wk[$n], 'off' => $ok[$n]];
            }
        }
        if ($onlyWk || $onlyOk || $chgK) {
            $id[$kind] = [
                'only_web' => $onlyWk,
                'only_off' => $onlyOk,
                'changed' => $chgK,
            ];
        }
    }
    $wfk = $wi['fks'] ?? [];
    $ofk = $oi['fks'] ?? [];
    $onlyWfk = array_diff_key($wfk, $ofk);
    $onlyOfk = array_diff_key($ofk, $wfk);
    $chgFk = [];
    foreach (array_intersect_key($wfk, $ofk) as $n => $def) {
        if ($wfk[$n] !== $ofk[$n]) {
            $chgFk[$n] = ['web' => $wfk[$n], 'off' => $ofk[$n]];
        }
    }
    if ($onlyWfk || $onlyOfk || $chgFk) {
        $id['fks'] = ['only_web' => $onlyWfk, 'only_off' => $onlyOfk, 'changed' => $chgFk];
    }
    if ($id) {
        $idxDiffs[$t] = $id;
    }

    $we = $web['tables'][$t]['engine'] ?? '';
    $oe = $off['tables'][$t]['engine'] ?? '';
    if ($we !== $oe) {
        $engineDiffs[$t] = ['web' => $we, 'off' => $oe];
    }
}

$report[] = "=== COLUMN DIFFS (" . count($colDiffs) . " tables) ===";
foreach ($colDiffs as $t => $d) {
    $report[] = "-- $t";
    if ($d['only_web']) {
        $report[] = "  only website: " . implode(', ', $d['only_web']);
    }
    if ($d['only_off']) {
        $report[] = "  only offline: " . implode(', ', $d['only_off']);
    }
    foreach ($d['changed'] as $c => $defs) {
        $report[] = "  CHANGED `$c`";
        $report[] = "    WEB: {$defs['web']}";
        $report[] = "    OFF: {$defs['off']}";
    }
}
$report[] = "";
$report[] = "=== INDEX / FK DIFFS (" . count($idxDiffs) . " tables) ===";
foreach ($idxDiffs as $t => $d) {
    $report[] = "-- $t";
    $report[] = json_encode($d, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
$report[] = "";
$report[] = "=== ENGINE DIFFS ===";
$report[] = $engineDiffs ? json_encode($engineDiffs, JSON_PRETTY_PRINT) : "(none)";
$report[] = "";

$onlyWebViews = array_diff(array_keys($web['views']), array_keys($off['views']));
$onlyOffViews = array_diff(array_keys($off['views']), array_keys($web['views']));
$report[] = "=== VIEWS only website === " . ($onlyWebViews ? implode(',', $onlyWebViews) : '(none)');
$report[] = "=== VIEWS only offline === " . ($onlyOffViews ? implode(',', $onlyOffViews) : '(none)');

$report[] = "";
$report[] = "=== INSERT BATCH COUNTS (tables with different batch counts) ===";
$allInsertTables = array_unique(array_merge(array_keys($web['insert_counts']), array_keys($off['insert_counts'])));
sort($allInsertTables);
foreach ($allInsertTables as $t) {
    $a = $web['insert_counts'][$t] ?? 0;
    $b = $off['insert_counts'][$t] ?? 0;
    if ($a !== $b) {
        $report[] = "$t\tweb_batches=$a\toff_batches=$b";
    }
}

$out = implode("\n", $report);
file_put_contents(__DIR__ . '/_schema_diff_report.txt', $out);
echo "Wrote _schema_diff_report.txt (" . strlen($out) . " bytes)\n";
echo "Tables web=" . count($web['tables']) . " off=" . count($off['tables']) . "\n";
echo "only web=" . count($onlyWeb) . " only off=" . count($onlyOff) . " colDiffs=" . count($colDiffs) . " idxDiffs=" . count($idxDiffs) . "\n";
