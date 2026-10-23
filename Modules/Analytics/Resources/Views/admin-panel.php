<?php
$panelUser = auth()->user();
$panelBranchContext = (new \Modules\Analytics\Services\MobilePanelAccess())->capabilities($panelUser)['branchContext'];
$isSiteAdminPanel = \Modules\System\Services\SiteAdminAccess::allows($panelUser);
$hasMemberManagementRole = $panelUser && (bool)\Core\database\DB::table('academy_branch_members')
    ->join('academy_branch_member_roles', 'academy_branch_member_roles.member_id', '=', 'academy_branch_members.member_id')
    ->join('access_system_roles','access_system_roles.role_id','=','academy_branch_member_roles.role_id')
    ->where('academy_branch_members.user_id', (int)$panelUser['user_id'])
    ->where('academy_branch_members.status', 'active')
    ->whereIn('access_system_roles.name', ['academy_owner','academy_manager','branch_manager','academy_receptionist','branch_receptionist','academy_branch_owner','academy_branch_manager','academy_branch_receptionist'])
    ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
$hasContractManagementRole = $panelUser && (bool)\Core\database\DB::table('academy_branch_members')
    ->join('academy_branch_member_contracts', 'academy_branch_member_contracts.member_id', '=', 'academy_branch_members.member_id')
    ->where('academy_branch_members.user_id', (int)$panelUser['user_id'])
    ->where('academy_branch_members.status', 'active')
    ->whereIn('academy_branch_member_contracts.type', ['owner','manager','receptionist'])
    ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_contracts.deleted_at')->first();
$showAcademyPanelSections = $isSiteAdminPanel || $hasMemberManagementRole || $hasContractManagementRole || in_array(($panelUser['type'] ?? ''), ['academy','branch'], true);
$canUsePanelMessageActions = $showAcademyPanelSections || (bool)\Core\database\DB::table('academy_branch_members')->where('user_id', (int) $panelUser['user_id'])->whereNull('deleted_at')->first();
?>
<div class="relative h-screen overflow-hidden">
    <script>window.panelBranchContext=<?= json_encode($panelBranchContext) ?>;</script>
    <script>window.adminCsrfToken=<?= json_encode(csrf_token()) ?>;window.adminMemberSchedulesData=[];window.adminAvailabilityExceptionsData=[];window.adminInlineTranslations=<?= json_encode($inlineTranslationCatalog??[],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;</script>
    <div id="sidebarOverlay" class="hidden fixed inset-0 z-30 bg-black/40 lg:hidden"></div>
    <div class="flex h-full">
        <? component('sidebar'); ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
        <? component('header'); ?>
        <main class="flex-1 overflow-auto p-8" id="mainContent">
            <?
            component('add-user-modal');
            component('panel-page-header-template');
            component('panel-organization-tabs-template');
            component('panel-filters-template');
            component('panel-table-template');
            component('panel-help-template');
            if ($showAcademyPanelSections) component('dashboard');
            else component('student-dashboard');
            component('account');
            component('settings');
            component('points');
            component('notation');
            component('chat');
            component('messages', ['canUseMessageActions' => $canUsePanelMessageActions]);
            component('notifications', ['canUseNotificationActions' => $canUsePanelMessageActions, 'canCreateNotifications' => $showAcademyPanelSections]);
            if (!$showAcademyPanelSections) component('my-learning');
            if (!$showAcademyPanelSections) component('personal-panel-sections');
            if (!$showAcademyPanelSections && (new \Modules\Academy\Services\AcademyClassScheduleService())->hasTeacherSessions((int) $panelUser['user_id'])) component('schedules');
            component('gallery');
            if ((int) ($panelUser['user_id'] ?? 0) === 1) {
                component('awards');
                component('certificates');
                component('experiences');
                component('educations');
                component('events');
                component('polls');
                component('publications');
                component('badges');
            }
            if ($showAcademyPanelSections) {
            component('reports');
            component('chart-gallery');
            component('students');
            component('teachers');
            component('branches');
            component('branch-types');
            component('classrooms');
            component('classroom-types');
            component('courses');
            component('course-levels');
            component('terms');
            component('finance');
            component('scheduling-rules');
            component('schedules');
            component('instruments');
            component('lessons');
            component('member-schedules');
            component('availabilities');
            component('availability-exceptions');
            component('users');
            if ($isSiteAdminPanel) {
                component('national-holidays');
                component('tracking');
                component('roles');
                component('permissions');
                component('guides', ['guides' => $guides ?? [], 'guideDocuments' => $guideDocuments ?? []]);
            }
            //component('favorites');
            //component('ratings');
            //component('approvals');
            //component('contracts');
            //component('profiles');
            //component('rating-summaries');

            if ($isSiteAdminPanel) {
            component('posts');
            component('post-categories');
            component('post-editor');
            component('pages');
            component('comments');
            component('media');
            }
            component('contact-us');
            component('academy-enroll');
            component('academy-requests');
            }
            ?>
        </main>
    </div>
</div>

