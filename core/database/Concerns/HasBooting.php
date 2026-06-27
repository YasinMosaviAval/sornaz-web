<?php

namespace Core\Database\Concerns;

trait HasBooting {


    protected static array $booted = [];


    protected static function bootIfNotBooted(): void {
        $class = static::class;
        if (isset(static::$booted[$class])) {return;}
        static::$booted[$class] = true;
        static::boot();
    }


    protected static function boot(): void {
        static::bootTraits();
    }


    protected static function bootTraits(): void {
        $traits = class_uses(static::class);
        foreach ($traits as $trait) {
            $parts = explode('\\', $trait);
            $shortName = end($parts);
            $method = 'boot' . $shortName;
            if (method_exists(static::class, $method)) {
                forward_static_call([static::class, $method]);
            }
        }
    }


}