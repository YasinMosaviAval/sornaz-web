<?php

namespace Core\database\Relations;

use RuntimeException;

class RelationResolver {

    public static function resolve(string $model, string $relation): Relation {
        $instance = new $model();
        if (!method_exists($instance, $relation)) {
            throw new RuntimeException("Relation {$relation} not found.");
        }
        return $instance->{$relation}();
    }

}