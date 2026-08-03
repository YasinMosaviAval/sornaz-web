<?php

namespace Core\database\Concerns;

trait BuildsQueryClauses {



    public function join(string $table, string $first, string $operator, string $second): static {
        $this->joins[] = "INNER JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }



    public function leftJoin(string $table, string $first, string $operator, string $second): static {
        $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }



    public function orderBy(string $column, string $direction = 'ASC'): static {
        $this->orders[] = "{$column} {$direction}";
        return $this;
    }



    public function latest(?string $column = null): static {
        $column ??= $this->modelClass::getPrimaryKey();
        return $this->orderBy($column, 'DESC');
    }



    public function oldest(?string $column = null): static {
        $column ??= $this->modelClass::getPrimaryKey();
        return $this->orderBy($column, 'ASC');
    }



    public function limit(int $limit): static {
        $this->limit = $limit;
        return $this;
    }



    public function offset(int $offset): static {
        $this->offset = $offset;
        return $this;
    }




}