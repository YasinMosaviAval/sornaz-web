<?php
function load($name, $side) {
    return json_decode(file_get_contents(__DIR__ . "/_rows_{$side}_{$name}.json"), true) ?: [];
}
function idx($rows, $pk) {
    $o = [];
    foreach ($rows as $r) $o[(string)$r[$pk]] = $r;
    return $o;
}
$wr = idx(load('access_system_roles','web'), 'role_id');
$or = idx(load('access_system_roles','off'), 'role_id');
echo "=== EXISTING ROLES 1-19 COMPARE ===\n";
foreach (range(1,19) as $id) {
    $a = $wr[$id] ?? [];
    $b = $or[$id] ?? [];
    $keys = array_unique(array_merge(array_keys($a), array_keys($b)));
    $diffs = [];
    foreach ($keys as $k) {
        if (($a[$k] ?? '∅') !== ($b[$k] ?? '∅')) {
            $diffs[$k] = ['web'=>$a[$k]??'∅','off'=>$b[$k]??'∅'];
        }
    }
    echo "role $id name_web=".($a['name']??'?')." name_off=".($b['name']??'?')."\n";
    if ($diffs) {
        echo "  ".json_encode($diffs, JSON_UNESCAPED_UNICODE)."\n";
    }
}

$wp = idx(load('access_system_permissions','web'), 'permission_id');
$op = idx(load('access_system_permissions','off'), 'permission_id');
echo "\n=== WEB PERM COLS === ".implode(',', array_keys($wp['1']??[]))."\n";
echo "=== OFF PERM COLS === ".implode(',', array_keys($op['1']??[]))."\n";
echo "=== WEB ROLE COLS === ".implode(',', array_keys($wr['1']??[]))."\n";
echo "=== OFF ROLE COLS === ".implode(',', array_keys($or['1']??[]))."\n";

echo "\n=== FIRST WEB PERM ===\n".json_encode($wp['1']??[], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)."\n";
echo "\n=== FIRST OFF PERM ===\n".json_encode($op['1']??[], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)."\n";
echo "\n=== FIRST NEW OFF PERM 189 ===\n".json_encode($op['189']??[], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)."\n";
echo "\n=== FIRST WEB ROLE ===\n".json_encode($wr['1']??[], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)."\n";
echo "\n=== FIRST OFF ROLE ===\n".json_encode($or['1']??[], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)."\n";
echo "\n=== OFF ROLE 20 ===\n".json_encode($or['20']??[], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)."\n";

// How many old perms are soft-deleted offline?
$soft = 0; $alive = 0;
foreach ($op as $id=>$r) {
    $d = $r['deleted_at'] ?? 'NULL';
    if ($d !== 'NULL' && $d !== null) $soft++; else $alive++;
}
echo "\nOFF perms alive=$alive soft_deleted=$soft\n";
$softW=0;$aliveW=0;
foreach ($wp as $id=>$r) {
    $d = $r['deleted_at'] ?? 'NULL';
    if ($d !== 'NULL' && $d !== null) $softW++; else $aliveW++;
}
echo "WEB perms alive=$aliveW soft_deleted=$softW\n";

$softR=0;$aliveR=0;
foreach ($or as $id=>$r) {
    $d = $r['deleted_at'] ?? 'NULL';
    if ($d !== 'NULL' && $d !== null) $softR++; else $aliveR++;
}
echo "OFF roles alive=$aliveR soft_deleted=$softR\n";
