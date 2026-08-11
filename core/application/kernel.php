<?php

namespace Core\application;

use Core\http\Request;
use Core\http\Response;
use Core\middleware\MiddlewarePipeline;
use Core\router\router;
use Core\http\ResponseInterface;
use Core\validation\ValidationException;
use Exception;
use Throwable;
use ReflectionFunction;
use ReflectionMethod;

class Kernel {

    public function handle() {
        $request = new Request();
        session()->start();
        $locale = session()->get('locale', 'fa');
        app()->setLocale(in_array($locale, ['fa', 'en'], true) ? $locale : 'fa');
        $route = Router::dispatch($request->method(), $request->uri());
        $response = new Response();
        if (!$route) {
            $response->send('404 Not Found');
            return;
        }
        $action = $route['action'];
        $params = $route['params'];
        $middlewares = $route['middlewares'] ?? [];
        $instances = [];
        $map = require base_path('config/middleware.php');
        foreach ($middlewares as $alias) {
            $instances[] = app()
                ->container()
                ->make($map[$alias]);
        }
        $destination = function () use ($action, $params) {
            if (is_callable($action)) {
                return $this->invokeCallable($action, $params);
            }
            if (is_array($action)) {
                [$controllerClass, $method] = $action;
                $controller = app()
                    ->container()
                    ->make($controllerClass);
                return $this->invokeControllerMethod($controller, $method, $params);
            }
            return null;
        };
        try {
            $pipeline = new MiddlewarePipeline($instances, $request);
            $result = $pipeline->then($destination);
            if ($result instanceof ResponseInterface) {
                $result->send();
                return;
            }
            if (is_array($result)) {
                $response->json($result);
                return;
            }
            $response->send((string)$result);
        } catch (ValidationException $e) {
            session()->flash('_errors', $e->errors());
            session()->flash('_old', $_POST);
            back()->send();
        } catch (Throwable $e) {
            $expectsJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
                || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
            if ($expectsJson) {
                error_log('[JSON Request Error] ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                \Core\http\ResponseFactory::json([
                    'success' => false,
                    'message' => 'خطایی در پردازش درخواست رخ داد. لطفاً دوباره تلاش کنید.',
                ], 500)->send();
                return;
            }
            throw $e;
        }
    }



    protected function invokeControllerMethod(object $controller, string $method, array $routeParams) {
        $reflection = new ReflectionMethod($controller, $method);
        $arguments = [];
        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            if (isset($routeParams[$name])) {
                $arguments[] = $routeParams[$name];
                continue;
            }
            $type = $parameter->getType();
            if ($type && !$type->isBuiltin()) {
                $arguments[] = app()->container()->make($type->getName());
                continue;
            }
            if ( $parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }
            throw new Exception("Cannot resolve parameter {$name}");
        }
        return $reflection->invokeArgs($controller, $arguments);
    }



    protected function invokeCallable(callable $callable, array $routeParams = []) {
        $reflection = new ReflectionFunction($callable);
        $arguments = [];
        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            if (isset($routeParams[$name])) {
                $arguments[] = $routeParams[$name];
                continue;
            }
            $type = $parameter->getType();
            if ($type && !$type->isBuiltin()) {
                $arguments[] = app()->container()->make($type->getName());
                continue;
            }
            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }
            throw new Exception("Cannot resolve parameter {$name}");
        }
        return $reflection->invokeArgs($arguments);
    }



}

