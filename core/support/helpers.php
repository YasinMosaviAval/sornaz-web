<?php

use Core\Session\Session;
use Core\Http\RedirectResponse;
use Core\View\View;

function app() {
    return Core\Application\Application::getInstance();
}

function base_path(string $path = ''): string {
    return dirname(__DIR__, 2) . ($path ? DIRECTORY_SEPARATOR . $path : '');
}

function session(): Session {
    return app()
        ->container()
        ->make(Session::class);
}

function redirect(string $url, int $status = 302): RedirectResponse {
    return new RedirectResponse($url, $status);
}

function back(): RedirectResponse {
    return new RedirectResponse($_SERVER['HTTP_REFERER'] ?? '/');
}


function view(string $view, array $data = []): View {
    return new View($view, $data);
}


function errors(): array {
    return session()->peekFlash(
        '_errors',
        []
    );
}

function error(string $field): string {
    $errors = errors();

    if (!isset($errors[$field][0])) {
        return '';
    }

    return $errors[$field][0];
}

function old(string $key, mixed $default = ''): mixed {
    $old = session()->peekFlash('_old', []);
    return $old[$key] ?? $default;
}


function csrf_token(): string {
    return app()
        ->container()
        ->make(\Core\Csrf\Csrf::class)
        ->token();
}

function csrf_field(): string {
    return sprintf(
        '<input type="hidden" name="_token" value="%s">',
        csrf_token()
    );
}


function auth(): \Core\Auth\Auth {
    return app()
        ->container()
        ->make(
            \Core\Auth\Auth::class
        );
}


function user(): ?array {
    return auth()->user();
}


function user_id(): ?int {
    return auth()->id();
}


function can(
    string $ability,
    mixed ...$arguments
): bool {

    return \Core\Auth\Gate::allows(
        $ability,
        ...$arguments
    );
}


function cannot(
    string $ability,
    mixed ...$arguments
): bool {

    return \Core\Auth\Gate::denies(
        $ability,
        ...$arguments
    );
}


function db(): \PDO
{
    return app()
        ->container()
        ->make(
            \Core\Database\Connection::class
        )
        ->pdo();
}



function query(): \Core\Database\DB
{
    return new \Core\Database\DB();
}




function transaction(
    callable $callback
)
{
    db()->beginTransaction();

    try {

        $result =
            $callback();

        db()->commit();

        return $result;

    } catch (\Throwable $e) {

        db()->rollback();

        throw $e;
    }
}


function events()
{
    return app()
        ->container()
        ->make(
            \Core\Events\EventDispatcher::class
        );
}