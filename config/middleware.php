<?php

use Core\auth\GuestMiddleware;
use Core\csrf\CsrfMiddleware;
use Core\middleware\AuthMiddleware;

return [
    'auth' => AuthMiddleware::class,
    'guest' => GuestMiddleware::class,
    'csrf' => CsrfMiddleware::class,
];