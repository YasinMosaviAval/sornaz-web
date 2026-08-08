<?
$authentication_array = getFilteredList(setIndexforDataArray($authentication, 'variable_name'), 'authentication');

$oldInput = session()->getFlash('_old_input', []);
$errors   = session()->getFlash('_errors', []);
?>

<div id="register" class="min-h-[80vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 text-white text-2xl mb-4 shadow-lg">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1 class="text-3xl font-bold"><?= $authentication_array["authentication_register_page_title"]["translated_value"] ?></h1>
            <p class="text-gray-500 mt-2"><?= $authentication_array["authentication_register_welcome"]["translated_value"] ?></p>
        </div>

        <form method="POST" action="/register" class="bg-white rounded-3xl shadow-sm p-8 border border-gray-100" onsubmit="return validateRegisterForm(this)">
            <input type="hidden" name="_token" value="<?= app()->container()->make(\Core\csrf\Csrf::class)->token() ?>">

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2"><?= $authentication_array["authentication_email_label"]["translated_value"] ?> *</label>
                    <input name="email" type="email" autocomplete="email"
                           value="<?= e($oldInput['email'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"
                           placeholder="<?= $authentication_array["authentication_email_placeholder"]["translated_value"] ?>">
                    <?php if (!empty($errors['email'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2"><?= $authentication_array["authentication_username_label"]["translated_value"] ?> *</label>
                    <input name="username" type="text" autocomplete="username"
                           value="<?= e($oldInput['username'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"
                           placeholder="<?= $authentication_array["authentication_username_placeholder"]["translated_value"] ?>">
                    <?php if (!empty($errors['username'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['username']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2"><?= $authentication_array["authentication_fullname_label"]["translated_value"] ?></label>
                    <input name="full_name" type="text"
                           value="<?= e($oldInput['full_name'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"
                           placeholder="<?= $authentication_array["authentication_fullname_placeholder"]["translated_value"] ?>">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2"><?= $authentication_array["authentication_password_label"]["translated_value"] ?> *</label>
                    <div class="relative">
                        <input id="regPassword" name="password" type="password" autocomplete="new-password"
                               class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"
                               placeholder="<?= $authentication_array["authentication_password_placeholder"]["translated_value"] ?>">
                        <button type="button" onclick="togglePassword('regPassword', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php if (!empty($errors['password'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2"><?= $authentication_array["authentication_confirm_password_label"]["translated_value"] ?> *</label>
                    <div class="relative">
                        <input id="regPassword2" name="password2" type="password" autocomplete="new-password"
                               class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"
                               placeholder="<?= $authentication_array["authentication_confirm_password_placeholder"]["translated_value"] ?>">
                        <button type="button" onclick="togglePassword('regPassword2', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <label class="flex items-start gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" id="regTerms" name="terms" class="mt-1 rounded border-gray-300 text-indigo-600">
                    <span>
                        <button type="button" onclick="openTermsModal()" class="text-indigo-600 hover:underline font-medium"><?= $authentication_array["authentication_rules_link"]["translated_value"] ?></button>
                        <?= $authentication_array["authentication_rules_text"]["translated_value"] ?>
                    </span>
                </label>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium transition"><?= $authentication_array["authentication_register"]["translated_value"] ?></button>
            </div>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-gray-400"><?= $authentication_array["authentication_or"]["translated_value"] ?></span></div>
            </div>

            <a href="/system/login" class="w-full border border-gray-300 hover:bg-gray-50 py-3.5 rounded-2xl font-medium transition text-center block"><?= $authentication_array["authentication_login"]["translated_value"] ?></a>
        </form>

        <p class="text-center text-sm text-gray-400 mt-6">
            <a href="/page/home" class="hover:text-indigo-600"><?= $authentication_array["authentication_back_to_home"]["translated_value"] ?></a>
        </p>
    </div>
</div>