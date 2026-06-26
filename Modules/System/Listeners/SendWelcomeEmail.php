<?php

namespace Modules\System\Listeners;

use Core\Events\ListenerInterface;

class SendWelcomeEmail implements ListenerInterface {
    public function handle(
        object $event
    ): void {

        echo 'Email Sent<br>';
    }
}