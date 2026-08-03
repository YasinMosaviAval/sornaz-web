<?php

namespace Core\database\Concerns;

use Core\database\Builder;
use Core\database\DB;

trait HasGlobalScopes {


    // protected static string $table;
    protected static array $globalScopes = [];


    public static function query(): Builder {
        static::bootIfNotBooted();
        $builder = DB::table(static::$table)->model(static::class);
        foreach (static::$globalScopes[static::class] ?? [] as $scope) {
            $builder->addScope($scope);
        }
        return $builder;
    }


    public static function addGlobalScope(object $scope): void {
        static::$globalScopes[static::class][] = $scope;
    }


    public static function withTrashed(): Builder {
        return static::query()->withTrashed();
    }


    public static function onlyTrashed(): Builder {
        return static::query()->onlyTrashed();
    }






}