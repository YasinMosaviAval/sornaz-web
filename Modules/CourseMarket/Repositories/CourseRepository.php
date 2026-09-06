<?php
namespace Modules\CourseMarket\Repositories;

use PDO;

class CourseRepository
{
    public function __construct(private PDO $db) {}

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
        $this->query('INSERT INTO '.$table.' (`'.implode('`,`', array_keys($values)).'`) VALUES ('.implode(',', array_fill(0, count($values), '?')).')', array_values($values));
        return (int)$this->db->lastInsertId();
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
