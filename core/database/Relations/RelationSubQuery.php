<?php

namespace Core\Database\Relations;

use Core\Database\Builder;

class RelationSubQuery
{
    protected Builder $builder;

    public function __construct(
        Builder $builder
    ) {
        $this->builder = $builder;
    }

    public function make(
        Relation $relation
    ): Builder {

        return $relation->builder();

    }
}