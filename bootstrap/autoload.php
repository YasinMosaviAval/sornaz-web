<?php

$loader = require dirname(__DIR__).'/vendor/autoload.php';
$resolveClassPath = require __DIR__.'/class-paths.php';
$applicationRoot = dirname(__DIR__);

spl_autoload_register(static function (string $class) use ($loader, $resolveClassPath, $applicationRoot): void {
    $map = $loader->getClassMap();
    $expected = $map[$class] ?? null;
    if ($expected === null || is_file($expected)) return;
    $actual = $resolveClassPath($applicationRoot, $expected);
    if ($actual === null) return;
    $loader->addClassMap([$class=>$actual]);
    require_once $actual;
}, true, true);

return $loader;
