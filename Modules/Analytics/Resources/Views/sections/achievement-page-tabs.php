<?php
component('panel-page-tabs', [
 'active' => $active ?? 'awards',
 'label' => locale() === 'en' ? 'Records and achievements' : 'سوابق و دستاوردها',
 'tabs' => [
  'awards' => ['labels' => ['پاداش‌ها و جوایز', 'Awards']],
  'badges' => ['labels' => ['نشان‌ها', 'Badges']],
  'experiences' => ['labels' => ['سوابق کاری', 'Work experience']],
  'certificates' => ['labels' => ['گواهی‌ها', 'Certificates']],
  'educations' => ['labels' => ['تحصیلات', 'Education']],
  'events' => ['labels' => ['رویدادها', 'Events']],
  'polls' => ['labels' => ['نظرسنجی‌ها', 'Polls']],
  'publications' => ['labels' => ['آثار منتشر شده', 'Publications']],
 ],
]);
