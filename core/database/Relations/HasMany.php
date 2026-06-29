<?php

namespace Core\Database\Relations;

class HasMany extends HasOneOrMany {


    public function match(
        array $models,
        array $results,
        string $relation
    ): void
    {
        $dictionary = [];

        foreach ($results as $result) {

            $dictionary[
                $result->{$this->foreignKey}
            ][] = $result;
        }

        foreach ($models as $model) {

            $model->setRelation(
                $relation,
                $dictionary[
                    $model->{$this->localKey}
                ] ?? []
            );
        }
    }


}