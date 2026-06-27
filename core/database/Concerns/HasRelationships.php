<?php

namespace Core\Database\Concerns;

use Core\Database\DB;

trait HasRelationships {


    protected function belongsTo(string $related, string $foreignKey, string $ownerKey = 'id') {return $related::query()->where($ownerKey, $this->$foreignKey)->first();}
    protected function hasOne   (string $related, string $foreignKey, string $localKey = 'id') {return $related::query()->where($foreignKey, $this->$localKey)->first();}
    protected function hasMany  (string $related, string $foreignKey, string $localKey = 'id'): array {return $related::query()->where($foreignKey, $this->$localKey)->get();}

    protected function belongsToMany(string $related, string $pivotTable, string $foreignPivotKey, string $relatedPivotKey, string $localKey, string $relatedKey): array {
        $pivotRows = DB::table($pivotTable)->where($foreignPivotKey, $this->$localKey)->get();
        $ids = [];
        foreach ($pivotRows as $row) {
            $ids[] = $row[$relatedPivotKey];
        }
        if (!$ids) {return [];}
        return $related::query()->whereIn($relatedKey, $ids)->get();
    }


}