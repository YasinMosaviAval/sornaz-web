<?php

namespace Core\Database\Concerns;

use Core\Database\DB;
use Core\Database\Relations\HasMany;
use Core\Database\Relations\HasOne;
use Core\Database\Relations\BelongsTo;
use Core\Database\Relations\BelongsToMany;

trait HasRelationships {


    protected function belongsTo(string $related, string $foreignKey, string $ownerKey = 'id'): BelongsTo {
        return new BelongsTo(
            $this,
            $related,
            $foreignKey,
            $ownerKey
        );
    }


    protected function hasOne(string $related, string $foreignKey, string $localKey = 'id'): HasOne {
        return new HasOne(
            $this,
            $related,
            $foreignKey,
            $localKey
        );
    }


    protected function hasMany(string $related, string $foreignKey, string $localKey = 'id'): HasMany {
        return new HasMany(
            $this,
            $related,
            $foreignKey,
            $localKey
        );
    }


    protected function belongsToMany(string $related, string $pivotTable, string $foreignPivotKey, string $relatedPivotKey, string $localKey, string $relatedKey): BelongsToMany {
        return new BelongsToMany(
            $this,
            $related,
            $pivotTable,
            $foreignPivotKey,
            $relatedPivotKey,
            $localKey,
            $relatedKey
        );
    }


}