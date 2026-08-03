<?php

namespace Core\database\Concerns;

use Closure;
use Core\database\Relations\HasMany;

trait BuildsRelationAggregates {



    protected function loadRelationAggregate(array $models, string $relationName, string $aggregate, ?string $column = null, ?Closure $constraint = null): void {
        if (empty($models)) {return;}
        $first = $models[0];
        $relation = $first->{$relationName}();
        if (!$relation instanceof HasMany) {return;}
        $relatedClass = $relation->getRelated();
        $foreignKey = $relation->getForeignKey();
        $localKey = $relation->getLocalKey();
        foreach ($models as $model) {
            $query = $relatedClass::query()->where($foreignKey, $model->{$localKey});
            if ($constraint) {
                $constraint($query);
            }
            $count = $query->count();
            $attribute = match ($aggregate) {
                'count' => "{$relationName}_count",
                'exists' => "{$relationName}_exists",
                default => "{$relationName}_{$aggregate}",
            };
            $value = match ($aggregate) {
                'count' => $count,
                'exists' => $count > 0,
                default => $count,
            };
            $model->$attribute = $value;
        }
    }





}