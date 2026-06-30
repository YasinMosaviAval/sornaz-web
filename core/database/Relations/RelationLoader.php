<?php

namespace Core\Database\Relations;

class RelationLoader
{
    public function load(
        array $models,
        array $tree
    ): void {

        if (empty($models)) {
            return;
        }

        foreach ($tree as $relationName => $children) {

            $constraint = $children['_constraint'] ?? null;

            unset($children['_constraint']);

            $relation = $models[0]->{$relationName}();

            $relation->initRelation(
                $models,
                $relationName
            );

            $relation->addEagerConstraints(
                $models
            );

            if ($constraint instanceof \Closure) {

                $constraint(
                    $relation->getQuery()
                );

            }

            $results = $relation->getEager();

            $relation->match(
                $models,
                $results,
                $relationName
            );

            if (!empty($children)) {

                $nestedModels = [];

                foreach ($models as $model) {

                    $loaded =
                        $model->getRelation(
                            $relationName
                        );

                    if ($loaded === null) {
                        continue;
                    }

                    if (is_array($loaded)) {

                        $nestedModels = array_merge(
                            $nestedModels,
                            $loaded
                        );

                    } else {

                        $nestedModels[] = $loaded;

                    }

                }

                $this->load(
                    $nestedModels,
                    $children
                );

            }

        }

    }
}