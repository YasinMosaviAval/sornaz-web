<?php
// CLI migration; DDL auto-commits. Back up first; steps may be resumed after repair.
final class TextStorageMigration
{
    private array $policy;
    private array $texts;
    private array $mapping;
    public function __construct(private PDO $pdo)
    {
        $this->policy=require __DIR__.'/../../config/text-storage-migration.php';
        $this->texts=require __DIR__.'/../../config/translated-fields.php';
        $this->mapping=require __DIR__.'/../../config/table-names.php';
    }
    private function column(string $table,string $field): ?array
    {
        $s=$this->pdo->prepare('SELECT COLUMN_TYPE,DATA_TYPE,IS_NULLABLE,COLLATION_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?');$s->execute([$table,$field]);return $s->fetch(PDO::FETCH_ASSOC)?:null;
    }
    private function usage(string $table,string $field): array
    {
        return $this->pdo->query("SELECT COUNT(*) rows_count,COALESCE(SUM(`$field` IS NOT NULL AND LENGTH(`$field`)>0),0) nonempty,COALESCE(MAX(CHAR_LENGTH(`$field`)),0) max_characters,COALESCE(MAX(LENGTH(`$field`)),0) max_bytes FROM `$table`")->fetch(PDO::FETCH_ASSOC);
    }
    private function lookup(string $store,string $entity,int $id,string $field,string $locale): array
    {
        $s=$this->pdo->prepare("SELECT translation_id,value,version FROM `$store` WHERE table_name=? AND table_id=? AND field=? AND locale=? AND deleted_at IS NULL ORDER BY version DESC,translation_id DESC");$s->execute([$entity,$id,$field,$locale]);return $s->fetchAll(PDO::FETCH_ASSOC);
    }
    public function plan(): array
    {
        $objects=(int)$this->pdo->query("SELECT COUNT(*) FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA=DATABASE()")->fetchColumn();
        if($objects){throw new LogicException('Triggers require review');}
        $expected=$this->policy['retained_text']??[];
        foreach(['varchar','binary','unused'] as $action){foreach($this->policy[$action] as $table=>$fields){foreach($fields as $field=>$target){$expected[]=$table.'.'.($action==='unused'?$target:$field);}}}
        foreach($this->texts as $entity=>$config){foreach($config['fields'] as $field){$expected[]=$this->mapping[$entity].'.'.$field;}}
        foreach($this->pdo->query("SELECT TABLE_NAME,COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND DATA_TYPE IN ('text','tinytext','mediumtext','longtext') AND TABLE_NAME NOT IN ('p_translations','f_translations')")->fetchAll(PDO::FETCH_ASSOC) as $row){if(!in_array($row['TABLE_NAME'].'.'.$row['COLUMN_NAME'],$expected,true)){throw new LogicException('Unclassified TEXT column; no changes');}}
        if((int)$this->pdo->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_TYPE='BASE TABLE' AND ENGINE<>'InnoDB'")->fetchColumn()){throw new LogicException('Nontransactional table; no changes');}
        $report=[];
        foreach(['varchar','binary','unused'] as $action){foreach($this->policy[$action] as $table=>$fields){foreach($fields as $field=>$target){if($action==='unused'){$field=$target;$target='DROP';}$column=$this->column($table,$field);if(!$column){continue;}$usage=$this->usage($table,$field);
            if($action==='varchar'&&(int)$usage['max_characters']>$target){throw new LogicException('VARCHAR overflow: '.$table.'.'.$field);}
            if(in_array($table.'.'.$field,$this->policy['json'],true)&&(int)$this->pdo->query("SELECT COUNT(*) FROM `$table` WHERE `$field` IS NOT NULL AND NOT JSON_VALID(CAST(`$field` AS CHAR))")->fetchColumn()){throw new LogicException('Invalid JSON: '.$table.'.'.$field);}
            $report[$table.'.'.$field]=['action'=>$action,'from'=>$column['COLUMN_TYPE'],'to'=>$action==='varchar'?'VARCHAR('.$target.')':$target]+$usage;
        }}}
        foreach($this->texts as $entity=>$config){$table=$this->mapping[$entity];$store=$this->mapping[$config['store']]??$config['store'];foreach($config['fields'] as $field){if(!$this->column($table,$field)){continue;}$report[$table.'.'.$field]=['action'=>'translation','from'=>$this->column($table,$field)['COLUMN_TYPE'],'to'=>$store.' / '.$entity.' / '.$field]+$this->usage($table,$field);
            foreach($this->pdo->query("SELECT * FROM `$table` WHERE `$field` IS NOT NULL AND LENGTH(`$field`)>0")->fetchAll(PDO::FETCH_ASSOC) as $row){$locale=in_array($row['locale']??'fa',['fa','en'],true)?($row['locale']??'fa'):'fa';$matches=$this->lookup($store,$entity,(int)$row[$config['key']],$field,$locale);foreach($matches as $match){if($match['value']!==$row[$field]){throw new LogicException('Conflicting existing translation: '.$table.'.'.$field);}}}
        }}
        return $report;
    }
    public function apply(string $directory): array
    {
        $report=$this->plan();require_once __DIR__.'/DatabaseHardeningMigration.php';(new DatabaseHardeningMigration($this->pdo))->backup($directory);
        $moved=0;$this->pdo->beginTransaction();
        try{foreach($this->texts as $entity=>$config){$table=$this->mapping[$entity];$store=$this->mapping[$config['store']]??$config['store'];foreach($config['fields'] as $field){if(!$this->column($table,$field)){continue;}
            foreach($this->pdo->query("SELECT * FROM `$table` WHERE `$field` IS NOT NULL AND LENGTH(`$field`)>0")->fetchAll(PDO::FETCH_ASSOC) as $row){$locale=in_array($row['locale']??'fa',['fa','en'],true)?($row['locale']??'fa'):'fa';$id=(int)$row[$config['key']];if(!$this->lookup($store,$entity,$id,$field,$locale)){$s=$this->pdo->prepare("INSERT INTO `$store` (table_name,table_id,field,locale,value,version,created_at,created_by,updated_at,updated_by) VALUES (?,?,?,?,?,1,CURRENT_TIMESTAMP,?,CURRENT_TIMESTAMP,?)");$actor=$row['created_by']??$row['user_id']??$row['owner_id']??$row['sender_id']??null;$s->execute([$entity,$id,$field,$locale,$row[$field],$actor,$actor]);$moved++;}if($this->lookup($store,$entity,$id,$field,$locale)[0]['value']!==$row[$field]){throw new LogicException('Translation verification failed');}}
        }}$this->pdo->commit();}catch(Throwable $error){$this->pdo->rollBack();throw $error;}
        foreach($report as $name=>$item){[$table,$field]=explode('.',$name);$column=$this->column($table,$field);$beforeHash=$this->fingerprint($table,$field);
            if(in_array($item['action'],['translation','unused'],true)){$this->pdo->exec("ALTER TABLE `$table` DROP COLUMN `$field`");continue;}
            $json=in_array($name,$this->policy['json'],true);
            if($json){$this->dropJsonChecks($table,$field);}
            $type=$item['to'];$collation=in_array($item['action'],['varchar','binary'],true)?' CHARACTER SET utf8mb4 COLLATE '.($column['COLLATION_NAME']?:'utf8mb4_unicode_ci'):'';$null=$column['IS_NULLABLE']==='YES'?' NULL DEFAULT NULL':' NOT NULL';
            if(strtolower($column['COLUMN_TYPE'])!==strtolower($type)){$this->pdo->exec("ALTER TABLE `$table` MODIFY COLUMN `$field` $type$collation$null");}
            if($json){$check='ck_text_'.substr(sha1($name),0,20);$this->pdo->exec("ALTER TABLE `$table` ADD CONSTRAINT `$check` CHECK (`$field` IS NULL OR JSON_VALID(CAST(`$field` AS CHAR)))");}
            if($this->fingerprint($table,$field)!==$beforeHash){throw new LogicException('Data changed during conversion: '.$name);}
        }
        $remaining=(int)$this->pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND DATA_TYPE IN ('text','tinytext','mediumtext','longtext') AND TABLE_NAME NOT IN ('p_translations','f_translations')")->fetchColumn();
        foreach($this->texts as $entity=>$config){foreach($config['fields'] as $field){if($this->column($this->mapping[$entity],$field)){throw new LogicException('Translatable source column remains');}}}
        $result=['fields'=>$report,'translations_inserted'=>$moved,'remaining_text_columns'=>$remaining];file_put_contents($directory.'/result.json',json_encode($result,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR));return $result;
    }
    private function fingerprint(string $table,string $field): string
    {
        $keys=$this->pdo->query("SHOW KEYS FROM `$table` WHERE Key_name='PRIMARY'")->fetchAll(PDO::FETCH_ASSOC);$order=implode(',',array_map(fn($r)=>'`'.$r['Column_name'].'`',$keys));if(!$order){throw new LogicException('Missing stable primary key');}
        $hash=hash_init('sha256');foreach($this->pdo->query("SELECT `$field` FROM `$table` ORDER BY $order",PDO::FETCH_NUM) as $row){hash_update($hash,$row[0]===null?'N;':'S'.strlen($row[0]).':'.$row[0].';');}return hash_final($hash);
    }
    private function dropJsonChecks(string $table,string $field): void
    {
        $join=str_contains($this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION),'MariaDB')?' AND cc.TABLE_NAME=tc.TABLE_NAME ':'';
        $s=$this->pdo->prepare('SELECT DISTINCT tc.CONSTRAINT_NAME,cc.CHECK_CLAUSE FROM information_schema.TABLE_CONSTRAINTS tc JOIN information_schema.CHECK_CONSTRAINTS cc ON cc.CONSTRAINT_SCHEMA=tc.CONSTRAINT_SCHEMA AND cc.CONSTRAINT_NAME=tc.CONSTRAINT_NAME '.$join.' WHERE tc.TABLE_SCHEMA=DATABASE() AND tc.TABLE_NAME=? AND tc.CONSTRAINT_TYPE=\'CHECK\'');$s->execute([$table]);
        foreach($s->fetchAll(PDO::FETCH_ASSOC) as $row){if($join&&$row['CONSTRAINT_NAME']===$field){continue;}if(str_contains($row['CHECK_CLAUSE'],'`'.$field.'`')||$row['CONSTRAINT_NAME']==='ck_text_'.substr(sha1($table.'.'.$field),0,20)){$kind=str_contains($this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION),'MariaDB')?'CONSTRAINT':'CHECK';$name=str_replace('`','``',$row['CONSTRAINT_NAME']);$this->pdo->exec("ALTER TABLE `$table` DROP $kind `$name`");}}
    }
}
