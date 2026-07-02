<?php

namespace Core\Database\Concerns;

trait BuildsSelectQueries {



    public function select(string ...$columns): static {
        if (count($columns) === 1 && is_array($columns[0])) {
            $columns = $columns[0];
        }
        $this->selects = $columns;
        return $this;
    }



    public function addSelect(string ...$columns): static {
        if (count($columns) === 1 && is_array($columns[0])) {
            $columns = $columns[0];
        }
        $this->selects = array_merge($this->selects, $columns);
        return $this;
    }



    public function selectRaw(string $expression): static {
        $this->selects[] = $expression;
        return $this;
    }



    public function distinct(bool $value = true): static {
        $this->distinct = $value;
        return $this;
    }





}