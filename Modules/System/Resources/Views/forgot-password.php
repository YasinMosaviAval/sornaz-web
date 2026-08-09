<?
$authentication_array = getFilteredList(setIndexforDataArray($authentication, 'variable_name'), 'authentication');
?>

<div id="forgot-password" class="min-h-[80vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="/assets/images/logo/cropped-favicon_512x512.jpg" alt="لوگوی سرناز" class="inline-block w-20 h-20 rounded-2xl object-cover mb-4 shadow-lg">
            <h1 class="text-3xl font-bold"><?= $authentication_array["authentication_forgot_password_page_title"]["translated_value"] ?></h1>
            <p class="text-gray-500 mt-2"><?= $authentication_array["authentication_forgot_password_welcome"]["translated_value"] ?></p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 border border-gray-100">
            <input type="hidden" id="fpCsrf" value="<?= e(csrf_token()) ?>">
            <p id="fpError" class="hidden text-red-500 text-sm text-center mb-4"></p>

            <!-- مرحله ۱: انتخاب روش + ورود شناسه -->
            <div id="fpStep1" class="space-y-5">
                <p class="text-sm text-gray-500 text-center mb-2"><?= $authentication_array["authentication_forgot_password_receive_type"]["translated_value"] ?></p>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" id="fpMethodEmail" onclick="setFpMethod('email')" class="fp-method border-2 border-indigo-600 bg-indigo-50 text-indigo-700 py-3 rounded-2xl font-medium text-sm">
                        <i class="fas fa-envelope mb-1 block text-lg"></i>
                        <?= $authentication_array["authentication_forgot_password_change_send_type"]["translated_value"] ?>
                    </button>
                    <button type="button" id="fpMethodPhone" onclick="setFpMethod('phone')" class="fp-method border-2 border-gray-200 hover:border-gray-300 py-3 rounded-2xl font-medium text-sm text-gray-600">
                        <i class="fas fa-mobile-alt mb-1 block text-lg"></i>
                        <?= $authentication_array["authentication_forgot_password_phone_receive"]["translated_value"] ?>
                    </button>
                </div>

                <div id="fpEmailBox">
                    <label class="block text-sm font-medium mb-2"><?= $authentication_array["authentication_forgot_password_email_title"]["translated_value"] ?></label>
                    <input id="fpEmail" type="email" placeholder="<?= $authentication_array["authentication_forgot_password_email_hint"]["translated_value"] ?>" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
                </div>
                <div id="fpPhoneBox" class="hidden">
                    <label class="block text-sm font-medium mb-2"><?= $authentication_array["authentication_forgot_password_phone_title"]["translated_value"] ?></label>
                    <input id="fpPhone" type="tel" placeholder="<?= $authentication_array["authentication_forgot_password_phone_hint"]["translated_value"] ?>" dir="ltr" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500 text-left">
                </div>

                <button onclick="sendFpOtp()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium"><?= $authentication_array["authentication_forgot_password_send_otp"]["translated_value"] ?></button>
            </div>
            <!-- مرحله ۲: وارد کردن OTP -->
            <div id="fpStep2" class="hidden space-y-5">
                <p class="text-sm text-gray-500 text-center">
                    <?= $authentication_array["authentication_forgot_password_sent_otp_first_text"]["translated_value"] ?>
                    <strong id="fpSentTo" class="text-gray-800"></strong>
                    <?= $authentication_array["authentication_forgot_password_sent_otp_last_text"]["translated_value"] ?>
                </p>
                <div class="flex justify-center gap-2" id="fpOtpInputs" dir="ltr">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                </div>
                <p class="text-center text-sm text-gray-400">
                    <span id="fpTimer">۰۲:۰۰</span>
                    ·
                    <button type="button" id="fpResendBtn" onclick="sendFpOtp()" disabled class="text-indigo-600 disabled:text-gray-300 disabled:cursor-not-allowed"><?= $authentication_array["authentication_forgot_password_resend_otp"]["translated_value"] ?></button>
                </p>
                <button onclick="verifyFpOtp()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium"><?= $authentication_array["authentication_forgot_password_confirm_otp"]["translated_value"] ?></button>
                <button onclick="fpGoStep(1)" class="w-full text-sm text-gray-500 hover:text-indigo-600"><?= $authentication_array["authentication_forgot_password_change_send_type"]["translated_value"] ?></button>
            </div>
            <!-- مرحله ۳: رمز جدید -->
            <div id="fpStep3" class="hidden space-y-5">
                <p class="text-sm text-gray-500 text-center"><?= $authentication_array["authentication_forgot_password_write_password_text"]["translated_value"] ?></p>
                <div>
                    <label class="block text-sm font-medium mb-2"><?= $authentication_array["authentication_forgot_password_new_password_title"]["translated_value"] ?></label>
                    <div class="relative">
                        <input id="fpNewPass" type="password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 pr-12 focus:outline-none focus:border-indigo-500" placeholder="<?= $authentication_array["authentication_forgot_password_new_password_hint"]["translated_value"] ?>">
                        <button type="button" onclick="togglePassword('fpNewPass', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2"><?= $authentication_array["authentication_forgot_password_confirm_new_password_title"]["translated_value"] ?></label>
                    <input id="fpNewPass2" type="password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="<?= $authentication_array["authentication_forgot_password_confirm_new_password_hint"]["translated_value"] ?>">
                </div>
                <button onclick="resetPassword()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium"><?= $authentication_array["authentication_forgot_password_save_new_password"]["translated_value"] ?></button>
            </div>
        </div>

        <p class="text-center text-sm text-gray-400 mt-6">
            <a href="/system/login" class="hover:text-indigo-600"><?= $authentication_array["authentication_back_to_login"]["translated_value"] ?></a>
        </p>
    </div>
</div>
