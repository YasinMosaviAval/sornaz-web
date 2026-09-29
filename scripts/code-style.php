<?php

if (PHP_SAPI !== 'cli') {
    exit(1);
}

chdir(dirname(__DIR__));
$mode = $argv[1] ?? 'check';
if (!in_array($mode, ['check', 'fix'], true)) {
    fwrite(STDERR, "Usage: php scripts/code-style.php [check|fix]\n");
    exit(1);
}
$fixer = 'storage/code-quality/php-cs-fixer.phar';
$prettier = 'scripts/format-tools/node_modules/prettier/bin/prettier.cjs';
if (!is_file($fixer) || !is_file($prettier)) {
    fwrite(STDERR, "Install formatting tools first; see docs/code-maintenance.md.\n");
    exit(1);
}
$commands = [[PHP_BINARY, $fixer, 'fix', '--config=.php-cs-fixer.dist.php', '--sequential', '--show-progress=none']];
if ($mode === 'check') {
    $commands[0][] = '--dry-run';
}
$commands[] = ['node', $prettier, $mode === 'fix' ? '--write' : '--check', 'assets/**/*.js', '!assets/vendor/**', '!assets/**/*.min.js'];
$failed = false;
foreach ($commands as $command) {
    $process = proc_open($command, [STDIN, STDOUT, STDERR], $pipes);
    if (!is_resource($process) || proc_close($process) !== 0) {
        $failed = true;
    }
}
exit($failed ? 1 : 0);
