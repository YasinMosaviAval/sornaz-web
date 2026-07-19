<?php

namespace Core\Router;

class Route {
    
    public string $method;
    public string $uri;
    public mixed $action;
    public array $middlewares = [];


    public function __construct(string $method, string $uri, mixed $action) {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
    }


    public function middleware(string|array $middleware): static {
        $middleware = (array)$middleware;
        $this->middlewares = array_merge($this->middlewares, $middleware);
        return $this;
    }


}