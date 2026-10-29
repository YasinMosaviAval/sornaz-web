<main id="musicPractice" class="mx-auto max-w-7xl space-y-5 px-3 py-6">
    <?php if(($_GET['payment']??'')==='complete'): ?><p role="status" class="rounded-xl border border-green-300 bg-green-50 p-3 text-green-800"><?= locale()==='en'?'Payment verified. Refresh your tests to see the updated access.':'پرداخت تأیید شد. با به‌روزرسانی آزمون‌ها، دسترسی تازه نمایش داده می‌شود.' ?></p><?php endif; ?>
    <?php if(($_GET['payment']??'')==='failed'): ?><p role="status" class="rounded-xl border border-amber-300 bg-amber-50 p-3 text-amber-800"><?= locale()==='en'?'Payment verification was not completed. Check your access status again.':'تأیید پرداخت کامل نشد. وضعیت دسترسی را دوباره بررسی کنید.' ?></p><?php endif; ?>
    <div class="flex flex-wrap items-center justify-between gap-3"><h1 class="text-2xl font-bold"><?= locale()==='en'?'Tests':'آزمون‌ها' ?></h1><div class="flex gap-2"><button type="button" data-practice-upload class="rounded-xl border p-3" title="<?= locale()==='en'?'Upload saved results':'بارگذاری نتایج ذخیره‌شده' ?>"><i class="fas fa-upload"></i></button><button type="button" data-practice-refresh class="rounded-xl border p-3" title="<?= locale()==='en'?'Refresh':'به‌روزرسانی' ?>"><i class="fas fa-sync-alt"></i></button><button type="button" data-practice-manage hidden class="rounded-xl border px-4"><?= locale()==='en'?'Manage tests':'مدیریت آزمون‌ها' ?></button></div></div>
    <p data-practice-status role="status" class="text-sm text-gray-500"></p>
    <nav data-practice-breadcrumb class="flex flex-wrap gap-2" aria-label="<?= locale()==='en'?'Test categories':'بخش‌های آزمون' ?>"></nav>
    <div data-practice-list class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3"></div>
    <section data-practice-player hidden class="space-y-4 rounded-2xl border bg-white p-5"></section>
    <section data-practice-editor hidden class="space-y-4 rounded-2xl border bg-white p-5"></section>
</main>
<script>window.musicPracticeBoot=<?= json_encode($practiceBoot,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script defer src="/assets/Education/js/music-practice.js?v=<?= e(\Core\support\AssetManager::version('/assets/Education/js/music-practice.js')) ?>"></script>
