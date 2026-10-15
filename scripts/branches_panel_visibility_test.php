<?php
// Render the actual branches section for two authenticated account IDs.
function auth(): object {
    return new class {
        public function id(): int { return $GLOBALS['branchTestUserId']; }
    };
}
function csrf_token(): string { return 'test-token'; }
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }

foreach ([1 => true, 2 => false] as $userId => $hasCounters) {
    $GLOBALS['branchTestUserId'] = $userId;
    ob_start();
    require dirname(__DIR__) . '/Modules/Analytics/Resources/Views/sections/branches.php';
    $html = ob_get_clean();
    foreach (['branchesAcademiesCount', 'branchesCount'] as $id) {
        if (str_contains($html, 'id="' . $id . '"') !== $hasCounters) {
            throw new RuntimeException("Unexpected $id visibility for user $userId");
        }
    }
}
echo "Branches panel: counters rendered only for user 1.\n";
