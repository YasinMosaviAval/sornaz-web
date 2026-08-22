<?php
$headerPanelUser = auth()->user();
$headerHasAcademyAccess = \Modules\System\Services\SiteAdminAccess::allows($headerPanelUser)
    || in_array(($headerPanelUser['type'] ?? ''), ['academy','branch'], true)
    || ($headerPanelUser && (bool)\Core\database\DB::table('academy_branch_members')
        ->join('academy_branch_member_roles', 'academy_branch_member_roles.member_id', '=', 'academy_branch_members.member_id')
        ->where('academy_branch_members.user_id', (int)$headerPanelUser['user_id'])->whereIn('academy_branch_member_roles.role_id', [7,16])
        ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->first());
?>
<header class="bg-white border-b shadow-sm">
    <div class="flex items-center justify-between gap-2 px-2 sm:px-3 md:px-8 py-3 md:py-4">
        <div class="flex items-center gap-3 min-w-0">
            <button id="mobileMenuBtn" onclick="toggleSidebar()" class="lg:hidden text-2xl shrink-0"><i class="fas fa-bars"></i></button>
        </div>
        <div class="flex items-center gap-1.5 sm:gap-2 md:gap-5 flex-shrink min-w-0">
            <? component('inline-edit-switch'); ?>
            <? component('language-switcher'); ?>
            <? component('theme-switcher'); ?>
            <?php if($headerHasAcademyAccess): ?>
            <button onclick="showSection('points')" class="relative p-1.5 hidden md:block">
                <i class="fas fa-coins text-xl md:text-2xl text-gray-600"></i>
            </button>
            <button onclick="showSection('messages')" class="relative p-1.5 hidden sm:block">
                <i class="fas fa-envelope text-xl md:text-2xl text-gray-600"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center">۳</span>
            </button>
            <button onclick="showSection('notifications')" class="relative p-1.5 hidden sm:block">
                <i class="fas fa-bell text-xl md:text-2xl text-gray-600"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center">۳</span>
            </button>
            <?php endif; ?>
            <div class="hidden md:flex items-center gap-2 min-w-0">
                <div class="text-right hidden sm:block">
                    <p class="font-medium text-sm truncate" dir="ltr">@<?= e(auth()->user()['username'] ?? '') ?></p>
                    <p class="text-xs text-gray-500 truncate">نام کاربری</p>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-user text-indigo-700"></i>
                </div>
            </div>
        </div>
    </div>
</header>
