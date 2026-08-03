<?php

namespace Core\database\Concerns;

use Core\database\Relations\HasMany;
use Core\database\Relations\HasOne;
use Core\database\Relations\BelongsTo;
use Core\database\Relations\BelongsToMany;

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