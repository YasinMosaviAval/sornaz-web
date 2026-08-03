<?php

namespace Core\database\Scopes;

use Core\database\Builder;

interface Scope {

    public function apply(Builder $builder): void;

}