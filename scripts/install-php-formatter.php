<?php

if (PHP_SAPI !== 'cli') {
    exit(1);
}

$version = '3.95.27';
$checksum = '47231757eefdd08778e2bd047d98a63dfaad881006c8c1d1bc870d63be7f6a9b';
$target = dirname(__DIR__).'/storage/code-quality/php-cs-fixer.phar';
if (is_file($target) && hash_file('sha256', $target) === $checksum) {
    echo "PHP CS Fixer $version is already installed.\n";
    exit(0);
}
$url = "https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/v$version/php-cs-fixer.phar";
$context = stream_context_create(['http' => ['timeout' => 90], 'ssl' => ['verify_peer' => true, 'verify_peer_name' => true]]);
$data = file_get_contents($url, false, $context);
if ($data === false || !hash_equals($checksum, hash('sha256', $data))) {
    fwrite(STDERR, "Download failed or checksum did not match. No tool was installed.\n");
    exit(1);
}
if (!is_dir(dirname($target))) {
    mkdir(dirname($target), 0775, true);
}
if (file_put_contents($target, $data) !== strlen($data)) {
    fwrite(STDERR, "Cannot save formatter.\n");
    exit(1);
}
echo "Installed PHP CS Fixer $version with verified SHA-256.\n";
