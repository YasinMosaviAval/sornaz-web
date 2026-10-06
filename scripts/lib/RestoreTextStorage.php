<?php
// CLI only: source is a separately restored, trusted pre-migration backup.
final class RestoreTextStorage
{
    public static function apply(PDO $target, PDO $source): array
    {
        $rows=$source->query('SELECT post_id,pinged,updated_at FROM f_posts ORDER BY post_id')->fetchAll(PDO::FETCH_ASSOC);
        $ids=$target->query('SELECT post_id FROM f_posts ORDER BY post_id')->fetchAll(PDO::FETCH_COLUMN);
        if(array_map('intval',$ids)!==array_map(fn($r)=>(int)$r['post_id'],$rows)){throw new LogicException('Post identities differ; no restoration');}
        $policy=require __DIR__.'/../../config/text-storage-migration.php';
        $fields=$policy['binary'];$fields['f_posts']=['pinged'=>'TEXT'];
        $checks=[];
        foreach($fields as $table=>$columns){foreach($columns as $field=>$type){
            $q=$source->prepare('SELECT COLUMN_TYPE,IS_NULLABLE,COLLATION_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?');$q->execute([$table,$field]);$old=$q->fetch(PDO::FETCH_ASSOC);
            if(!$old||!in_array(strtolower($old['COLUMN_TYPE']),['text','longtext'],true)||!preg_match('/^[a-z0-9_]+$/',$old['COLLATION_NAME'])){throw new LogicException('Unexpected original text definition');}
            $exists=$target->query("SHOW COLUMNS FROM `$table` LIKE ".$target->quote($field))->fetch();
            if(!$exists&&$field!=='pinged'){throw new LogicException('Missing technical field');}
            $before=$exists?$target->query("SELECT `$field` FROM `$table` ORDER BY ".self::keys($target,$table))->fetchAll(PDO::FETCH_COLUMN):null;
            $definition=$old['COLUMN_TYPE'].' CHARACTER SET utf8mb4 COLLATE '.$old['COLLATION_NAME'].($old['IS_NULLABLE']==='YES'?' NULL DEFAULT NULL':' NOT NULL');
            $target->exec("ALTER TABLE `$table` ".($exists?'MODIFY':'ADD')." COLUMN `$field` $definition");
            if($before!==null&&$field!=='pinged'&&$before!==$target->query("SELECT `$field` FROM `$table` ORDER BY ".self::keys($target,$table))->fetchAll(PDO::FETCH_COLUMN)){throw new LogicException('Technical data changed');}
            $checks[$table.'.'.$field]=$old['COLUMN_TYPE'];
        }}
        $target->beginTransaction();
        try{$q=$target->prepare('UPDATE f_posts SET pinged=?,updated_at=? WHERE post_id=?');foreach($rows as $row){$q->execute([$row['pinged'],$row['updated_at'],$row['post_id']]);}
            if($target->query('SELECT post_id,pinged,updated_at FROM f_posts ORDER BY post_id')->fetchAll(PDO::FETCH_ASSOC)!==$rows){throw new LogicException('Historical URL verification failed');}$target->commit();
        }catch(Throwable $e){$target->rollBack();throw $e;}
        return ['types'=>$checks,'posts_verified'=>count($rows),'nonempty_urls'=>count(array_filter($rows,fn($r)=>$r['pinged']!==null&&$r['pinged']!==''))];
    }
    private static function keys(PDO $pdo,string $table):string
    {
        $rows=$pdo->query("SHOW KEYS FROM `$table` WHERE Key_name='PRIMARY'")->fetchAll(PDO::FETCH_ASSOC);
        usort($rows,fn($a,$b)=>$a['Seq_in_index']<=>$b['Seq_in_index']);
        if(!$rows){throw new LogicException('Stable primary key required');}
        return implode(',',array_map(fn($r)=>'`'.$r['Column_name'].'`',$rows));
    }
}
