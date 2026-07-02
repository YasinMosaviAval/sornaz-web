<?php

namespace Core\Database\Relations;

class RelationPath
{
    public static function parse(array $relations): array
    {
        $tree = [];

        foreach ($relations as $relation => $constraint) {

            $parts = explode('.', $relation);

            $current =& $tree;

            foreach ($parts as $part) {

                if (!isset($current[$part])) {
                    $current[$part] = [];
                }

                $current =& $current[$part];
            }

            $current['_constraint'] = $constraint;
        }

        return $tree;
    }
}