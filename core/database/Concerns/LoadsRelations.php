<?php

namespace Core\Database\Concerns;

use Core\Database\Relations\RelationLoader;

trait LoadsRelations {


    public function load(string|array $relations): static {
        $this->loadRelations(is_array($relations) ? array_fill_keys($relations, null) : [$relations => null]);
        return $this;
    }


    public function loadMissing(string|array $relations): static {
        $relations = (array)$relations;
        $missing = [];
        foreach ($relations as $relation) {
            if (!$this->relationLoaded($relation)) {
                $missing[] = $relation;
            }
        }
        if (!empty($missing)) {
            $loader = $this->newRelationLoader();
            $loader->parse(array_fill_keys($missing, null));
            $loader->filterTree(fn($relation) => !$this->relationLoaded($relation));
            $this->loadRelationLoader($loader);
        }
        return $this;
    }


    protected function loadRelations(array $relations): void {
        (new RelationLoader())->parse($relations)->load([$this]);
    }


    protected function loadRelationLoader(RelationLoader $loader): void {$loader->load([$this]);}


    protected function newRelationLoader(): RelationLoader {return new RelationLoader();}



}