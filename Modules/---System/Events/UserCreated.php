<?php

namespace Modules\System\Events;

use Modules\System\Models\User;

class UserCreated {
    public function __construct(
        public User $user
    ) {
    }
}