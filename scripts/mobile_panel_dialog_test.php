<?php
/** Real catalog and active web forms; no database or application output. */
require __DIR__ . '/mobile_panel_parity_test.php';
expectParity(!isset($sections['members']['actions']['create']['formContractVersion']), 'Old clients must retain their compatible form contract');
$sections = (new \Modules\Analytics\Services\MobilePanelCatalog())->sections(true);

function dialogFields(array $sections, string $section, string $operation = 'create'): array {
    return array_column($sections[$section]['actions'][$operation]['fields'], null, 'key');
}
function dialogKeys(array $sections, string $section): array {
    return array_keys(dialogFields($sections, $section));
}
$source = file_get_contents(__DIR__ . '/../assets/Analytics/js/points.js');
preg_match_all('/\bname="([^"]+)"/', $source, $matches);
expectParity(dialogKeys($sections, 'points') === array_values(array_unique($matches[1])), 'Point dialog differs from the active website form');
expectParity(dialogKeys($sections, 'course-levels') === ['name', 'summary', 'description'], 'Lesson-level form has fields absent from website');
expectParity(dialogKeys($sections, 'classroom-types') === ['type', 'title', 'summary', 'description'], 'Classroom-type form differs from website');
expectParity(dialogKeys($sections, 'members') === ['name', 'gender', 'nationalId', 'birthDate', 'phone', 'profileVisibility', 'organizationUserId', 'type', 'roleId', 'contractTitle', 'startDate', 'endDate', 'currencyId', 'price', 'lessonId', 'levelId', 'contractDescription', 'registration'], 'Staff field order differs from website');
$staff = dialogFields($sections, 'members');
expectParity($staff['lessonId']['visibleWhen'] === ['_form.type' => 'teacher'], 'Non-teacher contracts expose teaching fields');
expectParity($staff['levelId']['options']['allowedIdsFrom']['field'] === 'level_ids', 'Teaching levels must use offered lesson levels');
expectParity($staff['roleId']['options']['staffRoles'] === true, 'Staff role scoping missing');
$classroom = dialogFields($sections, 'classrooms');
expectParity($classroom['onlineUrl']['visibleWhen'] === ['_form.deliveryMode' => 'online'], 'Online classroom link condition missing');
expectParity($classroom['status']['options'] === ['available' => 'در دسترس', 'unavailable' => 'خارج از دسترس'], 'Classroom statuses differ from website');
expectParity(!isset(dialogFields($sections, 'courses')['level_id']), 'Courses must select an offered lesson rather than an unrelated level');
expectParity(!isset(dialogFields($sections, 'roles')['group']) && !isset(dialogFields($sections, 'permissions')['name']), 'Legacy access fields still exposed');
expectParity(dialogFields($sections, 'students')['parentFatherName']['visibleWhen'] === ['_form.birthDate' => ['underAge' => 18]], 'Parent fields must follow student age');
expectParity(dialogFields($sections, 'students')['availabilities']['visibleWhen'] === ['_selected.termId.status' => 'ongoing'], 'Student availability condition missing');
$term = dialogFields($sections, 'terms');
$batch = dialogFields($sections, 'terms', 'batch');
expectParity(isset($batch['requestKey'], $batch['rangeStart'], $batch['rangeEnd']) && $batch['teachers']['maxItems'] === 1, 'Batch retry identity, range or single-teacher restriction missing');
expectParity(!array_intersect(['status', 'discountId', 'installmentCount'], array_keys($batch)) && !isset($batch['classroomId']), 'Batch exposes website-hidden financial/status fields or requires a classroom');
$assignment = dialogFields($sections, 'terms', 'assign-student');
expectParity(!array_intersect(['organizationUserId', 'termId', 'availabilities'], array_keys($assignment)) && isset($assignment['addresses'], $assignment['parentName']), 'Term assignment must derive its organization and term while preserving student/parent/address fields');
expectParity(isset($term['sessionCount'], $term['recurrence']), 'Term recurrence/session count absent');
expectParity(array_column($term['sessions']['options'], 'key') === ['date', 'timezoneId', 'startTime', 'durationMinutes'], 'Term session dialog differs from website');
expectParity($term['status']['valueWhen']['value'] === 'pending', 'Receptionist term status must remain pending');
expectParity(dialogKeys($sections, 'notifications') === ['audience', 'title', 'body'], 'Notification form must not have an extra draft checkbox');
expectParity(array_column($sections['notifications']['actions']['create']['submitButtons'], 'values') === [['asDraft' => false], ['asDraft' => true]], 'Publish/draft actions missing');
expectParity(isset(dialogFields($sections, 'availability-exceptions')['targetValue']) && !isset(dialogFields($sections, 'availability-exceptions')['targetType']), 'Exception target must be a single selector');
foreach (['gallery', 'my-gallery', 'media'] as $key) expectParity(!isset(dialogFields($sections, $key, 'update')['file']), 'Editing media must not require uploading again');
foreach (['posts', 'polls', 'badges'] as $key) {
    $date = $key === 'posts' ? 'published_at' : ($key === 'polls' ? 'expires_at' : 'granted_at');
    expectParity(dialogFields($sections, $key)[$date]['type'] === 'datetime', 'Date/time field truncated to a date');
}
foreach ($sections as $section) foreach ($section['actions'] as $action) {
    if (($action['formContractVersion'] ?? 0) !== 2) continue;
    $keys = array_column($action['fields'], 'key');
    expectParity(count($keys) === count(array_unique($keys)), 'Duplicate form field');
}
if (($export = array_search('--export', $argv, true)) !== false) {
    $fixture = [];
    foreach (['members', 'students', 'classrooms', 'courses', 'terms', 'points', 'roles', 'permissions', 'notifications', 'availability-exceptions', 'polls'] as $key) {
        $fixture[$key] = $sections[$key]['actions']['create'];
        unset($fixture[$key]['path'], $fixture[$key]['method'], $fixture[$key]['row']);
    }
    file_put_contents($argv[$export + 1], json_encode($fixture, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . "\n");
}
echo "Mobile dialogs: active web fields, conditional options, create/edit contracts and permissions passed.\n";
