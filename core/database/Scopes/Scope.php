<?php

namespace Core\Database\Scopes;

use Core\Database\Builder;

interface Scope
{
    public function apply(Builder $builder): void;
}