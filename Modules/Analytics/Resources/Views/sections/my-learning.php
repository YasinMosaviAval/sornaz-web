<?php
$learningUserId = (int)(auth()->id() ?? 0);
$learningLocale = locale() === 'en' ? 'en' : 'fa';
$learningSql = "SELECT m.member_id,e.type AS enrollment_type,e.status AS enrollment_status,
 t.term_id,t.status AS term_status,t.start_date,t.end_date,
 c.course_id,c.branch_id,
 s.term_session_id,s.delivery_mode,b.status AS booking_status,b.requested_date,b.start_time,b.end_time,
 r.classroom_id,
 (SELECT value FROM translations WHERE table_name='academy_branch_classrooms' AND table_id=r.classroom_id AND field='online_join_url' AND locale='fa' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1) AS online_join_url,
 COALESCE((SELECT value FROM translations WHERE table_name='academy_branch_courses' AND table_id=c.course_id AND field='title' AND locale=? AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('دوره ',c.course_id)) AS course_title,
 COALESCE((SELECT value FROM translations WHERE table_name='academy_branch_course_terms' AND table_id=t.term_id AND field='title' AND locale=? AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('ترم ',t.term_id)) AS term_title,
 COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=c.branch_id AND field='name' AND locale=? AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',c.branch_id)) AS branch_title,
 COALESCE((SELECT value FROM translations WHERE table_name='academy_branch_classrooms' AND table_id=r.classroom_id AND field='title' AND locale=? AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),IF(r.classroom_id IS NULL,'—',CONCAT('کلاس ',r.classroom_id))) AS classroom_title
 FROM academy_branch_members m
 JOIN academy_branch_course_term_enrollments e ON e.member_id=m.member_id AND e.deleted_at IS NULL AND e.type IN ('teacher','student')
 JOIN academy_branch_course_terms t ON t.term_id=e.term_id AND t.deleted_at IS NULL
 JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL
 LEFT JOIN academy_branch_course_term_sessions s ON s.term_id=t.term_id AND e.status='active' AND s.deleted_at IS NULL
 LEFT JOIN academy_branch_bookings b ON b.booking_id=s.booking_id AND b.deleted_at IS NULL
 LEFT JOIN academy_branch_classrooms r ON r.classroom_id=s.classroom_id AND r.deleted_at IS NULL
 WHERE m.user_id=? AND m.deleted_at IS NULL
 ORDER BY COALESCE(b.requested_date,t.start_date) DESC,b.start_time";
$learningStatement = db()->prepare($learningSql);
$learningStatement->execute([$learningLocale,$learningLocale,$learningLocale,$learningLocale,$learningUserId]);
$learningRows = $learningStatement->fetchAll(\PDO::FETCH_ASSOC);
$learningCourses = $learningTerms = $learningClasses = [];
$learningTeacherNames = [];
foreach ($learningRows as $learningRow) {
    $learningKey = (int)$learningRow['term_id'] . ':' . (int)$learningRow['member_id'];
    if (!array_key_exists($learningKey, $learningTeacherNames)) {
        $waiting = \Core\database\DB::table('academy_branch_course_term_waiting_list')->where('term_id', (int)$learningRow['term_id'])->where('member_id', (int)$learningRow['member_id'])->whereNull('deleted_at')->orderBy('term_waiting_list_id', 'DESC')->first();
        $chosenTeacher = $waiting ? \Core\database\DB::table('translations')->where('table_name', 'academy_branch_course_term_waiting_list')->where('table_id', (int)$waiting['term_waiting_list_id'])->where('field', 'final_teacher_id')->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first() : null;
        $teacherMember = $chosenTeacher ? \Core\database\DB::table('academy_branch_members')->where('member_id', (int)$chosenTeacher['value'])->whereNull('deleted_at')->first() : null;
        $teacherText = $teacherMember ? \Core\database\DB::table('translations')->where('table_name', 'users')->where('table_id', (int)$teacherMember['user_id'])->where('field', 'full_name')->where('locale', $learningLocale)->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first() : null;
        $learningTeacherNames[$learningKey] = (string)($teacherText['value'] ?? '');
    }
    $learningRow['teacher_name'] = $learningTeacherNames[$learningKey];
    $learningCourses[(int)$learningRow['course_id']] = $learningRow;
    $learningTerms[(int)$learningRow['term_id']] = $learningRow;
    if (!empty($learningRow['term_session_id'])) $learningClasses[(int)$learningRow['term_session_id']] = $learningRow;
}
$learningEmpty = static fn(string $message): string => '<div class="rounded-2xl border border-dashed bg-white p-10 text-center text-sm text-gray-400 dark:border-slate-700 dark:bg-slate-900">'.e($message).'</div>';
$learningRole = static fn(array $row): string => ($row['enrollment_type'] ?? '') === 'teacher' ? 'مدرس' : 'هنرجو';
?>
<section id="my-classrooms" class="section hidden space-y-6">
 <header><h1 class="text-3xl font-bold">کلاس‌های من</h1><p class="mt-1 text-gray-500">جلسه‌های کلاس‌هایی که در آن‌ها مدرس یا هنرجو هستید</p></header>
 <div class="grid gap-4 lg:grid-cols-2">
  <?php if(!$learningClasses): ?><?= $learningEmpty('هنوز جلسه کلاسی برای شما ثبت نشده است.') ?><?php endif; ?>
  <?php foreach($learningClasses as $row): $joinUrl = trim((string)($row['online_join_url'] ?? '')); $canJoin = ($row['delivery_mode'] ?? '') === 'online' && !in_array(($row['booking_status'] ?? ''), ['canceled', 'rejected'], true) && filter_var($joinUrl, FILTER_VALIDATE_URL) && parse_url($joinUrl, PHP_URL_SCHEME) === 'https'; ?>
   <article class="rounded-2xl border bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"><div class="flex items-start justify-between gap-3"><div><h2 class="font-bold"><?=e($row['course_title'])?></h2><p class="mt-1 text-sm text-gray-500"><?=e($row['term_title'])?> · <?=($row['delivery_mode'] ?? '') === 'online' ? 'کلاس آنلاین' : e($row['classroom_title'])?></p></div><span class="rounded-full bg-indigo-50 px-3 py-1 text-xs text-indigo-700"><?=e($learningRole($row))?></span></div><div class="mt-4 flex flex-wrap gap-3 text-sm text-gray-600 dark:text-slate-300"><span><i class="far fa-calendar ml-1"></i><?=e((string)($row['requested_date']??'—'))?></span><span dir="ltr"><i class="far fa-clock ml-1"></i><?=e(substr((string)($row['start_time']??''),0,5))?> – <?=e(substr((string)($row['end_time']??''),0,5))?></span><span><i class="fas fa-building ml-1"></i><?=e($row['branch_title'])?></span><?php if($row['teacher_name']):?><span><i class="fas fa-chalkboard-teacher ml-1"></i><?=e($row['teacher_name'])?></span><?php endif;?></div><?php if($canJoin):?><a href="<?=e($joinUrl)?>" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm text-white">ورود به کلاس آنلاین</a><?php endif;?></article>
  <?php endforeach; ?>
 </div>
</section>
<section id="my-courses" class="section hidden space-y-6">
 <header><h1 class="text-3xl font-bold">دوره‌های من</h1><p class="mt-1 text-gray-500">فقط دوره‌هایی که در آن‌ها عضویت دارید</p></header>
 <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
  <?php if(!$learningCourses): ?><?= $learningEmpty('هنوز دوره‌ای برای شما ثبت نشده است.') ?><?php endif; ?>
  <?php foreach($learningCourses as $row): ?><article class="rounded-2xl border bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"><div class="flex items-start justify-between gap-3"><h2 class="font-bold"><?=e($row['course_title'])?></h2><span class="rounded-full bg-indigo-50 px-3 py-1 text-xs text-indigo-700"><?=e($learningRole($row))?></span></div><p class="mt-3 text-sm text-gray-500"><i class="fas fa-building ml-1"></i><?=e($row['branch_title'])?></p></article><?php endforeach; ?>
 </div>
</section>
<section id="my-terms" class="section hidden space-y-6">
 <header><h1 class="text-3xl font-bold">ترم‌های من</h1><p class="mt-1 text-gray-500">ترم‌های مرتبط با ثبت‌نام‌های آموزشی شما</p></header>
 <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
  <?php if(!$learningTerms): ?><?= $learningEmpty('هنوز ترمی برای شما ثبت نشده است.') ?><?php endif; ?>
  <?php foreach($learningTerms as $row): ?><article class="rounded-2xl border bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"><div class="flex items-start justify-between gap-3"><div><h2 class="font-bold"><?=e($row['term_title'])?></h2><p class="mt-1 text-sm text-gray-500"><?=e($row['course_title'])?></p></div><span class="rounded-full bg-indigo-50 px-3 py-1 text-xs text-indigo-700"><?=($row['enrollment_status']??'')==='active'?e($learningRole($row)):'در انتظار برنامه‌ریزی'?></span></div><div class="mt-4 flex justify-between text-xs text-gray-500"><span><?=e((string)$row['start_date'])?></span><span>تا</span><span><?=e((string)$row['end_date'])?></span></div></article><?php endforeach; ?>
 </div>
</section>
