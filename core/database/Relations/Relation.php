<?php

namespace Core\Database\Relations;

use Core\Database\Builder;
use Core\Database\Model;

abstract class Relation {


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


    abstract public function getExistenceQuery(Builder $query): Builder;

}