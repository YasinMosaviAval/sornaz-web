<?php
// FTP deployments may preserve old directory casing even after a classmap update.
// Resolve only a trusted Composer path inside this application, never URL input.
return static function (string $root, string $path): ?string {
    $root = rtrim(str_replace('\\', '/', $root), '/');
    $path = str_replace('\\', '/', $path);
    // Composer's static map contains vendor/composer/../../ segments.
    $parts = [];
    foreach (explode('/', $path) as $part) {
        if ($part === '' || $part === '.') continue;
        if ($part === '..') {
            if (!$parts) return null;
            array_pop($parts);
        } else $parts[] = $part;
    }
    $path = (str_starts_with($path, '/') ? '/' : '').implode('/', $parts);
    if (!str_starts_with($path, $root.'/')) return null;
    $current = $root;
    foreach (explode('/', substr($path, strlen($root) + 1)) as $part) {
        if ($part === '' || $part === '.' || $part === '..' || !is_dir($current)) return null;
        $entries = scandir($current);
        if ($entries === false) return null;
        if (in_array($part, $entries, true)) {
            $current .= '/'.$part;
            continue;
        }
        $matches = array_values(array_filter($entries, static fn(string $entry): bool => strcasecmp($entry, $part) === 0));
        // Never guess if a deployment contains two differently cased candidates.
        if (count($matches) !== 1) return null;
        $current .= '/'.$matches[0];
    }
    $resolved = realpath($current);
    if (!$resolved || !is_file($resolved)) return null;
    $resolved = str_replace('\\', '/', $resolved);
    $realRoot = str_replace('\\', '/', realpath($root) ?: $root);
    return str_starts_with($resolved, $realRoot.'/') ? $resolved : null;
};
