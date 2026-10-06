<?php
// Schemas only from the application; all changes use a random synthetic database.
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/lib/TextStorageMigration.php';
$dsn=getenv('SORNAZ_TEST_MYSQL_DSN');if(!$dsn){exit("Set test connection variables.\n");}
$p=new PDO($dsn,getenv('SORNAZ_TEST_MYSQL_USER')?:'',getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$fixture='sornaz_text_test_'.bin2hex(random_bytes(6));$created=false;$checks=0;
function textCheck(bool $ok,string $message):void{if(!$ok){throw new LogicException($message);}$GLOBALS['checks']++;}
try{
    $schemas=[];foreach($p->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_TYPE='BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN) as $table){$schemas[]=$p->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM)[1];}
    $p->exec("CREATE DATABASE `$fixture` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");$created=true;$p->exec("USE `$fixture`");$p->exec('SET FOREIGN_KEY_CHECKS=0');foreach($schemas as $sql){$p->exec($sql);}
    $p->exec("INSERT INTO f_users(user_id,username,status) VALUES(1,'fixture-admin','active')");
    $policy=require __DIR__.'/../config/text-storage-migration.php';$texts=require __DIR__.'/../config/translated-fields.php';$mapping=require __DIR__.'/../config/table-names.php';$samples=[];
    foreach(['varchar','binary','unused'] as $action){foreach($policy[$action] as $table=>$fields){foreach($fields as $field=>$target){if($action==='unused'){$field=$target;}$samples[$table][$field]=$action==='binary'?'{}':($action==='unused'?'unused-trackback':'fixture-path');}}}
    foreach($texts as $entity=>$config){foreach($config['fields'] as $field){$samples[$mapping[$entity]][$field]='fixture-'.$field;}}
    // Recreate the legacy input shape when the source schema has already migrated.
    foreach(['varchar','binary'] as $action){foreach($policy[$action] as $table=>$fields){foreach($fields as $field=>$target){
        $column=$p->query("SHOW COLUMNS FROM `$table` LIKE ".$p->quote($field))->fetch();
        $old=$action==='binary'&&$target==='LONGTEXT'?'LONGTEXT':'TEXT';if($table==='f_comments'){$old='TINYTEXT';}
        $nullable=$column['Null']==='YES'?' NULL':' NOT NULL';$p->exec("ALTER TABLE `$table` MODIFY COLUMN `$field` $old$nullable");
    }}}
    foreach($texts as $entity=>$config){$table=$mapping[$entity];foreach($config['fields'] as $field){if(!$p->query("SHOW COLUMNS FROM `$table` LIKE ".$p->quote($field))->fetch()){$p->exec("ALTER TABLE `$table` ADD COLUMN `$field` TEXT NULL");}}}
    if(!$p->query("SHOW COLUMNS FROM f_posts LIKE 'pinged'")->fetch()){$p->exec('ALTER TABLE f_posts ADD COLUMN pinged TEXT NULL');}
    $samples['f_conversations']=[];
    $samples['f_posts']=['pinged'=>'https://example.test/old-article'];
    foreach($samples as $table=>$values){
        $columns=$p->query("SELECT COLUMN_NAME,COLUMN_KEY,DATA_TYPE,COLUMN_TYPE,IS_NULLABLE,COLUMN_DEFAULT,EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='$table'")->fetchAll();
        foreach($columns as $column){$name=$column['COLUMN_NAME'];if(str_contains($column['EXTRA'],'GENERATED')){continue;}if($column['COLUMN_KEY']==='PRI'){$values[$name]=1;}elseif($column['IS_NULLABLE']==='NO'&&$column['COLUMN_DEFAULT']===null&&!isset($values[$name])){
            $values[$name]=in_array($column['DATA_TYPE'],['int','bigint','tinyint','smallint','mediumint','decimal'],true)?1:($column['DATA_TYPE']==='datetime'?'2026-10-05 12:00:00':'fixture');
        }}
        if($table==='f_user_settings'){$values['user_id']=1;}
        $sql="INSERT INTO `$table` (`".implode('`,`',array_keys($values)).'`) VALUES ('.implode(',',array_fill(0,count($values),'?')).')';$p->prepare($sql)->execute(array_values($values));
    }
    $p->exec('SET FOREIGN_KEY_CHECKS=1');
    $migration=new TextStorageMigration($p);textCheck(count($migration->plan())===32,'Incomplete field plan');
    $p->exec("UPDATE f_media_files SET path=REPEAT('x',2049)");
    try{$migration->plan();throw new RuntimeException('Expected overflow rejection');}catch(LogicException $e){textCheck(str_contains($e->getMessage(),'VARCHAR overflow'),'Unexpected overflow error');}
    $p->exec("UPDATE f_media_files SET path='fixture-path'");
    $p->exec("INSERT INTO f_translations(table_name,table_id,field,locale,value,version) VALUES('user_point_rules',1,'description','fa','conflict',1)");
    try{$migration->plan();throw new RuntimeException('Expected conflict rejection');}catch(LogicException $e){textCheck(str_contains($e->getMessage(),'Conflicting'),'Unexpected conflict error');}
    $p->exec("DELETE FROM f_translations");
    $p->exec("UPDATE f_posts SET pinged='https://example.test/old-article'");
    $result=$migration->apply(__DIR__.'/../storage/backups/text-storage-tests/'.$fixture.'/first');
    textCheck($result['translations_inserted']===12,'Not all human fields migrated');textCheck($result['remaining_text_columns']===12,'Technical TEXT count changed');
    foreach($samples as $table=>$values){foreach($values as $field=>$value){if(isset($policy['varchar'][$table][$field])||isset($policy['binary'][$table][$field])){textCheck($p->query("SELECT `$field` FROM `$table`")->fetchColumn()===$value,'Value changed: '.$table.'.'.$field);}}}
    foreach($texts as $entity=>$config){$store=$mapping[$config['store']]??$config['store'];foreach($config['fields'] as $field){$q=$p->prepare("SELECT value FROM `$store` WHERE table_name=? AND table_id=1 AND field=? AND locale='fa'");$q->execute([$entity,$field]);textCheck($q->fetchColumn()==='fixture-'.$field,'Translation missing');}}
    textCheck((int)$p->query('SELECT COUNT(*) FROM f_posts')->fetchColumn()===1&&(int)$p->query('SELECT COUNT(*) FROM f_comments')->fetchColumn()===1,'Posts/comments removed');
    textCheck($p->query('SELECT pinged FROM f_posts')->fetchColumn()==='https://example.test/old-article','Historical URL changed');
    $repeat=$migration->apply(__DIR__.'/../storage/backups/text-storage-tests/'.$fixture.'/repeat');textCheck($repeat['translations_inserted']===0,'Repeat duplicated translations');
    $translatedDsn=preg_replace('/dbname=[^;]+/','dbname='.$fixture,$dsn);$db=new \Core\database\PrefixedPDO($translatedDsn,getenv('SORNAZ_TEST_MYSQL_USER')?:'',getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    \Core\translation\EntityText::save('conversation_messages',1,'body','English fixture',1,$db,'en');
    textCheck(\Core\translation\EntityText::get('conversation_messages',1,'body',$db,'en')==='English fixture','English read failed');
    textCheck(\Core\translation\EntityText::get('conversation_messages',1,'body',$db,'fa')==='fixture-body','Original text overwritten');
    \Core\translation\EntityText::save('conversation_messages',1,'body','Edited English',1,$db,'en');
    textCheck((int)$db->query("SELECT COUNT(*) FROM f_translations WHERE table_name='conversation_messages' AND table_id=1 AND field='body' AND locale='en'")->fetchColumn()===1,'Repeated edit duplicated translation');
    try{\Core\translation\EntityText::atomic($db,function()use($db){$db->exec('INSERT INTO conversation_messages(conversation_message_id,conversation_id,sender_id) VALUES(2,1,1)');\Core\translation\EntityText::save('conversation_messages',2,'body','rollback',1,$db,'fa');throw new RuntimeException('Forced rollback');});}catch(RuntimeException $e){textCheck($e->getMessage()==='Forced rollback','Unexpected rollback failure');}
    textCheck((int)$db->query('SELECT COUNT(*) FROM conversation_messages WHERE conversation_message_id=2')->fetchColumn()===0,'Parent failed to roll back');
    textCheck((int)$db->query("SELECT COUNT(*) FROM f_translations WHERE table_name='conversation_messages' AND table_id=2")->fetchColumn()===0,'Translation failed to roll back');
    echo "Text storage: $checks MySQL checks passed; migration, capacity guards, preserved values, locale, repeat and rollback.\n";
}finally{if($created){$p->exec("DROP DATABASE `$fixture`");}}
