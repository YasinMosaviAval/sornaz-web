<?php

namespace Core\Database\Concerns;

use Core\Database\Relations\RelationLoader;

trait LoadsRelations {

    public function load(string|array $relations): static {
        $loader = new RelationLoader();
        $loader->parse(is_array($relations) ? array_fill_keys($relations, null) : [$relations => null])->load([$this]);
        return $this;
    }

}