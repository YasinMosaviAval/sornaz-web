<?php

namespace Core\Events;

class EventDispatcher {


    protected array $listeners = [];


    public function listen(string $event, callable|string $listener): void {
        $this->listeners[$event][] = $listener;
    }


    public function dispatch(object $event): void {
        $eventClass = get_class($event);
        foreach ($this->listeners[$eventClass] ?? [] as $listener) {
            if (is_string($listener)) {
                $instance = app()->container()->make($listener);
                try {
                    $instance->handle($event);
                } catch (\Throwable $e) {
                    // report($e);
                }
                continue;
            }
            $listener($event);
        }
    }



}
