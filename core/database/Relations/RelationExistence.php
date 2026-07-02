<?php

namespace Core\Database\Relations;

use Core\Database\Builder;

class RelationExistence {

    protected Builder $builder;
    protected RelationSubQuery $subQuery;



    public function __construct(Builder $builder) {
        $this->builder = $builder;
        $this->subQuery = new RelationSubQuery($builder);
    }



    public function has(string $relation): Builder {
        $model = new ($this->builder->getModelClass());
        $relationObject = $this->resolveRelation($model, $relation);
        $query = $this->subQuery->exists($relationObject);
        $this->builder->whereExists($query);
        return $this->builder;
    }



    public function doesntHave(string $relation): Builder {
        $model = new ($this->builder->getModelClass());
        $relationObject = $this->resolveRelation($model, $relation);
        $query = $this->subQuery->exists($relationObject);
        $this->builder->whereNotExists($query);
        return $this->builder;
    }



    public function whereHas(string $relation, \Closure $callback): Builder {
        $model = new ($this->builder->getModelClass());
        $relationObject = $this->resolveRelation($model, $relation);
        $query = $this->subQuery->whereExists($relationObject, $callback);
        $this->builder->whereExists($query);
        return $this->builder;
    }




    protected function resolveRelation(object $model, string $path): Relation {
        $parts = explode('.', $path);
        $relation = null;
        foreach ($parts as $part) {
            /** @var Relation $relation */
            $relation = $model->{$part}();
            $model = new ($relation->getRelated());
        }
        return $relation;
    }




}