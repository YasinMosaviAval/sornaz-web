<?php

namespace Core\events\Contracts;

use Core\events\EventDispatcher;

interface EventSubscriber {

    public function subscribe(
        EventDispatcher $events
    ): void;
}