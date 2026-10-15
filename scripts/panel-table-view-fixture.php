<?php
namespace Core\database {
    final class DB {
        public static function table(string $table): object {
            return new class($table) {
                public function __construct(private string $table) {}
                public function __call(string $method, array $args): mixed {
                    return $method === 'first' ? ($this->table === 'academies' ? ['user_id' => 10] : null) : $this;
                }
            };
        }
    }
}
namespace {
// Isolated view fixture: no application bootstrap or database connection.
require_once __DIR__ . '/../Modules/Analytics/Services/PanelTableRenderer.php';
require_once __DIR__ . '/../Modules/System/Services/SiteAdminAccess.php';
function e($value): string { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function locale(): string { return $GLOBALS['argv'][2] ?? 'fa'; }
function csrf_token(): string { return 'fixture'; }
function auth(): object { return new class {
    public function id(): int { return 10; }
    public function user(): array { return ['user_id' => 10, 'type' => 'academy']; }
}; }
function component(string $name, array $data = []): void {
    if (!preg_match('/^[a-z-]+$/D', $name)) throw new RuntimeException('Invalid fixture view');
    extract($data);
    require __DIR__ . '/../Modules/Analytics/Resources/Views/sections/' . $name . '.php';
}
$previewPanelMode = 'user';
if (($argv[1] ?? '') === 'data') {
    component('panel-table-template', json_decode($argv[3], true, 512, JSON_THROW_ON_ERROR));
    exit;
}
component($argv[1] ?? 'panel-table-template', ['previewPanelMode' => $previewPanelMode]);
}
