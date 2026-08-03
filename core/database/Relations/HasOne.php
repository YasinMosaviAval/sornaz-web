<?php

namespace Core\database\Relations;

use Core\database\Model;
use Core\database\Builder;

class HasOne extends HasOneOrMany {

    protected array $eagerKeys = [];



    public function __construct(Model $parent, string $related, string $foreignKey, string $localKey) {
        parent::__construct($parent, $related, $foreignKey, $localKey);
    }



    public function initRelation(array $models, string $relation): void {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }
    }



    public function addEagerConstraints(array $models): void {
        $this->eagerKeys = [];
        foreach ($models as $model) {
            $this->eagerKeys[] = $model->{$this->localKey};
        }
        $this->eagerKeys = array_values(array_unique($this->eagerKeys));
    }



    public function getEager(): array {
        if (empty($this->eagerKeys)) {return [];}
        return $this->newQuery()->whereIn($this->foreignKey, $this->eagerKeys)->get();
    }



    public function match(array $models, array $results, string $relation): void {
        $dictionary = [];
        foreach ($results as $result) {
            $dictionary[$result->{$this->foreignKey}] = $result;
        }
        foreach ($models as $model) {
            $key = $model->{$this->localKey};
            $model->setRelation($relation, $dictionary[$key] ?? null);
        }
    }



    public function getResults() {
        return $this->newQuery()->where($this->foreignKey, $this->parent->{$this->localKey})->first();
    }



    public function getExistenceQuery(Builder $query): Builder {
        return $query->whereColumn(
            $this->getRelatedTable().'.'.$this->foreignKey,
            $this->getParentTable().'.'.$this->localKey
        );
    }






}