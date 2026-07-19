<?php

namespace Core\Database\Relations;

abstract class BelongsToRelation extends Relation {


    public function ownerKey(): string {return $this->localKey;}


    public function addEagerConstraints(array $models): void {
        $keys = [];
        foreach ($models as $model) {
            $keys[] = $model->{$this->foreignKey};
        }
        $this->query->whereIn($this->localKey, array_unique($keys));
    }


    public function getEager(): array {
        return $this->query->get();
    }


    abstract public function match(
        array $models,
        array $results,
        string $relation
    ): void;




}