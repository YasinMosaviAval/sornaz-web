<?php
$isSiteAdmin = \Modules\System\Services\SiteAdminAccess::allows(auth()->user());
$stats = $testStats ?? [];
$localizedNumber = static function (int|float|string $number): string {
    $formatted = number_format((float)$number, 0, '.', ',');
    return locale() === 'fa' ? strtr($formatted, ['0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹']) : $formatted;
};
?>
<?php if ($isSiteAdmin && env('APP_ENV', 'production') === 'local'): ?>
<section id="tests" class="section hidden">
    <div class="mb-7">
        <h1 class="text-3xl font-bold">مرکز تست‌های پنل ادمین</h1>
        <p class="mt-2 text-gray-500">تمام ابزارهای ساخت و مدیریت داده‌های آزمایشی از این صفحه اجرا می‌شوند.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <article class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5"><h2 class="text-xl font-bold">تست ۵: ترم‌ها، جلسات و حضور و غیاب</h2><p class="mt-2 text-sm leading-7 text-gray-500">برای هر دوره بین ۱ تا ۳ ترم کم‌حجم به همراه جلسات، استادها، هنرجویان و حضور و غیاب جلسات برگزارشده ایجاد می‌شود.</p></div>
            <div class="space-y-3"><form method="POST" action="/analytics/_test/seed-branch-terms" onsubmit="return AppDialog.confirmSubmit(event, 'ترم‌ها و حضور و غیاب آزمایشی ایجاد شوند؟');"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"><button class="w-full rounded-2xl bg-emerald-600 px-5 py-3.5 font-medium text-white hover:bg-emerald-700"><i class="fas fa-calendar-alt ml-2"></i>اجرای تست ترم‌ها و جلسات</button></form><form method="POST" action="/analytics/_test/delete-branch-terms" onsubmit="return AppDialog.confirmSubmit(event, 'تمام اطلاعات ایجادشده توسط تست ۵ کاملاً حذف شوند؟');"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"><button class="w-full rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 font-medium text-red-700 hover:bg-red-100"><i class="fas fa-undo-alt ml-2"></i>برگشت و حذف کامل تست ۵</button></form></div>
        </article>
        <article class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5"><h2 class="text-xl font-bold">تست ۴: دوره‌های شعب</h2><p class="mt-2 text-sm leading-7 text-gray-500">برای هر شعبه مجموعه‌ای کم‌حجم از دوره‌های واقعی بر اساس درس‌های ارائه‌شده همان شعبه و سطح‌های آموزشی ایجاد می‌شود.</p></div>
            <div class="space-y-3"><form method="POST" action="/analytics/_test/seed-branch-courses" onsubmit="return AppDialog.confirmSubmit(event, 'دوره‌های آزمایشی شعب ایجاد یا همگام‌سازی شوند؟');"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"><button class="w-full rounded-2xl bg-sky-600 px-5 py-3.5 font-medium text-white hover:bg-sky-700"><i class="fas fa-book-open ml-2"></i>اجرای تست دوره‌های شعب</button></form><form method="POST" action="/analytics/_test/delete-branch-courses" onsubmit="return AppDialog.confirmSubmit(event, 'تمام دوره‌های ایجادشده توسط تست ۴ حذف شوند؟');"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"><button class="w-full rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 font-medium text-red-700 hover:bg-red-100"><i class="fas fa-undo-alt ml-2"></i>برگشت و حذف اطلاعات تست ۴</button></form></div>
        </article>

        <article class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">تست ۱: مدیران آموزشگاه</h2>
                    <p class="mt-2 text-sm leading-7 text-gray-500">ایجاد و همگام‌سازی <?= $localizedNumber(10) ?> کاربر انسانی با اطلاعات هویتی، آدرس، راه ارتباطی و ساز و درس موسیقی برای هر کاربر.</p>
                    <ul class="mt-4 space-y-2 text-sm leading-6 text-gray-600">
                        <li class="flex items-center justify-between gap-4"><span class="flex items-start gap-2"><i class="fas fa-check-circle mt-1 text-emerald-600"></i>آدرس‌ها و راه‌های ارتباطی</span><strong class="shrink-0 text-gray-700"><?= $localizedNumber(($stats['addresses'] ?? 0) + ($stats['contacts'] ?? 0)) ?></strong></li>
                        <li class="flex items-center justify-between gap-4"><span class="flex items-start gap-2"><i class="fas fa-check-circle mt-1 text-emerald-600"></i>پروفایل، کاور، گالری و ویدیو</span><strong class="shrink-0 text-gray-700"><?= $localizedNumber($stats['media'] ?? 0) ?></strong></li>
                        <li class="flex items-center justify-between gap-4"><span class="flex items-start gap-2"><i class="fas fa-check-circle mt-1 text-emerald-600"></i>سازها، درس‌ها و سطح‌های آموزشی</span><strong class="shrink-0 text-gray-700"><?= $localizedNumber(($stats['catalog_instruments'] ?? 0) + ($stats['catalog_lessons'] ?? 0) + ($stats['levels'] ?? 0)) ?></strong></li>
                        <li class="flex items-center justify-between gap-4"><span class="flex items-start gap-2"><i class="fas fa-check-circle mt-1 text-emerald-600"></i>سازها و درس‌های انتخاب‌شده کاربران</span><strong class="shrink-0 text-gray-700"><?= $localizedNumber(($stats['instruments'] ?? 0) + ($stats['lessons'] ?? 0)) ?></strong></li>
                        <li class="flex items-center justify-between gap-4"><span class="flex items-start gap-2"><i class="fas fa-check-circle mt-1 text-emerald-600"></i>برنامه‌های حضور هفتگی چندبازه‌ای</span><strong class="shrink-0 text-gray-700"><?= $localizedNumber($stats['availabilities'] ?? 0) ?></strong></li>
                        <li class="flex items-center justify-between gap-4"><span class="flex items-start gap-2"><i class="fas fa-check-circle mt-1 text-emerald-600"></i>تعطیلات، مرخصی‌ها و استثناها</span><strong class="shrink-0 text-gray-700"><?= $localizedNumber($stats['exceptions'] ?? 0) ?></strong></li>
                    </ul>
                </div>
                <div class="text-left"><span class="block rounded-2xl bg-indigo-50 px-3 py-2 text-sm font-bold text-indigo-700"><?= $localizedNumber($stats['total'] ?? 0) ?>/<?= $localizedNumber(10) ?> کاربر</span></div>
            </div>
            <div class="mb-5 grid grid-cols-3 gap-3 text-center text-sm">
                <div class="rounded-2xl bg-amber-50 p-3"><strong class="block text-lg text-amber-700"><?= $localizedNumber($stats['pending'] ?? 0) ?></strong><span class="text-gray-500">در انتظار</span></div>
                <div class="rounded-2xl bg-emerald-50 p-3"><strong class="block text-lg text-emerald-700"><?= $localizedNumber($stats['approved'] ?? 0) ?></strong><span class="text-gray-500">تأییدشده</span></div>
                <div class="rounded-2xl bg-gray-100 p-3"><strong class="block text-lg text-gray-700"><?= $localizedNumber($stats['other'] ?? 0) ?></strong><span class="text-gray-500">سایر</span></div>
            </div>
            <form method="POST" action="/analytics/_test/seed-academy-managers" onsubmit="return AppDialog.confirmSubmit(event, '۱۰ مدیر آموزشگاه آزمایشی ایجاد یا همگام‌سازی شوند؟');">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <button type="submit" class="w-full rounded-2xl bg-indigo-600 px-5 py-3.5 font-medium text-white transition hover:bg-indigo-700"><i class="fas fa-users-cog ml-2"></i>اجرای تست مدیران آموزشگاه</button>
            </form>
            <form method="POST" action="/analytics/_test/delete-academy-managers" class="mt-3" onsubmit="return AppDialog.confirmSubmit(event, 'تمام مدیران آموزشگاه آزمایشی و ترجمه نام آن‌ها حذف شوند؟');">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <button type="submit" class="w-full rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 font-medium text-red-700 transition hover:bg-red-100"><i class="fas fa-user-minus ml-2"></i>حذف مدیران آموزشگاه آزمایشی</button>
            </form>
            <p class="mt-3 text-xs text-gray-400">اجرای مجدد idempotent است و کاربران دارای پیشوند تست را همگام می‌کند.</p>
        </article>
        <article class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5"><h2 class="text-xl font-bold">تست ۳: شعب فرعی، اعضا و قراردادها</h2><p class="mt-2 text-sm leading-7 text-gray-500">برای ۱۰ آموزشگاه تست ۲ حداکثر یک شعبه غیر اصلی و تعداد محدودی مدرس، منشی، کارمند، مدیر و هنرجو ایجاد می‌شود.</p>
                <ul class="mt-4 space-y-2 text-sm leading-6 text-gray-600">
                    <li class="flex items-center justify-between gap-4"><span><i class="fas fa-check-circle ml-2 text-emerald-600"></i>شعبه‌های فرعی غیر اصلی</span><strong><?= $localizedNumber($stats['extra_branches'] ?? 0) ?></strong></li>
                    <li class="flex items-center justify-between gap-4"><span><i class="fas fa-chalkboard-teacher ml-2 text-indigo-600"></i>مدرس‌ها</span><strong><?= $localizedNumber($stats['network_teachers'] ?? 0) ?></strong></li>
                    <li class="flex items-center justify-between gap-4"><span><i class="fas fa-headset ml-2 text-sky-600"></i>منشی‌ها</span><strong><?= $localizedNumber($stats['network_receptionists'] ?? 0) ?></strong></li>
                    <li class="flex items-center justify-between gap-4"><span><i class="fas fa-user-tie ml-2 text-amber-600"></i>کارمندان و مدیران جدید</span><strong><?= $localizedNumber(($stats['network_employees'] ?? 0)+($stats['network_managers'] ?? 0)) ?></strong></li>
                    <li class="flex items-center justify-between gap-4"><span><i class="fas fa-user-graduate ml-2 text-violet-600"></i>هنرجویان مدرس‌ها</span><strong><?= $localizedNumber($stats['network_students'] ?? 0) ?></strong></li>
                    <li class="flex items-center justify-between gap-4"><span><i class="fas fa-file-signature ml-2 text-emerald-600"></i>عضویت‌ها و قراردادها</span><strong><?= $localizedNumber(($stats['network_memberships'] ?? 0)+($stats['network_contracts'] ?? 0)) ?></strong></li>
                </ul>
            </div>
            <div class="space-y-3"><form method="POST" action="/academy/_test/seed-branch-network" onsubmit="return AppDialog.confirmSubmit(event, 'شعب فرعی، اعضا و قراردادهای آزمایشی ایجاد یا همگام‌سازی شوند؟');"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"><button type="submit" class="w-full rounded-2xl bg-violet-600 px-5 py-3.5 font-medium text-white hover:bg-violet-700"><i class="fas fa-network-wired ml-2"></i>اجرای تست شبکه شعب و اعضا</button></form>
                <form method="POST" action="/academy/_test/delete-branch-network" onsubmit="return AppDialog.confirmSubmit(event, 'تمام شعب فرعی، اعضا، هنرجویان و قراردادهای ایجادشده توسط تست ۳ کاملاً حذف شوند؟');"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"><button type="submit" class="w-full rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 font-medium text-red-700 hover:bg-red-100"><i class="fas fa-undo-alt ml-2"></i>برگشت و حذف اطلاعات تست ۳</button></form></div>
            <p class="mt-3 text-xs text-gray-400">عملیات برگشت فقط داده‌های تست ۳ را حذف می‌کند و آموزشگاه‌ها، شعب اصلی و مدیران تست‌های قبلی باقی می‌مانند.</p>
        </article>

        <article class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-xl font-bold">تست ۲: آموزشگاه‌ها و شعب اصلی</h2>
                <p class="mt-2 text-sm leading-7 text-gray-500">برای هر مدیر تست ۱، یک حساب آموزشگاه و یک حساب شعبه اصلی با اطلاعات پایه، ترجمه فارسی و انگلیسی و رابطه‌های مدیریتی ایجاد می‌شود.</p>
                <ul class="mt-4 space-y-2 text-sm leading-6 text-gray-600">
                    <li class="flex items-center justify-between gap-4"><span class="flex items-start gap-2"><i class="fas fa-check-circle mt-1 text-emerald-600"></i>آموزشگاه‌های دارای حساب اختصاصی</span><strong class="shrink-0 text-gray-700"><?= $localizedNumber($stats['academies'] ?? 0) ?></strong></li>
                    <li class="flex items-center justify-between gap-4"><span class="flex items-start gap-2"><i class="fas fa-check-circle mt-1 text-emerald-600"></i>شعبه‌های اصلی آموزشگاه‌ها</span><strong class="shrink-0 text-gray-700"><?= $localizedNumber($stats['branches'] ?? 0) ?></strong></li>
                    <li class="flex items-center justify-between gap-4"><span class="flex items-start gap-2"><i class="fas fa-check-circle mt-1 text-emerald-600"></i>عضویت مدیر در آموزشگاه و شعبه اصلی</span><strong class="shrink-0 text-gray-700"><?= $localizedNumber($stats['academy_memberships'] ?? 0) ?></strong></li>
                </ul>
            </div>
            <div class="space-y-3">
                <form method="POST" action="/academy/_test/seed-sample-academies">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                    <button type="submit" class="w-full rounded-2xl bg-emerald-600 px-5 py-3.5 font-medium text-white hover:bg-emerald-700"><i class="fas fa-database ml-2"></i>اجرای تست آموزشگاه‌ها و شعب اصلی</button>
                </form>
                <form method="POST" action="/academy/_test/delete-sample-academies" onsubmit="return AppDialog.confirmSubmit(event, 'تمام اطلاعات آموزشگاه‌های نمونه حذف شوند؟');">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                    <button type="submit" class="w-full rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 font-medium text-red-700 hover:bg-red-100"><i class="fas fa-trash-alt ml-2"></i>حذف اطلاعات نمونه آموزشگاه‌ها</button>
                </form>
            </div>
        </article>
    </div>
</section>
<?php endif; ?>
