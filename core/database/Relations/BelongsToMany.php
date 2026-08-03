<?php

namespace Core\database\Relations;

use Core\database\DB;
use Core\database\Model;
use Core\database\Builder;

class BelongsToMany extends Relation {

    protected string $pivotTable;
    protected string $relatedPivotKey;
    protected string $relatedKey;
    protected array $eagerKeys = [];
    protected array $pivotRows = [];



    public function __construct(Model $parent, string $related, string $pivotTable, string $foreignPivotKey, string $relatedPivotKey, string $localKey, string $relatedKey) {
        parent::__construct($parent, $related, $foreignPivotKey, $localKey);
        $this->pivotTable = $pivotTable;
        $this->relatedPivotKey = $relatedPivotKey;
        $this->relatedKey = $relatedKey;
    }



    public function getResults() {
        $pivotRows = DB::table($this->pivotTable)->where($this->foreignKey, $this->parent->{$this->localKey})->get();
        $ids = [];
        foreach ($pivotRows as $row) {
            $ids[] = $row[$this->relatedPivotKey];
        }
        if (empty($ids)) {return [];}
        return $this->newQuery()->whereIn($this->relatedKey, $ids)->get();
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
        $this->pivotRows = DB::table($this->pivotTable)->whereIn($this->foreignKey, $this->eagerKeys)->get();
        $ids = [];
        foreach ($this->pivotRows as $row) {
            $ids[] = $row[$this->relatedPivotKey];
        }
        $ids = array_values(array_unique($ids));
        if (empty($ids)) {return [];}
        return $this->newQuery()->whereIn($this->relatedKey, $ids)->get();
    }



    public function initRelation(array $models, string $relation): void {
        foreach ($models as $model) {
            $model->setRelation($relation, []);
        }
    }



    public function match(array $models, array $results, string $relation): void {
        $relatedDictionary = [];
        foreach ($results as $result) {
            $relatedDictionary[$result->{$this->relatedKey}] = $result;
        }
        $pivotDictionary = [];
        foreach ($this->pivotRows as $row) {
            $pivotDictionary[$row[$this->foreignKey]][] = $row[$this->relatedPivotKey];
        }
        foreach ($models as $model) {
            $items = [];
            $pivotIds = $pivotDictionary[$model->{$this->localKey}] ?? [];
            foreach ($pivotIds as $id) {
                if (isset($relatedDictionary[$id])) {
                    $items[] = $relatedDictionary[$id];
                }
            }
            $model->setRelation($relation, $items);
        }
    }


    // TODO: Support has()/whereHas() for BelongsToMany in v1.1
    public function getExistenceQuery(Builder $query): Builder {
        return $query;
    }




}