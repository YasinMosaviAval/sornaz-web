<?php

namespace Core\Database\Relations;

class HasOne extends HasOneOrMany {


    public function initRelation(
        array $models,
        string $relation
    ): array {

        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }

        return $models;
    }



    public function match(
        array $models,
        array $results,
        string $relation
    ): void {

        $dictionary = [];

        foreach ($results as $result) {

            $dictionary[
                $result->{$this->foreignKey}
            ] = $result;
        }

        foreach ($models as $model) {

            $model->setRelation(
                $relation,
                $dictionary[
                    $model->{$this->localKey}
                ] ?? null
            );
        }
    }


}