<?php
// CLI only. Read schema and print a migration; no schema is changed here.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__.'/../bootstrap/autoload.php';
require_once __DIR__.'/../bootstrap/app.php';
$pdo = db();
$tables = $pdo->query("SELECT TABLE_NAME,ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_TYPE='BASE TABLE'")->fetchAll(PDO::FETCH_ASSOC);
$stores = array_values(array_intersect([\Core\database\TableNames::physical('translations'), 'f_translations'], array_column($tables,'TABLE_NAME')));
if (!$stores) throw new RuntimeException('No translation store found.');
foreach ($stores as $store) {
    $fields = array_column($pdo->query("SHOW COLUMNS FROM `$store`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
    if (array_diff(['table_name','table_id','deleted_at','deleted_by'],$fields)) throw new RuntimeException('Translation deletion columns missing.');
}
$sql = "-- Translation deletion synchronization; review before applying.\nDELIMITER $$\n";
foreach ($tables as $table) {
    $physical = $table['TABLE_NAME'];
    if (in_array($physical,$stores,true)) continue;
    $logical = \Core\database\TableNames::logical($physical);
    $quoted = '`'.str_replace('`','``',$physical).'`';
    $columns = $pdo->query("SHOW COLUMNS FROM $quoted")->fetchAll(PDO::FETCH_ASSOC);
    $fields = array_column($columns,'Field');
    $primary = array_column(array_filter($columns,fn($c)=>$c['Key']==='PRI'),'Field');
    if (count($primary)!==1) continue;
    $pk = str_replace('`','``',$primary[0]);
    $discriminators = $pdo->quote($logical).','.$pdo->quote($physical);
    foreach (['UPDATE','DELETE'] as $operation) {
        if ($operation==='UPDATE' && !in_array('deleted_at',$fields,true)) continue;
        $name = 'tr_delete_'.substr(hash('sha256',$physical.$operation),0,24);
        $timestamp = $operation==='UPDATE' ? 'NEW.deleted_at' : 'CURRENT_TIMESTAMP';
        $actor = in_array('deleted_by',$fields,true) ? ($operation==='UPDATE' ? 'NEW.deleted_by' : 'COALESCE(@sornaz_deleted_by,OLD.deleted_by)') : '@sornaz_deleted_by';
        $updates = '';
        foreach ($stores as $store) $updates .= "UPDATE `$store` SET deleted_at=$timestamp,deleted_by=$actor WHERE table_name IN ($discriminators) AND table_id=OLD.`$pk` AND deleted_at IS NULL;\n";
        $condition = $operation==='UPDATE' ? 'IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN' : '';
        $end = $operation==='UPDATE' ? 'END IF;' : '';
        $sql .= "DROP TRIGGER IF EXISTS `$name`$$\nCREATE TRIGGER `$name` AFTER $operation ON $quoted FOR EACH ROW BEGIN\n$condition\n$updates$end\nEND$$\n";
    }
}
echo $sql."DELIMITER ;\n";
