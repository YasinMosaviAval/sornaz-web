<?php

namespace Core\Database\Aggregates;

use Closure;
use Core\Database\Builder;

class AggregateLoader {

    protected Builder $builder;

    public function __construct(Builder $builder){$this->builder = $builder;}


    public function load(array $models): void {
        if (empty($models)) {return;}
        /*
        |--------------------------------------------------------------------------
        | withCount()
        |--------------------------------------------------------------------------
        */
        foreach ($this->builder->getWithCounts() as $relation => $constraint) {
            $this->loadAggregate($models, $relation, 'count', null, $constraint);
        }
        /*
        |--------------------------------------------------------------------------
        | withExists()
        |--------------------------------------------------------------------------
        */
        foreach ($this->builder->getWithExists() as $relation => $constraint) {
            $this->loadAggregate($models, $relation, 'exists', null, $constraint);
        }
    }

    /**
     * Generic Aggregate Loader
     */
    protected function loadAggregate(array $models, string $relation, string $aggregate, ?string $column, ?Closure $constraint): void {
        $this->builder->loadRelationAggregate($models, $relation, $aggregate, $column, $constraint);
    }



}