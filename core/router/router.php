<?php

namespace Core\Router;

class Router {
    protected static array $groupStack = [];
    protected static array $routes = [];



    public static function group(array $attributes, callable $callback) {
        static::$groupStack[] = $attributes;
        $callback();
        array_pop(static::$groupStack);
    }



    protected static function currentPrefix(): string {
        $prefix = '';
        foreach (static::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= $group['prefix'];
            }
        }
        return $prefix;
    }



    protected static function addRoute(string $method, string $uri, mixed $action): Route {
        $uri = static::currentPrefix() . $uri;
        $route = new Route($method, $uri, $action);
        static::$routes[$method][] = $route;
        return $route;
    }



    // AI VSCode Copilot suggested this function, but it was commented out in the original code.
    public static function dispatch(string $method, string $uri) {
        $routes = static::$routes[$method] ?? [];
        foreach ($routes as $route) {
            preg_match_all(
                '/\{([a-zA-Z0-9_]+)\}/',
                $route->uri,
                $parameterNames
            );
            $pattern = preg_replace(
                '/\{([a-zA-Z0-9_]+)\}/',
                '([^/]+)',
                $route->uri
            );
            $pattern = '#^' . rtrim($pattern, '/') . '/?$#';
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $params = [];
                foreach ($parameterNames[1] as $index => $name) {
                    $params[$name] = $matches[$index];
                }
                return [
                    'action' => $route->action,
                    'params' => $params,
                    'middlewares' => $route->middlewares
                ];
            }
        }
        return null;
    }


    public static function get(string $uri, mixed $action) {return static::addRoute('GET', $uri, $action);}
    public static function post(string $uri, mixed $action) {return static::addRoute('POST', $uri, $action);}
    public static function put(string $uri, mixed $action) {return static::addRoute('PUT', $uri, $action);}
    public static function patch(string $uri, mixed $action) {return static::addRoute('PATCH', $uri, $action);}
    public static function delete(string $uri, mixed $action) {return static::addRoute('DELETE', $uri, $action);}
}