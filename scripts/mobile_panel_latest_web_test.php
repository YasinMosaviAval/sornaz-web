<?php
/** Catalog/route contract checks only; no application database is opened. */
require __DIR__ . '/mobile_panel_parity_test.php';
$native = (new \Modules\Analytics\Services\MobilePanelCatalog())->sections(true, true);
$older = (new \Modules\Analytics\Services\MobilePanelCatalog())->sections(true);
$olderFields = array_column($older['terms']['actions']['create']['fields'], null, 'key');
expectParity(isset($olderFields['installmentCount'], $olderFields['discountId']) && count($older['terms']['actions']['assign-student']['steps']) === 4, 'Older app payment forms lost compatibility');
expectParity(!isset($older['posts']['optionActions']) && !in_array('metadata', $older['posts']['presentation']['rowActions'], true), 'New option merges or editors leak into older app');
$post = $native['posts'];
$metadata = array_column($post['actions']['metadata']['fields'], null, 'key');
$full = array_column($post['actions']['update']['fields'], null, 'key');
$acceptedStatuses = (new ReflectionClass(\Modules\Analytics\Services\AdminPostService::class))->getConstant('STATUSES');
foreach (['create', 'update', 'metadata'] as $operation) {
    $fields = array_column($post['actions'][$operation]['fields'], null, 'key');
    expectParity(array_keys($fields['status']['options']) === $acceptedStatuses, 'A valid existing post status cannot be selected in the native editor: ' . $operation);
    expectParity(array_keys($fields['status']['optionsEn']) === $acceptedStatuses && $fields['status']['optionsEn']['inherit'] === 'Inherited' && $fields['status']['optionsEn']['auto-draft'] === 'Auto draft' && $fields['status']['optionsEn']['request-pending'] === 'Request pending' && $fields['status']['optionsEn']['request-confirmed'] === 'Request confirmed', 'Native post statuses lack authoritative English labels: ' . $operation);
}
$olderPost = array_column($older['posts']['actions']['update']['fields'], null, 'key');
expectParity(array_keys($olderPost['status']['options']) === ['draft', 'published', 'pending', 'private', 'future', 'trash'], 'Latest post states changed the legacy client schema');
expectParity(!isset($olderPost['status']['optionsEn']), 'New status labels leak into the legacy client schema');
expectParity(!array_intersect(['content', 'summary', 'description'], array_keys($metadata)), 'Inline post settings expose article text');
expectParity(isset($full['content'], $full['summary'], $full['description']), 'Full post editing lost article fields');
expectParity($post['actions']['metadata']['path'] === '/analytics/admin-posts/{id}/metadata' && $post['actions']['metadata']['inline'], 'Safe metadata operation missing');
foreach (['metadata', 'cover-upload', 'status'] as $operation) {
    $action = $post['actions'][$operation];
    $route = \Core\router\Router::dispatch($action['method'], str_replace('{id}', '1', $action['path']));
    expectParity(in_array('site-admin', $route['middlewares'], true) && in_array('csrf', $route['middlewares'], true), 'Post operation lost authorization or web CSRF');
}
expectParity($metadata['categories']['type'] === 'multi' && $metadata['categories']['csv'] && $metadata['categories']['preserveUnknown'], 'Post category selection cannot preserve existing CSV values');
expectParity($metadata['related_posts_id']['options']['source'] === 'relatedPosts' && $metadata['related_posts_id']['csv'], 'Related posts are still numeric-ID text');
expectParity($metadata['related_posts_id']['options']['excludeSource'] === 'record.id', 'A post must not offer itself as a related post');
expectParity(!isset($metadata['cover_media_id']) && $metadata['coverFile']['extensions'] === ['png', 'jpg', 'jpeg', 'webp'], 'Featured image exposes media IDs or accepts arbitrary files');
expectParity($metadata['coverFile']['uploadAction'] === 'cover-upload' && $post['actions']['cover-upload']['path'] === '/analytics/admin-media/upload-post-cover', 'Featured image upload does not use the existing validated endpoint');
expectParity($full['summary']['minLines'] === 5 && $full['description']['minLines'] === 10, 'Post text field heights diverge from web');
$filters = array_column($post['presentation']['filters'], null, 'queryKey');
expectParity(isset($filters['category']) && $filters['category']['optionSource']['source'] === 'categories', 'Post category filter is missing or filters only one client page');
expectParity(count(array_filter($post['presentation']['filters'], fn ($filter) => ($filter['queryKey'] ?? '') === 'category')) === 1, 'Category filter is duplicated');
$columns = array_column($post['presentation']['columns'], null, 'key');
expectParity(isset($columns['category_names']) && $columns['status']['action'] === 'status', 'Category names or inline status action missing');
expectParity(in_array('metadata', $post['presentation']['rowActions'], true) && $post['presentation']['titleAction'] === 'update', 'Row and title editing actions are not separated');
expectParity($post['actions']['list']['query']['excludeType'] === 'page', 'Removed page content leaks back into posts');
$termFields = array_column($native['terms']['actions']['create']['fields'], null, 'key');
expectParity(!isset($termFields['installmentCount']) && !isset($termFields['discountId']), 'Payment fields remain in term creation');
$assignment = array_column($native['terms']['actions']['assign-student']['fields'], null, 'key');
expectParity(isset($assignment['installmentCount'], $assignment['discountId']) && count($native['terms']['actions']['assign-student']['steps']) === 5, 'Fifth assignment stage missing');
expectParity($assignment['installmentCount']['min'] === 1 && $assignment['installmentCount']['step'] === 1 && $assignment['installmentCount']['wizardStep'] === 4 && $assignment['installmentCount']['integer'], 'Installment count and wizard step rules conflict');
expectParity($assignment['installmentCount']['maxFrom'] === 'record.sessionCount' && $assignment['installmentCount']['minimumMax'] === 2, 'Assignment payment limit differs from server');
expectParity($native['terms']['actions']['batch']['icon'] === 'playlist_add' && $native['gallery']['compact'], 'Latest term/gallery presentation is missing');
$database = $native['database-management'];
expectParity($database['access'] === 'founder' && $database['sensitive'] && !$database['cache'] && $database['minClientFeatures'] === 4, 'Database section must be founder-only and uncached');
$keys = array_keys($native);
expectParity(array_search('database-management', $keys, true) === array_search('guides', $keys, true) + 1, 'Database section is not immediately after guides');
expectParity(array_keys($database['actions']) === ['list', 'rows', 'download-schema', 'table-schema', 'download-data', 'table-data'], 'Database operations are incomplete');
expectParity($database['actions']['rows']['requiredQuery'] === ['table'], 'Table rows must require an explicit selected table');
foreach (['schema', 'data'] as $mode) {
    expectParity($database['actions']['download-' . $mode]['query'] === ['mode' => $mode, 'table' => ''], 'Global export permits overriding its scope or mode');
    expectParity(!isset($database['actions']['download-' . $mode]['requiredQuery']), 'Whole-database download must not require a table');
    expectParity($database['actions']['table-' . $mode]['params'] === ['table' => 'name'] && $database['actions']['table-' . $mode]['download'], 'Per-table download is not bound to a table row');
    expectParity($database['actions']['table-' . $mode]['requiredQuery'] === ['table'], 'Per-table download must not fall back to exporting the entire database');
}
foreach ($database['actions'] as $action) expectParity($action['method'] === 'GET', 'Database administration must remain read-only');
$personalAppearance = array_column($native['my-settings']['actions']['save']['fields'], null, 'key');
expectParity(!isset($personalAppearance['fontWeight']) && !isset($personalAppearance['cornerRadius']), 'Personal settings expose administrator appearance controls');
expectParity(isset($personalAppearance['colorTheme']['options']['original']), 'Original Flutter palette missing from the web/mobile appearance contract');
foreach (['create', 'update', 'delete', 'status'] as $operation) expectParity($native['badges']['actions'][$operation]['admin'] === true, 'Badge mutation is not administrator-only');
foreach (['session-files' => 'GET', 'session-file-upload' => 'POST', 'session-file-download' => 'GET', 'session-file-delete' => 'POST'] as $name => $method) {
    $action = $native['schedules']['actions'][$name];
    expectParity($action['method'] === $method && $action['hidden'] && empty($action['admin']), 'Session files must use hidden member actions');
    $route = \Core\router\Router::dispatch($method, str_replace(['{id}', '{file}'], ['1', '2'], $action['path']));
    expectParity(in_array('auth', $route['middlewares'], true), 'Session files lost web authentication');
    if ($method === 'POST') expectParity(in_array('csrf', $route['middlewares'], true), 'Session file mutations lost CSRF');
}
echo "Latest mobile web parity: safe post metadata, typed image/category/related choices, server filtering, fifth payment step, compact gallery and founder database operations passed.\n";
