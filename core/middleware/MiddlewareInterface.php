<?php

namespace Core\middleware;

use Core\http\Request;

interface MiddlewareInterface {

    public function handle(
        Request $request,
        callable $next
    );

}