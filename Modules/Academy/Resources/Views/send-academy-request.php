<?php
$oldInput = session()->getFlash('_old_input', []);
$errors = session()->getFlash('_errors', []);
$firstError = !empty($errors) ? reset($errors) : '';
?>
<div id="academyFlashMessage" class="hidden" data-error="<?= e(is_array($firstError) ? (reset($firstError) ?: '') : $firstError) ?>"></div>

<div id="page-academy-request">
    <div class="max-w-2xl mx-auto px-4 py-12 md:py-16">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-100 text-indigo-600 mb-5"><i class="fas fa-school text-2xl"></i></div>
            <h1 class="text-3xl md:text-4xl font-bold mb-3"><?= e(trans('academy.form.title', 'آموزشگاهتان را به سُرناز بیاورید')) ?></h1>
            <p class="text-gray-500 leading-7 max-w-xl mx-auto"><?= e(trans('academy.form.intro')) ?></p>
        </div>

        <form id="academyRegistrationForm" method="POST" action="/academy/send-academy-request" novalidate class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8" onsubmit="return handleAcademyRegistrationSubmit(this)">
            <input type="hidden" name="_token" value="<?= app()->container()->make(\Core\csrf\Csrf::class)->token() ?>">
            <input type="hidden" name="register_method" id="academyRegMethod" value="<?= e($oldInput['register_method'] ?? 'email') ?>">
            <input type="hidden" name="otp" id="academyRegOtp" value="">

            <div id="academyDetailsStep" class="space-y-5">
                <p class="text-sm text-gray-500 text-center"><?= e(trans('academy.form.choose_method')) ?></p>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" id="academyMethodEmail" onclick="setAcademyRegistrationMethod('email')" class="academy-method border-2 py-3 rounded-2xl font-medium text-sm"><i class="fas fa-envelope mb-1 block text-lg"></i> <?= e(trans('academy.form.with_email')) ?></button>
                    <button type="button" id="academyMethodPhone" onclick="setAcademyRegistrationMethod('phone')" class="academy-method border-2 py-3 rounded-2xl font-medium text-sm"><i class="fas fa-mobile-alt mb-1 block text-lg"></i> <?= e(trans('academy.form.with_phone')) ?></button>
                </div>

                <div id="academyEmailBox"><label class="block text-sm font-medium mb-2"><?= e(trans('academy.form.email')) ?> *</label><input name="email" type="email" autocomplete="email" value="<?= e($oldInput['email'] ?? '') ?>" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="academy@example.com"><?php if (!empty($errors['email'])): ?><p class="text-red-500 text-xs mt-1"><?= e($errors['email']) ?></p><?php endif; ?></div>
                <div id="academyPhoneBox" class="hidden"><label class="block text-sm font-medium mb-2"><?= e(trans('academy.form.phone')) ?> *</label><input name="phone" type="tel" autocomplete="tel" dir="ltr" value="<?= e($oldInput['phone'] ?? '') ?>" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 text-left focus:outline-none focus:border-indigo-500" placeholder="09123456789"><?php if (!empty($errors['phone'])): ?><p class="text-red-500 text-xs mt-1"><?= e($errors['phone']) ?></p><?php endif; ?></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium mb-2"><?= e(trans('academy.form.username')) ?> *</label><input name="username" type="text" autocomplete="username" value="<?= e($oldInput['username'] ?? '') ?>" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="academy_username"><p data-username-hint class="mt-1 text-xs text-gray-400">نام کاربری: حداقل ۳ و حداکثر ۱۰۰ کاراکتر، فقط حروف انگلیسی، عدد و _</p><?php if (!empty($errors['username'])): ?><p class="text-red-500 text-xs mt-1"><?= e($errors['username']) ?></p><?php endif; ?></div>
                    <div><label class="block text-sm font-medium mb-2"><?= e($academyNameLabel ?? trans('academy.form.academy_name')) ?> *</label><input name="academy_name" type="text" value="<?= e($oldInput['academy_name'] ?? '') ?>" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"><?php if (!empty($errors['academy_name'])): ?><p class="text-red-500 text-xs mt-1"><?= e($errors['academy_name']) ?></p><?php endif; ?></div>
                </div>
                <div><label class="block text-sm font-medium mb-2"><?= e(trans('academy.form.slogan')) ?></label><input name="slogan" type="text" value="<?= e($oldInput['slogan'] ?? '') ?>" placeholder="<?= e(trans('academy.form.slogan_placeholder')) ?>" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"></div>
                <div><label class="block text-sm font-medium mb-2"><?= e($shortDescriptionLabel ?? trans('academy.form.short_description')) ?></label><textarea name="short_description" rows="2" maxlength="500" placeholder="<?= e(trans('academy.form.short_description_placeholder')) ?>" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"><?= e($oldInput['short_description'] ?? '') ?></textarea></div>
                <div><label class="block text-sm font-medium mb-2"><?= e(trans('academy.form.biography')) ?></label><textarea name="biography" rows="4" placeholder="<?= e(trans('academy.form.biography_placeholder')) ?>" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"><?= e($oldInput['biography'] ?? '') ?></textarea></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium mb-2"><?= e(trans('academy.form.password')) ?> *</label><div class="relative"><input id="academyPassword" name="password" type="password" autocomplete="new-password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="<?= e(trans('academy.form.password_placeholder')) ?>"><button type="button" onclick="togglePassword('academyPassword', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-eye"></i></button></div></div>
                    <div><label class="block text-sm font-medium mb-2"><?= e(trans('academy.form.password_confirm')) ?> *</label><div class="relative"><input id="academyPassword2" name="password2" type="password" autocomplete="new-password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"><button type="button" onclick="togglePassword('academyPassword2', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-eye"></i></button></div></div>
                </div>
                <div data-password-strength="academyPassword">
                    <div class="flex items-center justify-between text-xs mb-1.5"><span class="text-gray-500"><?= e(trans('academy.form.password_strength')) ?></span><span data-strength-label class="font-medium text-red-600"><?= e(trans('academy.js.strength_very_weak')) ?></span></div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-gray-200" dir="ltr"><div data-strength-bar class="h-full rounded-full transition-all duration-300" style="width: 0%; background-color: hsl(0 75% 48%)"></div></div>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-1 mt-2 text-xs text-gray-400">
                        <li data-criterion="upper">○ <?= e(trans('academy.form.criterion_upper')) ?></li><li data-criterion="lower">○ <?= e(trans('academy.form.criterion_lower')) ?></li><li data-criterion="number">○ <?= e(trans('academy.form.criterion_number')) ?></li><li data-criterion="special">○ <?= e(trans('academy.form.criterion_special')) ?></li><li data-criterion="length">○ <?= e(trans('academy.form.criterion_length')) ?></li>
                    </ul>
                </div>

                <label class="flex items-start gap-2 text-sm text-gray-600 cursor-pointer"><input type="checkbox" id="academyTerms" name="terms" value="1" <?= !empty($oldInput['terms']) ? 'checked' : '' ?> class="mt-1 rounded border-gray-300 text-indigo-600"><span><button type="button" onclick="openAcademyTermsModal()" class="text-indigo-600 hover:underline font-medium"><?= e(trans('academy.terms.title')) ?></button> <?= e(trans('academy.terms.agreement')) ?></span></label>
                <?php if (!empty($errors['terms'])): ?><p class="text-red-500 text-xs"><?= e($errors['terms']) ?></p><?php endif; ?>
                <p id="academyFormError" class="hidden text-red-500 text-sm text-center"></p>
                <button type="submit" id="academySendOtpBtn" class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 text-white py-4 rounded-2xl font-medium transition"><?= e(trans('academy.form.send_otp')) ?></button>
            </div>

            <div id="academyOtpStep" class="hidden space-y-5">
                <p class="text-sm text-gray-500 text-center"><?= e(trans('academy.form.otp_prefix')) ?> <strong id="academySentTo" class="text-gray-800"></strong> <?= e(trans('academy.form.otp_suffix')) ?></p>
                <div class="flex justify-center gap-1 sm:gap-2" dir="ltr"><?php for ($i = 0; $i < 6; $i++): ?><input maxlength="1" class="academy-otp w-10 h-12 sm:w-12 sm:h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric"><?php endfor; ?></div>
                <p id="academyOtpError" class="hidden text-red-500 text-sm text-center"></p>
                <p class="text-center text-sm text-gray-400"><span id="academyTimer">02:00</span> · <button type="button" id="academyResendBtn" onclick="sendAcademyRegistrationOtp()" disabled class="text-indigo-600 disabled:text-gray-300"><?= e(trans('academy.form.resend')) ?></button></p>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium transition"><?= e(trans('academy.form.submit')) ?></button>
                <button type="button" onclick="showAcademyRegistrationDetails()" class="w-full text-sm text-gray-500 hover:text-indigo-600"><?= e(trans('academy.form.change_details')) ?></button>
            </div>
        </form>
        <p class="text-center text-sm text-gray-400 mt-8">
            <?= e(trans('academy.form.already_registered')) ?>
            <a href="/academy/academies" class="text-indigo-600 hover:underline"><?= e(trans('academy.form.back_to_list')) ?></a>
        </p>
    </div>
</div>
<?php pushScript('academy-registration.js'); ?>
