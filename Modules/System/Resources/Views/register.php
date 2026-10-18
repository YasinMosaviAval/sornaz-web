<?
$authentication_array = [];
foreach (translations('authentication_') as $key => $value) $authentication_array[$key] = ['translated_value' => $value];

$oldInput = session()->getFlash('_old_input', []);
$errors   = session()->getFlash('_errors', []);
$firstError = !empty($errors) ? reset($errors) : '';
?>

<div id="authFlashMessage" class="hidden" data-success="" data-error="<?= e(is_array($firstError) ? (reset($firstError) ?: '') : $firstError) ?>"></div>

<div id="register" class="min-h-[80vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="/assets/images/logo/cropped-favicon_512x512.jpg" alt="<?= e(trans('auth.logo_alt', 'لوگوی سرناز')) ?>" class="inline-block w-20 h-20 rounded-2xl object-cover mb-4 shadow-lg">
            <h1 class="text-3xl font-bold"><?= $authentication_array["authentication_register_page_title"]["translated_value"] ?></h1>
            <p class="text-gray-500 mt-2"><?= $authentication_array["authentication_register_welcome"]["translated_value"] ?></p>
        </div>

        <?php component('System::partials.registration-form', ['oldInput' => $oldInput, 'errors' => $errors]); ?>


        <p class="text-center text-sm text-gray-400 mt-6">
            <a href="/page/home" class="hover:text-indigo-600"><?= $authentication_array["authentication_back_to_home"]["translated_value"] ?></a>
        </p>
    </div>
</div>
