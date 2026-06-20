<?php

namespace Core\Application;

use Core\Http\Request;
use Core\Http\Response;
use Core\Router\Router;

class Kernel {
    public function handle() {
        $request = new Request();

        $route = Router::dispatch(
            $request->method(),
            $request->uri()
        );

        $response = new Response();

        if (!$route) {
            $response->send('404 Not Found');
            return;
        }

        $action = $route['action'];
        $params = $route['params'];

        $middlewares = $route['middlewares'] ?? [];


        foreach ($middlewares as $alias) {

            $map = require base_path('config/middleware.php');

            $class = $map[$alias];

            $middleware = app()
                ->container()
                ->make($class);

            $middleware->handle(fn() => null);
        }

        if (is_callable($action)) {
            $result = call_user_func_array($action, $params);
        } elseif (is_array($action)) {
            [$controllerClass, $method] = $action;
            
            $controller = app()
                ->container()
                ->make($controllerClass);

            $result = $this->invokeControllerMethod($controller, $method, $params);
        }

        $response->send($result);
    }


    protected function invokeControllerMethod(object $controller, string $method, array $routeParams) {
        $reflection = new \ReflectionMethod($controller, $method);

        $arguments = [];

        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (isset($routeParams[$name])) {
                $arguments[] = $routeParams[$name];
                continue;
            }

            $type = $parameter->getType();

            if ($type && !$type->isBuiltin()) {
                $arguments[] = app()
                    ->container()
                    ->make($type->getName());
                continue;
            }

            if ( $parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }

            throw new \Exception("Cannot resolve parameter {$name}");
        }

        return $reflection->invokeArgs($controller, $arguments);
    }
}