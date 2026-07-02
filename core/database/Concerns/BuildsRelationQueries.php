<?php

namespace Core\Database\Concerns;

use Core\Database\Relations\RelationExistence;
use Core\Database\Relations\RelationLoader;
use Core\Database\Relations\RelationPath;

trait BuildsRelationQueries {



    public function with(string|array $relations): static {
        foreach ((array)$relations as $key => $value) {
            if (is_int($key)) {
                $this->eagerLoads[$value] = null;
            } else {
                $this->eagerLoads[$key] = $value;
            }
        }
        return $this;
    }



    public function withCount(string|array $relations): static {
        foreach ((array)$relations as $key => $value) {
            if (is_int($key)) {
                $this->withCounts[$value] = null;
            } else {
                $this->withCounts[$key] = $value;
            }
        }
        return $this;
    }



    public function withExists(string|array $relations): static {
        foreach ((array)$relations as $key => $value) {
            if (is_int($key)) {
                $this->withExists[$value] = null;
            } else {
                $this->withExists[$key] = $value;
            }
        }
        return $this;
    }



    public function getWithCounts(): array {return $this->withCounts;}



    public function getWithExists(): array {return $this->withExists;}



    public function getEagerLoads(): array {return $this->eagerLoads;}



    protected function eagerLoadRelations(array $models): void {
        if (empty($this->eagerLoads)) {return;}
        (new RelationLoader())->parse($this->eagerLoads)->load($models);
    }



    protected function parseEagerLoads(): array {return RelationPath::parse($this->eagerLoads);}



    protected function relationExistence(): RelationExistence {
        if ($this->relationExistence === null) {
            $this->relationExistence = new RelationExistence($this);
        }
        return $this->relationExistence;
    }



    public function has(string $relation): static {return $this->relationExistence()->has($relation);}



    public function doesntHave(string $relation): static {return $this->relationExistence()->doesntHave($relation);}



    public function whereHas(string $relation, \Closure $callback): static {return $this->relationExistence()->whereHas($relation, $callback);}



    public function whereRelation(string $relation, string $column, mixed $value, string $operator = '='): static {
        return $this->relationExistence()->whereRelation($relation, $column, $value, $operator);
    }



    public function orWhereHas(string $relation, \Closure $callback): static {
        return $this->relationExistence()->orWhereHas($relation, $callback);
    }




}