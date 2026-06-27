<?php

namespace Core\Database\Concerns;

use Core\Database\Model;

trait HasEvents {
    /**
     * Registered model events.
     *
     * [
     *     User::class => [
     *         'creating' => [],
     *         'created'  => [],
     *     ]
     * ]
     */
    protected static array $events = [];


    /**
     * Register an event listener.
     */
    protected static function registerEvent(string $event, callable $callback): void {static::$events[static::class][$event][] = $callback;}


    /**
     * Generic event registration.
     */
    public static function on(string $event, callable $callback): void {static::registerEvent($event, $callback);}


    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */
    public static function booting(callable $callback): void {static::registerEvent('booting', $callback);}
    public static function booted(callable $callback): void {static::registerEvent('booted', $callback);}

    public static function retrieving(callable $callback): void {static::registerEvent('retrieving', $callback);}
    
    public static function creating(callable $callback): void {static::registerEvent('creating', $callback);}
    public static function created(callable $callback): void {static::registerEvent('created', $callback);}
    
    public static function saving(callable $callback): void {static::registerEvent('saving', $callback);}
    public static function saved(callable $callback): void {static::registerEvent('saved', $callback);}
    
    public static function updating(callable $callback): void {static::registerEvent('updating', $callback);}
    public static function updated(callable $callback): void {static::registerEvent('updated', $callback);}
    
    public static function deleting(callable $callback): void {static::registerEvent('deleting', $callback);}
    public static function deleted(callable $callback): void {static::registerEvent('deleted', $callback);}
    
    public static function restoring(callable $callback): void {static::registerEvent('restoring', $callback);}
    public static function restored(callable $callback): void {static::registerEvent('restored', $callback);}
    
    public static function forceDeleting(callable $callback): void {static::registerEvent('forceDeleting', $callback);}
    public static function forceDeleted(callable $callback): void {static::registerEvent('forceDeleted', $callback);}


    
    /**
     * Fire an event.
     *
     * Returning false from a listener
     * stops the remaining listeners.
     */
    protected static function fireEvent(string $event, Model $model): bool {
        foreach (static::$events[static::class][$event] ?? [] as $listener) {
            $result = $listener($model);
            if ($result === false) {return false;}
        }
        static::fireObservers($event, $model);
        return true;
    }


    /**
     * Remove all listeners.
     */
    public static function flushEvents(): void {unset(static::$events[static::class]);}


    /**
     * Get registered listeners.
     */
    public static function getEvents(): array {return static::$events[static::class] ?? [];}



}