<?php

abstract class BaseController {

  protected Db $db;

  public function __construct() {
    $this->db = Db::getInstance();
  }


  // ── View ──────────────────────────────────────────────────────────────────

  /**
   * صفحه کامل رو داخل theme رندر می‌کنه
   * Usage: $this->view('/page/home', 'عنوان', ['key' => 'val']);
   */
  protected function view(string $viewPath, string $pageTitle = null, array $data = []): void {
    View::render($viewPath, $pageTitle, $data);
  }

  /**
   * partial view بدون theme
   */
  protected function partial(string $viewPath, array $data = [], bool $return = false): string|null {
    return View::partial($viewPath, $data, $return);
  }


  // ── JSON ──────────────────────────────────────────────────────────────────

  protected function json(mixed $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
  }

  protected function success(mixed $data = null, string $message = 'OK'): never {
    $this->json(['success' => true, 'message' => $message, 'data' => $data]);
  }

  protected function error(string $message, int $status = 400, mixed $data = null): never {
    $this->json(['success' => false, 'message' => $message, 'data' => $data], $status);
  }


  // ── Auth ──────────────────────────────────────────────────────────────────

  protected function isLoggedIn(): bool {
    return getUserId() > 0;
  }

  protected function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
  }

  /**
   * برای صفحات Web: ریدایرکت به لاگین
   */
  protected function requireLogin(string $redirect = '/login'): void {
    if (!$this->isLoggedIn()) {
      $this->redirect($redirect);
    }
  }

  /**
   * برای API: 401 برمیگردونه
   */
  protected function requireAuth(): void {
    if (!$this->isLoggedIn()) {
      $this->error('Unauthorized', 401);
    }
  }

  /**
   * بررسی نقش — اگه نداشت برای API: 403 JSON، برای Web: 403 متن
   * Usage: $this->requireRole('admin')
   *        $this->requireRole(['admin', 'manager1'])
   */
  protected function requireRole(string|array $roles): void {
    $roles = (array) $roles;
    foreach ($roles as $role) {
      if (has_role(session_get('role', ''), $role)) return;
    }
    if ($this->isApiRequest()) {
      $this->error('Forbidden', 403);
    } else {
      http_response_code(403);
      echo "Forbidden";
      exit;
    }
  }


  // ── Request ───────────────────────────────────────────────────────────────

  protected function input(string $key, mixed $default = null): mixed {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
  }

  protected function post(string $key, mixed $default = null): mixed {
    return $_POST[$key] ?? $default;
  }

  protected function get(string $key, mixed $default = null): mixed {
    return $_GET[$key] ?? $default;
  }

  /**
   * JSON body برای API — parse یک‌بار انجام میشه
   */
  protected function body(): array {
    static $parsed = null;
    if ($parsed === null) {
      $raw    = file_get_contents('php://input');
      $parsed = json_decode($raw, true) ?? [];
    }
    return $parsed;
  }

  protected function isApiRequest(): bool {
    return str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/');
  }


  // ── Redirect ──────────────────────────────────────────────────────────────

  protected function redirect(string $url): never {
    header("Location: $url");
    exit;
  }
}
