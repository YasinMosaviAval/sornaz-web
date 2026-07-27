<?php

use Core\Auth\GuestMiddleware;
use Core\Csrf\CsrfMiddleware;
use Core\Middleware\AuthMiddleware;

return [
    'auth' => AuthMiddleware::class,
    'guest' => GuestMiddleware::class,
    'csrf' => CsrfMiddleware::class,
];