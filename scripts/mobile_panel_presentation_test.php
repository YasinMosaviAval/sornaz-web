<?php
require __DIR__ . '/mobile_panel_parity_test.php';
expectParity(!isset($sections['course-levels']['presentation']), 'Legacy clients must retain their presentation');
$sections = (new \Modules\Analytics\Services\MobilePanelCatalog())->sections(true);
$classroomColumns = array_column($sections['classrooms']['presentation']['columns'], null, 'key');
expectParity($classroomColumns['branchName']['label'] === 'سازمان', 'Classroom ownership column must be labeled Organization');
$levels = $sections['course-levels']['presentation'];
expectParity(array_column($levels['columns'], 'key') === ['name', 'summary'], 'Lesson-level columns differ from website');
expectParity($levels['rowActions'] === ['details'], 'Lesson levels expose unsupported row mutations');
expectParity(array_column($levels['detailFields'], 'key') === ['name', 'summary', 'description'], 'Level details differ from website');
expectParity(!in_array('status', array_column($sections['students']['presentation']['columns'], 'key'), true), 'Students expose a status column absent from website');
foreach (['roles', 'permissions'] as $key) {
    expectParity($sections[$key]['presentation']['rowActions'] === ['update', 'delete'], 'Access table exposes extra actions');
}
foreach (['typeLabel' => 'showCategory', 'status' => 'showStatus'] as $key => $permission) {
    $columns = array_column($sections['classroom-types']['presentation']['columns'], null, 'key');
    expectParity($columns[$key]['permission'] === $permission, 'Role-specific classroom-type column leaked');
}
$pointColumns = array_column($sections['points']['presentation']['columns'], null, 'key');
expectParity($sections['points']['presentation']['actionsPermission'] === 'canManage', 'Non-admin point rules expose an action column');
expectParity(array_column($sections['users']['presentation']['filters'], 'webId') === ['accessUserStatus'], 'Organization selection is duplicated in access filters');
expectParity(count($sections['terms']['presentation']['filters']) === 7, 'Term filters are incomplete');
expectParity(count($sections['classrooms']['presentation']['filters']) === 4, 'Classroom filters are incomplete');
$activity = array_column($sections['notifications']['presentation']['filters'], null, 'webId')['filterNotificationActivity'] ?? null;
expectParity($activity && $activity['rowPath'] === 'activityTitle' && $activity['distinct'], 'Activity-title filter must use actual notification activity titles');
foreach (['organization-holidays' => ['vacation'], 'employee-leaves' => ['holiday', 'closed']] as $key => $excluded) {
    $fields = array_column($sections[$key]['actions']['create']['fields'], null, 'key');
    expectParity(!isset($fields['targetValue']) && !isset($fields['targetType']), 'Scoped exception forms must not offer a mixed target');
    expectParity(!array_intersect($excluded, array_keys($fields['type']['options'])), 'Invalid scoped exception types exposed');
    expectParity(isset($fields['membershipId']) === ($key === 'employee-leaves'), 'Only employee leaves select a member');
    if ($key === 'employee-leaves') expectParity($fields['membershipId']['dependsOn'] === ['organizationUserId'] && $fields['membershipId']['options']['requireMatch'], 'Employee selection must wait for and match organization');
    $filter = array_column($sections[$key]['presentation']['filters'], null, 'webId')['filterHolidayLeaveType'];
    expectParity(!array_intersect($excluded, array_keys($filter['options'])), 'Table type filters must match scoped form types');
    foreach ($sections[$key]['actions'] as $action) {
        $route = \Core\router\Router::dispatch($action['method'], str_replace('{id}', '1', $action['path']));
        expectParity(in_array('academy-panel', $route['middlewares'], true), 'Scoped operation lost panel authorization');
        if ($action['method'] === 'POST') expectParity(in_array('csrf', $route['middlewares'], true), 'Scoped mutation lost CSRF');
    }
}
$termFields = array_column($sections['terms']['actions']['create']['fields'], null, 'key');
expectParity($termFields['organizationUserId']['options'] === ['source' => 'organizations', 'id' => 'user_id'], 'Term organization must use the website account IDs');
expectParity(isset($sections['classroom-types']['actions']['create-from-classroom']['hidden']), 'Context-only classroom-type action leaks into toolbar');
foreach (['source', 'branchName', 'status'] as $key) expectParity($pointColumns[$key]['dataPermission'] === 'canManage', 'Site-admin point column leaked');
foreach ($sections as $key => $section) {
    if (in_array('--coverage', $argv, true) && !isset($section['presentation'])) echo $key . ': ' . $section['rows'] . "\n";
    if (!isset($section['presentation'])) continue;
    $contract = $section['presentation'];
    foreach ($contract['filters'] as $filter) {
        if (!isset($filter['webId'])) continue;
        $file = $key === 'members' ? 'teachers' : (in_array($key, ['organization-holidays', 'employee-leaves'], true) ? 'availability-exceptions' : $key);
        $view = __DIR__ . '/../Modules/Analytics/Resources/Views/sections/' . $file . '.php';
        expectParity(is_file($view) && str_contains(file_get_contents($view), 'id="' . $filter['webId'] . '"'), 'Native filter has no matching web control: ' . $key . '/' . $filter['webId']);
    }
    $keys = array_column($contract['columns'], 'key');
    expectParity(count($keys) === count(array_unique($keys)), 'Duplicate table column: ' . $key);
    expectParity(!in_array('status', $contract['rowActions'], true), 'Status must be an inline control');
    foreach ($contract['rowActions'] as $action) expectParity($action === 'details' || isset($section['actions'][$action]), 'Unknown row action: ' . $key . '/' . $action);
}
if (($export = array_search('--export', $argv, true)) !== false) {
    $fixture = [];
    foreach ($sections as $key => $section) if (isset($section['presentation'])) $fixture[$key] = $section['presentation'];
    file_put_contents($argv[$export + 1], json_encode($fixture, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . "\n");
}
if (($export = array_search('--export-dart', $argv, true)) !== false) {
    $fixture = [];
    foreach ($sections as $key => $section) if (isset($section['presentation'])) $fixture[$key] = $section['presentation'];
    file_put_contents($argv[$export + 1], "// Generated from the real web catalog; regenerate with mobile_panel_presentation_test.php --export-dart.\nconst Map<String, dynamic> panelWebPresentation = " . json_encode($fixture, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . ";\n");
}
echo "Mobile presentation: website columns, explicit actions, detail fields and role-specific visibility passed.\n";
