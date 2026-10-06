<?php
// CLI-only initial-version builder. No automatic DDL and no foreign-key disabling.
final class InitialDatabase
{
    private array $columns;
    private array $foreignKeys;
    private array $policy;
    private array $mapping;
    public function __construct(private PDO $pdo)
    {
        $this->policy=require __DIR__.'/../../config/initial-database-policy.php';
        $this->mapping=require __DIR__.'/../../config/table-names.php';
        $this->columns=$pdo->query('SELECT TABLE_NAME,COLUMN_NAME,COLUMN_KEY,IS_NULLABLE,EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()')->fetchAll(PDO::FETCH_ASSOC);
        $this->foreignKeys=$pdo->query('SELECT TABLE_NAME,COLUMN_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL')->fetchAll(PDO::FETCH_ASSOC);
    }
    private function scalar(string $sql):mixed{return $this->pdo->query($sql)->fetchColumn();}
    private function keys(string $table):array{return array_column(array_filter($this->columns,fn($c)=>$c['TABLE_NAME']===$table&&$c['COLUMN_KEY']==='PRI'),'COLUMN_NAME');}
    private function predicate(string $table):string{return $this->policy[$table]==='translations'?$this->translationPredicate():$this->policy[$table];}
    private function translationPredicate():string
    {
        $parts=[];
        foreach($this->mapping as $logical=>$table){
            if(in_array($this->policy[$table],['0','translations'],true)){continue;}
            $keys=$this->keys($table);if(count($keys)!==1){throw new LogicException('Composite translation target requires policy: '.$table);}
            $names=array_unique([$logical,$table]);$quoted=implode(',',array_map(fn($name)=>$this->pdo->quote($name),$names));
            $parts[]="(table_name IN ($quoted) AND table_id IN (SELECT `{$keys[0]}` FROM `$table` WHERE {$this->policy[$table]}))";
        }
        return $parts?'('.implode(' OR ',$parts).')':'0';
    }
    public function plan():array
    {
        $tables=$this->pdo->query("SELECT TABLE_NAME,ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_TYPE='BASE TABLE'")->fetchAll(PDO::FETCH_ASSOC);
        $names=array_column($tables,'TABLE_NAME');$expected=array_keys($this->policy);sort($names);sort($expected);
        if($names!==$expected||array_filter($tables,fn($t)=>$t['ENGINE']!=='InnoDB')){throw new LogicException('Unknown, missing or non-InnoDB tables; no changes');}
        foreach(['TRIGGERS'=>'TRIGGER_SCHEMA','VIEWS'=>'TABLE_SCHEMA','ROUTINES'=>'ROUTINE_SCHEMA','EVENTS'=>'EVENT_SCHEMA'] as $object=>$schema){if($this->scalar("SELECT COUNT(*) FROM information_schema.$object WHERE $schema=DATABASE()")){throw new LogicException('Review database objects before reset');}}
        if($this->scalar('SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME IS NOT NULL AND (TABLE_SCHEMA=DATABASE() OR REFERENCED_TABLE_SCHEMA=DATABASE()) AND (TABLE_SCHEMA<>DATABASE() OR REFERENCED_TABLE_SCHEMA<>DATABASE())')){throw new LogicException('Cross-schema reference; no changes');}
        if((int)$this->scalar('SELECT COUNT(*) FROM f_users WHERE user_id=1 AND deleted_at IS NULL')!==1){throw new LogicException('Active administrator missing; no changes');}
        $known=array_unique(array_merge(array_keys($this->mapping),array_values($this->mapping),['user_notifications']));$quoted=implode(',',array_map(fn($name)=>$this->pdo->quote($name),$known));
        foreach(['p_translations','f_translations'] as $table){if($this->scalar("SELECT COUNT(*) FROM `$table` WHERE table_name IS NOT NULL AND table_name NOT IN ($quoted)")){throw new LogicException('Unclassified translation entity; no changes');}}
        $plan=[];foreach($names as $table){$before=(int)$this->scalar("SELECT COUNT(*) FROM `$table`");$kept=(int)$this->scalar("SELECT COUNT(*) FROM `$table` WHERE ".$this->predicate($table));$plan[$table]=['before'=>$before,'keep'=>$kept,'delete'=>$before-$kept];}
        $this->order();return $plan;
    }
    private function order():array
    {
        $children=[];foreach($this->foreignKeys as $fk){if($fk['TABLE_NAME']!==$fk['REFERENCED_TABLE_NAME']){$children[$fk['REFERENCED_TABLE_NAME']][]=$fk['TABLE_NAME'];}}
        $visited=[];$active=[];$ordered=[];
        $visit=function(string $table)use(&$visit,&$visited,&$active,&$ordered,$children):void{
            if(isset($visited[$table])){return;}if(isset($active[$table])){throw new LogicException('Cyclic FK graph; no changes');}$active[$table]=true;
            foreach($children[$table]??[] as $child){$visit($child);}unset($active[$table]);$visited[$table]=true;$ordered[]=$table;
        };
        foreach(array_keys($this->policy) as $table){$visit($table);}return $ordered;
    }
    public function apply(string $directory):array
    {
        $plan=$this->plan();require_once __DIR__.'/DatabaseHardeningMigration.php';
        (new DatabaseHardeningMigration($this->pdo))->backup($directory);
        $admin=$this->pdo->query('SELECT * FROM f_users WHERE user_id=1')->fetch(PDO::FETCH_ASSOC);
        $protected=['user_id','username','password','phone','email','national_code','type','status'];
        $identity=array_intersect_key($admin,array_flip($protected));
        $this->pdo->beginTransaction();
        try{
            $this->unlinkSelfReferences();
            foreach($this->order() as $table){if($this->policy[$table]==='translations'){continue;}$this->pdo->exec("DELETE FROM `$table` WHERE NOT COALESCE((".$this->predicate($table).'),0)');}
            foreach(['p_translations','f_translations'] as $table){$this->pdo->exec("DELETE FROM `$table` WHERE NOT COALESCE((".$this->translationPredicate().'),0)');}
            $this->clearRemovedActors();$this->clearContentActivity();$this->verify();
            $after=$this->pdo->query('SELECT * FROM f_users WHERE user_id=1')->fetch(PDO::FETCH_ASSOC);
            if(array_intersect_key($after,array_flip($protected))!==$identity){throw new LogicException('Administrator identity changed; rollback');}
            $this->pdo->commit();
        }catch(Throwable $error){$this->pdo->rollBack();throw $error;}
        foreach($plan as $table=>&$row){$row['after']=(int)$this->scalar("SELECT COUNT(*) FROM `$table`");if($row['after']!==$row['keep']){throw new LogicException('Unexpected post-reset count; backup retained: '.$table);}}unset($row);
        if(file_put_contents($directory.'/result.json',json_encode($plan,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR))===false){throw new RuntimeException('Result write failed; reset committed and backup retained');}
        return $plan;
    }
    private function unlinkSelfReferences():void
    {
        foreach($this->foreignKeys as $fk){if($fk['TABLE_NAME']!==$fk['REFERENCED_TABLE_NAME']){continue;}$table=$fk['TABLE_NAME'];$column=$fk['COLUMN_NAME'];
            $definition=array_values(array_filter($this->columns,fn($c)=>$c['TABLE_NAME']===$table&&$c['COLUMN_NAME']===$column))[0];
            if($this->policy[$table]!=='0'||$definition['IS_NULLABLE']!=='YES'){throw new LogicException('Self reference requires a separate policy');}
            $this->pdo->exec("UPDATE `$table` SET `$column`=NULL WHERE `$column` IS NOT NULL");
        }
    }
    private function clearRemovedActors():void
    {
        foreach($this->columns as $column){if(in_array($column['TABLE_NAME'],['f_posts','f_comments'],true)||!in_array($column['COLUMN_NAME'],['created_by','updated_by','approved_by','deleted_by'],true)){continue;}$table=$column['TABLE_NAME'];$name=$column['COLUMN_NAME'];
            $where="`$name` IS NOT NULL AND `$name`<>1";
            if($column['IS_NULLABLE']!=='YES'&&$this->scalar("SELECT COUNT(*) FROM `$table` WHERE $where")){throw new LogicException('Required historical actor: '.$table.'.'.$name);}
            if($column['IS_NULLABLE']==='YES'){$this->pdo->exec("UPDATE `$table` SET `$name`=NULL WHERE $where");}
        }
    }
    private function clearContentActivity():void
    {
        $this->pdo->exec('UPDATE f_posts p LEFT JOIN f_media_files m ON m.media_file_id=p.cover_media_id SET p.cover_media_id=NULL WHERE p.cover_media_id IS NOT NULL AND m.media_file_id IS NULL');
        $this->pdo->exec('UPDATE f_user_profiles p LEFT JOIN f_media_files m ON m.media_file_id=p.picture_media_id SET p.picture_media_id=NULL WHERE p.picture_media_id IS NOT NULL AND m.media_file_id IS NULL');
    }
    public function verify():void
    {
        if((int)$this->scalar('SELECT COUNT(*) FROM f_users')!==1||(int)$this->scalar('SELECT COUNT(*) FROM f_users WHERE user_id=1')!==1){throw new LogicException('Unexpected user survived');}
        foreach($this->policy as $table=>$rule){if($this->scalar("SELECT COUNT(*) FROM `$table` WHERE NOT COALESCE((".$this->predicate($table).'),0)')){throw new LogicException('Unexpected retained row: '.$table);}}
        foreach($this->foreignKeys as $fk){$table=$fk['TABLE_NAME'];$column=$fk['COLUMN_NAME'];$parent=$fk['REFERENCED_TABLE_NAME'];$key=$fk['REFERENCED_COLUMN_NAME'];if($this->scalar("SELECT COUNT(*) FROM `$table` c LEFT JOIN `$parent` p ON p.`$key`=c.`$column` WHERE c.`$column` IS NOT NULL AND p.`$key` IS NULL")){throw new LogicException('FK orphan: '.$table.'.'.$column);}}
    }
    public function export(string $directory):string
    {
        require_once __DIR__.'/DatabaseHardeningMigration.php';
        $this->verify();$snapshot=$directory.'/initial-snapshot';(new DatabaseHardeningMigration($this->pdo))->backup($snapshot);
        $sql=file_get_contents($snapshot.'/before.sql');$sql=preg_replace('/ AUTO_INCREMENT=\d+\b/','',$sql);
        $sourceMode=(string)$this->scalar('SELECT @@SESSION.sql_mode');
        $mode='NO_AUTO_VALUE_ON_ZERO'.(str_contains($sourceMode,'NO_BACKSLASH_ESCAPES')?',NO_BACKSLASH_ESCAPES':'');
        $sql='SET SQL_MODE='.$this->pdo->quote($mode).";\n".$sql;
        $path=$directory.'/initial-database.sql';if(file_put_contents($path,$sql)===false){throw new RuntimeException('Initial export failed');}return $path;
    }
}
