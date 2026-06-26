<?php

namespace Modules\System\Listeners;


class WriteAuditLog {
    public function handle($event)
    {
        echo 'Audit Log Written<br>';
    }
}