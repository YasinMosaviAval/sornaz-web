<?php
$previewOnlySections = [
    ['key' => 'notation-preview', 'label' => 'نت‌نویسی', 'access' => 'common', 'fields' => [['key' => 'title', 'label' => 'عنوان نت', 'type' => 'text'], ['key' => 'instrument', 'label' => 'ساز', 'type' => 'select'], ['key' => 'clef', 'label' => 'کلید', 'type' => 'select'], ['key' => 'tempo', 'label' => 'سرعت', 'type' => 'number']], 'actions' => [['key' => 'all', 'label' => 'همه', 'fields' => []], ['key' => 'mine', 'label' => 'نت‌های من', 'fields' => []], ['key' => 'saved', 'label' => 'ذخیره‌شده‌ها', 'fields' => []], ['key' => 'create', 'label' => 'نوشتن نت جدید', 'fields' => [['key' => 'title', 'label' => 'عنوان نت', 'type' => 'text'], ['key' => 'instrument', 'label' => 'ساز', 'type' => 'select'], ['key' => 'clef', 'label' => 'کلید', 'type' => 'select'], ['key' => 'tempo', 'label' => 'سرعت', 'type' => 'number']]]]],
    ['key' => 'auth-preview', 'label' => 'ورود و بازیابی حساب', 'access' => 'guest', 'fields' => [], 'actions' => [['key' => 'login', 'label' => 'ورود', 'fields' => [['key' => 'identity', 'label' => 'شماره همراه یا ایمیل', 'type' => 'text'], ['key' => 'password', 'label' => 'رمز عبور', 'type' => 'password']]], ['key' => 'register', 'label' => 'ثبت‌نام', 'fields' => [['key' => 'name', 'label' => 'نام', 'type' => 'text'], ['key' => 'identity', 'label' => 'شماره همراه یا ایمیل', 'type' => 'text'], ['key' => 'password', 'label' => 'رمز عبور', 'type' => 'password']]], ['key' => 'recover', 'label' => 'بازیابی رمز عبور', 'fields' => [['key' => 'identity', 'label' => 'شماره همراه یا ایمیل', 'type' => 'text'], ['key' => 'code', 'label' => 'کد تأیید', 'type' => 'text']]]]],
    ['key' => 'community-preview', 'label' => 'جامعه و پروفایل اجتماعی', 'access' => 'common', 'fields' => [], 'actions' => [['key' => 'feed', 'label' => 'خوراک', 'fields' => []], ['key' => 'post', 'label' => 'انتشار نوشته', 'fields' => [['key' => 'text', 'label' => 'متن نوشته', 'type' => 'multiline'], ['key' => 'media', 'label' => 'رسانه', 'type' => 'file']]], ['key' => 'saved', 'label' => 'ذخیره‌شده‌ها', 'fields' => []], ['key' => 'conversations', 'label' => 'پیام‌های اجتماعی', 'fields' => []]]],
    ['key' => 'market-preview', 'label' => 'خرید و یادگیری دوره', 'access' => 'common', 'fields' => [], 'actions' => [['key' => 'checkout', 'label' => 'ثبت سفارش', 'fields' => [['key' => 'course', 'label' => 'دوره', 'type' => 'select'], ['key' => 'discount', 'label' => 'کد تخفیف', 'type' => 'text']]], ['key' => 'learning', 'label' => 'مسیر یادگیری', 'fields' => []], ['key' => 'progress', 'label' => 'پیشرفت درس‌ها', 'fields' => []]]],
];
$previewData = array_merge($previewSections, $previewOnlySections);
$previewRole = $previewRole ?? 'admin';
?>
<link rel="stylesheet" href="/assets/Analytics/css/public-ui-preview-v2.css?v=1">
<div class="sornaz-preview-panel relative h-screen overflow-hidden" id="sornazPreview" dir="rtl">
    <? component('panel-table-template'); ?>
    <? component('panel-filters-template'); ?>
    <div id="sidebarOverlay" class="hidden fixed inset-0 z-30 bg-black/40 lg:hidden"></div>
    <div class="flex h-full">
        <? component('sidebar', ['previewPanelMode' => $previewRole]); ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <? component('header', ['previewPanelMode' => $previewRole]); ?>
            <main class="flex-1 overflow-auto bg-gray-100 p-4 md:p-8" id="mainContent">
                <div class="sornaz-preview-actions mb-5 flex flex-wrap items-center gap-2">
                    <button type="button" id="sornazPreviewAdd" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700"><i class="fas fa-plus ml-2"></i>افزودن</button>
                    <button type="button" id="sornazPreviewExcel" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fas fa-file-excel ml-2 text-green-600"></i>خروجی اکسل</button>
                    <button type="button" id="sornazPreviewPdf" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fas fa-file-pdf ml-2 text-red-600"></i>خروجی پی‌دی‌اف</button>
                </div>
                <div class="sornaz-preview-tabs mb-5 flex flex-wrap gap-2" id="sornazPreviewTabs" role="tablist"></div>
                <div id="sornazPreviewBody"></div>
                <div id="sornazPreviewOriginal">
                    <?php
                    $previewComponents = ['dashboard', 'account', 'notation', 'chat', 'gallery', 'awards', 'certificates', 'experiences', 'educations', 'events', 'polls', 'publications', 'badges', 'reports', 'chart-gallery', 'students', 'teachers', 'branches', 'classrooms', 'courses', 'course-levels', 'terms', 'finance', 'scheduling-rules', 'schedules', 'instruments', 'lessons', 'member-schedules', 'availabilities', 'availability-exceptions', 'users', 'tracking', 'roles', 'permissions', 'posts', 'post-categories', 'post-editor', 'pages', 'comments', 'media', 'contact-us', 'academy-enroll', 'academy-requests'];
                    if ($previewRole === 'user') {
                        $previewComponents = array_values(array_diff($previewComponents, ['dashboard', 'gallery', 'finance', 'lessons', 'member-schedules']));
                    }
                    foreach ($previewComponents as $previewComponent) {
                        component($previewComponent, ['previewPanelMode' => $previewRole]);
                    }
                    if ($previewRole === 'user') {
                        component('preview-student-learning');
                        component('personal-panel-sections', ['previewPanelMode' => 'user']);
                    }
                    component('messages', ['canUseMessageActions' => true]);
                    component('notifications', ['canUseNotificationActions' => true, 'canCreateNotifications' => true]);
                    component('settings', ['previewPanelMode' => $previewRole]);
                    component('preview-protected-sections');
                    ?>
                </div>
            </main>
        </div>
    </div>
</div>
<script id="sornazPreviewData" type="application/json"><?= json_encode($previewData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<script src="/assets/Analytics/js/public-ui-preview-v2.js?v=1" defer></script>
