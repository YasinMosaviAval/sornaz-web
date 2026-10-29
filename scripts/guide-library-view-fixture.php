<?php
// Read-only render fixture; no application bootstrap or database access.
require __DIR__ . '/../Modules/Analytics/Services/AdminGuideService.php';
function base_path(string $path = ''): string { return dirname(__DIR__) . '/' . $path; }
function e($value): string { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function locale(): string { return 'fa'; }
function auth(): object { return new class { public function id(): int { return (int) ($GLOBALS['argv'][1] ?? 1); } }; }
$guides = [];
$guideDocuments = (new Modules\Analytics\Services\AdminGuideService())->documents();
require base_path('Modules/Analytics/Resources/Views/sections/guides.php');
