<?php
// Isolated checks: no application bootstrap, credentials, or production database.
require dirname(__DIR__).'/vendor/composer/ClassLoader.php';
$loader = new Composer\Autoload\ClassLoader();
$map = require dirname(__DIR__).'/vendor/composer/autoload_classmap.php';
$loader->addClassMap($map);
$loader->register();
$root = sys_get_temp_dir().'/sornaz-security-'.bin2hex(random_bytes(6));
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
function base_path(string $path = ''): string { global $root; return $root.'/'.$path; }
function db(): PDO { global $pdo; return $pdo; }
$checks = 0;
function check(bool $condition, string $message): void {
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    $checks++;
}
function denied(callable $callback): void {
    try { $callback(); } catch (RuntimeException $e) { check(in_array($e->getCode(), [404,422], true), 'Expected denial'); return; }
    throw new RuntimeException('Operation unexpectedly allowed');
}
$directory = $root.'/storage/account-media/1/2026/09';
mkdir($directory, 0775, true);
$filename = str_repeat('a', 32).'.png';
$image = $directory.'/'.$filename;
$png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=');
file_put_contents($image, $png);
try {
    // Check actual directory entries, rather than Windows' case-insensitive is_file().
    foreach ($map as $class => $path) {
        $relative = str_replace('\\', '/', substr($path, strlen(dirname(__DIR__)) + 1));
        $current = dirname(__DIR__);
        foreach (explode('/', $relative) as $segment) {
            check(in_array($segment, scandir($current), true), 'Wrong path casing: '.$path);
            $current .= '/'.$segment;
        }
        if (str_starts_with($class, 'Core\\')) {
            check(class_exists($class) || interface_exists($class) || trait_exists($class), 'Cannot autoload '.$class);
        }
    }
    $policy = Modules\Analytics\Services\ChatAttachmentPolicy::class;
    $info = $policy::inspect($image, 'photo.jpg');
    check($info['extension'] === 'png' && $info['size'] === strlen($png), 'Use detected MIME and actual size');
    foreach (['image.php','image.pHp.jpg','page.html','icon.svg','script.js','run.ps1'] as $name) denied(fn()=>$policy::inspect($image, $name));
    file_put_contents($root.'/active', '<html><script>alert(1)</script></html>');
    denied(fn()=>$policy::inspect($root.'/active', 'photo.png'));
    $pdo->exec('CREATE TABLE media_files (media_file_id INTEGER, path TEXT, disk TEXT, visibility TEXT, collection TEXT, deleted_at TEXT, user_id INTEGER DEFAULT 2); CREATE TABLE academy_documents (media_file_id INTEGER)');
    $insert = $pdo->prepare('INSERT INTO media_files (media_file_id,path,disk,visibility,collection,deleted_at) VALUES (1, ?, ?, ?, ?, ?)');
    $relative = 'storage/account-media/1/2026/09/'.$filename;
    $insert->execute([$relative,'public','public','avatar',null]);
    $service = new Modules\Analytics\Services\PublicAccountMediaService();
    $find = fn()=>$service->find('1','2026','09',$filename);
    check($find()['mime'] === 'image/png', 'Public avatar should load');
    foreach ([['disk','private'],['visibility','private'],['collection','document'],['deleted_at','2026-09-27']] as [$column,$value]) {
        $pdo->prepare("UPDATE media_files SET $column=?")->execute([$value]);
        denied($find);
        $pdo->exec('DELETE FROM media_files');
        $insert->execute([$relative,'public','public','avatar',null]);
    }
    $pdo->exec('INSERT INTO academy_documents VALUES(1)');
    denied($find);
    $pdo->exec('DELETE FROM academy_documents');
    denied(fn()=>$service->find('..','2026','09',$filename));
    denied(fn()=>$service->find('1','2026','09','../../.env'));
    $libraryDir = $root.'/assets/media/library/2026/09';
    mkdir($libraryDir, 0775, true);
    copy($image, $libraryDir.'/'.$filename);
    $pdo->prepare('UPDATE media_files SET path=?')->execute(['assets/media/library/2026/09/'.$filename]);
    check($service->library('2026','09',$filename)['inline'], 'Public library image');
    $pdo->exec("UPDATE media_files SET visibility='private'");
    denied(fn()=>$service->library('2026','09',$filename));
    denied(fn()=>$service->library('2026','09',$filename,['user_id'=>3,'type'=>'human']));
    check($service->library('2026','09',$filename,['user_id'=>2,'type'=>'human'])['inline'], 'Owner can view private library image');
    $pdo->exec("UPDATE media_files SET visibility='academy_only'");
    denied(fn()=>$service->library('2026','09',$filename));
    $_SERVER['HTTP_RANGE'] = 'bytes=1-4';
    ob_start();
    (new Core\http\DownloadResponse($image, $filename, 'image/png', true))->send();
    $body = ob_get_clean();
    check(http_response_code() === 206 && $body === substr($png,1,4), 'Inline byte range');
    $_SERVER['HTTP_RANGE'] = 'bytes=99999-';
    ob_start();
    (new Core\http\DownloadResponse($image, $filename, 'image/png', true))->send();
    check(ob_get_clean() === '' && http_response_code() === 416, 'Invalid range rejected');
    unset($_SERVER['HTTP_RANGE']);
    $_SERVER['REQUEST_METHOD'] = 'HEAD';
    ob_start();
    (new Core\http\DownloadResponse($image, $filename, 'image/png', true))->send();
    check(ob_get_clean() === '', 'HEAD must not include a body');
    unset($_SERVER['REQUEST_METHOD']);
    echo "Release security: $checks checks passed.\n";
} finally {
    if (isset($libraryDir)) {
        if (is_file($libraryDir.'/'.$filename)) unlink($libraryDir.'/'.$filename);
        for ($dir = $libraryDir; $dir !== $root; $dir = dirname($dir)) rmdir($dir);
    }
    foreach ([$image, $root.'/active'] as $file) if (is_file($file)) unlink($file);
    for ($dir = $directory; str_starts_with($dir, $root); $dir = dirname($dir)) rmdir($dir);
}
