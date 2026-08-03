<?php

namespace Core\database\Concerns;

trait HasObservers {

    protected static array $observers = [];

    public static function observe(string $observer): void{
        static::$observers[static::class][] = $observer;
    }

    protected static function fireObservers(string $event, self $model): void {
        foreach (static::$observers[static::class] ?? [] as $observerClass) {
            $observer = app()->container()->make($observerClass);
            if (method_exists($observer, $event)) {
                $observer->$event($model);
            }
        }
    }


}