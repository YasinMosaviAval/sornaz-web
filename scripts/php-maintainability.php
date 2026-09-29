<?php

// CLI only. Never boots the application or connects to its database.
if (PHP_SAPI !== 'cli') {
    exit(1);
}

$root = dirname(__DIR__);
$snapshotPath = $root.'/storage/code-quality/php-tokens.json';
$mode = $argv[1] ?? 'report';
$complexityPath = __DIR__.'/php-complexity-baseline.json';
$methods = [];
$files = [];
foreach (['core', 'Modules'] as $directory) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/'.$directory, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
        if ($file->getExtension() === 'php' && !preg_match('~/ (Resources|Lib) /~x', $relative)) {
            $files[$relative] = $file->getPathname();
        }
    }
}
ksort($files);
$hashes = [];
$windowsCommentHashes = [];
$longLines = [];
foreach ($files as $relative => $path) {
    $source = file_get_contents($path);
    $tokens = token_get_all($source, TOKEN_PARSE);
    foreach ($tokens as $index => $token) {
        if (!is_array($token) || $token[0] !== T_FUNCTION) {
            continue;
        }
        $cursor = $index + 1;
        while (isset($tokens[$cursor]) && is_array($tokens[$cursor]) && $tokens[$cursor][0] === T_WHITESPACE) {
            ++$cursor;
        }
        if (!is_array($tokens[$cursor] ?? null) || $tokens[$cursor][0] !== T_STRING) {
            continue; // Anonymous functions are counted as part of their enclosing method.
        }
        $name = $tokens[$cursor][1];
        while (isset($tokens[$cursor]) && $tokens[$cursor] !== '{' && $tokens[$cursor] !== ';') {
            ++$cursor;
        }
        if (($tokens[$cursor] ?? null) !== '{') {
            continue;
        }
        $depth = 1;
        $size = 0;
        for (++$cursor; isset($tokens[$cursor]) && $depth > 0; ++$cursor) {
            $part = $tokens[$cursor];
            if ($part === '{' || (is_array($part) && in_array($part[0], [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES], true))) {
                ++$depth;
            } elseif ($part === '}') {
                --$depth;
            }
            if (!is_array($part) || !in_array($part[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                ++$size;
            }
        }
        if ($size > 400) {
            $methods[$relative.'::'.$name] = $size;
        }
    }
    $meaningful = [];
    foreach ($tokens as $token) {
        if (is_array($token)) {
            if ($token[0] === T_WHITESPACE) {
                continue;
            }
            // The opening tag includes its trailing whitespace.
            $meaningful[] = [$token[0], $token[0] === T_OPEN_TAG ? trim($token[1]) : $token[1]];
        } else {
            $meaningful[] = $token;
        }
    }
    $hashes[$relative] = hash('sha256', serialize($meaningful));
    $windowsComments = array_map(static function ($token) {
        if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
            $token[1] = str_replace("\n", "\r\n", str_replace("\r\n", "\n", $token[1]));
        }
        return $token;
    }, $meaningful);
    $windowsCommentHashes[$relative] = hash('sha256', serialize($windowsComments));
    $maximum = max(array_map('strlen', explode("\n", $source)));
    if ($maximum > 200) {
        $longLines[$relative] = $maximum;
    }
}
if ($mode === 'snapshot') {
    if (!is_dir(dirname($snapshotPath))) {
        mkdir(dirname($snapshotPath), 0775, true);
    }
    file_put_contents($snapshotPath, json_encode($hashes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo count($hashes)." PHP token snapshots saved.\n";
} elseif ($mode === 'verify') {
    if (!is_file($snapshotPath)) {
        fwrite(STDERR, "Run snapshot before formatting.\n");
        exit(1);
    }
    $before = json_decode(file_get_contents($snapshotPath), true, flags: JSON_THROW_ON_ERROR);
    $changed = [];
    foreach ($before as $file => $hash) {
        if ($hash !== ($hashes[$file] ?? null) && $hash !== ($windowsCommentHashes[$file] ?? null)) {
            $changed[] = $file;
        }
    }
    $changed = array_merge($changed, array_keys(array_diff_key($hashes, $before)));
    if ($changed) {
        fwrite(STDERR, "Non-whitespace changes:\n".implode("\n", $changed)."\n");
        exit(1);
    }
    echo count($hashes)." files retain identical tokens except whitespace and comment line endings.\n";
} elseif ($mode === 'baseline') {
    ksort($methods);
    file_put_contents($complexityPath, json_encode($methods, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
    echo count($methods)." existing large methods recorded for gradual reduction.\n";
} elseif ($mode === 'check') {
    $baseline = json_decode(file_get_contents($complexityPath), true, flags: JSON_THROW_ON_ERROR);
    $regressions = [];
    foreach ($methods as $name => $size) {
        if ($size > ($baseline[$name] ?? 400)) {
            $regressions[] = "$name: $size tokens (allowed ".($baseline[$name] ?? 400).")";
        }
    }
    if ($regressions) {
        fwrite(STDERR, "Extract responsibilities before growing these methods:\n".implode("\n", $regressions)."\n");
        exit(1);
    }
    echo "No new or enlarged methods beyond the complexity baseline.\n";
} elseif ($mode === 'report') {
    arsort($longLines);
    echo count($hashes)." PHP source files; ".count($longLines)." files with lines over 200 bytes.\n";
    foreach (array_slice($longLines, 0, 20, true) as $file => $length) {
        echo "$length\t$file\n";
    }
    arsort($methods);
    echo count($methods)." named functions/methods exceed 400 body tokens (not cyclomatic complexity).\n";
    foreach (array_slice($methods, 0, 20, true) as $name => $size) {
        echo "$size\t$name\n";
    }
} else {
    fwrite(STDERR, "Usage: php scripts/php-maintainability.php [report|snapshot|verify|baseline|check]\n");
    exit(1);
}
