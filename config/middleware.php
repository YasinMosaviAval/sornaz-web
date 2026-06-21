<?php

return [

    'auth' => \Core\Middleware\AuthMiddleware::class,

    'guest' => \Core\Auth\GuestMiddleware::class,

    'csrf' => \Core\Csrf\CsrfMiddleware::class,

];