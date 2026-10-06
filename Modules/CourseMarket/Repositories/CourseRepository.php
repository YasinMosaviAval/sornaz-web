<?php
namespace Modules\CourseMarket\Repositories;

use PDO;

class CourseRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function connection(): PDO
    {
        return $this->db;
    }

    public function query(string $sql, array $params = []): array
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->columnCount() ? $statement->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function one(string $sql, array $params = []): ?array
    {
        return $this->query($sql, $params)[0] ?? null;
    }

    public function insert(string $table, array $values): int
    {
        $config=require __DIR__.'/../../../config/translated-fields.php';
        $texts=array_intersect_key($values,array_flip($config[$table]['fields']??[]));
        $values=array_diff_key($values,$texts);
        $own=$texts&&!$this->db->inTransaction();if($own){$this->db->beginTransaction();}
        try{
            $this->query('INSERT INTO ' . $table . ' (`' . implode('`,`', array_keys($values)) . '`) VALUES (' . implode(',', array_fill(0, count($values), '?')) . ')', array_values($values));
            $id=(int)$this->db->lastInsertId();
            foreach($texts as $field=>$value){\Core\translation\EntityText::save($table,$id,$field,$value,(int)($values['user_id']??$values['owner_id']??0),$this->db,$values['locale']??null);}
            if($own){$this->db->commit();}return $id;
        }catch(\Throwable $error){if($own&&$this->db->inTransaction()){$this->db->rollBack();}throw $error;}
    }

    public function transaction(callable $callback): mixed
    {
        $this->db->beginTransaction();
        try {
            $result = $callback();
            $this->db->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
