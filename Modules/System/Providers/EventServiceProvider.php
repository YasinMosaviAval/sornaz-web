<?php

namespace Modules\System\Providers;

use Modules\System\Events\UserCreated;
use Modules\System\Listeners\SendWelcomeEmail;
use Modules\System\Listeners\CreateTranslationRecord;
use Modules\System\Listeners\WriteAuditLog;

class EventServiceProvider {

    protected array $listen = [
        UserCreated::class => [
            SendWelcomeEmail::class,
            CreateTranslationRecord::class,
            WriteAuditLog::class
        ],
    ];


    public function register(): void {
        foreach (
            $this->listen
            as $event => $listeners
        ) {

            foreach (
                $listeners
                as $listener
            ) {

                events()->listen(
                    $event,
                    $listener
                );
            }
        }
    }
}