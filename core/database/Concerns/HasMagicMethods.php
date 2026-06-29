<?php

namespace Core\Database\Concerns;

trait HasMagicMethods
{
    public function __call(string $method, array $arguments)
    {
        return static::query()->$method(...$arguments);
    }

    public static function __callStatic(string $method, array $arguments)
    {
        return static::query()->$method(...$arguments);
    }
}