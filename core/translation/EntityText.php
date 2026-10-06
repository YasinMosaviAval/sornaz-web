<?php
namespace Core\translation;

use PDO;
use LogicException;

/** Storage only. Callers must check access to the parent entity before using it. */
final class EntityText
{
    public static function atomic(PDO $pdo,callable $callback): mixed
    {
        $own=!$pdo->inTransaction();if($own){$pdo->beginTransaction();}
        try{$result=$callback();if($own){$pdo->commit();}return $result;}
        catch(\Throwable $error){if($own&&$pdo->inTransaction()){$pdo->rollBack();}throw $error;}
    }

    private static function config(string $table, string $field): array
    {
        static $config;
        $config ??= require __DIR__.'/../../config/translated-fields.php';
        $item=$config[$table]??null;
        if(!$item||!in_array($field,$item['fields'],true)){throw new LogicException('Unknown translated field');}
        return $item;
    }

    public static function expression(string $table, string $id, string $field, ?string $language=null): string
    {
        $config=self::config($table,$field);
        if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*(?:\.[a-zA-Z_][a-zA-Z0-9_]*)?$/',$id)){throw new LogicException('Invalid entity ID expression');}
        $language=$language??(function_exists('locale')?locale():'fa');
        $language=$language==='en'?'en':'fa';
        return "COALESCE((SELECT et.value FROM {$config['store']} et WHERE et.table_name='$table' AND et.table_id=$id AND et.field='$field' AND et.deleted_at IS NULL ORDER BY (et.locale='$language') DESC,(et.locale='fa') DESC,et.version DESC,et.translation_id DESC LIMIT 1),'')";
    }

    public static function get(string $table,int $id,string $field,?PDO $pdo=null,?string $language=null): string
    {
        $pdo??=db();$config=self::config($table,$field);
        $query=$pdo->prepare('SELECT '.self::expression($table,$config['key'],$field,$language).' FROM '.$table.' WHERE '.$config['key'].'=?');
        $query->execute([$id]);$value=$query->fetchColumn();return $value===false||$value===null?'':(string)$value;
    }

    public static function save(string $table,int $id,string $field,?string $value,int $actor,?PDO $pdo=null,?string $language=null): void
    {
        $pdo??=db();$config=self::config($table,$field);
        $language=$language??(function_exists('locale')?locale():'fa');$language=$language==='en'?'en':'fa';
        $own=!$pdo->inTransaction();if($own){$pdo->beginTransaction();}
        try{
            $lock=$pdo->prepare('SELECT '.$config['key'].' FROM '.$table.' WHERE '.$config['key'].'=?'.($pdo->getAttribute(PDO::ATTR_DRIVER_NAME)==='mysql'?' FOR UPDATE':''));
            $lock->execute([$id]);if($lock->fetchColumn()===false){throw new LogicException('Translation parent missing');}
            $query=$pdo->prepare('SELECT translation_id FROM '.$config['store'].' WHERE table_name=? AND table_id=? AND field=? AND locale=? AND deleted_at IS NULL ORDER BY version DESC,translation_id DESC LIMIT 1');
            $query->execute([$table,$id,$field,$language]);$translation=$query->fetchColumn();
            if($translation!==false){
                $query=$pdo->prepare('UPDATE '.$config['store'].' SET value=?,updated_at=CURRENT_TIMESTAMP,updated_by=? WHERE translation_id=?');$query->execute([$value??'',$actor,$translation]);
            }else{
                $query=$pdo->prepare('INSERT INTO '.$config['store'].' (table_name,table_id,field,locale,value,version,created_at,created_by,updated_at,updated_by) VALUES (?,?,?,?,?,1,CURRENT_TIMESTAMP,?,CURRENT_TIMESTAMP,?)');
                $query->execute([$table,$id,$field,$language,$value??'',$actor,$actor]);
            }
            if($own){$pdo->commit();}
        }catch(\Throwable $error){if($own&&$pdo->inTransaction()){$pdo->rollBack();}throw $error;}
    }
}
