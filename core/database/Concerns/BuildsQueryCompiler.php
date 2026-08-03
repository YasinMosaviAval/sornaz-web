<?php

namespace Core\database\Concerns;

trait BuildsQueryCompiler {


    protected function compileDistinct(): string {return $this->distinct ? 'DISTINCT ' : '';}



    protected function compileSelect(): string {
        return 'SELECT ' . $this->compileDistinct() . implode(',', $this->selects) . " FROM {$this->table}";
    }



    protected function compileJoins(): string {
        return empty($this->joins) ? '' : implode(' ', $this->joins);
    }



    protected function compileWhere(): string {
        $parts = array_merge($this->wheres, $this->rawWheres);
        if (empty($parts)) {return '';}
        return 'WHERE ' . implode(' AND ', $parts);
    }



    protected function compileOrderBy(): string {
        if (empty($this->orders)) {return '';}
        return 'ORDER BY ' . implode(',', $this->orders);
    }



    protected function compileLimit(): string {
        if ($this->limit === null) {return '';}
        return "LIMIT {$this->limit}";
    }



    protected function compileOffset(): string {
        if ($this->offset === null) {return '';}
        return "OFFSET {$this->offset}";
    }



    protected function buildSelect(): string {
        return implode(
            ' ',
            array_filter([
                $this->compileSelect(),
                $this->compileJoins(),
                $this->compileWhere(),
                $this->compileOrderBy(),
                $this->compileLimit(),
                $this->compileOffset(),
            ])
        );
    }








}