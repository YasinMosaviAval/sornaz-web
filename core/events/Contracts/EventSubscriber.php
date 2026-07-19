<?php

namespace Core\Events\Contracts;

use Core\Events\EventDispatcher;

interface EventSubscriber {


    public function subscribe(
        EventDispatcher $events
    ): void;
}