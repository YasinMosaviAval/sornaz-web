<?php

function load($name, $side) {
    $p = __DIR__ . "/_rows_{$side}_{$name}.json";
    $j = json_decode(file_get_contents($p), true);
    return is_array($j) ? $j : [];
}

function pick(array $row, array $keys): array {
    $out = [];
    foreach ($keys as $k) {
        $out[$k] = $row[$k] ?? null;
    }
    return $out;
}

function idx(array $rows, string $pk): array {
    $out = [];
    foreach ($rows as $r) {
        if (!isset($r[$pk])) continue;
        $out[(string)$r[$pk]] = $r;
    }
    return $out;
}

function unq($v) {
    if ($v === null || strtoupper((string)$v) === 'NULL') return null;
    $v = (string)$v;
    if (strlen($v) >= 2 && $v[0] === "'" && substr($v, -1) === "'") {
        return str_replace("''", "'", substr($v, 1, -1));
    }
    return $v;
}

// ---- permissions ----
$wp = idx(load('access_system_permissions', 'web'), 'permission_id');
$op = idx(load('access_system_permissions', 'off'), 'permission_id');
$onlyOffP = array_diff_key($op, $wp);
$onlyWebP = array_diff_key($wp, $op);
$chgP = [];
foreach (array_intersect_key($wp, $op) as $id => $r) {
    $a = $wp[$id]; $b = $op[$id];
    $diff = [];
    foreach (array_unique(array_merge(array_keys($a), array_keys($b))) as $k) {
        if (($a[$k] ?? null) !== ($b[$k] ?? null)) {
            $diff[$k] = ['web' => $a[$k] ?? null, 'off' => $b[$k] ?? null];
        }
    }
    // ignore new cols that only exist offline (resource/action/scope/risk_level) if web is missing them
    $ignoreIfWebMissing = ['resource','action','scope','risk_level'];
    $real = [];
    foreach ($diff as $k => $v) {
        if (in_array($k, $ignoreIfWebMissing, true) && ($v['web'] === null) && $v['off'] !== null) {
            continue;
        }
        $real[$k] = $v;
    }
    if ($real) $chgP[$id] = $real;
}

echo "PERMISSIONS web=".count($wp)." off=".count($op)." only_off=".count($onlyOffP)." only_web=".count($onlyWebP)." changed=".count($chgP)."\n";
echo "only_off ids: ".implode(',', array_keys($onlyOffP))."\n";
if ($onlyOffP) {
    echo "sample only_off:\n";
    $i=0;
    foreach ($onlyOffP as $id=>$r) {
        echo "  $id name=".($r['name']??'')." key=".($r['permission_key']??$r['key']??'')." resource=".($r['resource']??'')." action=".($r['action']??'')."\n";
        if (++$i>=30) { echo "  ...\n"; break; }
    }
}
if ($chgP) {
    echo "changed sample:\n";
    $i=0;
    foreach ($chgP as $id=>$d) {
        echo "  $id ".json_encode($d, JSON_UNESCAPED_UNICODE)."\n";
        if (++$i>=15) break;
    }
}

// ---- roles ----
$wr = idx(load('access_system_roles', 'web'), 'role_id');
$or = idx(load('access_system_roles', 'off'), 'role_id');
$onlyOffR = array_diff_key($or, $wr);
$onlyWebR = array_diff_key($wr, $or);
echo "\nROLES web=".count($wr)." off=".count($or)." only_off=".count($onlyOffR)." only_web=".count($onlyWebR)."\n";
echo "web role ids: ".implode(',', array_keys($wr))."\n";
echo "off role ids: ".implode(',', array_keys($or))."\n";
echo "only_off roles:\n";
foreach ($onlyOffR as $id=>$r) {
    echo "  $id name=".unq($r['name']??'')." slug=".unq($r['slug']??$r['key']??'')." type=".unq($r['type']??'')."\n";
}

// ---- role_permissions ----
function rpKey($r) {
    return ($r['role_id'] ?? '').':'.($r['permission_id'] ?? '');
}
$wrp = [];
foreach (load('access_system_role_permissions', 'web') as $r) $wrp[rpKey($r)] = $r;
$orp = [];
foreach (load('access_system_role_permissions', 'off') as $r) $orp[rpKey($r)] = $r;
$onlyOffRp = array_diff_key($orp, $wrp);
$onlyWebRp = array_diff_key($wrp, $orp);
echo "\nROLE_PERMS web=".count($wrp)." off=".count($orp)." only_off=".count($onlyOffRp)." only_web=".count($onlyWebRp)."\n";

// group only_off by role
$byRole = [];
foreach ($onlyOffRp as $k=>$r) {
    $byRole[$r['role_id']] = ($byRole[$r['role_id']] ?? 0) + 1;
}
ksort($byRole);
echo "only_off role_perms by role_id:\n";
foreach ($byRole as $rid=>$c) echo "  role $rid => $c\n";

// ---- categories ----
$wc = load('categories', 'web');
$oc = load('categories', 'off');
echo "\nCATEGORIES web=".count($wc)." off=".count($oc)."\n";
foreach ($oc as $r) {
    echo "  off ".json_encode($r, JSON_UNESCAPED_UNICODE)."\n";
}

// ---- settings ----
$ws = load('z_settings', 'web');
$os = load('z_settings', 'off');
echo "\nSETTINGS web=".count($ws)." off=".count($os)."\n";
foreach ($os as $r) {
    echo "  off key=".($r['setting_key']??$r['key']??$r['name']??'?')." => ".substr(json_encode($r, JSON_UNESCAPED_UNICODE),0,300)."\n";
}

// ---- translations ----
function tKey($r) {
    return implode('|', [
        unq($r['table_name'] ?? ''),
        unq($r['table_id'] ?? ''),
        unq($r['locale'] ?? ''),
        unq($r['field'] ?? ''),
    ]);
}
$wt = [];
foreach (load('f_translations', 'web') as $r) $wt[tKey($r)] = $r;
$ot = [];
foreach (load('f_translations', 'off') as $r) $ot[tKey($r)] = $r;
$onlyOffT = array_diff_key($ot, $wt);
$onlyWebT = array_diff_key($wt, $ot);
$chgT = [];
foreach (array_intersect_key($wt, $ot) as $k => $r) {
    if (($wt[$k]['value'] ?? null) !== ($ot[$k]['value'] ?? null)) {
        $chgT[$k] = ['web' => $wt[$k]['value'] ?? null, 'off' => $ot[$k]['value'] ?? null];
    }
}
echo "\nTRANSLATIONS web=".count($wt)." off=".count($ot)." only_off=".count($onlyOffT)." only_web=".count($onlyWebT)." changed_value=".count($chgT)."\n";

$byTable = [];
foreach ($onlyOffT as $k=>$r) {
    $tn = unq($r['table_name'] ?? '?');
    $byTable[$tn] = ($byTable[$tn] ?? 0) + 1;
}
echo "only_off translations by table_name:\n";
foreach ($byTable as $tn=>$c) echo "  $tn => $c\n";

echo "sample only_off translations:\n";
$i=0;
foreach ($onlyOffT as $k=>$r) {
    echo "  $k value=".substr(unq($r['value']??''),0,80)."\n";
    if (++$i>=25) { echo "  ...\n"; break; }
}

if ($chgT) {
    echo "changed translation values sample:\n";
    $i=0;
    foreach ($chgT as $k=>$d) {
        echo "  $k\n    WEB=".substr((string)$d['web'],0,80)."\n    OFF=".substr((string)$d['off'],0,80)."\n";
        if (++$i>=10) break;
    }
}

// dump keys for generator
file_put_contents(__DIR__.'/_diff_only_off_permissions.json', json_encode(array_values($onlyOffP), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
file_put_contents(__DIR__.'/_diff_only_off_roles.json', json_encode(array_values($onlyOffR), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
file_put_contents(__DIR__.'/_diff_only_off_role_perms.json', json_encode(array_values($onlyOffRp), JSON_UNESCAPED_UNICODE));
file_put_contents(__DIR__.'/_diff_only_off_translations.json', json_encode(array_values($onlyOffT), JSON_UNESCAPED_UNICODE));
file_put_contents(__DIR__.'/_diff_only_off_categories.json', json_encode($oc, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
file_put_contents(__DIR__.'/_diff_only_off_settings.json', json_encode($os, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
file_put_contents(__DIR__.'/_diff_changed_permissions.json', json_encode($chgP, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
file_put_contents(__DIR__.'/_diff_changed_translations.json', json_encode($chgT, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

echo "\nWrote diff json files\n";
