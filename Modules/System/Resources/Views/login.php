<?
$authentication_array = getFilteredList(setIndexforDataArray($authentication, 'variable_name'), 'authentication');

$oldInput = session()->getFlash('_old_input', []);
$errors   = session()->getFlash('_errors', []);
$success  = session()->getFlash('auth_success');
?>

<div id="authFlashMessage" class="hidden"
     data-success="<?= e($success ?? '') ?>"
     data-error="<?= e(!empty($errors['identifier']) ? $errors['identifier'] : '') ?>"></div>

<div id="login" class="min-h-[80vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="/assets/images/logo/cropped-favicon_512x512.jpg" alt="لوگوی سرناز" class="inline-block w-20 h-20 rounded-2xl object-cover mb-4 shadow-lg">
            <h1 class="text-3xl font-bold"><?= $authentication_array["authentication_login_page_title"]["translated_value"] ?></h1>
            <p class="text-gray-500 mt-2"><?= $authentication_array["authentication_login_welcome"]["translated_value"] ?></p>
        </div>

        <form id="loginForm" method="POST" action="/login" novalidate onsubmit="return validateLoginForm(this)" class="bg-white rounded-3xl shadow-sm p-8 border border-gray-100">
            <input type="hidden" name="_token" value="<?= app()->container()->make(\Core\csrf\Csrf::class)->token() ?>">

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام کاربری، ایمیل یا شماره موبایل</label>
                    <input name="identifier" type="text" autocomplete="username"
                            value="<?= e($oldInput['identifier'] ?? '') ?>"
                            class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
                            <!-- placeholder="username، email@example.com یا 09123456789"> -->
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2"><?= $authentication_array["authentication_password_label"]["translated_value"] ?></label>
                    <div class="relative">
                        <input id="loginPassword" name="password" type="password" autocomplete="current-password"
                                class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"
                                placeholder="<?= $authentication_array["authentication_password_placeholder"]["translated_value"] ?>">
                        <button type="button" onclick="togglePassword('loginPassword', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" <?= !empty($oldInput['remember']) ? 'checked' : '' ?> class="rounded border-gray-300 text-indigo-600">
                        <span class="text-gray-600"><?= $authentication_array["authentication_remember"]["translated_value"] ?></span>
                    </label>
                    <a href="/system/forgot-password" class="text-indigo-600 hover:underline"><?= $authentication_array["authentication_forgot_password"]["translated_value"] ?></a>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium transition"><?= $authentication_array["authentication_login"]["translated_value"] ?></button>
            </div>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-gray-400"><?= $authentication_array["authentication_or"]["translated_value"] ?></span></div>
            </div>

            <a href="/system/register" class="w-full border border-gray-300 hover:bg-gray-50 py-3.5 rounded-2xl font-medium transition text-center block"><?= $authentication_array["authentication_register"]["translated_value"] ?></a>
        </form>

        <p class="text-center text-sm text-gray-400 mt-6">
            <a href="/page/home" class="hover:text-indigo-600"><?= $authentication_array["authentication_back_to_home"]["translated_value"] ?></a>
        </p>
    </div>
</div>
