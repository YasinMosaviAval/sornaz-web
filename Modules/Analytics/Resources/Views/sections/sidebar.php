<?php
$previewRole = $previewPanelMode ?? null;
$hasTeachingEnrollment = false;
if ($previewRole !== null) {
    $panelUser = ['user_id' => 0, 'type' => $previewRole];
    $isSiteAdmin = $previewRole === 'admin';
    $isBranchAccount = $previewRole === 'branch';
    $hasAcademyManagementRole = $previewRole === 'academy';
    $hasContractManagementRole = false;
    $hasAcademyPanelAccess = in_array($previewRole, ['admin', 'academy', 'branch'], true);
    $panelUserId = 0;
    $hasLearningEnrollment = $previewRole === 'user';
    $ownsAcademy = $previewRole === 'academy';
    $isAcademyManager = $previewRole === 'academy';
    $canCreateClassroomType = $previewRole !== 'user';
} else {
$panelUser = auth()->user();
$isSiteAdmin = \Modules\System\Services\SiteAdminAccess::allows($panelUser);
$isBranchAccount = ($panelUser['type'] ?? '') === 'branch';
$hasAcademyManagementRole = $panelUser && (bool)\Core\database\DB::table('academy_branch_members')
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
$hasAcademyPanelAccess = $isSiteAdmin || $isBranchAccount || $hasAcademyManagementRole || $hasContractManagementRole
    || in_array(($panelUser['type'] ?? ''), ['academy', 'branch'], true);
$panelUserId = (int)($panelUser['user_id'] ?? 0);
$hasLearningEnrollment = $panelUserId && (bool)\Core\database\DB::table('academy_branch_members')
    ->join('academy_branch_course_term_enrollments', 'academy_branch_course_term_enrollments.member_id', '=', 'academy_branch_members.member_id')
    ->where('academy_branch_members.user_id', $panelUserId)
    ->where('academy_branch_members.status', 'active')
    ->whereIn('academy_branch_course_term_enrollments.type', ['teacher','student'])
    ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_course_term_enrollments.deleted_at')->first();
$hasTeachingEnrollment = $panelUserId ? \Core\database\DB::table('academy_branch_members')->join('academy_branch_course_term_enrollments', 'academy_branch_course_term_enrollments.member_id', '=', 'academy_branch_members.member_id')->where('academy_branch_members.user_id', $panelUserId)->where('academy_branch_members.status', 'active')->where('academy_branch_course_term_enrollments.type', 'teacher')->where('academy_branch_course_term_enrollments.status', 'active')->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_course_term_enrollments.deleted_at')->first() : null;
$ownsAcademy = $panelUserId && (\Core\database\DB::table('academies')->where('user_id',$panelUserId)->whereNull('deleted_at')->first()
    || \Core\database\DB::table('academies')->where('created_by',$panelUserId)->whereNull('deleted_at')->first());
$isAcademyManager = $panelUserId && (bool)\Core\database\DB::table('academy_branch_members')
    ->join('academy_branch_member_roles','academy_branch_member_roles.member_id','=','academy_branch_members.member_id')
    ->join('access_system_roles','access_system_roles.role_id','=','academy_branch_member_roles.role_id')
    ->where('academy_branch_members.user_id',$panelUserId)
    ->whereRaw("(access_system_roles.name LIKE 'academy_%manager%' AND access_system_roles.name NOT LIKE '%branch%')")
    ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
$hasAcademyPanelAccess = $hasAcademyPanelAccess || (bool) $ownsAcademy;
$canCreateClassroomType = $isSiteAdmin || $ownsAcademy || $isAcademyManager
    || ($panelUserId && (new \Modules\System\Services\AccessControl())->allows($panelUserId, \Modules\Academy\Services\AcademyClassroomService::CREATE_TYPE_PERMISSION));
}
$sidebarUsername = (string) ($panelUser['username'] ?? '');
$sidebarFullName = '';
if ($previewRole !== null) {
    $sidebarFullName = 'کاربر نمونه';
} elseif ($panelUserId) {
    $sidebarNameRow = \Core\database\DB::table('translations')
        ->where('table_name', 'users')->where('table_id', $panelUserId)
        ->where('field', 'full_name')->where('locale', locale())
        ->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
    if (!$sidebarNameRow && locale() !== 'fa') {
        $sidebarNameRow = \Core\database\DB::table('translations')
            ->where('table_name', 'users')->where('table_id', $panelUserId)
            ->where('field', 'full_name')->where('locale', 'fa')
            ->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
    }
    $sidebarFullName = trim((string) ($sidebarNameRow['value'] ?? ''));
}
if ($sidebarFullName === '') $sidebarFullName = $sidebarUsername;
?>
<div id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 bg-indigo-900 text-white flex flex-col shadow-2xl transform -translate-x-full transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:shadow-none">
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-indigo-800">
        <div class="flex items-center gap-3">
            <div class="relative flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-indigo-700" aria-hidden="true">
                <i id="sidebarAvatarFallback" class="fas fa-user text-lg text-indigo-100"></i>
                <img id="sidebarAvatar" class="absolute inset-0 hidden h-full w-full object-cover" alt="">
            </div>
            <div class="sidebar-text min-w-0">
                <h1 class="truncate text-lg font-bold" title="<?= e($sidebarFullName) ?>"><?= e($sidebarFullName) ?></h1>
                <p class="truncate text-xs text-indigo-300" dir="ltr" title="<?= e($sidebarUsername) ?>"><?= e($sidebarUsername) ?></p>
            </div>
        </div>
        <!-- فقط موبایل -->
        <button class="lg:hidden text-xl" onclick="closeMobileSidebar()"><i class="fas fa-times"></i></button>
    </div>
    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden p-4">
        <ul class="space-y-2">
                <?php if ((int)auth()->id() === 1 || $previewRole === 'admin'): ?>
                <li><a href="#" onclick="showSection('guides')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-white"><i class="fas fa-book-reader w-5 text-center"></i> راهنمای عملکردها</a></li>
                <?php endif; ?>
            <?php if ($isSiteAdmin && ($previewRole === 'admin' || env('APP_ENV', 'production') === 'local')) { ?>
                <?php if ($previewRole === null): ?>
                <li>--------------------------------</li>
                <?php endif; ?>
                <li><a href="#" onclick="showSection('posts')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-file-alt w-5 text-center"></i> نوشته‌ها</a></li>
                <li><a href="#" onclick="showSection('pages')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-copy w-5 text-center"></i> برگه‌ها</a></li>
                <li><a href="#" onclick="showSection('media')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-photo-video w-5 text-center"></i> رسانه‌ها</a></li>
                <li><a href="#" onclick="showSection('comments')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-comments w-5 text-center"></i> دیدگاه‌ها</a></li>
                <li><a href="#" onclick="showSection('chart-gallery')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-chart-pie w-5 text-center"></i> گالری نمودارها</a></li>
                <li><a href="#" onclick="showSection('reports')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-chart-bar w-5 text-center"></i> گزارش‌ها</a></li>
                <li>--------------------------------</li>
                <!--
                    <li>--------------------------------</li>
                    <li><a href="#" onclick="showSection('contact-us')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-chart-bar w-5 text-center"></i> contact-us</a></li>
                    <li><a href="#" onclick="showSection('academy-enroll')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-chart-bar w-5 text-center"></i> academy-enroll</a></li>
                    <li><a href="#" onclick="showSection('academy-requests')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-chart-bar w-5 text-center"></i> academy-requests</a></li>
                    <li>--------------------------------</li>
                -->
            <?php } ?>
            <li><a href="#" onclick="showSection('dashboard')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-white"><i class="fas fa-home w-5 text-center"></i> داشبورد</a></li>
            <li><a href="#" onclick="showSection('account')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-white"><i class="fas fa-user-cog w-5 text-center"></i> حساب کاربری</a></li>
            <?php if ((int) ($panelUser['user_id'] ?? 0) === 1 || $previewRole === 'admin'): ?>
            <li><a href="#" onclick="showSection('notation')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-music w-5 text-center"></i> <?= locale()==='en'?'Music Sheets':'نت‌نویسی' ?></a></li>
            <?php endif; ?>
            <?php if ($hasAcademyPanelAccess): ?>
            <?php if ((int) ($panelUser['user_id'] ?? 0) === 1): ?>
            <li>
                <button type="button" onclick="toggleSidebarSubmenu('gallerySubmenu', this)" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-right text-white hover:text-white">
                    <span class="flex items-center gap-3"><i class="fas fa-images w-5 text-center"></i> گالری</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 submenu-chevron"></i>
                </button>
                <ul id="gallerySubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2">
                    <li><a href="#" onclick="showSection('gallery-cover')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-white hover:text-white"><i class="fas fa-image w-4"></i>کاور</a></li>
                    <li><a href="#" onclick="showSection('gallery-logo')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-white hover:text-white"><i class="fas fa-certificate w-4"></i>لوگو</a></li>
                    <li><a href="#" onclick="showSection('gallery-intro-video')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-white hover:text-white"><i class="fas fa-video w-4"></i>ویدیو معرفی</a></li>
                    <li><a href="#" onclick="showSection('gallery-collection')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-white hover:text-white"><i class="fas fa-photo-video w-4"></i>مجموعه عکس‌ها و ویدیوها</a></li>
                </ul>
            </li>
            <?php else: ?>
            <li><a href="#" onclick="showSection('gallery')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-images w-5 text-center"></i> گالری</a></li>
            <?php endif; ?>
            <?php endif; ?>
            <?php if (!$hasAcademyPanelAccess && $hasTeachingEnrollment): ?>
            <li><a href="#" onclick="showSection('schedules')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-calendar-alt w-5 text-center"></i> جلسه‌ها و یادداشت استاد</a></li>
            <?php endif; ?>
            <?php if(!$hasAcademyPanelAccess && $hasLearningEnrollment): ?>
            <li><a href="#" onclick="showSection('my-classrooms')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-chalkboard w-5 text-center"></i> کلاس‌های من</a></li>
            <li><a href="#" onclick="showSection('my-courses')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-book-open w-5 text-center"></i> دوره‌های من</a></li>
            <li><a href="#" onclick="showSection('my-terms')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-calendar-check w-5 text-center"></i> ترم‌های من</a></li>
            <?php endif; ?>
            <?php if ((int) ($panelUser['user_id'] ?? 0) === 1 || $previewRole === 'admin'): ?>
            <li><button type="button" onclick="toggleSidebarSubmenu('profileAchievementsSubmenu',this)" class="flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-right text-yellow-300 transition hover:bg-indigo-800 hover:text-yellow-200"><span class="flex items-center gap-3"><i class="fas fa-trophy w-5 text-center"></i> سوابق و دستاوردها</span><i class="fas fa-chevron-down text-xs submenu-chevron"></i></button>
                <ul id="profileAchievementsSubmenu" class="mt-1 mr-4 hidden space-y-1 border-r border-indigo-700/60 pr-2">
                    <li><a href="#" onclick="showSection('awards')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-yellow-300 hover:bg-indigo-800 hover:text-yellow-200"><i class="fas fa-award w-4"></i>پاداش‌ها و جوایز</a></li>
                    <li><a href="#" onclick="showSection('badges')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-yellow-300 hover:bg-indigo-800 hover:text-yellow-200"><i class="fas fa-medal w-4"></i>نشان‌ها</a></li>
                    <li><a href="#" onclick="showSection('experiences')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-yellow-300 hover:bg-indigo-800 hover:text-yellow-200"><i class="fas fa-briefcase w-4"></i>سوابق کاری</a></li>
                    <li><a href="#" onclick="showSection('certificates')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-yellow-300 hover:bg-indigo-800 hover:text-yellow-200"><i class="fas fa-certificate w-4"></i>گواهی‌ها</a></li>
                    <li><a href="#" onclick="showSection('educations')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-yellow-300 hover:bg-indigo-800 hover:text-yellow-200"><i class="fas fa-graduation-cap w-4"></i>تحصیلات</a></li>
                    <li><a href="#" onclick="showSection('events')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-yellow-300 hover:bg-indigo-800 hover:text-yellow-200"><i class="fas fa-calendar-day w-4"></i>رویدادها</a></li>
                    <li><a href="#" onclick="showSection('polls')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-yellow-300 hover:bg-indigo-800 hover:text-yellow-200"><i class="fas fa-poll w-4"></i>نظرسنجی‌ها</a></li>
                    <li><a href="#" onclick="showSection('publications')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-yellow-300 hover:bg-indigo-800 hover:text-yellow-200"><i class="fas fa-book-open w-4"></i>آثار منتشر شده</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if($isSiteAdmin): ?><li><a href="#" onclick="showSection('tracking')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-white"><i class="fas fa-chart-line w-5 text-center"></i> تحلیل رفتار کاربران</a></li><?php endif; ?>
            <?php if($isSiteAdmin): ?><li><a href="#" onclick="showSection('national-holidays')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-white"><i class="fas fa-calendar-times w-5 text-center"></i> تعطیلات رسمی کشور</a></li><?php endif; ?>
            <?php if($hasAcademyPanelAccess): ?>
            <?php if(!$isBranchAccount): ?>
            <?php if ((int) ($panelUser['user_id'] ?? 0) === 1): ?>
            <li>
                <button type="button" onclick="toggleSidebarSubmenu('branchesSubmenu',this)" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800">
                    <span class="flex gap-3"><i class="fas fa-building w-5"></i>سازمان</span>
                    <i class="fas fa-chevron-down text-xs submenu-chevron"></i>
                </button><ul id="branchesSubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2">
                    <li><a href="#" onclick="showSection('branches')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm"><i class="fas fa-building w-4"></i>سازمان</a></li>
                    <?php if($isSiteAdmin):?>
                        <li><a href="#" onclick="showSection('branch-types')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm"><i class="fas fa-layer-group w-4"></i>انواع آموزشی</a></li>
                    <?php endif;?>
                    </ul>
                </li>
            <?php else: ?>
            <li><a href="#" onclick="showSection('branches')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-building w-5 text-center"></i>سازمان</a></li>
            <?php endif; ?>
            <?php endif; ?>
            <?php if ($isSiteAdmin): ?>
            <li><a href="#" onclick="showSection('teachers')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-chalkboard-teacher w-5 text-center"></i> کارمندها</a></li>
            <?php endif; ?>
<?php if($isSiteAdmin || $ownsAcademy || $isAcademyManager || $isBranchAccount || $hasAcademyManagementRole): ?>
            <?php if ((int) ($panelUser['user_id'] ?? 0) === 1 || $previewRole === 'admin'): ?>
            <li>
                <button type="button" onclick="toggleSidebarSubmenu('profilesSubmenu', this)" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-right text-white">
                    <span class="flex items-center gap-3"><i class="fas fa-id-card w-5 text-center"></i> نقش‌ها و دسترسی‌ها</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 submenu-chevron"></i>
                </button>
                <ul id="profilesSubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2">
                    <li><a href="#" onclick="showSection('users')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-white"><i class="fas fa-users w-4"></i>کاربران</a></li>
                    <?php if($isSiteAdmin): ?>
                    <li><a href="#" onclick="showSection('roles')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-white"><i class="fas fa-user-tag w-4"></i>نقش‌ها</a></li>
                    <li><a href="#" onclick="showSection('permissions')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-white"><i class="fas fa-key w-4"></i>دسترسی‌ها</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
            <?php endif; ?>
            <?php if ((int) ($panelUser['user_id'] ?? 0) === 1 || $previewRole === 'admin'): ?>
            <li>
                <button type="button" onclick="showSection('lessons')" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800">
                    <span class="flex gap-3"><i class="fas fa-book w-5"></i>درس‌ها</span><i class="fas fa-chevron-down text-xs submenu-chevron"></i>
                </button>
                <ul id="lessonsSubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2">
                    <li><a href="#" onclick="showSection('lessons')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm"><i class="fas fa-book w-4"></i>درس‌ها</a></li>
                    <li><a href="#" onclick="showSection('course-levels')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm"><i class="fas fa-signal w-4"></i>سطح درس‌ها</a></li>
                </ul>
            </li>
            <?php else: ?>
            <li><a href="#" onclick="showSection('lessons')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-book w-5 text-center"></i>درس‌ها</a></li>
            <?php endif; ?>
            <li><a href="#" onclick="showSection('classrooms')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-door-open w-5 text-center"></i> کلاس‌ها</a></li>
            <?php if (!$isSiteAdmin): ?>
            <li><a href="#" onclick="showSection('teachers')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-chalkboard-teacher w-5 text-center"></i> کارمندها</a></li>
            <?php endif; ?>
            <!-- <li><a href="#" onclick="showSection('instruments')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-guitar w-5 text-center"></i> سازها</a></li> -->
            <li><a href="#" onclick="showSection('terms')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-calendar-check w-5 text-center"></i> ترم‌ها</a></li>
            <li><a href="#" onclick="showSection('students')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-users w-5 text-center"></i> هنرجویان</a></li>
            <li><a href="#" onclick="showSection('schedules')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-calendar-alt w-5 text-center"></i> تقویم کلاس‌ها</a></li>
            <li><a href="#" onclick="showSection('finance')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-money-bill-wave w-5 text-center"></i> امور مالی</a></li>
            <?php endif; ?>
            <?php if (!$hasAcademyPanelAccess): ?>
            <li><a href="#" onclick="showSection('gallery')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-images w-5 text-center"></i> گالری</a></li>
            <li><a href="#" onclick="showSection('lessons')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-book w-5 text-center"></i> درس‌ها</a></li>
            <li><a href="#" onclick="showSection('member-schedules')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-calendar-alt w-5 text-center"></i> کارمندها</a></li>
            <li><a href="#" onclick="showSection('finance')" class="nav-link flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-money-bill-wave w-5 text-center"></i> امور مالی</a></li>
            <?php endif; ?>
            <li><a href="#settings" onclick="showSection('settings')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-cog w-5 text-center"></i> تنظیمات</a></li>
        </ul>
    </nav>
</div>
