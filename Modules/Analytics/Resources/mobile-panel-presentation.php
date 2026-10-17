<?php
/** Native presentation of the active Analytics tables. Permissions remain server enforced. */
$definitions = [
    'reports' => 'title:عنوان|branchName:شعبه|type:نوع|periodLabel:بازه زمانی|date:تاریخ|status:وضعیت',
    'course-levels' => 'name:عنوان|summary:خلاصه',
    'members' => 'name:نام|typeLabel:نوع قرارداد|contractTitle:عنوان قرارداد|branch:سازمان|lessonName:درس|startDate:تاریخ شروع|endDate:تاریخ خاتمه|price:مبلغ قرارداد|status:وضعیت',
    'students' => 'name:نام هنرجو|instrument:ترم آموزشی|level:سطح|teacher:استاد|branch:سازمان',
    'lessons' => 'title:عنوان / درس|levelTitle:سطح|start_date:زمان شروع|is_primary:اصلی|branchName:سازمان|status:وضعیت',
    'courses' => 'name:نام دوره|level:سطح|branchName:سازمان|instrument:درس|teacher_capacity:ظرفیت اساتید|student_capacity:ظرفیت هنرجوها|status:وضعیت',
    'classrooms' => 'name:نام کلاس|typeLabel:نوع کلاس|branchName:شعبه|capacity:ظرفیت|equipment:تجهیزات|status:وضعیت',
    'classroom-types' => 'createdByName:ایجادکننده|title:عنوان|summary:خلاصه|typeLabel:دسته|status:وضعیت',
    'terms' => 'name:نام|branchName:شعبه|course:دوره مرتبط|start:تاریخ شروع|end:تاریخ پایان|days:روز برگزاری|timeLabel:ساعت برگزاری|status:وضعیت',
    'finance' => 'title:فاکتور|branchName:شعبه|course:دوره|amount:مبلغ|date:تاریخ|status:وضعیت',
    'member-schedules' => 'name:نام عضو|role:نقش|day:روز|timeLabel:ساعت|repeatPeriod:دوره تکرار|timezone:منطقه زمانی|branchName:شعبه|status:وضعیت',
    'availability-exceptions' => 'name:نام|date:تاریخ|timeLabel:ساعت|typeLabel:نوع|timezone:منطقه زمانی|branchName:شعبه|status:وضعیت',
    'scheduling-rules' => 'title:عنوان قانون|branchName:سازمان|type:نوع|value:مقدار|status:وضعیت',
    'roles' => 'title:عنوان / نام|scope:دامنه|level:درجه|parent:والد|permissionIds:مجوزها|panelVisible:نمایش در پنل',
    'permissions' => 'title:عنوان / نام|resource:منبع|action:عملیات|scope:دامنه|risk:خطر|panelVisible:نمایش در پنل',
    'users' => 'name:کاربر|roles:نقش‌ها|permissions:مجوزهای اختصاصی|status:وضعیت',
    'messages' => 'title:عنوان|sender:فرستنده|receiver:گیرنده|type:نوع|date:تاریخ|status:وضعیت|readStatus:وضعیت خواندن',
    'notifications' => 'title:عنوان اعلان|recipientUsername:گیرنده|branchName:فرستنده|audience:مخاطب|priority:اولویت|date:تاریخ انتشار|status:وضعیت|readStatus:وضعیت خواندن',
    'national-holidays' => 'title:عنوان|date:تاریخ|status:وضعیت',
    'availabilities' => 'branchName:سازمان|day:روز|timeLabel:ساعت|repeatPeriod:دوره تکرار|timezone:منطقه زمانی|status:وضعیت',
    'schedules' => 'title:عنوان|day:روز|time:ساعت|student:هنرجو|teacher:استاد|lesson:درس|classroom:کلاس|type:نوع|status:وضعیت',
    'posts' => 'title:عنوان|author_name:نویسنده|categories:دسته‌ها|type:نوع|status:وضعیت|views_count:بازدید|published_at:تاریخ',
    'post-categories' => 'title_fa:عنوان فارسی|title_en:عنوان انگلیسی|slug:نامک|group:گروه|posts_count:تعداد نوشته‌ها',
    'comments' => 'author:نویسنده|content:دیدگاه|postTitle:نوشته|status:وضعیت|createdAt:تاریخ',
    'media' => 'title:فایل|mime:نوع|size:حجم|createdAt:تاریخ',
    'points' => 'title:عنوان|type:نوع|category:دسته|points:امتیاز|source:منبع / عملیات|branchName:سازمان|repeatMode:کنترل تکرار|status:وضعیت',
    'my-lessons' => 'title:درس|level:سطح|is_primary:اصلی|summary:خلاصه|status:وضعیت|start_date:زمان شروع',
    'my-schedule' => 'day:روز یا تاریخ|start:شروع|end:پایان|repeatPeriod:تکرار|summary:خلاصه|status:وضعیت',
    'my-finance' => 'term:ترم|amount:مبلغ قابل پرداخت|status:وضعیت|dueDate:سررسید',
    'my-points' => 'type:نوع|points:امتیاز|action:عملیات|rule_id:قانون امتیاز|updated_at:تاریخ ویرایش',
    'my-courses' => 'course_title:دوره|enrollment_type:نقش|branch_title:شعبه',
    'my-terms' => 'term_title:ترم|course_title:دوره|enrollment_type:نقش|start_date:شروع|end_date:پایان',
    'my-classrooms' => 'course_title:دوره|term_title:ترم|classroom_title:کلاس|enrollment_type:نقش|requested_date:تاریخ|start_time:شروع|end_time:پایان|branch_title:شعبه',
    'guides' => 'title:عنوان|content:متن راهنما',
    'pages' => 'title:عنوان|texts:متن‌های ویرایش‌شده|images:تصاویر|icons:آیکن‌ها|status:وضعیت',
    'waiting-approvals' => 'studentName:هنرجو|termName:ترم',
    'waiting-schedules' => 'studentName:هنرجو|termName:ترم',
    'my-waiting' => 'studentName:هنرجو|termName:ترم|status:وضعیت',
    'awards' => 'title:عنوان|issuer:اعطاکننده|date:تاریخ',
    'badges' => 'badge_title:نشان|granted_at:زمان اعطا|expires_at:انقضا|status:وضعیت نشان',
    'experiences' => 'title:عنوان|organization:محل فعالیت|start_date:شروع|end_date:پایان',
    'certificates' => 'title:عنوان|issuer:صادرکننده|issue_date:صدور|expire_date:انقضا',
    'educations' => 'degree:مدرک|institution:مرکز آموزشی|field:رشته|start_date:شروع|end_date:پایان',
    'events' => 'title:عنوان|event_type:نوع|event_date:تاریخ',
    'polls' => 'question:پرسش|type:نوع|status:وضعیت نظرسنجی|votes_count:رأی|expires_at:انقضا',
    'publications' => 'title:عنوان|publisher:ناشر|published_date:تاریخ انتشار|is_peer_reviewed:داوری‌شده',
];
$profiles = ['awards', 'badges', 'experiences', 'certificates', 'educations', 'events', 'polls', 'publications'];
$result = [];
foreach ($definitions as $key => $definition) {
    $columns = $key === 'my-points' ? [['key' => '_index', 'label' => 'ردیف']] : [];
    foreach (explode('|', $definition) as $pair) {
        [$field, $label] = explode(':', $pair, 2);
        $column = ['key' => $field, 'label' => $label];
        if ($key === 'users' && $field === 'status') $column['type'] = 'text';
        if ($field === 'readStatus') $column['type'] = 'badge';
        if ($key === 'classroom-types' && in_array($field, ['typeLabel', 'status'], true)) {
            $column['permission'] = $field === 'status' ? 'showStatus' : 'showCategory';
        }
        if ($key === 'points' && in_array($field, ['source', 'branchName', 'status'], true)) $column['dataPermission'] = 'canManage';
        if ($field === 'status' && !in_array($key, [...$profiles, 'my-lessons', 'my-schedule', 'my-finance', 'posts', 'comments', 'finance', 'schedules', 'users', 'pages', 'my-waiting'], true)) $column['action'] = 'status';
        $columns[] = $column;
    }
    if (in_array($key, $profiles, true)) {
        $columns[] = ['key' => 'user_name', 'label' => 'سازمان'];
        $columns[] = ['key' => 'approved_at', 'label' => 'وضعیت تأیید', 'type' => 'approval', 'action' => 'status'];
    }
    $result[$key] = [
        'version' => 1, 'columns' => $columns,
        'rowActions' => $key === 'course-levels' ? ['details'] : (in_array($key, ['roles', 'permissions', 'post-categories', 'points'], true) ? ['update', 'delete'] : ($key === 'media' ? ['update'] : null)),
    ];
}
$result['course-levels']['detailTitle'] = 'جزئیات سطح درس';
$result['course-levels']['detailFields'] = [
    ['key' => 'name', 'label' => 'عنوان'], ['key' => 'summary', 'label' => 'خلاصه'], ['key' => 'description', 'label' => 'توضیحات'],
];
$result['classroom-types']['detailFields'] = [
    ['key' => 'title', 'label' => 'عنوان'], ['key' => 'summary', 'label' => 'خلاصه'],
    ['key' => 'typeLabel', 'label' => 'دسته', 'permission' => 'showCategory'],
    ['key' => 'status', 'label' => 'وضعیت', 'permission' => 'showStatus'],
    ['key' => 'createdByName', 'label' => 'ایجادکننده'], ['key' => 'description', 'label' => 'توضیحات'],
];
$result['lessons']['detailFields'] = array_map(function ($pair) {
    [$key, $label] = explode(':', $pair, 2); return compact('key', 'label');
}, explode('|', 'summary:خلاصه|description:توضیحات|levelTitle:سطح|start_date:زمان شروع|is_primary:اصلی|status:وضعیت|branchName:سازمان'));
$result['users']['rowActions'] = ['roles', 'permissions', 'details'];
$result['messages']['rowActions'] = ['details', 'delete'];
$details = [
 'members' => 'name:نام|gender:جنسیت|nationalId:کد ملی|birthDate:تاریخ تولد|phone:شماره تماس|typeLabel:نوع قرارداد|status:وضعیت|profileVisibility:نمایش پروفایل|lessonName:درس|level:سطح|contractTitle:عنوان قرارداد|branch:سازمان|startDate:تاریخ شروع|endDate:تاریخ خاتمه|price:مبلغ قرارداد|currency:نوع پول|contractDescription:شرح قرارداد',
 'students' => 'name:نام هنرجو|gender:جنسیت|nationalId:کد ملی|birthDate:تاریخ تولد|phone:شماره تماس|instrument:ترم آموزشی|level:سطح|teacher:استاد|branch:سازمان|fatherName:نام پدر|parentName:نام سرپرست|parentPhone:تماس سرپرست|summary:خلاصه|description:توضیحات',
 'classrooms' => 'name:نام کلاس|typeLabel:نوع کلاس|deliveryMode:شیوه برگزاری|branchName:سازمان|capacity:ظرفیت|status:وضعیت|onlineUrl:پیوند کلاس آنلاین|summary:خلاصه|description:توضیحات|equipment:تجهیزات',
 'courses' => 'name:نام دوره|instrument:درس|level:سطح|branchName:سازمان|teacher_capacity:ظرفیت اساتید|student_capacity:ظرفیت هنرجوها|status:وضعیت|summary:خلاصه|description:توضیحات',
 'terms' => 'name:نام ترم|branchName:سازمان|course:دوره مرتبط|status:وضعیت|start:شروع|end:پایان|repeatType:دوره تکرار|sessionCount:تعداد جلسات|cost:هزینه ترم|currencyId:نوع پول|installmentCount:تعداد اقساط|teachers:استادها|students:هنرجویان|summary:خلاصه|description:توضیحات',
 'messages' => 'title:عنوان|sender:فرستنده|receiver:گیرنده|date:تاریخ|body:متن پیام',
 'notifications' => 'title:عنوان|branchName:فرستنده|recipientUsername:گیرنده|audience:مخاطبان|priority:اولویت|date:تاریخ انتشار|status:وضعیت|body:متن اعلان',
 'posts' => 'summary:خلاصه|description:توضیحات|content:محتوا|author_name:نویسنده|slug:نامک|categories:دسته‌ها|published_at:تاریخ انتشار|views_count:بازدید|comment_count:دیدگاه‌ها',
];
foreach ($details as $key => $definition) $result[$key]['detailFields'] = array_map(function ($pair) { [$key, $label] = explode(':', $pair, 2); return compact('key', 'label'); }, explode('|', $definition));
foreach ($result['classrooms']['detailFields'] as &$field) if ($field['key'] === 'equipment') $field += ['type' => 'rows', 'options' => [['key' => 'name', 'label' => 'تجهیز'], ['key' => 'qty', 'label' => 'تعداد']]];
unset($field);
$result['users']['detailFields'] = [
    ['key' => 'name', 'label' => 'کاربر'], ['key' => 'roles', 'label' => 'نقش‌ها'],
    ['key' => 'rolePermissions', 'label' => 'مجوزهای نقش‌ها'], ['key' => 'directPermissions', 'label' => 'مجوزهای اختصاصی'],
];
$result['my-points']['columns'][0] = ['key' => '_index', 'label' => 'ردیف'];
$result['my-points']['rowActions'] = [];
$result['my-points']['showActions'] = false;
$result['points']['actionsPermission'] = 'canManage';
$result['points']['summaries'] = [['label' => 'قوانین فعال', 'countRows' => 'rules', 'where' => ['status' => 'active'], 'description' => 'اعمال خودکار با سقف و کنترل تکرار']];
$result['my-points']['summaries'] = [['label' => 'امتیاز عمومی من', 'path' => 'balance.general'], ['label' => 'امتیاز حرفه‌ای من', 'path' => 'balance.professional']];
foreach (['my-courses', 'my-terms', 'my-classrooms'] as $key) {
    $result[$key]['columns'] = array_slice($result[$key]['columns'], 1);
    $result[$key]['rowActions'] = [];
    $result[$key]['showActions'] = false;
}
$filterFile = __DIR__ . '/mobile-panel-filters.php';
$filters = is_file($filterFile) ? require $filterFile : [];
foreach ($result as $key => &$contract) {
    $contract['version'] = is_file($filterFile) ? 2 : 1;
    $contract['filters'] = $filters[$key] ?? [];
    $contract['organizationMode'] = in_array($key, $profiles, true) ? 'owner' : ($key === 'points' ? 'points' : 'branch');
    $contract['organizationFilter'] = in_array($key, ['users', 'members', 'students', 'lessons', 'courses', 'terms', 'classrooms', 'availabilities', 'member-schedules', 'availability-exceptions', 'scheduling-rules', 'schedules', 'finance', 'points', 'messages', 'notifications', ...$profiles], true);
}
unset($contract);
foreach (['organization-holidays' => 'سازمان', 'employee-leaves' => 'کارمند'] as $key => $label) {
    $result[$key] = $result['availability-exceptions'];
    $result[$key]['filters'] = $filters[$key] ?? [];
    foreach ($result[$key]['columns'] as &$column) if ($column['key'] === 'name') $column['label'] = $label;
    unset($column);
}
$result['term-discounts'] = [
    'version' => 2, 'organizationFilter' => true, 'organizationMode' => 'branch',
    'columns' => [
        ['key' => 'name', 'label' => 'عنوان تخفیف'], ['key' => 'typeLabel', 'label' => 'نوع'],
        ['key' => 'valueLabel', 'label' => 'مقدار'], ['key' => 'organizationName', 'label' => 'سازمان'],
    ],
    'rowActions' => ['details', 'update', 'delete'],
    'detailTitle' => 'جزئیات تخفیف',
    'detailFields' => [
        ['key' => 'name', 'label' => 'عنوان'], ['key' => 'typeLabel', 'label' => 'نوع'],
        ['key' => 'value', 'label' => 'مقدار'], ['key' => 'organizationName', 'label' => 'سازمان'],
        ['key' => 'startDate', 'label' => 'تاریخ شروع'], ['key' => 'endDate', 'label' => 'تاریخ پایان'],
    ],
    'filters' => [['key' => 'termDiscountTypeFilter', 'webId' => 'termDiscountTypeFilter', 'label' => 'همه انواع تخفیف', 'type' => 'select', 'rowPath' => 'type', 'options' => ['percentage' => 'درصدی', 'fixed' => 'مبلغ ثابت']]],
];
$result['my-points']['views']['rules'] = $result['points'];
$guideFile = __DIR__ . '/panel-page-guidance.php';
$guidance = is_file($guideFile) ? require $guideFile : [];
foreach ($result as $key => &$contract) {
    $guideKey = $key === 'members' ? 'teachers' : $key;
    if (isset($guidance[$guideKey])) $contract['guidance'] = $guidance[$guideKey];
}
unset($contract);
$result['my-points']['views']['rules']['rowActions'] = [];
$result['my-points']['views']['rules']['showActions'] = false;
$result['reports']['rowActions'] = ['details'];
$result['reports']['detailTitle'] = 'جزئیات گزارش';
$result['reports']['columns'][5]['type'] = 'badge';
$result['reports']['detailFields'] = [
    ['key' => 'title', 'label' => 'عنوان'], ['key' => 'branchName', 'label' => 'شعبه'],
    ['key' => 'type', 'label' => 'نوع'], ['key' => 'periodLabel', 'label' => 'بازه زمانی'],
    ['key' => 'date', 'label' => 'تاریخ تولید'], ['key' => 'periodFrom', 'label' => 'از تاریخ'],
    ['key' => 'periodTo', 'label' => 'تا تاریخ'], ['key' => 'status', 'label' => 'وضعیت'],
    ['key' => 'summary', 'label' => 'خلاصه گزارش'],
    ['key' => 'metrics', 'label' => 'شاخص‌ها', 'type' => 'rows', 'options' => [
        ['key' => 'label', 'label' => 'شاخص'], ['key' => 'value', 'label' => 'مقدار'],
    ]],
];
$result['reports']['filters'] = [
    ['key' => 'branchId', 'queryKey' => 'branchId', 'label' => 'همه شعبه‌ها', 'type' => 'select', 'options' => ['source' => 'branches']],
    ['key' => 'type', 'rowPath' => 'type', 'label' => 'همه انواع گزارش', 'type' => 'select', 'options' => ['حضور و غیاب' => 'حضور و غیاب', 'مالی' => 'مالی', 'ثبت‌نام' => 'ثبت‌نام', 'عملکرد' => 'عملکرد']],
    ['key' => 'status', 'rowPath' => 'status', 'label' => 'همه وضعیت‌ها', 'type' => 'select', 'options' => ['آماده' => 'آماده', 'در حال تهیه' => 'در حال تهیه']],
    ['key' => 'period', 'rowPath' => 'period', 'label' => 'همه بازه‌ها', 'type' => 'select', 'options' => ['weekly' => 'هفت روز اخیر', 'monthly' => 'ماه جاری', 'current' => 'وضعیت جاری']],
    ['key' => 'from', 'rowPath' => 'dateISO', 'operator' => 'gte', 'label' => 'از تاریخ', 'type' => 'date'],
    ['key' => 'to', 'rowPath' => 'dateISO', 'operator' => 'lte', 'label' => 'تا تاریخ', 'type' => 'date'],
];
return $result;
