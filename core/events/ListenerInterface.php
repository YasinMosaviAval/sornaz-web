<?php

namespace Core\events;

interface ListenerInterface {

    public function handle(
        object $event
    ): void;
    
}