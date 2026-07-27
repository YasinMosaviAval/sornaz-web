<?php

namespace Core\Middleware;

use Core\Http\Request;

class MiddlewarePipeline {

    protected array $middlewares;
    protected Request $request;

    public function __construct(array $middlewares, Request $request) {
        $this->middlewares = $middlewares;
        $this->request = $request;
    }


    public function then(callable $destination) {
    // return $destination();
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            function ($next, $middleware) {
                return function (Request $request) use ($middleware, $next) {
                    return $middleware->handle($request, $next);
                };
            },
            $destination
        );
        return $pipeline($this->request);
    }
}
