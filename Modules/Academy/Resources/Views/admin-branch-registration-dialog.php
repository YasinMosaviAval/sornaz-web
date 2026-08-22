<?php
ob_start();
include __DIR__ . '/register-main-branch.php';
$form = ob_get_clean();
$academyId = (int)($academyId ?? 0);
$form = str_replace('/academy/register-main-branch/send-otp', '/academy/admin/branches/registration/send-otp', $form);
$form = str_replace('/academy/register-main-branch"', '/academy/admin/branches/registration" data-otp-endpoint="/academy/admin/branches/registration/send-otp"', $form);
$form = str_replace('شعبه اصلی آموزشگاهتان را ثبت کنید', 'شعبه جدید آموزشگاهتان را ثبت کنید', $form);
$form = str_replace('ثبت شعبه اصلی آموزشگاه', 'ثبت شعبه جدید آموزشگاه', $form);
$form = str_replace('<input type="hidden" name="otp" id="academyRegOtp" value="">', '<input type="hidden" name="otp" id="academyRegOtp" value=""><input type="hidden" name="academy_id" value="' . $academyId . '">', $form);
$form = preg_replace('/<p class="text-center text-sm text-gray-400 mt-8">.*?<\/p>/s', '', $form) ?? $form;
?>
<div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 p-4" onclick="if(event.target===this) closeModal()">
    <div class="relative mx-auto my-4 w-full max-w-3xl rounded-3xl bg-white shadow-2xl" onclick="event.stopPropagation()">
        <button type="button" onclick="closeModal()" aria-label="بستن" class="absolute left-5 top-4 z-20 text-3xl text-gray-400 hover:text-gray-700">×</button>
        <?= $form ?>
    </div>
</div>
