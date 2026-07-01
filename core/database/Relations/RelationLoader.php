<?php

namespace Core\Database\Relations;

class RelationLoader {


    protected array $tree = [];
    protected array $parsedTree = [];


    public function parse(array $relations): static {
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
        $this->parsedTree = $tree;
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


    public function load(array $models): void {
        if (!$this->hasTree()) {return;}
        $this->processNode($models, $this->parsedTree);
    }


    public function getTree(): array {return $this->parsedTree;}


    public function setTree(array $tree): static {$this->parsedTree = $tree; return $this;}


    public function mapTree(callable $callback): static {
        $this->parsedTree = $this->mapNode($this->parsedTree, $callback);
        return $this;
    }


    protected function mapNode(array $tree, callable $callback): array {
        $result = [];
        foreach ($tree as $name => $children) {
            if ($name === '_constraint') {
                $result[$name] = $children;
                continue;
            }
            $constraint = $children['_constraint'] ?? null;
            unset($children['_constraint']);
            $children = $this->mapNode($children, $callback);
            if ($constraint !== null) {
                $children['_constraint'] = $constraint;
            }
            $mapped = $callback($name, $children);
            if ($mapped !== null) {
                $result[$name] = $mapped;
            }
        }
        return $result;
    }


    public function filterTree(callable $callback): static {
        $this->parsedTree = $this->filterNode($this->parsedTree, $callback);
        return $this;
    }

    protected function filterNode(array $tree, callable $callback): array {
        $result = [];
        foreach ($tree as $relation => $children) {
            if ($relation === '_constraint') {continue;}
            $constraint = $children['_constraint'] ?? null;
            unset($children['_constraint']);
            $children = $this->filterNode($children, $callback);
            if ($constraint !== null) {
                $children['_constraint'] = $constraint;
            }
            if ($callback($relation, $children)) {
                $result[$relation] = $children;
            }
        }
        return $result;
    }



    public function hasTree(): bool {return !empty($this->parsedTree);}

    public function clear(): static {$this->parsedTree = []; return $this;}


}