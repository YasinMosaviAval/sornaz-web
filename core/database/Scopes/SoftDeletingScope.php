<?php

namespace Core\database\Scopes;

use Core\database\Builder;

class SoftDeletingScope implements Scope {

    public function apply(Builder $builder): void {
        if ($builder->isWithTrashed()) {
            return;
        }
        if ($builder->isOnlyTrashed()) {
            $builder->whereNotNull('deleted_at');
            return;
        }
        $builder->whereNull('deleted_at');
    }


}