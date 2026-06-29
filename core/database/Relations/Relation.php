<?php

namespace Core\Database\Relations;

use Core\Database\Builder;

abstract class Relation {


    protected Builder $query;
    protected object $parent;
    protected string $related;
    protected string $foreignKey;
    protected string $localKey;

    public function __construct(Builder $query, object $parent, string $related, string $foreignKey, string $localKey) {
        $this->query = $query;
        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    public function get(): array {return $this->query->get();}
    public function first() {return $this->query->first();}
    public function count(): int {return $this->query->count();}
    public function builder(): Builder {return $this->query;}


    public function getParent(): object {return $this->parent;}
    public function getRelated(): string {return $this->related;}
    public function getForeignKey(): string {return $this->foreignKey;}
    public function getLocalKey(): string {return $this->localKey;}
    public function getQuery(): Builder {return $this->query;}

    
    abstract public function addEagerConstraints(array $models): void;

    abstract public function getEager(): array;
    
    abstract public function match(
        array $models,
        array $results,
        string $relation
    ): void;

    public function initRelation(
        array $models,
        string $relation
    ): array {

        foreach ($models as $model) {
            $model->setRelation($relation, []);
        }

        return $models;
    }


    public function __call($method, $arguments) {
        $result = $this->query->$method(...$arguments);
        if ($result instanceof Builder) {
            return $this;
        }
        return $result;
    }

    protected function relatedPrimaryKey(): string {return $this->related::getPrimaryKey();}

    
    public function getParentKey(): mixed {return $this->parent->{$this->localKey};}
    public function getRelatedPrimaryKey(): string {return $this->relatedPrimaryKey();}



}