<?php

namespace Core\Database\Relations;

use Core\Database\Builder;

class RelationSubQuery
{
    protected Builder $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function make(Relation $relation): Builder
    {
        return $relation->newQuery();
    }

    public function exists(Relation $relation): Builder
    {
        $query = $this->make($relation);

        $query->selectRaw('1');

        return $relation->getExistenceQuery($query);
    }

    public function whereExists(
        Relation $relation,
        ?\Closure $callback = null
    ): Builder {

        $query = $this->exists($relation);

        if ($callback) {
            $callback($query);
        }

        return $query;
    }
}