<?php
$tabs = [
    'branches' => ['labels' => ['مدیریت شعبه‌ها', 'Branch management']],
    'availabilities' => ['labels' => ['برنامه زمانی سازمان', 'Organization schedule']],
    'scheduling-rules' => ['labels' => ['قوانین زمانبندی سازمان', 'Organization scheduling rules']],
    'organization-holidays' => ['labels' => ['تعطیلات', 'Holidays']],
];
component('panel-page-tabs', ['active' => $active ?? 'branches', 'tabs' => $tabs, 'onSelect' => 'showOrganizationPage', 'label' => locale() === 'en' ? 'Organization' : 'سازمان']);
