<?php
$isSiteAdmin = \Modules\System\Services\SiteAdminAccess::allows(auth()->user());
$stats = $testStats ?? [];
?>
<?php if ($isSiteAdmin && env('APP_ENV', 'production') === 'local'): ?>
<section id="tests" class="section hidden">
    <div class="mb-7">
        <h1 class="text-3xl font-bold">مرکز تست‌های پنل ادمین</h1>
        <p class="mt-2 text-gray-500">تمام ابزارهای ساخت و مدیریت داده‌های آزمایشی از این صفحه اجرا می‌شوند.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <article class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">تست ۱: مدیران آموزشگاه</h2>
                    <p class="mt-2 text-sm leading-7 text-gray-500">ایجاد و همگام‌سازی ۵۰ کاربر انسانی با نام فارسی، اطلاعات هویتی و توزیع کنترل‌شده وضعیت‌ها.</p>
                </div>
                <span class="rounded-2xl bg-indigo-50 px-3 py-2 text-sm font-bold text-indigo-700"><?= (int)($stats['total'] ?? 0) ?>/۵۰</span>
            </div>
            <div class="mb-5 grid grid-cols-3 gap-3 text-center text-sm">
                <div class="rounded-2xl bg-amber-50 p-3"><strong class="block text-lg text-amber-700"><?= (int)($stats['pending'] ?? 0) ?></strong><span class="text-gray-500">در انتظار</span></div>
                <div class="rounded-2xl bg-emerald-50 p-3"><strong class="block text-lg text-emerald-700"><?= (int)($stats['approved'] ?? 0) ?></strong><span class="text-gray-500">تأییدشده</span></div>
                <div class="rounded-2xl bg-gray-100 p-3"><strong class="block text-lg text-gray-700"><?= (int)($stats['other'] ?? 0) ?></strong><span class="text-gray-500">سایر</span></div>
            </div>
            <form method="POST" action="/analytics/_test/seed-academy-managers" onsubmit="return confirm('۵۰ مدیر آموزشگاه آزمایشی ایجاد یا همگام‌سازی شوند؟');">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <button type="submit" class="w-full rounded-2xl bg-indigo-600 px-5 py-3.5 font-medium text-white transition hover:bg-indigo-700"><i class="fas fa-users-cog ml-2"></i>اجرای تست مدیران آموزشگاه</button>
            </form>
            <p class="mt-3 text-xs text-gray-400">اجرای مجدد idempotent است و کاربران دارای پیشوند تست را همگام می‌کند.</p>
        </article>

        <article class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-xl font-bold">داده‌های نمونه آموزشگاه و شعب</h2>
                <p class="mt-2 text-sm leading-7 text-gray-500">ابزار موجود برای ایجاد ۵۰ آموزشگاه، شعب و اطلاعات وابسته یا پاک‌سازی کامل آن‌ها.</p>
            </div>
            <div class="space-y-3">
                <form method="POST" action="/academy/_test/seed-sample-academies">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                    <button type="submit" class="w-full rounded-2xl bg-emerald-600 px-5 py-3.5 font-medium text-white hover:bg-emerald-700"><i class="fas fa-database ml-2"></i>ایجاد آموزشگاه‌های نمونه</button>
                </form>
                <form method="POST" action="/academy/_test/delete-sample-academies" onsubmit="return confirm('تمام اطلاعات آموزشگاه‌های نمونه حذف شوند؟');">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                    <button type="submit" class="w-full rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 font-medium text-red-700 hover:bg-red-100"><i class="fas fa-trash-alt ml-2"></i>حذف اطلاعات نمونه آموزشگاه‌ها</button>
                </form>
            </div>
        </article>
    </div>
</section>
<?php endif; ?>
