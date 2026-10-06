<?php
// SQLite fixtures only. Migrates their legacy inline values before exercising services.
require_once __DIR__.'/../../core/translation/EntityText.php';
final class TranslatedTextFixture
{
    public static function install(PDO $pdo): void
    {
        if($pdo->getAttribute(PDO::ATTR_DRIVER_NAME)!=='sqlite'){throw new LogicException('Fixture only');}
        foreach(['f_translations','translations'] as $store){
            $pdo->exec("CREATE TABLE IF NOT EXISTS $store(translation_id INTEGER PRIMARY KEY AUTOINCREMENT,table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,version INTEGER DEFAULT 1,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER,deleted_at TEXT)");
            $columns=array_column($pdo->query("PRAGMA table_info($store)")->fetchAll(PDO::FETCH_ASSOC),'name');
            foreach(['version'=>'INTEGER DEFAULT 1','created_at'=>'TEXT','created_by'=>'INTEGER','updated_at'=>'TEXT','updated_by'=>'INTEGER','deleted_at'=>'TEXT'] as $field=>$type){if(!in_array($field,$columns,true)){$pdo->exec("ALTER TABLE $store ADD COLUMN $field $type");}}
        }
        $config=require __DIR__.'/../../config/translated-fields.php';
        foreach($config as $table=>$item){$columns=array_column($pdo->query("PRAGMA table_info($table)")->fetchAll(PDO::FETCH_ASSOC),'name');foreach($item['fields'] as $field){if(!in_array($field,$columns,true)){continue;}
            $rows=$pdo->query("SELECT {$item['key']},$field FROM $table WHERE $field IS NOT NULL AND LENGTH($field)>0")->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as $row){\Core\translation\EntityText::save($table,(int)$row[$item['key']],$field,$row[$field],1,$pdo,'fa');}
            $pdo->exec("ALTER TABLE $table DROP COLUMN $field");
        }}
    }
}
