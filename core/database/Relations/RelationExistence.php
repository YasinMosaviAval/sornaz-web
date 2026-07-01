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
        $relationObject = $model->{$relation}();
        $this->subQuery->make($relationObject);
        return $this->builder;
    }

}