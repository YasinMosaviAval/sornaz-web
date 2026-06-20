<?php

class Router {

  private string $uri;
  private string $httpMethod;
  private string $controller;
  private string $method;
  private array  $params = [];

  // ── Boot ──────────────────────────────────────────────────────────────────

  public function __construct() {
    $this->httpMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $this->uri        = $this->parseUri();
  }


  // ── Resolve and Dispatch ──────────────────────────────────────────────────

  public function dispatch(): void {
    global $config;

    $uri = $this->applyRoutes($this->uri, $config['route'] ?? []);
    $this->parseParts($uri);

    // API prefix → ApiController variant
    $controllerClass = ucfirst($this->controller) . 'Controller';

    if (!class_exists($controllerClass)) {
      $this->notFound("Controller '$controllerClass' not found.");
      return;
    }

    $instance = new $controllerClass();

    // برای API: methodGet / methodPost / methodPut / methodDelete
    $httpSuffix  = ucfirst(strtolower($this->httpMethod));
    $apiMethod   = $this->method . $httpSuffix;

    if ($this->isApiRequest() && method_exists($instance, $apiMethod)) {
      call_user_func_array([$instance, $apiMethod], $this->params);
    } elseif (method_exists($instance, $this->method)) {
      call_user_func_array([$instance, $this->method], $this->params);
    } else {
      $this->notFound("Method '{$this->method}' not found in '$controllerClass'.");
    }
  }


  // ── Helpers ───────────────────────────────────────────────────────────────

  private function parseUri(): string {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';

    // strip query string
    if (str_contains($uri, '?')) {
      $uri = strstr($uri, '?', true);
      // populate $_GET manually (already done by PHP, but keep explicit)
    }

    global $config;
    $base = $config['app']['base'] ?? '';
    if ($base && str_starts_with($uri, $base)) {
      $uri = substr($uri, strlen($base));
    }

    return '/' . trim(urldecode($uri), '/');
  }

  private function applyRoutes(string $uri, array $routes): string {
    foreach ($routes as $alias => $target) {
      $pattern = '/^' . str_replace(['/', '*'], ['\\/', '(.*)'], $alias) . '/u';
      if (preg_match($pattern, $uri)) {
        return preg_replace($pattern, $target, $uri);
      }
    }
    return $uri;
  }

  private function parseParts(string $uri): void {
    $parts = explode('/', trim($uri, '/'));

    $this->controller = (strlen($parts[0] ?? '') > 0) ? $parts[0] : 'page';
    $this->method     = (strlen($parts[1] ?? '') > 0) ? $parts[1] : 'home';
    $this->params     = array_slice($parts, 2);
  }

  private function isApiRequest(): bool {
    return str_starts_with($this->uri, '/api/');
  }

  private function notFound(string $detail = ''): void {
    global $config;
    $isLocal = ($config['app']['env'] ?? 'local') === 'local';

    http_response_code(404);

    if ($this->isApiRequest()) {
      header('Content-Type: application/json');
      echo json_encode(['error' => 'Not Found', 'detail' => $isLocal ? $detail : '']);
    } else {
      echo $isLocal ? "<b>404:</b> $detail" : '404 — Page not found.';
    }
  }

  // ── Accessors (برای استفاده در Controller اگه لازم شد) ───────────────────

  public function getController(): string  { return $this->controller; }
  public function getMethod(): string      { return $this->method; }
  public function getParams(): array       { return $this->params; }
  public function getHttpMethod(): string  { return $this->httpMethod; }
}
