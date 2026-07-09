<?php

namespace Core\Database;

use PDO;
use Core\Database\Relations\RelationExistence;

use Core\Database\Concerns\BuildsWhereQueries;
use Core\Database\Concerns\BuildsRelationQueries;
use Core\Database\Concerns\BuildsRelationAggregates;
use Core\Database\Concerns\BuildsSelectQueries;
use Core\Database\Concerns\BuildsQueryCompiler;
use Core\Database\Concerns\BuildsMutationQueries;
use Core\Database\Concerns\ExecutesQueries;
use Core\Database\Concerns\BuildsQueryClauses;

class Builder {


    use BuildsWhereQueries;
    use BuildsRelationQueries;
    use BuildsRelationAggregates;
    use BuildsSelectQueries;
    use BuildsQueryCompiler;
    use BuildsMutationQueries;
    use ExecutesQueries;
    use BuildsQueryClauses;


    /*
    |--------------------------------------------------------------------------
    | Connection
    |--------------------------------------------------------------------------
    */
    protected PDO $pdo;
    protected string $table;
    protected ?string $modelClass = null;
    protected ?string $primaryKey = null;
    /*
    |--------------------------------------------------------------------------
    | Query State
    |--------------------------------------------------------------------------
    */
    protected array $selects = ['*'];
    protected bool $distinct = false;
    protected array $joins = [];
    protected array $wheres = [];
    protected array $rawWheres = [];
    protected array $bindings = [];
    protected array $orders = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    */
    protected array $fillable = [];
    protected array $guarded = [];
    protected array $casts = [];
    protected bool $timestamps = false;
    protected bool $softDeletes = false;
    protected bool $withTrashed = false;
    protected bool $onlyTrashed = false;

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    protected array $scopes = [];
    protected bool $scopesApplied = false;

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    protected array $eagerLoads = [];
    protected array $withCounts = [];
    protected array $withExists = [];
    protected ?RelationExistence $relationExistence = null;



    public function __construct(PDO $pdo) {$this->pdo = $pdo;}



    public function table(string $table): static {
        $this->table = $table;
        return $this;
    }



    public function lastInsertId(): string {return $this->pdo->lastInsertId();}



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



    protected function applyScopes(): void {
        if($this->scopesApplied){return;}
        $this->scopesApplied=true;
        foreach($this->scopes as $scope){
            $scope->apply($this);
        }
    }



    public function __call(string $method, array $arguments) {
        if (!$this->modelClass) {
            throw new \BadMethodCallException("Method {$method} does not exist.");
        }
        $scope = 'scope' . ucfirst($method);
        if (!method_exists($this->modelClass, $scope)) {
            throw new \BadMethodCallException("Method {$method} does not exist.");
        }
        $model = new $this->modelClass();
        array_unshift($arguments, $this);
        $result = $model->$scope(...$arguments);
        return $result instanceof self ? $result : $this;
    }



    public function getPrimaryKey(): string {return $this->primaryKey;}
    public function getFillable(): array {return $this->fillable;}
    public function getGuarded(): array {return $this->guarded;}
    public function getCasts(): array {return $this->casts;}
    public function usesTimestamps(): bool {return $this->timestamps;}
    public static function usesSoftDeletes(): bool {return in_array(SoftDeletes::class, class_uses(static::class));}



    protected function collectKeys(array $models, string $key): array {
        $ids = [];
        foreach ($models as $model) {
            $value = $model->$key;
            if ($value !== null) {
                $ids[] = $value;
            }
        }
        return array_values(array_unique($ids));
    }



    protected function groupModels(array $rows, string $foreignKey): array {
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row->$foreignKey][] = $row;
        }
        return $grouped;
    }



    public function getModelClass(): ?string {return $this->modelClass;}



    public function toSql(): string {return $this->buildSelect();}



    public function getBindings(): array {return $this->bindings;}



    public function whereExists(Builder $query): static {
        $this->rawWheres[] = 'EXISTS (' . $query->toSql() . ')';
        $this->bindings = array_merge($this->bindings, $query->getBindings());
        return $this;
    }



    public function whereNotExists(Builder $query): static {
        $this->rawWheres[] = 'NOT EXISTS (' . $query->toSql() . ')';
        $this->bindings = array_merge($this->bindings, $query->getBindings());
        return $this;
    }



    public function orWhereExists(Builder $query): static {
        $sql = 'OR EXISTS (' . $query->toSql() . ')';
        $this->rawWheres[] = $sql;
        $this->bindings = array_merge($this->bindings, $query->getBindings());
        return $this;
    }


    public function exists(): bool {
        return $this->first() !== null;
    }





}