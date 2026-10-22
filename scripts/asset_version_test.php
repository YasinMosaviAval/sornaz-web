<?php
// Real asset helpers with disposable local files; no application or database boot.
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) {
    if (isset($map[$class])) require_once $map[$class];
});
require_once __DIR__ . '/../core/support/helpers.php';
$module = 'AssetVersionFixture' . bin2hex(random_bytes(6));
$root = base_path('assets/' . $module);
mkdir($root . '/js', 0700, true);
mkdir($root . '/css', 0700, true);
$js = $root . '/js/fixture.js';
$css = $root . '/css/fixture.css';
try {
    $property = new ReflectionProperty(Core\view\View::class, 'currentModule');
    $property->setValue(null, $module);
    Core\view\View::pushScript('fixture.js');
    Core\view\View::pushStyle('fixture.css');
    $stamp = time() - 600;
    file_put_contents($js, 'console.log("old01");');
    file_put_contents($css, 'body{color:#111111}');
    touch($js, $stamp); touch($css, $stamp);
    clearstatcache();
    $oldScript = scripts(); $oldStyle = styles();
    file_put_contents($js, 'console.log("new01");');
    file_put_contents($css, 'body{color:#222222}');
    touch($js, $stamp); touch($css, $stamp);
    clearstatcache();
    $newScript = scripts(); $newStyle = styles();
    if ($oldScript === $newScript || $oldStyle === $newStyle) throw new LogicException('Changed content with preserved timestamp still uses the old cached URL');
    if ($newScript !== scripts() || $newStyle !== styles()) throw new LogicException('Unchanged content produces unstable URLs');
    touch($js, $stamp + 30); touch($css, $stamp + 30);
    clearstatcache();
    if ($newScript !== scripts() || $newStyle !== styles()) throw new LogicException('Timestamp-only upload invalidates unchanged content');
    if (!str_contains($newScript, '/assets/' . $module . '/js/fixture.js?v=')) throw new LogicException('Public asset path changed');
    echo "Asset versions: real JS/CSS helpers, same-size replacements, preserved timestamps and stable unchanged URLs passed.\n";
} finally {
    foreach ([$js, $css] as $path) if (is_file($path)) unlink($path);
    rmdir($root . '/js'); rmdir($root . '/css'); rmdir($root);
}
