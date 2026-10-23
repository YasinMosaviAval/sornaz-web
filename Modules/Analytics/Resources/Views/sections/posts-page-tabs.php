<?php
component('panel-page-tabs', [
    'active' => $active ?? 'posts',
    'label' => locale() === 'en' ? 'Posts' : 'نوشته‌ها',
    'tabs' => [
        'post-categories' => ['labels' => ['دسته‌بندی نوشته‌ها', 'Post categories']],
        'posts' => ['labels' => ['نوشته‌ها', 'Posts']],
        'media' => ['labels' => ['رسانه‌ها', 'Media']],
        'comments' => ['labels' => ['دیدگاه‌ها', 'Comments']],
    ],
]);
