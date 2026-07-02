<?php

namespace Core\Database\Concerns;

use Core\Database\Builder;

trait BuildsWhereQueries {



    public function where(string $column, mixed $value, string $operator = '='): static {
        if (strtoupper($operator) === 'IS' && $value === null) {
            $this->wheres[] = "{$column} IS NULL";
            return $this;
        }
        if (strtoupper($operator) === 'IS NOT' && $value === null) {
            $this->wheres[] = "{$column} IS NOT NULL";
            return $this;
        }
        $this->wheres[] = "{$column} {$operator} ?";
        $this->bindings[] = $value;
        return $this;
    }



    public function orWhere(string $column, mixed $value, string $operator = '='): static {
        $condition = "{$column} {$operator} ?";
        if (empty($this->wheres) && empty($this->rawWheres)) {
            $this->wheres[] = $condition;
        } else {
            $this->rawWheres[] = 'OR ' . $condition;
        }
        $this->bindings[] = $value;
        return $this;
    }



    public function whereRaw(string $sql, array $bindings = []): static {
        $this->rawWheres[] = $sql;
        $this->bindings = array_merge($this->bindings, $bindings);
        return $this;
    }



    public function orWhereRaw(string $sql, array $bindings = []): static {
        $this->rawWheres[] = 'OR ' . $sql;
        $this->bindings = array_merge($this->bindings, $bindings);
        return $this;
    }



    public function whereColumn(string $first, string $second, string $operator = '='): static {
        $this->wheres[] = "{$first} {$operator} {$second}";
        return $this;
    }



    public function whereIn(string $column, array $values): static {
        if (empty($values)) {
            $this->wheres[] = '1 = 0';
            return $this;
        }
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = "{$column} IN ({$placeholders})";
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }



    public function whereNull(string $column): static {
        $this->wheres[] = "{$column} IS NULL";
        return $this;
    }



    public function whereNotNull(string $column): static {
        $this->wheres[] = "{$column} IS NOT NULL";
        return $this;
    }



}