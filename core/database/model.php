<?php

namespace Core\Database;

abstract class Model {
    protected static string $table;
    protected static string $primaryKey = 'id';
    protected array $attributes = [];
    protected static array $events = [];
    protected static array $booted = [];
    protected static array $globalScopes = [];


    public function __construct(array $attributes = []) {
        $this->attributes = $attributes;
    }


    public function __get(string $key) {
        return $this->attributes[$key] ?? null;
    }


    public function toArray(): array {
        return $this->attributes;
    }


    public static function query(): Builder {
        static::bootIfNotBooted();
        $builder = DB::table(static::$table);
        foreach (static::$globalScopes[static::class] ?? [] as $scope) {
            $builder->addScope($scope);
        }
        return $builder;
    }




    public static function all(): array {
        $query = static::query();
        $rows = $query->get();
        return array_map(fn($row) => new static($row), $rows);
    }


    public static function find($id): ?static{
        $row = static::query()->find($id, static::$primaryKey);
        return $row ? new static($row) : null;
    }


    public function save(): bool {
        return DB::table(
            static::$table
        )->insert(
            $this->attributes
        );
    }


    public static function create(array $data): bool {
        $model = new static($data);
        static::fireEvent('creating', $model);
        $result = DB::table(static::$table)->insert($data);
        static::fireEvent('created', $model);
        return $result;
    }

    public static function creating(callable $callback): void {
        static::$events[static::class]['creating'][] = $callback;
    }


    public static function created(callable $callback): void {
        static::$events[static::class]['created'][] = $callback;
    }


    public function update(array $data): bool {
        // $model = new static($data);
        static::fireEvent('updating', $this);
        $result = DB::table(static::$table)
            ->where(static::$primaryKey, $this->{static::$primaryKey})
            ->update($data);
        static::fireEvent('updated', $this);
        return $result;
    }


    public static function updating(callable $callback): void {
        static::$events[static::class]['updating'][] = $callback;
    }


    public static function updated(callable $callback): void {
        static::$events[static::class]['updated'][] = $callback;
    }


    public function delete(): bool {
        // $model = new static($data);
        static::fireEvent('deleting', $this);
        $result = DB::table(static::$table)
            ->where(static::$primaryKey, $this->{static::$primaryKey})
            ->delete();
        static::fireEvent('deleted', $this);
        return $result;
    }


    public static function deleting(callable $callback): void {
        static::$events[static::class]['deleting'][] = $callback;
    }


    public static function deleted(callable $callback): void {
        static::$events[static::class]['deleted'][] = $callback;
    }


    protected static function fireEvent(string $event, Model $model): void {
        foreach (
            static::$events[static::class][$event] ?? []
            as $listener
        ) {
            $listener($model);
        }
    }


    protected function belongsTo(string $related, string $foreignKey, string $ownerKey = 'id') {
        $row = $related::query()
            ->where($ownerKey, $this->$foreignKey)
            ->first();
        return $row ? new $related($row) : null;
    }


    protected function hasMany(string $related, string $foreignKey, string $localKey = 'id'): array {
        $rows = $related::query()
            ->where($foreignKey, $this->$localKey)
            ->get();
        return array_map(fn($row) => new $related($row), $rows);
    }


    protected function hasOne(string $related, string $foreignKey, string $localKey = 'id') {
        $row = $related::query()
            ->where($foreignKey, $this->$localKey)
            ->first();
        return $row ? new $related($row) : null;
    }


    protected function belongsToMany(
        string $related,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        string $localKey,
        string $relatedKey
    ): array {

        $pivotRows =
            DB::table($pivotTable)
            ->where(
                $foreignPivotKey,
                $this->$localKey
            )
            ->get();

        $ids = [];

        foreach ($pivotRows as $row) {
            $ids[] =
                $row[$relatedPivotKey];
        }

        if (!$ids) {
            return [];
        }

        $rows =
            $related::query()
            ->whereIn(
                $relatedKey,
                $ids
            )
            ->get();

        return array_map(
            fn($row) => new $related($row),
            $rows
        );
    }



public static function withTrashed(): Builder
{
    return static::query()
        ->withTrashed();
}


public static function onlyTrashed(): Builder
{
    return static::query()
        ->onlyTrashed();
}



protected static function bootIfNotBooted(): void
{
    $class = static::class;

    if (isset(static::$booted[$class])) {
        return;
    }

    static::$booted[$class] = true;

    static::boot();
}


protected static function boot(): void
{
    static::bootTraits();
}


protected static function bootTraits(): void
{
    $traits = class_uses(static::class);

    foreach ($traits as $trait) {

        $parts = explode('\\', $trait);

        $shortName = end($parts);

        $method = 'boot' . $shortName;

        if (method_exists(static::class, $method)) {
            forward_static_call(
                [static::class, $method]
            );
        }
    }
}



public static function addGlobalScope($scope): void
{
    static::$globalScopes[static::class][] = $scope;
}





}