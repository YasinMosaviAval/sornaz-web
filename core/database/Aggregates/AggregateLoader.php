<?php

namespace Core\Database\Aggregates;

use Core\Database\Builder;

class AggregateLoader
{
    protected Builder $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function load(array $models): void
    {
        if (empty($models)) {
            return;
        }

        if (empty($this->builder->getWithCounts())) {
            return;
        }

        foreach (
            $this->builder->getWithCounts()
            as $relation => $constraint
        ) {

            $this->loadCount(
                $models,
                $relation,
                $constraint
            );

        }
    }

    protected function loadCount(
        array $models,
        string $relation,
        ?\Closure $constraint
    ): void
    {
        $this->builder->loadRelationAggregate(
            $models,
            $relation,
            'count',
            null,
            $constraint
        );
    }
}