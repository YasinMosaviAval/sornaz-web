<?php

namespace Core\Database;

use PDO;

class Builder {
    protected PDO $pdo;
    protected string $table;
    protected array $wheres = [];
    protected array $bindings = [];
    protected array $orders = [];
    protected ?int $limit = null;
    protected array $selects = ['*'];
    protected array $joins = [];
    protected ?int $offset = null;
    protected bool $withTrashed = false;
    protected bool $onlyTrashed = false;
    protected array $scopes = [];
    protected bool $scopesApplied=false;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function table(string $table): static {
        $this->table = $table;
        return $this;
    }

    // public function where(string $column, mixed $value, string $operator = '='): static {
    //     $this->wheres[] = "{$column} {$operator} ?";
    //     $this->bindings[] = $value;
    //     return $this;
    // }

    public function where(string $column, mixed $value, string $operator = '='): static {
        if (strtoupper($operator) === 'IS' && $value === null) {
            $this->wheres[] = "{$column} IS NULL";
            return $this;
        }
        if (strtoupper($operator) === 'IS NOT' && $value === null) {
            $this->wheres[] = "{$column} IS NOT NULL";
            return $this;
        }
        $this->wheres[] = "{$column} {$operator} ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static {
        $this->orders[] = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): static {
        $this->limit = $limit;
        return $this;
    }

    protected function buildSelect(): string {
        $sql = "SELECT " . implode(',', $this->selects) . " FROM {$this->table}";

        if ($this->joins) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if ($this->wheres) {
            $sql .= ' WHERE ' . implode( ' AND ', $this->wheres);
        }

        if ($this->orders) {
            $sql .= ' ORDER BY ' . implode( ',', $this->orders);
        }

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }


    public function get(): array {
        // $this->applyScopes();
        $stmt = $this->pdo->prepare($this->buildSelect());
        $stmt->execute($this->bindings);
        return $stmt->fetchAll();
    }



    public function first(): ?array {
        // $this->applyScopes();
        $this->limit(1);
        return $this->get()[0] ?? null;
    }


    public function select(array|string $columns): static {
        $this->selects = (array) $columns;
        return $this;
    }



    public function join(string $table, string $first, string $operator, string $second): static {
        $this->joins[] = "INNER JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }




    public function leftJoin(string $table, string $first, string $operator, string $second): static {
        $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }


    public function count(): int {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        if ($this->wheres) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return (int) $stmt->fetch()['total'];
    }


    public function offset(int $offset): static {
        $this->offset = $offset;
        return $this;
    }


    public function paginate(int $page = 1, int $perPage = 20): array {
        // $this->applyScopes();
        $total = $this->count();
        $offset = ($page - 1) * $perPage;
        $data = $this->limit($perPage)
            ->offset($offset)
            ->get();
            
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage)
        ];
    }



    public function insert(array $data): bool {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_values($data));
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
        return $stmt->execute($bindings);
    }




    public function delete(): bool {
        $sql = "DELETE FROM {$this->table}";
        if ($this->wheres) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->bindings);
    }



    public function lastInsertId(): string {
        return $this->pdo->lastInsertId();
    }


    public function find(mixed $id, string $primaryKey = 'user_id'): ?array {
        return $this
            ->where($primaryKey, $id)
            ->first();
    }



    public function whereIn(
        string $column,
        array $values
    ): static {

        if (empty($values)) {
            $this->wheres[] = '1 = 0';
            return $this;
        }

        $placeholders = implode(
            ',',
            array_fill(
                0,
                count($values),
                '?'
            )
        );

        $this->wheres[] =
            "{$column} IN ({$placeholders})";

        $this->bindings =
            array_merge(
                $this->bindings,
                $values
            );

        return $this;
    }


    public function whereNull(string $column): static {
        $this->wheres[] = "{$column} IS NULL";
        return $this;
    }


    public function whereNotNull(string $column): static {
        $this->wheres[] = "{$column} IS NOT NULL";
        return $this;
    }


    public function withTrashed(): static {
        $this->withTrashed = true;
        return $this;
    }


    public function onlyTrashed(): static {
        $this->onlyTrashed = true;
        return $this;
    }


    public function addScope($scope): static {
        $this->scopes[] = $scope;
        return $this;
    }


    protected function applyScopes(): void {
        if($this->scopesApplied){return;}
        $this->scopesApplied=true;
        foreach($this->scopes as $scope){
            $scope->apply($this);
        }
    }


    public function isWithTrashed(): bool {
        return $this->withTrashed;
    }


    public function isOnlyTrashed(): bool {
        return $this->onlyTrashed;
    }







}