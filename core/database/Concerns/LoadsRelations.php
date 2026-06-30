<?php

namespace Core\Database\Concerns;

use Core\Database\Relations\RelationLoader;

trait LoadsRelations {

    public function load(string|array $relations): static {
        $loader = new RelationLoader();
        $loader->parse(is_array($relations) ? array_fill_keys($relations, null) : [$relations => null])->load([$this]);
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
            $this->load($missing);
        }
        return $this;
    }



}