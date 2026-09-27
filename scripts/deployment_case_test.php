<?php
$resolve = require __DIR__.'/../bootstrap/class-paths.php';
$root = sys_get_temp_dir().'/sornaz-case-'.bin2hex(random_bytes(6));
$folder = $root.'/core/module';
mkdir($folder, 0775, true);
$file = $folder.'/modulemanager.php';
file_put_contents($file, '<?php // fixture');
try {
    $expected = str_replace('\\','/',realpath($file));
    // Resolver compares directory entries exactly even when run on Windows.
    if ($resolve($root, $root.'/core/Module/ModuleManager.php') !== $expected) throw new RuntimeException('Old lowercase FTP paths were not resolved');
    if ($resolve($root, $root.'/vendor/composer/../../core/Module/ModuleManager.php') !== $expected) throw new RuntimeException('Composer static-map path was not resolved');
    if ($resolve($root, $root.'/core/module/modulemanager.php') !== $expected) throw new RuntimeException('Exact path broken');
    if ($resolve($root, $root.'/core/module/Missing.php') !== null) throw new RuntimeException('Missing file accepted');
    if ($resolve($root, $root.'/../outside.php') !== null) throw new RuntimeException('Traversal accepted');
    if ($resolve($root, dirname($root).'/outside.php') !== null) throw new RuntimeException('Outside path accepted');
    echo "Deployment filename casing: 6 checks passed.\n";
} finally {
    unlink($file);
    foreach ([$folder,dirname($folder),$root] as $directory) {
        for ($attempt=0; $attempt<20; $attempt++) {
            clearstatcache();
            if (@rmdir($directory)) continue 2;
            usleep(25000);
        }
        throw new RuntimeException('Cannot remove temporary fixture directory: '.$directory);
    }
}
