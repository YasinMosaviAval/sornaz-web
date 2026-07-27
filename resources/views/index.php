<?php

$columns = [
    ['title' => '#',         'field' => 'id'],
    ['title' => 'عنوان',     'field' => 'title'],
    ['title' => 'وضعیت',     'field' => 'status'],
    ['title' => 'شهر',       'field' => 'city'],
    ['title' => 'تاریخ ثبت', 'field' => 'created_at'],
    ['title' => '',           'field' => 'actions'],
];

?>

<?php

$rows = [];
foreach ($academies['data'] as $academy) {
    $rows[] = [
        'id' => $academy->user_id,
        'title' => e($academy->title),
        'status' => $academy->status
            ? '<span class="sn-badge success">فعال</span>'
            : '<span class="sn-badge danger">غیرفعال</span>',
        'city' => e($academy->city ?? '-'),
        'created_at' => e($academy->created_at),
        'actions' => sprintf(
            '<a href="/dashboard/academies/%d/edit" class="sn-btn primary">ویرایش</a>',
            $academy->user_id
        )
    ];
}
