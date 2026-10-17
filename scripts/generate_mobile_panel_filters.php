<?php
/** Development-only snapshot of the actual web filter controls; never executes a view. */
$root = dirname(__DIR__);
$files = ['members' => 'teachers', 'students' => 'students', 'lessons' => 'lessons', 'courses' => 'courses', 'terms' => 'terms', 'classrooms' => 'classrooms', 'classroom-types' => 'classroom-types', 'availabilities' => 'availabilities', 'member-schedules' => 'member-schedules', 'availability-exceptions' => 'availability-exceptions', 'scheduling-rules' => 'scheduling-rules', 'schedules' => 'schedules', 'finance' => 'finance', 'points' => 'points', 'messages' => 'messages', 'notifications' => 'notifications', 'posts' => 'posts', 'comments' => 'comments', 'media' => 'media', 'roles' => 'roles', 'permissions' => 'permissions', 'users' => 'users'];
// IDs identify controls in the active website. Paths describe values, not DB columns to display.
$paths = [
 'filterStaffType' => 'type', 'filterStaffStatus' => 'status', 'filterStaffCurrency' => 'currency', 'filterStaffLesson' => 'lessonId',
 'filterStudentAgeGroup' => '_ageGroup', 'filterStudentLevel' => 'level', 'filterStudentInstrument' => 'instrument',
 'filterLessonStatus' => 'status', 'filterLessonLevel' => 'level_id',
 'filterCourseStatus' => 'status', 'filterCourseInstrument' => 'instrument', 'filterCourseLevel' => 'level_id',
 'filterTermStatus' => 'status', 'filterTermCourse' => 'courseId', 'filterTermCurrency' => 'currencyId', 'filterTermDiscount' => 'discountId', 'filterTermClassroom' => 'classroomId', 'filterTermInstallmentCount' => 'installmentCount', 'filterTermWeekday' => '_weekday',
 'filterClassroomStatus' => 'status', 'filterClassroomType' => 'typeLabel', 'filterClassroomCapacity' => 'capacity', 'filterClassroomEquipment' => 'equipment',
 'classroomTypeStatusFilter' => 'status', 'classroomTypeCategoryFilter' => 'type',
 'accessUserStatus' => 'status', 'accessRoleScope' => 'scope', 'accessRoleLevel' => 'level', 'accessPermissionScope' => 'scope', 'accessPermissionRisk' => 'risk',
 'filterPointType' => 'type', 'filterPointCategory' => 'category', 'filterPointStatus' => 'status',
 'filterNotificationActivity' => 'activityTitle',
 'mediaType' => 'type', 'mediaMonth' => 'month',
 'postTypeFilter' => 'type', 'postVisibilityFilter' => 'visibility', 'commentPostFilter' => 'postId',
];
$sources = [
 'filterStaffLesson' => ['source' => 'staff_catalog.lessons', 'id' => 'id', 'label' => 'name'],
 'filterLessonLevel' => ['source' => 'levels', 'id' => 'level_id'],
 'filterTermCourse' => ['source' => 'courses'], 'filterTermCurrency' => ['source' => 'currencies'], 'filterTermDiscount' => ['source' => 'discounts'], 'filterTermClassroom' => ['source' => 'classrooms'],
 'classroomTypeCategoryFilter' => ['source' => 'typeOptions', 'id' => 'value'],
 'filterCourseLevel' => ['source' => 'levels'],
 'filterScheduleInstrument' => ['source' => 'lessons'], 'filterScheduleClassroom' => ['source' => 'classrooms'],
 'commentPostFilter' => ['source' => 'posts'],
];
$paths += ['filterBranchDay' => 'day', 'filterBranchRepeat' => 'repeatPeriod', 'filterBranchTimezone' => 'timezone', 'filterBranchStatus' => 'status', 'filterMemberRole' => 'role', 'filterMemberDay' => 'day', 'filterMemberRepeat' => 'repeatPeriod', 'filterMemberTimezone' => 'timezone', 'filterMemberStatus' => 'status', 'filterScheduleInstrument' => 'lesson'];
$result = [];
foreach ($files as $key => $file) {
 $path = "$root/Modules/Analytics/Resources/Views/sections/$file.php";
 if (!is_file($path)) continue;
 $html = file_get_contents($path);
 preg_match_all('/<select\b([^>]*)>(.*?)<\/select>/su', $html, $selects, PREG_SET_ORDER);
 $filters = [];
 foreach ($selects as $select) {
  if (!preg_match('/\bid="([^"]+)"/u', $select[1], $idMatch)) continue;
  $id = $idMatch[1];
  if (!preg_match('/^(filter|post(Type|Visibility)Filter$|commentPostFilter$|classroomType(Status|Category)Filter$|access(UserStatus|Role(Scope|Level)|Permission(Scope|Risk))$|media(Type|Month)$)/', $id)) continue;
  preg_match_all('/<option\b([^>]*)>(.*?)<\/option>/su', $select[2], $options, PREG_SET_ORDER);
  $values = []; $label = '';
  foreach ($options as $option) {
   $text = trim(html_entity_decode(strip_tags($option[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
   if (str_contains($text, '<?')) continue;
   $value = preg_match('/\bvalue="([^"]*)"/u', $option[1], $match) ? html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8') : $text;
   if ($value === '') $label = $text; else $values[$value] = $text;
  }
  $field = $paths[$id] ?? lcfirst(preg_replace('/^filter(?:BranchSchedule|MemberSchedule|HolidayLeave|Notification|Schedule|Finance|Message|Rule|Post|Comment)/', '', $id));
  if ($id === 'filterNotificationActivity') $label = 'همه عنوان‌های فعالیت';
  $filters[] = ['key' => $id, 'webId' => $id, 'label' => $label ?: 'فیلتر', 'type' => 'select', 'rowPath' => $field, 'options' => $values ?: new stdClass(), 'optionSource' => $sources[$id] ?? null, 'distinct' => !$values];
 }
 $result[$key] = $filters;
}
$result['users'] = array_values(array_filter($result['users'], fn ($f) => $f['webId'] === 'accessUserStatus'));
$result['users'][0]['queryKey'] = 'status';
foreach ($result['media'] as &$filter) $filter['queryKey'] = $filter['rowPath'];
unset($filter);
$scheduleQueries = ['filterScheduleDay' => 'day', 'filterScheduleType' => 'mode', 'filterScheduleStatus' => 'status', 'filterScheduleInstrument' => 'lesson', 'filterScheduleClassroom' => 'classroom', 'filterScheduleTimeFrom' => 'timeFrom', 'filterScheduleTimeTo' => 'timeTo'];
foreach ($result['schedules'] as &$filter) $filter['queryKey'] = $scheduleQueries[$filter['webId']];
unset($filter);
foreach (['posts', 'comments'] as $key) foreach ($result[$key] as &$filter) $filter['queryKey'] = $filter['rowPath'];
unset($filter);
$result['posts'][] = ['key' => 'status', 'label' => 'وضعیت', 'type' => 'select', 'rowPath' => 'status', 'queryKey' => 'status', 'options' => ['published' => 'منتشرشده', 'draft' => 'پیش‌نویس', 'pending' => 'در انتظار بررسی', 'private' => 'خصوصی', 'trash' => 'زباله‌دان', 'future' => 'زمان‌بندی‌شده']];
$result['comments'][] = ['key' => 'status', 'label' => 'وضعیت', 'type' => 'select', 'rowPath' => 'status', 'queryKey' => 'status', 'options' => ['approved' => 'تأیید شده', 'pending' => 'در انتظار', 'rejected' => 'رد شده', 'trash' => 'زباله‌دان']];
foreach (['_dateFrom' => ['filterFinanceFrom', 'از تاریخ'], '_dateTo' => ['filterFinanceTo', 'تا تاریخ']] as $path => [$key, $label]) $result['finance'][] = ['key' => $key, 'webId' => $key, 'label' => $label, 'type' => 'date', 'rowPath' => $path, 'visibleWhen' => ['_form.filterFinanceRange' => 'custom']];
$result['comments'][0]['queryKey'] = 'post';
$result['my-points'] = [
 ['key' => 'type', 'label' => 'نوع امتیاز', 'type' => 'select', 'rowPath' => 'type', 'distinct' => true, 'options' => new stdClass()],
 ['key' => 'action', 'label' => 'عملیات', 'type' => 'select', 'rowPath' => 'action', 'distinct' => true, 'options' => new stdClass()],
];
foreach ($result['classroom-types'] as &$filter) $filter['permission'] = $filter['rowPath'] === 'status' ? 'showStatus' : 'showCategory';
unset($filter);
$exceptionTypes = ['organization-holidays' => ['vacation'], 'employee-leaves' => ['holiday', 'closed']];
foreach ($exceptionTypes as $key => $excluded) {
 $result[$key] = $result['availability-exceptions'];
 foreach ($result[$key] as &$filter) if ($filter['webId'] === 'filterHolidayLeaveType') $filter['options'] = array_diff_key((array) $filter['options'], array_flip($excluded));
 unset($filter);
}
$json = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
file_put_contents("$root/Modules/Analytics/Resources/mobile-panel-filters.php", "<?php\n// Generated from active web controls by scripts/generate_mobile_panel_filters.php.\nreturn json_decode(<<<'JSON'\n$json\nJSON, true, 512, JSON_THROW_ON_ERROR);\n");
echo 'Generated web filters for ' . count($result) . " sections.\n";
