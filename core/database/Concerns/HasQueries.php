<?php

namespace Core\Database\Concerns;

trait HasQueries {


    public static function all(): array {return static::query()->get();}


    public static function find($id): ?static {return static::query()->find($id, static::$primaryKey);}


    public static function first(): ?static {return static::query()->first();}


    public static function where(string $column, mixed $value, string $operator = '=') {return static::query()->where($column, $value, $operator);}


    public static function whereIn(string $column, array $values) {return static::query()->whereIn($column, $values);}


    public static function orderBy(string $column, string $direction = 'ASC') {return static::query()->orderBy($column, $direction);}


    public static function paginate(int $page = 1, int $perPage = 20) {return static::query()->paginate($page, $perPage);}


}