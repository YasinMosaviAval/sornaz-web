<?php

namespace Core\database\Concerns;

use Core\database\DatabaseChangeNotifier;
use Core\database\AcademyInsertApproval;

trait BuildsMutationQueries
{

    private ?int $lastMutationInsertId = null;

    public function insert(array $data): bool
    {
        $data = AcademyInsertApproval::apply($this->pdo, $this->table, $data);
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute(array_values($data));
        if ($result && $stmt->rowCount() > 0) {
            $id = (int) $this->pdo->lastInsertId();
            if (!$id) {
                foreach ($data as $column => $value) {
                    if (str_ends_with($column, '_id') && is_numeric($value)) {
                        $id = (int) $value;
                        break;
                    }
                }
            }
            $this->lastMutationInsertId = $id ?: null;
            DatabaseChangeNotifier::record($this->pdo, $this->table, 'insert', $data, $id ?: null);
        }
        return $result;
    }

    public function update(array $data): bool
    {
        if (!empty($data['deleted_at']) && !in_array(\Core\database\TableNames::logical($this->table), ['translations', 'f_translations'], true)) {
            return $this->withTranslationDeletion($data, fn () => $this->executeUpdate($data));
        }
        return $this->executeUpdate($data);
    }

    private function executeUpdate(array $data): bool
    {
        $sets = [];
        $bindings = [];
        foreach ($data as $column => $value) {
            $sets[] = "{$column} = ?";
            $bindings[] = $value;
        }
        $sql = "UPDATE {$this->table} SET " . implode(',', $sets);
        $sql .= ' ' . $this->compileWhere();
        $bindings = array_merge($bindings, $this->bindings);
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($bindings);
        if ($result && $stmt->rowCount() > 0) {
            DatabaseChangeNotifier::record($this->pdo, $this->table, 'update', $data, $this->mutationEntityId());
        }
        return $result;
    }

    public function delete(): bool
    {
        if (!in_array(\Core\database\TableNames::logical($this->table), ['translations', 'f_translations'], true)) {
            return $this->withTranslationDeletion(['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => function_exists('auth') ? auth()->id() : null], fn () => $this->executeDelete());
        }
        return $this->executeDelete();
    }

    private function executeDelete(): bool
    {
        $sql = "DELETE FROM {$this->table}";
        $sql .= ' ' . $this->compileWhere();
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($this->bindings);
        if ($result && $stmt->rowCount() > 0) {
            DatabaseChangeNotifier::record($this->pdo, $this->table, 'delete', [], $this->mutationEntityId());
        }
        return $result;
    }

    private function withTranslationDeletion(array $data, callable $mutation): bool
    {
        $logical = \Core\database\TableNames::logical($this->table);
        $own = !$this->pdo->inTransaction();
        if ($own) $this->pdo->beginTransaction();
        try {
            // Read the exact affected rows before changing them; a WHERE binding is
            // not necessarily the primary key, and bulk updates can delete many rows.
            $driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $query = $this->pdo->prepare("SELECT * FROM {$this->table} " . $this->compileWhere() . ($driver === 'mysql' ? ' FOR UPDATE' : ''));
            $query->execute($this->bindings);
            $rows = $query->fetchAll(\PDO::FETCH_ASSOC);
            $keys = $this->deletionPrimaryKeys();
            if (count($keys) === 1 && $rows) {
                $ids = array_column($rows, $keys[0]);
                $mainStore = $this->pdo instanceof \Core\database\PrefixedPDO ? \Core\database\TableNames::physical('translations') : 'translations';
                foreach (array_unique([$mainStore, 'f_translations']) as $store) {
                    $exists = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite'
                        ? $this->pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?")
                        : $this->pdo->prepare('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=?');
                    $exists->execute([$store]);
                    if (!$exists->fetchColumn()) continue;
                    $query = $this->pdo->prepare("UPDATE `$store` SET deleted_at=?,deleted_by=? WHERE table_name IN (?,?) AND table_id IN (" . implode(',', array_fill(0, count($ids), '?')) . ') AND deleted_at IS NULL');
                    $query->execute([$data['deleted_at'], $data['deleted_by'] ?? null, $logical, \Core\database\TableNames::physical($logical), ...$ids]);
                }
            }
            $result = $mutation();
            if (!$result) throw new \RuntimeException('Deletion failed.');
            if ($own) $this->pdo->commit();
            return $result;
        } catch (\Throwable $error) {
            if ($own && $this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $error;
        }
    }

    private function deletionPrimaryKeys(): array
    {
            if ($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite') {
                $physical = $this->pdo instanceof \Core\database\PrefixedPDO ? \Core\database\TableNames::physical($this->table) : $this->table;
                $columns = $this->pdo->query("PRAGMA table_info($physical)")->fetchAll(\PDO::FETCH_ASSOC);
                $keys = array_column(array_filter($columns, fn ($c) => $c['pk'] > 0), 'name');
            } else {
                $columns = $this->pdo->query("SHOW COLUMNS FROM {$this->table}")->fetchAll(\PDO::FETCH_ASSOC);
                $keys = array_column(array_filter($columns, fn ($c) => $c['Key'] === 'PRI'), 'Field');
            }
        return $keys;
    }

    public function insertGetId(array $data): int|false
    {
        if (!$this->insert($data)) {
            return false;
        }
        return $this->lastMutationInsertId ?? (int) $this->pdo->lastInsertId();
    }

    private function mutationEntityId(): ?int
    {
        foreach (array_reverse($this->bindings) as $value) {
            if (is_numeric($value)) {
                return (int) $value;
            }
        }
        return null;
    }
}
