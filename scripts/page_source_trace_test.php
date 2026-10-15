<?php
require_once __DIR__ . '/../core/view/PageSourceTrace.php';
require_once __DIR__ . '/../core/http/ResponseInterface.php';
require_once __DIR__ . '/../core/http/JsonResponse.php';
require_once __DIR__ . '/../core/view/View.php';
function auth(): object { return new class { public function id(): int { return $GLOBALS['actor']; } }; }
function base_path(string $file = ''): string { return dirname(__DIR__) . '/' . $file; }
function check(bool $ok, string $message): void { if (!$ok) throw new RuntimeException($message); }
use Core\view\PageSourceTrace;
$_SERVER['HTTP_X_SORNAZ_SOURCE_TRACE'] = '1';
$html = '<html><head><title>Test</title></head><body><main>content</main></body></html>';
foreach ([0, 2, 7] as $actor) {
    check(PageSourceTrace::decorate($html) === $html, 'Non-founder HTML contains no source metadata');
    check(PageSourceTrace::jsonMetadata() === [], 'Forged opt-in header cannot bypass founder check');
}
$actor = 1;
PageSourceTrace::reset();
ob_start();
Core\view\View::component('Analytics::sections.panel-table-template');
ob_end_clean();
$manifest = PageSourceTrace::manifest();
check(in_array('Modules/Analytics/Resources/Views/sections/panel-table-template.php', $manifest['sections']['panel-table-template'], true), 'Actual component is recorded');
check(PageSourceTrace::relative(base_path('config/database.php')) === null, 'Configuration excluded');
check(PageSourceTrace::relative(base_path('storage/private.txt')) === null, 'Private files excluded');
check(PageSourceTrace::relative(dirname(__DIR__) . '/../outside.php') === null, 'Outside paths excluded');
$decorated = PageSourceTrace::decorate($html);
check(str_contains($decorated, 'id="pageSourceManifest"'), 'Founder receives manifest');
check(strpos($decorated, 'page-source-trace.js') < strpos($decorated, '<title>'), 'Collector loads before page scripts');
check(!str_contains($decorated, str_replace('\\', '/', dirname(__DIR__))), 'No absolute paths');
ob_start();
(new Core\http\JsonResponse(['success' => true]))->send();
$response = json_decode(ob_get_clean(), true, 512, JSON_THROW_ON_ERROR);
check(isset($response['_pageSources']) && $response['data']['success'] === true, 'Opt-in JSON keeps existing payload and adds trace');
unset($_SERVER['HTTP_X_SORNAZ_SOURCE_TRACE']);
check(PageSourceTrace::jsonMetadata() === [], 'Normal API responses unchanged');
$actor = 2;
check(PageSourceTrace::decorate($html) === $html, 'Earlier founder state never leaks to another actor');
echo "Page source trace: founder-only HTML/JSON, opt-in, component capture and private-path exclusion passed.\n";
