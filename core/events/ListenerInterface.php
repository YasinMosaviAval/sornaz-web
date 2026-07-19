<?php

namespace Core\Events;

interface ListenerInterface {


    public function handle(
        object $event
    ): void;
    
}