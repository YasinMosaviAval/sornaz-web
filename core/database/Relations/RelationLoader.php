<?php

namespace Core\Database\Relations;

class RelationLoader {


    protected array $tree = [];


    public function parse(array $eagerLoads): static {
        $this->tree = [];
        foreach ($eagerLoads as $relation => $constraint) {
            $parts = explode('.', $relation);
            $current =& $this->tree;
            foreach ($parts as $part) {
                if (!isset($current[$part])) {
                    $current[$part] = [];
                }
                $current =& $current[$part];
            }
            $current['_constraint'] = $constraint;
        }
        return $this;
    }


    protected function processNode(array $models, array $tree): void {
        if (empty($models)) {return;}
        foreach ($tree as $relationName => $children) {
            $constraint = $children['_constraint'] ?? null;
            unset($children['_constraint']);
            $relation = $models[0]->{$relationName}();
            $relation->initRelation($models, $relationName);
            $relation->addEagerConstraints($models);
            if ($constraint instanceof \Closure) {
                $constraint($relation->getQuery());
            }
            $results = $relation->getEager();
            $relation->match($models, $results, $relationName);
            if (!empty($children)) {
                $nestedModels = [];
                foreach ($models as $model) {
                    $loaded = $model->getRelation($relationName);
                    if ($loaded === null) {continue;}
                    if (is_array($loaded)) {
                        $nestedModels = array_merge($nestedModels, $loaded);
                    } else {
                        $nestedModels[] = $loaded;
                    }
                }
                $this->processNode($nestedModels, $children);
            }
        }
    }


    public function load(array $models): void {$this->processNode($models, $this->tree);}



}