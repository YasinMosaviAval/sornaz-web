<?php
component('panel-page-tabs', [
 'active' => $active ?? 'educations',
 'label' => locale() === 'en' ? 'Records and achievements' : 'سوابق و دستاوردها',
 'tabs' => [
  'educations' => ['labels' => ['تحصیلات', 'Education']],
  'certificates' => ['labels' => ['گواهی‌ها', 'Certificates']],
  'awards' => ['labels' => ['پاداش‌ها و جوایز', 'Awards']],
  'experiences' => ['labels' => ['سوابق کاری', 'Work experience']],
  'events' => ['labels' => ['رویدادها', 'Events']],
  'publications' => ['labels' => ['آثار منتشر شده', 'Publications']],
  'badges' => ['labels' => ['نشان‌ها', 'Badges']],
 ],
]);
