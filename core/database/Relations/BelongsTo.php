<?php

namespace Core\Database\Relations;

use Core\Database\Model;
use Core\Database\Builder;

class BelongsTo extends BelongsToRelation {


    protected array $eagerKeys = [];



    public function __construct(Model $parent, string $related, string $foreignKey, string $ownerKey) {
        parent::__construct(
            $parent,
            $related,
            $foreignKey,
            $ownerKey
        );
    }



    public function initRelation(array $models, string $relation): void {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }
    }



    public function addEagerConstraints(array $models): void {
        $this->eagerKeys = [];
        foreach ($models as $model) {
            $value = $model->{$this->foreignKey};
            if ($value !== null) {
                $this->eagerKeys[] = $value;
            }
        }
        $this->eagerKeys = array_values(array_unique($this->eagerKeys));
    }



    public function getEager(): array {
        if (empty($this->eagerKeys)) {return [];}
        return $this->newQuery()->whereIn($this->localKey, $this->eagerKeys)->get();
    }



    public function match(array $models, array $results, string $relation): void {
        $dictionary = [];
        foreach ($results as $result) {
            $dictionary[$result->{$this->localKey}] = $result;
        }
        foreach ($models as $model) {
            $key = $model->{$this->foreignKey};
            $model->setRelation($relation, $dictionary[$key] ?? null);
        }
    }



    public function getResults() {
        return $this->newQuery()->where($this->localKey, $this->parent->{$this->foreignKey})->first();
    }





    public function buildExistenceQuery(Builder $query): Builder {
        return $query->whereColumn(
            $this->getRelatedTable().'.'.$this->localKey,
            $this->getParentTable().'.'.$this->foreignKey
        );
    }




}