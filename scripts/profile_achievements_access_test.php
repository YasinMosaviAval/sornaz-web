<?php
// Isolated controller check: rejected requests must never reach the content service.
spl_autoload_register(static function (string $class): void {
    $path = dirname(__DIR__) . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($path)) require_once $path;
});
function auth(): object {
    return new class {
        public function id(): int { return $GLOBALS['actorId']; }
    };
}

$service = (new ReflectionClass(\Modules\Analytics\Services\AdminProfileContentService::class))->newInstanceWithoutConstructor();
$controller = new \Modules\Analytics\Controllers\Web\AdminProfileContentController($service);
foreach ([2, 99] as $actorId) {
    $GLOBALS['actorId'] = $actorId;
    foreach (['index' => ['awards'], 'store' => ['awards'], 'update' => ['awards', 3], 'status' => ['awards', 3], 'delete' => ['awards', 3], 'vote' => [3]] as $method => $args) {
        $response = $controller->$method(...$args);
        $status = (new ReflectionProperty($response, 'status'))->getValue($response);
        if ($status !== 403) throw new RuntimeException("$method accepted actor $actorId");
    }
}
echo "Profile achievements: non-owner read, write and vote denied before service access.\n";
