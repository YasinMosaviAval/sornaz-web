<?php

namespace Core\Database\Relations;

use Core\Database\Builder;
use Core\Database\Model;

abstract class Relation {

/*
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
    
    abstract public function match(array $models, array $results, string $relation): void;

    public function initRelation(array $models, string $relation): array {
        foreach ($models as $model) {
            $model->setRelation($relation, []);
        }
        return $models;
    }


    public function __call($method, $arguments) {
        $result = $this->query->$method(...$arguments);
        if ($result instanceof Builder) {return $this;}
        return $result;
    }

    protected function relatedPrimaryKey(): string {return $this->related::getPrimaryKey();}


    public function getParentKey(): mixed {return $this->parent->{$this->localKey};}
    public function getRelatedPrimaryKey(): string {return $this->relatedPrimaryKey();}
    public function getParentTable(): string {return $this->parent::getTable();}
    public function getRelatedTable(): string {return $this->related::getTable();}
*/


    protected Model $parent;
    protected string $related;
    protected string $foreignKey;
    protected string $localKey;


    public function __construct(Model $parent, string $related, string $foreignKey, string $localKey) {
        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    /**
     * یک Query جدید از مدل مرتبط
     */
    public function newQuery(): Builder {return $this->related::query();}

    /**
     * کلاس مدل مرتبط
     */
    public function getRelated(): string {return $this->related;}

    /**
     * مدل والد
     */
    public function getParent(): Model {return $this->parent;}

    /**
     * جدول والد
     */
    public function getParentTable(): string {return $this->parent::getTable();}

    /**
     * جدول مدل مرتبط
     */
    public function getRelatedTable(): string {return $this->related::getTable();}

    /**
     * کلید خارجی
     */
    public function getForeignKey(): string {return $this->foreignKey;}

    /**
     * کلید محلی
     */
    public function getLocalKey(): string {return $this->localKey;}

    /**
     * Lazy Loading
     */
    abstract public function getResults();

    /**
     * Eager Loading
     */
    abstract public function addEagerConstraints(array $models): void;


    abstract public function initRelation(array $models, string $relation): void;


    abstract public function match(array $models, array $results, string $relation): void;


    abstract public function buildExistenceQuery(Builder $query): Builder;

}