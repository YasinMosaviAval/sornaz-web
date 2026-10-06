<?php
// Read-only inventory. Private row samples are never printed or stored publicly.
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/../core/support/Environment.php';
\Core\support\Environment::load(__DIR__.'/../.env');
$c=require __DIR__.'/../config/database.php';
$pdo=new PDO(sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',$c['host'],$c['port'],$c['database'],$c['charset']),$c['username'],$c['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$out=[];
$out['columns']=$pdo->query('SELECT TABLE_NAME,COLUMN_NAME,COLUMN_TYPE,DATA_TYPE,IS_NULLABLE,COLUMN_KEY,EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() ORDER BY TABLE_NAME,ORDINAL_POSITION')->fetchAll();
$out['foreign_keys']=$pdo->query('SELECT TABLE_NAME,COLUMN_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME,CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL')->fetchAll();
$out['tables']=$pdo->query("SELECT TABLE_NAME,ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_TYPE='BASE TABLE' ORDER BY TABLE_NAME")->fetchAll();
foreach($out['tables'] as &$table){$name=$table['TABLE_NAME'];if(!preg_match('/^[fp]_[a-z0-9_]+$/',$name)){throw new LogicException('Unexpected table name');}$table['count']=(int)$pdo->query("SELECT COUNT(*) FROM `$name`")->fetchColumn();}unset($table);
$out['text_columns']=array_values(array_filter($out['columns'],fn($row)=>in_array($row['DATA_TYPE'],['text','tinytext','mediumtext','longtext'],true)&&!in_array($row['TABLE_NAME'],['p_translations','f_translations'],true)));
foreach($out['text_columns'] as &$column){$table=$column['TABLE_NAME'];$name=$column['COLUMN_NAME'];$column['nonempty_count']=(int)$pdo->query("SELECT COUNT(*) FROM `$table` WHERE `$name` IS NOT NULL AND LENGTH(`$name`)>0")->fetchColumn();}unset($column);
$out['translation_duplicates']=[];
foreach(['p_translations','f_translations'] as $table){
    $out['translation_duplicates'][$table]=$pdo->query("SELECT table_name,table_id,field,locale,version,COUNT(*) records,COUNT(DISTINCT SHA2(COALESCE(value,''),256)) different_values FROM `$table` WHERE deleted_at IS NULL GROUP BY table_name,table_id,field,locale,version HAVING COUNT(*)>1")->fetchAll();
    $out['translation_distribution'][$table]=$pdo->query("SELECT table_name,field,locale,COUNT(*) records FROM `$table` WHERE deleted_at IS NULL GROUP BY table_name,field,locale ORDER BY records DESC")->fetchAll();
}
$out['settings']=[];
foreach(['p_settings','f_settings'] as $table){$out['settings'][$table]=$pdo->query("SELECT setting_id,variable_name FROM `$table` WHERE deleted_at IS NULL")->fetchAll();}
$out['page_key_duplicates']=$pdo->query("SELECT t.table_name,s.variable_name,t.field,t.locale,t.version,COUNT(*) records,COUNT(DISTINCT SHA2(COALESCE(t.value,''),256)) different_values FROM p_translations t JOIN p_settings s ON s.setting_id=t.table_id WHERE t.table_name='settings' AND t.deleted_at IS NULL AND s.deleted_at IS NULL GROUP BY t.table_name,s.variable_name,t.field,t.locale,t.version HAVING COUNT(*)>1")->fetchAll();
$out['framework_key_duplicates']=$pdo->query("SELECT s.variable_name,t.field,t.locale,t.version,COUNT(*) records,COUNT(DISTINCT SHA2(COALESCE(t.value,''),256)) different_values FROM f_translations t JOIN f_settings s ON s.setting_id=t.table_id WHERE t.table_name='f_settings' AND t.deleted_at IS NULL AND s.deleted_at IS NULL GROUP BY s.variable_name,t.field,t.locale,t.version HAVING COUNT(*)>1")->fetchAll();
$out['cross_setting_keys']=$pdo->query("SELECT a.variable_name,COUNT(*) matches_found FROM p_settings a JOIN f_settings b ON BINARY a.variable_name=BINARY b.variable_name WHERE a.deleted_at IS NULL AND b.deleted_at IS NULL GROUP BY a.variable_name")->fetchAll();
$out['equal_text_groups']=$pdo->query("SELECT locale,COUNT(*) records,COUNT(DISTINCT table_name) entity_types FROM (SELECT table_name,locale,SHA2(value,256) fingerprint FROM p_translations WHERE deleted_at IS NULL AND value IS NOT NULL AND LENGTH(value)>0 UNION ALL SELECT table_name,locale,SHA2(value,256) FROM f_translations WHERE deleted_at IS NULL AND value IS NOT NULL AND LENGTH(value)>0) t GROUP BY locale,fingerprint HAVING COUNT(*)>1")->fetchAll();
$out['users_by_type']=$pdo->query('SELECT type,COUNT(*) records FROM f_users GROUP BY type')->fetchAll();
$out['admin_present']=(int)$pdo->query('SELECT COUNT(*) FROM f_users WHERE user_id=1 AND deleted_at IS NULL')->fetchColumn();
file_put_contents(__DIR__.'/../docs/database/content-audit-2026-10-05.local.json',json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR));
echo json_encode(['tables'=>count($out['tables']),'text_columns_outside_translations'=>count($out['text_columns']),'duplicate_groups'=>array_map('count',$out['translation_duplicates']),'page_key_duplicates'=>count($out['page_key_duplicates']),'framework_key_duplicates'=>count($out['framework_key_duplicates']),'equal_text_groups'=>count($out['equal_text_groups']),'admin_present'=>$out['admin_present']],JSON_PRETTY_PRINT),"\n";
