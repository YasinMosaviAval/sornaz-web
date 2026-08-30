<?php
$panelUser = auth()->user();
$isSiteAdminPanel = \Modules\System\Services\SiteAdminAccess::allows($panelUser);
$hasMemberManagementRole = $panelUser && (bool)\Core\database\DB::table('academy_branch_members')
    ->join('academy_branch_member_roles', 'academy_branch_member_roles.member_id', '=', 'academy_branch_members.member_id')
    ->where('academy_branch_members.user_id', (int)$panelUser['user_id'])->whereIn('academy_branch_member_roles.role_id', [7, 16])
    ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->first();
$showAcademyPanelSections = $isSiteAdminPanel || $hasMemberManagementRole || in_array(($panelUser['type'] ?? ''), ['academy','branch'], true);
?>
<div class="relative h-screen overflow-hidden">
    <script>window.adminCsrfToken=<?= json_encode(csrf_token()) ?>;window.adminMemberSchedulesData=<?= json_encode($scheduleFixtures['schedules']??[],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;window.adminAvailabilityExceptionsData=<?= json_encode($scheduleFixtures['exceptions']??[],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;window.adminInlineTranslations=<?= json_encode($inlineTranslationCatalog??[],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;</script>
    <div id="sidebarOverlay" class="hidden fixed inset-0 z-30 bg-black/40 lg:hidden"></div>
    <div class="flex h-full">
        <? component('sidebar'); ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
        <? component('header'); ?>
        <main class="flex-1 overflow-auto p-8" id="mainContent">
            <?php if ($message = session()->getFlash('admin_test_message')): ?>
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800"><?= e($message) ?></div>
            <?php endif; ?>
            <?php if ($error = session()->getFlash('admin_test_error')): ?>
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800"><?= e($error) ?></div>
            <?php endif; ?>
            <?php if ($report = session()->getFlash('admin_test_report')): ?>
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-900"><h2 class="mb-4 text-lg font-bold">گزارش کامل اجرای تست</h2><div class="grid gap-4 md:grid-cols-2"><?php foreach($report as $section):?><section class="rounded-xl bg-white/70 p-4"><h3 class="font-bold"><?=e($section['title'])?></h3><ul class="mt-2 list-disc space-y-1 pr-5 text-sm"><?php foreach($section['items'] as $item):?><li><?=e($item)?></li><?php endforeach?></ul></section><?php endforeach?></div></div><?php endif; ?>
            <?
            component('add-user-modal');
            component('dashboard');
            component('account');
            if ($showAcademyPanelSections) {
            component('reports');
            component('chart-gallery');
            component('messages');
            component('notifications');
            component('students');
            component('teachers');
            component('branches');
            component('branch-types');
            component('classrooms');
            component('classroom-types');
            component('courses');
            component('course-levels');
            component('terms');
            component('gallery');
            component('finance');
            component('scheduling-rules');
            component('schedules');
            component('instruments');
            component('lessons');
            component('member-schedules');
            component('availabilities');
            component('availability-exceptions');
            component('points');
            component('settings');
            if ($isSiteAdminPanel) {
                component('tracking');
                component('users');
                component('roles');
                component('permissions');
                component('guides', ['guides'=>$guides??[]]);
            }

            //component('awards');
            //component('certificates');
            //component('experiences');
            //component('educations');
            //component('events');
            //component('polls');
            //component('favorites');
            //component('publications');
            //component('ratings');
            //component('badges');
            //component('approvals');
            //component('contracts');
            //component('profiles');
            //component('rating-summaries');

            component('posts');
            component('post-categories');
            component('post-editor');
            component('pages');
            component('comments');
            component('media');
            component('contact-us');
            component('academy-enroll');
            component('academy-requests');
            component('tests', ['testStats' => $testStats ?? []]);
            }
            ?>
        </main>
    </div>
</div>

