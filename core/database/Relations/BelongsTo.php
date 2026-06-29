<?php

namespace Core\Database\Relations;

class BelongsTo extends BelongsToRelation {



public function match(
    array $models,
    array $results,
    string $relation
): void {

    $dictionary = [];

    foreach ($results as $result) {

        $dictionary[
            $result->{$this->localKey}
        ] = $result;
    }

    foreach ($models as $model) {

        $model->setRelation(
            $relation,
            $dictionary[
                $model->{$this->foreignKey}
            ] ?? null
        );
    }
}




    // public function addEagerConstraints(array $models): void
    // {
    //     $keys = [];

    //     foreach ($models as $model) {
    //         $keys[] = $model->{$this->foreignKey};
    //     }

    //     $this->query->whereIn(
    //         $this->localKey,
    //         array_unique($keys)
    //     );
    // }


}