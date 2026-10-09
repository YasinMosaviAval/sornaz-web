<?php // Static personal dashboard fixtures for the public design preview. ?>
<section id="dashboard" class="section hidden space-y-6" data-dashboard-kind="student">
    <?php component('my-learning-tabs', ['active' => 'dashboard']); ?>
    <div><h2 class="mb-4 text-xl font-bold">درخواست‌های ثبت‌نام من</h2><div class="grid gap-4 lg:grid-cols-2"><article class="rounded-2xl border bg-white p-5 shadow-sm"><div class="flex flex-wrap items-start justify-between gap-3"><div><h3 class="font-bold">ترم نمونهٔ پیانو</h3><p class="mt-1 text-sm text-gray-500">دورهٔ مقدماتی · درخواست #۱</p></div><span class="rounded-full bg-indigo-50 px-3 py-1 text-xs text-indigo-700">در انتظار بررسی آموزشگاه</span></div></article></div></div>
    <div class="grid gap-5 md:grid-cols-3"><article class="rounded-2xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">ترم‌های من</p><strong class="mt-2 block text-2xl text-indigo-700">۲</strong></article><article class="rounded-2xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">کلاس‌های من</p><strong class="mt-2 block text-2xl text-indigo-700">۳</strong></article><article class="rounded-2xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">امتیاز من</p><strong class="mt-2 block text-2xl text-indigo-700">۱۲۵</strong></article></div>
</section>
<?php foreach (['my-terms' => ['ترم‌های من', 'ترم نمونهٔ پیانو', '۱۴۰۵/۰۷/۱۷', 'فعال'], 'my-classrooms' => ['کلاس‌های من', 'کلاس پیانو', 'شنبه ۱۰:۰۰', 'فعال'], 'my-courses' => ['دوره‌های من', 'دورهٔ مقدماتی پیانو', 'مقدماتی', 'در حال برگزاری']] as $previewKey => $previewValues): ?>
<section id="<?= e($previewKey) ?>" class="section hidden space-y-6">
    <?php component('my-learning-tabs', ['active' => $previewKey]); ?>
    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm"><table class="w-full min-w-[600px] text-sm"><thead class="border-b bg-gray-50"><tr><th class="p-4 text-right"><?= e($previewValues[0]) ?></th><th class="p-4 text-right">زمان یا سطح</th><th class="p-4 text-right">وضعیت</th><th class="p-4 text-right">عملیات</th></tr></thead><tbody><tr><td class="p-4"><?= e($previewValues[1]) ?></td><td class="p-4"><?= e($previewValues[2]) ?></td><td class="p-4"><?= e($previewValues[3]) ?></td><td class="p-4 text-indigo-600">مشاهده</td></tr></tbody></table></div>
</section>
<?php endforeach; ?>
