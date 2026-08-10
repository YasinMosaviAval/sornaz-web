<?php

use Core\auth\GuestMiddleware;
use Core\csrf\CsrfMiddleware;
use Core\middleware\AuthMiddleware;
use Modules\Academy\Middleware\AcademyAccountMiddleware;

return [
    'auth' => AuthMiddleware::class,
    'guest' => GuestMiddleware::class,
    'csrf' => CsrfMiddleware::class,
    'academy' => AcademyAccountMiddleware::class,
];
