<?php

namespace Core\Database\Relations;

abstract class HasOneOrMany extends Relation {


    public function whereKey(mixed $value): static {
        $this->query->where($this->foreignKey, $value);
        return $this;
    }


    public function addEagerConstraints(array $models): void
    {
        $keys = [];

        foreach ($models as $model) {
            $keys[] = $model->{$this->localKey};
        }

        $this->query->whereIn(
            $this->foreignKey,
            array_unique($keys)
        );
    }


    public function getEager(): array
    {
        return $this->query->get();
    }



    abstract public function match(
        array $models,
        array $results,
        string $relation
    ): void;






}