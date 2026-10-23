<?php
component('panel-page-tabs', [
 'active' => $active ?? 'gallery-cover',
 'label' => locale() === 'en' ? 'Gallery' : 'گالری',
 'tabs' => [
  'gallery-cover' => ['labels' => ['کاور', 'Cover']],
  'gallery-logo' => ['labels' => ['لوگو', 'Logo']],
  'gallery-intro-video' => ['labels' => ['ویدیو معرفی', 'Introduction video']],
  'gallery-collection' => ['labels' => ['مجموعه عکس‌ها و ویدیوها', 'Photos and videos']],
 ],
]);
