<?php

namespace Core\Database\Relations;

use Core\Database\Builder;

class RelationExistence
{

    protected Builder $builder;

    public function __construct(
        Builder $builder
    ) {
        $this->builder = $builder;
    }

    public function has(
        string $relation
    ): Builder {

        return $this->builder;

    }

}