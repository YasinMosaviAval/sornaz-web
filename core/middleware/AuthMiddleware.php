<?php

namespace Core\Middleware;

class AuthMiddleware
    implements MiddlewareInterface
{
    public function handle(
        callable $next
    ) {

        echo "Auth Checked<br>";

        return $next();
    }
}