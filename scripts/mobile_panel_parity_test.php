<?php
/** Contract tests use the real route registry and catalog, without a database. */
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function (string $class) use ($map): void {
    if (isset($map[$class])) require_once $map[$class];
});
require __DIR__ . '/../Modules/Analytics/Routes/routes.php';
require __DIR__ . '/../Modules/Academy/Routes/web.php';
require __DIR__ . '/../Modules/Analytics/Routes/api.php';
$sections = (new \Modules\Analytics\Services\MobilePanelCatalog())->sections();
function expectParity(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
}
foreach ($sections as $section) {
    foreach ($section['actions'] as $action) {
        expectParity(\Core\router\Router::dispatch($action['method'], preg_replace('/\{\w+\}/', '1', $action['path'])) !== null, 'Unregistered mobile action: ' . $action['path']);
    }
}
foreach (['roles', 'permissions'] as $section) {
    foreach (['create', 'update'] as $operation) {
        expectParity(in_array('panelVisible', array_column($sections[$section]['actions'][$operation]['fields'], 'key'), true), 'Missing panel visibility');
    }
}
foreach (['roles' => 'roleIds', 'permissions' => 'permissionIds'] as $operation => $field) {
    expectParity(array_column($sections['users']['actions'][$operation]['fields'], 'key') === [$field], 'User access saves must be independent');
}
expectParity(($sections['users']['actions']['update']['hidden'] ?? false) === true, 'Legacy combined access action must remain callable but hidden in new clients');
expectParity($sections['users']['actions']['permissions']['fields'][0]['options']['excludeSource'] === 'record.rolePermissionIds', 'Inherited permissions must be separate');
expectParity($sections['national-holidays']['access'] === 'management', 'Managers need academy holiday settings');
foreach (['create', 'update', 'delete', 'status'] as $operation) {
    expectParity(($sections['national-holidays']['actions'][$operation]['admin'] ?? false) === true, 'Holiday mutations must remain site-admin only');
}
expectParity($sections['national-holidays']['actions']['academy-setting']['params'] === ['academyId' => 'academyId'], 'Academy parameter mapping missing');
foreach (['create', 'update'] as $operation) {
    expectParity(in_array('organizationUserId', array_column($sections['classrooms']['actions'][$operation]['fields'], 'key'), true), 'Academy-owned classrooms unavailable');
}
foreach (['waiting-approvals', 'waiting-schedules'] as $section) {
    expectParity($sections[$section]['rowId'] === 'requestId' && $sections[$section]['access'] === 'management', 'Waiting-list row identity or authorization missing');
}
expectParity(isset($sections['waiting-approvals']['actions']['approve'], $sections['waiting-schedules']['actions']['finalize']), 'Waiting operations missing');
expectParity($sections['my-waiting']['access'] === 'common' && $sections['my-waiting']['actions']['update']['path'] === '/analytics/my-waiting/{id}', 'Own waiting requests must use ownership-checked web update');
{
    $covered = [];
    foreach ($sections as $section) foreach ($section['actions'] as $action) {
        $route = \Core\router\Router::dispatch($action['method'], preg_replace('/\{\w+\}/', '1', $action['path']));
        $covered[$action['method'] . ':' . implode('::', $route['action'])] = true;
    }
    $routes = (new ReflectionProperty(\Core\router\Router::class, 'routes'))->getValue();
    $webOnly = [
        'AnalyticsController::adminPanel',
        'AdminGalleryController::realtimeVersion', 'AdminSchedulingRuleController::realtimeVersion',
        'AcademyBranchController::realtimeVersion', 'AcademyBranchController::staffRealtimeVersion',
        'AcademyClassroomController::classroomsRealtimeVersion', 'AcademyClassroomController::realtimeVersion',
        'AcademyBranchOfferingController::lessonsRealtimeVersion', 'AcademyBranchOfferingController::schedulesRealtimeVersion',
        'AcademyCourseController::realtimeVersion', 'AcademyTermController::realtimeVersion',
        'AcademyRegistrationController::createAdminBranchDialog', 'AcademyRegistrationController::sendAdminBranchOtp', 'AcademyRegistrationController::storeAdminBranch',
        'AdminAccountController::sendContactCode', 'AdminAccountController::verifyContactCode',
    ];
    foreach ($routes as $method => $items) foreach ($items as $route) {
        if (!is_array($route->action) || !preg_match('~/((analytics|academy)/admin|analytics/personal)~', $route->uri)) continue;
        if (!isset($covered[$method . ':' . implode('::', $route->action)])) {
            $short = substr(strrchr($route->action[0], '\\'), 1) . '::' . $route->action[1];
            expectParity(in_array($short, $webOnly, true), 'Unmapped panel operation: ' . $method . ' ' . $route->uri);
            if (in_array('--inventory', $argv, true)) echo 'Web transport/alternative: ' . $method . ' ' . $route->uri . "\n";
        }
    }
}
echo 'Panel parity contracts: ' . count($sections) . " sections, registered routes, independent access, holiday and classroom contracts passed.\n";
