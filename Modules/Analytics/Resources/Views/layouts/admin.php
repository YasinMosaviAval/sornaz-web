<!DOCTYPE html>
<html lang="<?= e(locale()) ?>" dir="<?= e(direction()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(trans('admin.meta.title','Sornaz Admin Panel')) ?></title>
    <script>(function(){const r=document.documentElement;r.dataset.theme=localStorage.getItem('sornaz.theme')||'indigo';r.dataset.mode=localStorage.getItem('sornaz.mode')||'light';})();</script>
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <link rel="stylesheet" href="/assets/theme/theme.css?v=<?= filemtime(base_path('assets/theme/theme.css')) ?: 1 ?>">
    <style>
        body { font-family: Vazirmatn, Tahoma, sans-serif; }
        .sidebar { transition: all 0.3s; }
        .card-hover:hover { transform: translateY(-5px); transition: all 0.3s; }
        main.site-content { animation:admin-page-in .14s ease-out; transition:opacity .09s ease,transform .09s ease; }
        body.language-changing main.site-content { opacity:0; transform:translateY(4px); }
        @keyframes admin-page-in { from { opacity:0;transform:translateY(4px); } to { opacity:1;transform:none; } }
        @media (prefers-reduced-motion:reduce) { main.site-content { animation:none;transition:none; } }
    </style>
    
    <script src="/assets/vendor/jspdf/jspdf.umd.min.js"></script>
    <script src="/assets/vendor/jspdf/jspdf.plugin.autotable.min.js"></script>
    <script src="/assets/vendor/html2canvas/html2canvas.min.js"></script>
    <?
    // pushStyle('reset.css');
    ?>
    <?//=styles()?>
</head>
<body class="bg-gray-100">
    <main class="site-content">
        <?=$slot?>
    </main>
    <?
    pushScript('sidebar.js');
    pushScript('add-user-modal.js');
    
    pushScript('dashboard-templates.js');
    pushScript('dashboard.js');
    pushScript('account-templates.js');
    pushScript('account.js');
    pushScript('report-templates.js');
    pushScript('reports.js');
    pushScript('chart-gallery.js');
    pushScript('message-templates.js');
    pushScript('messages.js');
    pushScript('notification-templates.js');
    pushScript('notifications.js');
    pushScript('student-templates.js');
    pushScript('students.js');
    pushScript('teacher-templates.js');
    pushScript('teachers.js');
    pushScript('branch-templates.js');
    pushScript('branches.js');
    pushScript('branch-types.js');
    pushScript('classroom-templates.js');
    pushScript('classrooms.js');
    pushScript('classroom-types.js');
    pushScript('course-templates.js');
    pushScript('courses.js');
    pushScript('course-levels.js');
    pushScript('term-templates.js');
    pushScript('terms.js');
    pushScript('gallery-templates.js');
    pushScript('gallery.js');
    pushScript('finance-templates.js');
    pushScript('finance.js');
    pushScript('scheduling-rule-templates.js');
    pushScript('scheduling-rules.js');
    pushScript('schedule-templates.js');
    pushScript('schedules.js');
    pushScript('instrument-templates.js');
    pushScript('instruments.js');
    pushScript('lesson-templates.js');
    pushScript('lessons.js');
    pushScript('member-schedule-templates.js');
    pushScript('member-schedules.js');
    pushScript('availability-templates.js');
    pushScript('availabilities.js');
    pushScript('availability-exception-templates.js');
    pushScript('availability-exceptions.js');
    pushScript('point-templates.js');
    pushScript('points.js');
    pushScript('user-templates.js');
    pushScript('users.js');
    pushScript('role-templates.js');
    pushScript('roles.js');
    pushScript('permission-templates.js');
    pushScript('permissions.js');
    
    // pushScript('profiles.js');
    // pushScript('contracts.js');


    // pushScript('awards.js');
    // pushScript('certificates.js');
    // pushScript('educations.js');
    // pushScript('events.js');
    // pushScript('publications.js');
    // pushScript('badges.js');
    // pushScript('approvals.js');
    // pushScript('experiences.js');

    // pushScript('polls.js');
    // pushScript('favorites.js');
    // pushScript('ratings.js');
    // pushScript('rating-summaries.js');



    pushScript('posts.js');
    pushScript('post-categories.js');
    pushScript('pages.js');
    pushScript('comments.js');
    pushScript('media.js');
    pushScript('settings.js');
    pushScript('contact-us.js');
    pushScript('academy-enroll.js');
    pushScript('academy-requests.js');
    
    pushScript('admin.js');
    pushScript('admin-i18n.js');
    pushScript('admin-inline-editor.js');
    ?>
    <div id="modalContainer"></div>
    <script>window.adminLocale=<?= json_encode(locale()) ?>;window.adminUiMap=<?= json_encode($adminUiMap??[],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;</script>
    <script src="/assets/theme/dialog.js?v=<?= filemtime(base_path('assets/theme/dialog.js')) ?: 1 ?>"></script>
    <script src="/assets/Analytics/js/network.js?v=<?= filemtime(base_path('assets/Analytics/js/network.js')) ?: 1 ?>"></script>
    <script src="/assets/vendor/echarts/echarts.min.js?v=6.1.0"></script>
    <script src="/assets/Analytics/js/charts.js?v=<?= filemtime(base_path('assets/Analytics/js/charts.js')) ?: 1 ?>"></script>
    <?=scripts()?>
    <script src="/assets/theme/theme.js?v=<?= filemtime(base_path('assets/theme/theme.js')) ?: 1 ?>"></script>
    <script src="/assets/theme/help-center.js?v=<?= filemtime(base_path('assets/theme/help-center.js')) ?: 1 ?>"></script>
</body>
</html>
