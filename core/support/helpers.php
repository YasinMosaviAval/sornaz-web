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

