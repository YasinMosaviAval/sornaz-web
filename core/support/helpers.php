<?php

use Core\Application\Application;
use Core\Auth\Auth;
use Core\Auth\Gate;
use Core\Container\Container;
use Core\Csrf\Csrf;
use Core\Database\Connection;
use Core\Database\DB;
use Core\Events\EventDispatcher;
use Core\Session\Session;
use Core\Http\RedirectResponse;
use Core\Http\Request;
use Core\Http\Response;
use Core\View\View;

function query(): DB {return new DB();}
function user_id(): ?int {return auth()->id();}
function user(): ?array {return auth()->user();}
function app() {return Application::getInstance();}
function container(): Container {return app()->container();}
function auth(): Auth {return container()->make(Auth::class);}
function csrf(): Csrf {return container()->make(Csrf::class);}
function events() {return container()->make(EventDispatcher::class);}
function errors(): array {return session()->peekFlash('_errors', []);}
function asset(string $path): string {return '/' . ltrim($path, '/');}
function session(): Session {return container()->make(Session::class);}
function request(): Request {return container()->make(Request::class);}
function db(): PDO {return container()->make(Connection::class)->pdo();}
function url(string $path = ''): string {return '/' . ltrim($path, '/');}
function response(): Response {return container()->make(Response::class);}
function csrf_token(): string {return container()->make(Csrf::class)->token();}
function view(string $view, array $data = []): View {return new View($view, $data);}
function app_path(string $path = ''): string {return base_path('app/' . ltrim($path, '/'));}
function lang_path(string $path = ''): string {return base_path('lang/' . ltrim($path, '/\\'));}
function back(): RedirectResponse {return new RedirectResponse($_SERVER['HTTP_REFERER'] ?? '/');}
function public_path(string $path = ''): string {return base_path('public/' . ltrim($path, '/'));}
function config_path(string $path = ''): string {return base_path('config/' . ltrim($path, '/'));}
function storage_path(string $path = ''): string {return base_path('storage/' . ltrim($path, '/'));}
function module_path(string $path = ''): string {return base_path('Modules/' . ltrim($path, '/\\'));}
function resource_path(string $path = ''): string {return base_path('resources/' . ltrim($path, '/'));}
function database_path(string $path = ''): string {return base_path('database/' . ltrim($path, '/\\'));}
function can(string $ability, mixed ...$arguments): bool {return Gate::allows($ability, ...$arguments);}
function bootstrap_path(string $path = ''): string {return base_path('bootstrap/' . ltrim($path, '/\\'));}
function cannot(string $ability, mixed ...$arguments): bool {return Gate::denies($ability, ...$arguments);}
function csrf_field(): string {return sprintf('<input type="hidden" name="_token" value="%s">', csrf_token());}
function redirect(string $url, int $status = 302): RedirectResponse {return new RedirectResponse($url, $status);}
function base_path(string $path = ''): string {return dirname(__DIR__, 2) . ($path ? DIRECTORY_SEPARATOR . $path : '');}
function env(string $key, mixed $default = null): mixed {return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;}
function abort(int $code = 404, string $message = ''): never {http_response_code($code); exit($message ?: "HTTP {$code}");}
function old(string $key, mixed $default = ''): mixed {$old = session()->peekFlash('_old', []); return $old[$key] ?? $default;}
function error(string $field): string {$errors = errors(); if (!isset($errors[$field][0])) {return '';} return $errors[$field][0];}

function value($value) {return is_callable($value) ? $value() : $value;}
function dump(...$vars): void {foreach ($vars as $var) {echo '<pre>'; var_dump($var); echo '</pre>';}}
function dd(...$vars): never {foreach ($vars as $var) {echo '<pre>'; var_dump($var); echo '</pre>';} exit;}


function transaction(callable $callback) {
    db()->beginTransaction();
    try {
        $result = $callback();
        db()->commit();
        return $result;
    } catch (\Throwable $e) {
        db()->rollback();
        throw $e;
    }
}


function config(string $key, mixed $default = null): mixed {
    static $configs = [];
    [$file, $item] = explode('.', $key, 2);
    if (!isset($configs[$file])) {
        $configs[$file] = require config_path($file . '.php');
    }
    return $configs[$file][$item] ?? $default;
}



if (!function_exists('component')) {
    function component(string $view, array $data = []): void {
        View::component($view, $data);
    }
}



if (!function_exists('componentExists')) {
    function componentExists(string $view): bool {
        return View::componentExists($view);
    }
}


if (!function_exists('e')) {
    function e(mixed $value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}