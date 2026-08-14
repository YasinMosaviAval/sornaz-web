<?php

namespace Core\database\Concerns;

use Core\database\DatabaseChangeNotifier;

trait BuildsMutationQueries {

    private ?int $lastMutationInsertId = null;



    public function insert(array $data): bool {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute(array_values($data));
        if ($result && $stmt->rowCount() > 0) {
            $id = (int)$this->pdo->lastInsertId();
            if (!$id) {
                foreach ($data as $column => $value) if (str_ends_with($column, '_id') && is_numeric($value)) {$id=(int)$value;break;}
            }
            $this->lastMutationInsertId = $id ?: null;
            DatabaseChangeNotifier::record($this->pdo, $this->table, 'insert', $data, $id ?: null);
        }
        return $result;
    }



    public function update(array $data): bool {
        $sets = [];
        $bindings = [];
        foreach ($data as $column => $value) {
            $sets[] = "{$column} = ?";
            $bindings[] = $value;
        }
        $sql = "UPDATE {$this->table} SET " . implode(',', $sets);
        if ($this->wheres) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
            $bindings = array_merge($bindings, $this->bindings);
        }
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($bindings);
        if ($result && $stmt->rowCount() > 0) DatabaseChangeNotifier::record($this->pdo, $this->table, 'update', $data, $this->mutationEntityId());
        return $result;
    }



    public function delete(): bool {
        $sql = "DELETE FROM {$this->table}";
        if ($this->wheres) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($this->bindings);
        if ($result && $stmt->rowCount() > 0) DatabaseChangeNotifier::record($this->pdo, $this->table, 'delete', [], $this->mutationEntityId());
        return $result;
    }




    public function insertGetId(array $data): int|false {
        if (!$this->insert($data)) {
            return false;
        }
        return $this->lastMutationInsertId ?? (int)$this->pdo->lastInsertId();
    }

    private function mutationEntityId(): ?int {
        foreach (array_reverse($this->bindings) as $value) if (is_numeric($value)) return (int)$value;
        return null;
    }




}
