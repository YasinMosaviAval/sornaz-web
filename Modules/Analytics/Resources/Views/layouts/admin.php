<!DOCTYPE html>
<html data-help-enabled="<?= (int)auth()->id() === 1 ? '1' : '0' ?>" data-inline-can-edit="<?= \Modules\System\Services\SiteAdminAccess::allows(auth()->user()) ? '1' : '0' ?>" lang="<?= e(locale()) ?>" dir="<?= e(direction()) ?>">
<head>
    <script>window.siteCsrfToken=<?= json_encode(csrf_token()) ?>;</script>
    <script src="/assets/theme/csrf.js?v=1"></script>
    <script defer src="/assets/System/js/registration.js?v=<?= filemtime(base_path('assets/System/js/registration.js')) ?: 1 ?>"></script>
    <meta charset="UTF-8">
    <link rel="icon" type="image/jpeg" href="/assets/images/logo/cropped-favicon_512x512.jpg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(trans('admin.meta.title','پنل کاربری سرناز')) ?></title>
    <script>(function(){const r=document.documentElement;r.dataset.theme=localStorage.getItem('sornaz.theme')||'indigo';r.dataset.mode=localStorage.getItem('sornaz.mode')||'light';})();</script>
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <script>tailwind.config={darkMode:['class','[data-mode="dark"]']};</script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <link rel="stylesheet" href="/assets/vendor/jalalidatepicker/jalalidatepicker.min.css">
    <link rel="stylesheet" href="/assets/theme/theme.css?v=<?= filemtime(base_path('assets/theme/theme.css')) ?: 1 ?>">
    <style>
        body { font-family: Vazirmatn, Tahoma, sans-serif; }
        .panel-page-toolbar { display:flex; flex-wrap:wrap; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1.5rem; }
        .panel-page-toolbar-tabs { flex:1 1 20rem; min-width:0; }
        .panel-page-toolbar-tabs .panel-page-tabs { margin-bottom:0; }
        .panel-page-toolbar-actions { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:.75rem; margin-inline-start:auto; }
        .panel-page-toolbar-actions:empty { display:none; }
        .panel-page-toolbar-actions > [data-panel-page-action-group] { margin-bottom:0; }
        .panel-add-icon,.panel-export-icon { position:relative!important; display:inline-flex!important; align-items:center!important; justify-content:center!important; flex:0 0 auto!important; width:44px!important; min-width:44px!important; height:44px!important; min-height:44px!important; padding:10px!important; gap:0!important; box-sizing:border-box; }
        .panel-add-icon > [data-panel-add-symbol],.panel-export-icon > [data-panel-export-symbol] { display:block; flex:none; width:22px; height:22px; pointer-events:none; }
        .panel-add-label,.panel-export-label,.panel-table-action-label { position:absolute!important; width:1px!important; height:1px!important; margin:-1px!important; padding:0!important; overflow:hidden!important; clip:rect(0,0,0,0)!important; white-space:nowrap!important; border:0!important; pointer-events:none; }
        .panel-add-icon:focus-visible,.panel-export-icon:focus-visible,.panel-table-action-icon:focus-visible { outline:2px solid var(--theme-500,#6366f1); outline-offset:3px; }
        .panel-table-action-icon { position:relative!important; display:inline-flex!important; align-items:center!important; justify-content:center!important; vertical-align:middle; flex:0 0 auto!important; width:32px!important; min-width:32px!important; height:32px!important; min-height:32px!important; padding:6px!important; gap:0!important; box-sizing:border-box; border-radius:8px; }
        .panel-table-action-icon > [data-panel-table-action-symbol] { display:block; flex:none; width:18px; height:18px; pointer-events:none; }
        .panel-table-action-icon:hover { box-shadow:inset 0 0 0 1px currentColor; }
        .panel-export-icon { border:1px solid var(--border,#d1d5db)!important; border-radius:1rem; background:var(--surface-card,#fff)!important; }
        .panel-export-icon[data-panel-export-icon="excel"] { color:#15803d!important; }
        .panel-export-icon[data-panel-export-icon="pdf"] { color:#dc2626!important; }
        .panel-export-icon:hover { background:var(--surface-muted,#f3f4f6)!important; }
        html[data-mode="dark"] .panel-export-icon[data-panel-export-icon="excel"] { color:#4ade80!important; }
        html[data-mode="dark"] .panel-export-icon[data-panel-export-icon="pdf"] { color:#f87171!important; }
        .panel-action-tooltip { position:fixed; z-index:100000; max-width:min(20rem,calc(100vw - 16px)); box-sizing:border-box; padding:8px 12px; border:1px solid var(--border,#d1d5db); border-radius:10px; background:var(--surface-card,#fff); color:var(--text,#111827); box-shadow:0 4px 16px #0002; font-size:12px; line-height:1.8; white-space:pre-line; overflow-wrap:anywhere; pointer-events:none; }
        .panel-action-tooltip[hidden] { display:none; }
        #sidebar { background-color:#fffdf7!important; color:#1f2937; }
        #sidebar .text-white:not(.text-yellow-300):not(.text-yellow-200) { color:inherit; }
        #sidebar .text-indigo-300 { color:var(--text-muted,#6b7280); }
        #sidebar .text-yellow-300 { color:#a16207; }
        #sidebar .text-yellow-300:hover { color:#854d0e; }
        #sidebar .nav-link:hover:not(.text-yellow-300),#sidebar .nav-link:is(.bg-indigo-600,.bg-indigo-700,.bg-indigo-800):not(.text-yellow-300) { color:#fff; }
        #sidebar .nav-link.text-yellow-300:hover,#sidebar .nav-link.text-yellow-300:is(.bg-indigo-600,.bg-indigo-700,.bg-indigo-800) { color:#fde047; }
        html[data-mode="dark"] #sidebar { background-color:#080b10!important; color:#f3f4f6; }
        html[data-mode="dark"] #sidebar .text-yellow-300 { color:#fde047; }
        html[data-mode="dark"] #sidebar .text-yellow-300:hover { color:#fef08a; }
        #mainContent > .section h1:not(:is([role="dialog"],[id$="Modal"],[contenteditable],article,.prose) h1),
        #mainContent > .section h1:not(:is([role="dialog"],[id$="Modal"],[contenteditable],article,.prose) h1) + p,
        #mainContent > .section [data-panel-description],
        #mainContent > .section .admin-ui-header [data-slot="heading"] { display:none; }
        .branch-detail-select,.branch-detail-select option { background:#fff!important;color:#111827!important; }
        .sidebar { transition: all 0.3s; }
        .card-hover:hover { transform: translateY(-5px); transition: all 0.3s; }
        main.site-content { animation:admin-page-in .14s ease-out; transition:opacity .09s ease,transform .09s ease; }
        body.language-changing main.site-content { opacity:0; transform:translateY(4px); }
        @keyframes admin-page-in { from { opacity:0;transform:translateY(4px); } to { opacity:1;transform:none; } }
        @media (prefers-reduced-motion:reduce) { main.site-content { animation:none;transition:none; } }
        .admin-ui-primary-action { background:#4f46e5; color:#fff; border-color:#4f46e5; }
        .admin-ui-primary-action:hover { background:#4338ca; }
        .admin-ui-table table { width:100%; }
        .admin-ui-table thead { border-bottom:1px solid #e5e7eb; background:#f9fafb; }
        .admin-ui-table tbody tr { transition:background-color .15s ease; }
        .admin-ui-table tbody tr:hover { background:#f9fafb; }
        [data-mode="dark"] .admin-ui-organization-tabs,[data-mode="dark"] .admin-ui-filters,[data-mode="dark"] .admin-ui-table { background:#111827; }
        [data-mode="dark"] .admin-ui-table thead { background:#1f2937; border-color:#374151; }
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
    
    pushScript('admin-runtime.js');
    pushScript('admin-ui-templates.js');
    pushScript('dashboard-templates.js');
    pushScript('dashboard.js');
    pushScript('tracking.js');
    pushScript('account-templates.js');
    pushScript('account.js');
    pushScript('chat.js');
    pushScript('report-templates.js');
    pushScript('reports.js');
    pushScript('chart-gallery.js');
    pushScript('message-templates.js');
    pushScript('messages.js');
    pushScript('notification-templates.js');
    pushScript('notifications.js');
    pushScript('student-term-schedule.js');
    pushScript('student-templates.js');
    pushScript('member-user-picker.js');
    pushScript('term-lesson-chart.js');
    pushScript('student-lesson-chart.js');
    pushScript('students.js');
    pushScript('teacher-templates.js');
    pushScript('contract-type-charts.js');
    pushScript('teachers.js');
    pushScript('branch-templates.js');
    pushScript('panel-organization-filters.js');
    pushScript('branches.js');
    pushScript('branch-types.js');
    pushScript('classroom-templates.js');
    pushScript('schedule-heatmap.js');
    pushScript('classroom-heatmap.js');
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
    pushScript('personal-offerings.js');
    pushScript('member-schedule-templates.js');
    pushScript('member-schedules.js');
    pushScript('availability-templates.js');
    pushScript('availabilities.js');
    pushScript('availability-exception-templates.js');
    pushScript('availability-exceptions.js');
    pushScript('national-holidays.js');
    pushScript('point-templates.js');
    pushScript('personal-points-chart.js');
    pushScript('points.js');
    pushScript('user-templates.js');
    pushScript('users.js');
    pushScript('role-templates.js');
    pushScript('roles.js');
    pushScript('permission-templates.js');
    pushScript('permissions.js');
    
    // pushScript('profiles.js');
    // pushScript('contracts.js');


    pushScript('profile-content.js');
    // pushScript('approvals.js');
    // pushScript('experiences.js');

    // pushScript('polls.js');
    // pushScript('favorites.js');
    // pushScript('ratings.js');
    // pushScript('rating-summaries.js');



    pushScript('posts.js');
    pushScript('post-categories.js');
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
    <?php component('panel-page-guide', ['asTemplate' => true]); ?>
    <script>window.canEditPanelGuides=<?= (int) auth()->id() === 1 ? 'true' : 'false' ?>;</script>
    <script src="/assets/Analytics/js/panel-page-guides.js?v=<?= filemtime(base_path('assets/Analytics/js/panel-page-guides.js')) ?: 1 ?>" defer></script>
    <script>window.adminLocale=<?= json_encode(locale()) ?>;window.adminUiMap=<?= json_encode($adminUiMap??[],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
    <script src="/assets/theme/dialog.js?v=<?= filemtime(base_path('assets/theme/dialog.js')) ?: 1 ?>"></script>
    <script src="/assets/Analytics/js/network.js?v=<?= filemtime(base_path('assets/Analytics/js/network.js')) ?: 1 ?>"></script>
    <script src="/assets/vendor/echarts/echarts.min.js?v=6.1.0"></script>
    <script src="/assets/Analytics/js/charts.js?v=<?= filemtime(base_path('assets/Analytics/js/charts.js')) ?: 1 ?>"></script>
    <script src="/assets/Academy/js/academy-registration.js?v=<?= filemtime(base_path('assets/Academy/js/academy-registration.js')) ?: 1 ?>"></script>
    <script src="/assets/Analytics/js/academy-export-access.js?v=<?= filemtime(base_path('assets/Analytics/js/academy-export-access.js')) ?: 1 ?>"></script>
    <?=scripts()?>
    <script src="/assets/vendor/jalalidatepicker/jalalidatepicker.min.js?v=<?= filemtime(base_path('assets/vendor/jalalidatepicker/jalalidatepicker.min.js')) ?: 1 ?>"></script>
    <script src="/assets/theme/localized-date.js?v=<?= filemtime(base_path('assets/theme/localized-date.js')) ?: 1 ?>"></script>
    <script>window.initLocalizedDateInputs?.(document);</script>
    <script src="/assets/theme/theme.js?v=<?= filemtime(base_path('assets/theme/theme.js')) ?: 1 ?>"></script>
    <?php if ((int)auth()->id() === 1): ?>
    <script src="/assets/theme/help-center.js?v=<?= filemtime(base_path('assets/theme/help-center.js')) ?: 1 ?>"></script>
    <?php endif; ?>
</body>
</html>
