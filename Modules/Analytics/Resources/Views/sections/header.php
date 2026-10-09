<?php
$headerPanelUser = auth()->user();
?>
<header class="bg-white border-b shadow-sm">
    <div class="flex items-center justify-between gap-2 px-2 sm:px-3 md:px-8 py-3 md:py-4">
        <div class="flex items-center gap-2 min-w-0">
            <button id="mobileMenuBtn" onclick="toggleSidebar()" class="lg:hidden text-2xl shrink-0"><i class="fas fa-bars"></i></button>
            <h1 id="panelPageTitle" class="min-w-0 truncate text-base font-bold text-gray-800 sm:text-lg" aria-live="polite"><?= e(locale() === 'en' ? 'Dashboard' : 'داشبورد') ?></h1>
        </div>
        <div class="flex items-center gap-1.5 sm:gap-2 md:gap-5 flex-shrink min-w-0">
            <? component('inline-edit-switch'); ?>
            <?php /* component('language-switcher'); */ ?>
            <?php /* component('theme-switcher'); */ ?>
            <?php if($headerPanelUser): ?>
            <?php $headerPointStmt=db()->prepare('SELECT COALESCE(SUM(points),0) FROM user_points WHERE user_id=? AND deleted_at IS NULL AND approved_at IS NOT NULL');$headerPointStmt->execute([(int)$headerPanelUser['user_id']]);$headerPointBalance=(int)$headerPointStmt->fetchColumn(); ?>
            <button onclick="showSection('points')" title="امتیازهای من" aria-label="امتیازهای من" class="relative p-1.5 flex items-center">
                <i class="fas fa-coins text-xl md:text-2xl text-gray-600"></i>
                <span id="headerPointBalance" class="absolute -top-1 -right-1 rounded-full bg-amber-500 px-1 min-w-4 h-4 md:min-w-5 md:h-5 flex items-center justify-center text-[10px] font-bold text-white"><?= number_format($headerPointBalance) ?></span>
            </button>
            <?php endif; ?>
            <button type="button" onclick="showSection('messages')" title="پیام‌ها" aria-label="پیام‌ها" class="relative p-1.5">
                <i class="fas fa-envelope text-xl md:text-2xl text-gray-600"></i>
                <span id="headerUnreadMessages" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full min-w-4 h-4 md:min-w-5 md:h-5 px-1 flex items-center justify-center">0</span>
            </button>
            <button type="button" onclick="showSection('notifications')" title="اعلان‌ها" aria-label="اعلان‌ها" class="relative p-1.5">
                <i class="fas fa-bell text-xl md:text-2xl text-gray-600"></i>
                <span id="headerUnreadNotifications" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full min-w-4 h-4 md:min-w-5 md:h-5 px-1 flex items-center justify-center">0</span>
            </button>
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
