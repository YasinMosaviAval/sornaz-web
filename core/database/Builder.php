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
    protected ?string $modelClass = null;
    protected ?string $primaryKey = null;
    protected array $fillable = [];
    protected array $guarded = [];
    protected array $casts = [];
    protected bool $timestamps = false;
    protected bool $softDeletes = false;
    protected array $eagerLoads = [];


    public function __construct(PDO $pdo) {$this->pdo = $pdo;}


    public function table(string $table): static {
        $this->table = $table;
        return $this;
    }


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


    // public function latest(?string $column = null): static {
    //     $this->query->orderBy($column ?? $this->relatedPrimaryKey(), 'DESC');
    //     return $this;
    // }
    public function latest(?string $column = null): static {$column ??= $this->modelClass::getPrimaryKey(); return $this->orderBy($column, 'DESC');}


public function oldest(?string $column = null): static {$column ??= $this->modelClass::getPrimaryKey(); return $this->orderBy($column, 'ASC');}

    // public function oldest(?string $column = null): static {
    //     $this->query->orderBy($column ?? $this->relatedPrimaryKey(), 'ASC');
    //     return $this;
    // }


    public function limit(int $limit): static {
        $this->limit = $limit;
        return $this;
    }



    


    public function get(): array {
        $this->applyScopes();
        $stmt = $this->pdo->prepare($this->buildSelect());
        // echo $this->buildSelect();
        // echo '<pre>';
        // print_r($this->bindings);
        // exit;
        $stmt->execute($this->bindings);
        $rows = $stmt->fetchAll();
        if (!$this->modelClass) {return $rows;}
        $models = array_map(fn($row)=>new $this->modelClass($row), $rows);
        $this->eagerLoadRelations($models);
        return $models;
    }


    public function first(): mixed {
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
        $total = $this->count();
        $offset = ($page - 1) * $perPage;
        $data = $this->limit($perPage)->offset($offset)->get();
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


    public function lastInsertId(): string {return $this->pdo->lastInsertId();}


    // public function find(mixed $id, string $primaryKey = 'user_id'): ?array {return $this->where($primaryKey, $id)->first();}

    public function find(mixed $id, string $primaryKey = 'user_id'): mixed {return $this->where($primaryKey, $id)->first();}


    public function whereIn(string $column, array $values): static {
        if (empty($values)) {
            $this->wheres[] = '1 = 0';
            return $this;
        }
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = "{$column} IN ({$placeholders})";
        $this->bindings = array_merge($this->bindings, $values);
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


    public function isWithTrashed(): bool {return $this->withTrashed;}


    public function isOnlyTrashed(): bool {return $this->onlyTrashed;}


    public function model(string $class): static {
        $this->modelClass = $class;
        $this->primaryKey = $class::getPrimaryKey();
        $this->fillable = $this->getFillable();
        $this->guarded = $this->getGuarded();
        $this->casts = $this->getCasts();
        $this->timestamps = $class::usesTimestamps();
        $this->softDeletes = $class::usesSoftDeletes();
        return $this;
    }

    protected function buildSelect(): string {
        $sql = "SELECT " . implode(',', $this->selects) . " FROM {$this->table}";
        if ($this->joins)  {$sql .= ' ' . implode(' ', $this->joins);}
        if ($this->wheres) {$sql .= ' WHERE ' . implode( ' AND ', $this->wheres);}
        if ($this->orders) {$sql .= ' ORDER BY ' . implode( ',', $this->orders);}
        if ($this->limit)  {$sql .= " LIMIT {$this->limit}";}
        if ($this->offset !== null) {$sql .= " OFFSET {$this->offset}";}
        return $sql;
    }


    protected function applyScopes(): void {
        if($this->scopesApplied){return;}
        $this->scopesApplied=true;
        foreach($this->scopes as $scope){
            $scope->apply($this);
        }
    }


    public function __call(string $method, array $arguments) {
        if (!$this->modelClass) {
            throw new \BadMethodCallException(
                "Method {$method} does not exist."
            );
        }
        $scope = 'scope' . ucfirst($method);
        if (!method_exists($this->modelClass, $scope)) {
            throw new \BadMethodCallException(
                "Method {$method} does not exist."
            );
        }
        $model = new $this->modelClass();
        array_unshift($arguments, $this);
        $result = $model->$scope(...$arguments);
        return $result instanceof self ? $result : $this;
    }


    public function getPrimaryKey(): string {return $this->$primaryKey;}
    public function getFillable(): array {return $this->fillable;}
    public function getGuarded(): array {return $this->guarded;}
    public function getCasts(): array {return $this->casts;}
    public function usesTimestamps(): bool {return $timestamps ?? true;}
    public static function usesSoftDeletes(): bool {return in_array(SoftDeletes::class, class_uses(static::class));}



public function with(string|array $relations): static
{
    foreach ((array)$relations as $key => $value) {

        if (is_int($key)) {
            $this->eagerLoads[$value] = null;
        } else {
            $this->eagerLoads[$key] = $value;
        }

    }

    return $this;
}


public function getEagerLoads(): array
{
    return $this->eagerLoads;
}


protected function eagerLoadRelations(array $models): void
{
    if (empty($this->eagerLoads)) {
        return;
    }

    $tree = $this->parseEagerLoads();

    $this->eagerLoadTree(
        $models,
        $tree
    );
}


protected function collectKeys(array $models, string $key): array
{
    $ids = [];

    foreach ($models as $model) {

        $value = $model->$key;

        if ($value !== null) {
            $ids[] = $value;
        }
    }

    return array_values(array_unique($ids));
}


protected function groupModels(array $rows, string $foreignKey): array
{
    $grouped = [];

    foreach ($rows as $row) {

        $grouped[$row->$foreignKey][] = $row;

    }

    return $grouped;
}


protected function parseEagerLoads(): array
{
    $tree = [];

    foreach ($this->eagerLoads as $relation => $constraint) {

        $parts = explode('.', $relation);

        $current =& $tree;

        foreach ($parts as $part) {

            if (!isset($current[$part])) {
                $current[$part] = [];
            }

            $current =& $current[$part];
        }

        $current['_constraint'] = $constraint;

    }

    return $tree;
}



protected function eagerLoadTree(
    array $models,
    array $tree
): void {

    if (empty($models)) {
        return;
    }

    foreach ($tree as $relationName => $children) {

        $constraint = $children['_constraint'] ?? null;

        unset($children['_constraint']);

        $relation = $models[0]->{$relationName}();

        /*
         * مقدار اولیه Relation
         */
        $relation->initRelation(
            $models,
            $relationName
        );

        /*
         * محدود کردن Query
         */
        $relation->addEagerConstraints(
            $models
        );

        /*
         * اعمال Closure
         */
        if ($constraint instanceof \Closure) {
            $constraint(
                $relation->getQuery()
            );
        }

        /*
         * اجرای Query
         */
        $results = $relation->getEager();

        /*
         * اتصال نتایج به مدل‌ها
         */
        $relation->match(
            $models,
            $results,
            $relationName
        );

        /*
         * Nested Relations
         */
        if (!empty($children)) {

            $nestedModels = [];

            foreach ($models as $model) {

                $loaded = $model->getRelation($relationName);

                if ($loaded === null) {
                    continue;
                }

                if (is_array($loaded)) {
                    $nestedModels = array_merge(
                        $nestedModels,
                        $loaded
                    );
                } else {
                    $nestedModels[] = $loaded;
                }
            }
            if (!empty($nestedModels)) {
                $this->eagerLoadTree(
                    $nestedModels,
                    $children
                );
            }
        }
    }
}




}