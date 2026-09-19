<?php $en=locale()==='en'; ?>
<section class="max-w-2xl mx-auto px-6 py-16 text-center">
    <h1 class="text-3xl font-bold mb-5"><?= $en?'Your academy is registered':'آموزشگاه شما ثبت شد' ?></h1>
    <p class="text-gray-600 leading-8 mb-8"><?= $en?'Creating a branch is optional. You can continue with your academy alone or register its main branch now. You can also add branches from your user panel later.':'ایجاد شعبه اختیاری است. می‌توانید آموزشگاه خود را بدون شعبه نگه دارید یا همین حالا شعبه اصلی آن را ثبت کنید. افزودن شعبه در آینده از پنل کاربری نیز امکان‌پذیر است.' ?></p>
    <div class="flex flex-col gap-4">
        <a href="/academy/register-main-branch" class="rounded-2xl bg-indigo-600 text-white px-6 py-4"><?= $en?'Register main branch':'ثبت شعبه اصلی' ?></a>
        <form method="POST" action="/academy/registration-complete/without-branch">
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
            <button class="w-full rounded-2xl border border-indigo-300 text-indigo-700 px-6 py-4" type="submit"><?= $en?'Continue without a branch':'ادامه بدون شعبه' ?></button>
        </form>
    </div>
</section>
