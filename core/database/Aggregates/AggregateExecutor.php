<?php

namespace Core\Database\Aggregates;

use Core\Database\Builder;

class AggregateExecutor
{
    public function execute(
        Builder $query,
        string $aggregate
    ): mixed {

        return match (strtolower($aggregate)) {

            'count' => $query->count(),

            'exists' => $query->count() > 0,

            default => throw new \RuntimeException(
                "Aggregate [{$aggregate}] is not supported."
            ),

        };

    }
}