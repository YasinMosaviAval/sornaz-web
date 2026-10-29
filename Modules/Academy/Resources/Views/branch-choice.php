<?php $en=locale()==='en'; $signedIn=auth()->check(); ?>
<section class="max-w-2xl mx-auto px-6 py-16 text-center">
    <h1 class="text-3xl font-bold mb-5"><?= $en?'Your academy is registered':'آموزشگاه شما ثبت شد' ?></h1>
    <p class="text-gray-600 leading-8 mb-8"><?= $en?'Add main and subsidiary branches from Organization in your user panel.':'شعبه‌های اصلی و فرعی را از منوی سازمان در پنل کاربری ثبت کنید.' ?></p>
    <div class="flex flex-col gap-4">
        <a href="<?= $signedIn ? '/analytics/admin-panel#branches' : '/system/login' ?>" class="rounded-2xl bg-indigo-600 text-white px-6 py-4"><?= $signedIn ? ($en?'Organization':'سازمان') : ($en?'Sign in to your academy account':'ورود به حساب آموزشگاه') ?></a>
    </div>
</section>
