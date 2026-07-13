<?php

namespace Core\Database\Concerns;

use Core\Database\Aggregates\AggregateLoader;

trait ExecutesQueries
{

    public function get(): array {
        $this->applyScopes();
        $stmt = $this->pdo->prepare($this->buildSelect());

        // $sql = $this->buildSelect();
        // dump($sql);
        // dump($this->bindings);
        // die;


        $stmt->execute($this->bindings);
        $rows = $stmt->fetchAll();
        if (!$this->modelClass) {return $rows;}
        $models = array_map(fn($row) => new $this->modelClass($row), $rows);
        $translator = new \Core\Translation\TranslationManager();
        $translator->warmup(
            $this->modelClass::getTable(),
            $models
        );
        $this->eagerLoadRelations($models);
        (new AggregateLoader($this))->load($models);
        return $models;
    }



    public function first(): mixed {
        $this->limit(1);
        return $this->get()[0] ?? null;
    }



    public function find(mixed $id, string $primaryKey = 'user_id'): mixed {
        return $this->where($primaryKey, $id)->first();
    }



    // public function count(): int {
    //     $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
    //     if ($this->wheres) {
    //         $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
    //     }
    //     $stmt = $this->pdo->prepare($sql);
    //     $stmt->execute($this->bindings);
    //     return (int)$stmt->fetch()['total'];
    // }
    public function count(): int {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        /*
        |--------------------------------------------------------------------------
        | Joins
        |--------------------------------------------------------------------------
        */
        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        /*
        |--------------------------------------------------------------------------
        | Wheres
        |--------------------------------------------------------------------------
        */
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return (int) $stmt->fetch()['total'];
    }



    public function paginate(int $page = 1, int $perPage = 20): array {
        $total = $this->count();
        $offset = ($page - 1) * $perPage;
        $data = $this->limit($perPage)->offset($offset)->get();
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage),
        ];
    }










}