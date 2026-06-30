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
            $this->loadRelations(array_fill_keys($missing,null));
        }
        return $this;
    }


    protected function loadRelations(array $relations): void {
        (new RelationLoader())->parse($relations)->load([$this]);
    }

}