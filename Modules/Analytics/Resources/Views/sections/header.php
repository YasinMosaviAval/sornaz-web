<?php
$previewRole = $previewPanelMode ?? null;
$headerPanelUser = $previewRole === null ? auth()->user() : ['user_id' => 0, 'username' => 'sample_user'];
?>
<header class="bg-white border-b shadow-sm">
    <div class="flex items-center justify-between gap-2 px-2 sm:px-3 md:px-8 py-3 md:py-4">
        <div class="flex items-center gap-2 min-w-0">
            <button id="mobileMenuBtn" onclick="toggleSidebar()" class="lg:hidden text-2xl shrink-0"><i class="fas fa-bars"></i></button>
            <h1 id="panelPageTitle" class="min-w-0 truncate text-base font-bold text-gray-800 sm:text-lg" aria-live="polite"><?= e(locale() === 'en' ? 'Dashboard' : 'داشبورد') ?></h1>
        </div>
        <div class="flex items-center gap-1.5 sm:gap-2 md:gap-5 flex-shrink min-w-0">
            <?php if ($previewRole === null) component('inline-edit-switch'); ?>
            <?php /* component('language-switcher'); */ ?>
            <?php /* component('theme-switcher'); */ ?>
            <?php if($headerPanelUser): ?>
            <?php if ($previewRole !== null) $headerPointBalance = 125; else { $headerPointStmt=db()->prepare('SELECT COALESCE(SUM(points),0) FROM user_points WHERE user_id=? AND deleted_at IS NULL AND approved_at IS NOT NULL');$headerPointStmt->execute([(int)$headerPanelUser['user_id']]);$headerPointBalance=(int)$headerPointStmt->fetchColumn(); } ?>
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
            <a href="/" class="flex shrink-0 items-center gap-1 rounded-xl border border-gray-200 px-2 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 sm:px-3 sm:text-sm" title="<?= e(locale() === 'en' ? 'Back to site' : 'برگشت به سایت') ?>">
                <i class="fas fa-arrow-left" aria-hidden="true"></i><span><?= e(locale() === 'en' ? 'Back to site' : 'برگشت به سایت') ?></span>
            </a>
        </div>
    </div>
    <?php if ($previewRole === null && (int) auth()->id() === 1): ?>
    <div class="border-t border-gray-100 px-3 py-1.5 text-right md:px-8"><a href="/analytics/public-ui-preview" class="text-xs font-medium text-indigo-700 hover:underline">نمایش عمومی بخش های غیرعمومی</a></div>
    <?php endif; ?>
</header>
