<?php

namespace Core\auth;

class Gate {

    protected static array $abilities = [];


    public static function define(string $ability, callable $callback): void {
        static::$abilities[$ability] = $callback;
    }


    public static function allows(string $ability, mixed ...$arguments): bool {
        if (!isset(static::$abilities[$ability])) {
            return false;
        }
        return (bool) call_user_func(static::$abilities[$ability], auth()->user(), ...$arguments);
    }


    public static function denies(string $ability, mixed ...$arguments): bool {
        return !static::allows($ability, ...$arguments);
    }




}