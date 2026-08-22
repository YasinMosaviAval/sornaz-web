<?php
$academyNameLabel = 'نام';
$shortDescriptionLabel = 'معرفی';
ob_start();
include __DIR__ . '/send-academy-request.php';
$branchPage = ob_get_clean();
$branchPage = str_replace('/academy/send-academy-request/send-otp', '/academy/register-main-branch/send-otp', $branchPage);
$branchPage = str_replace('/academy/send-academy-request"', '/academy/register-main-branch"', $branchPage);
$branchPage = str_replace('آموزشگاهتان را به سُرناز بیاورید', 'شعبه اصلی آموزشگاهتان را ثبت کنید', $branchPage);
$branchPage = str_replace('قوانین ثبت و فعالیت آموزشگاه', 'قوانین ثبت و فعالیت شعبه', $branchPage);
$branchPage = str_replace('قوانین ثبت آموزشگاه', 'قوانین ثبت و فعالیت شعبه', $branchPage);
$branchPage = str_replace('ثبت آموزشگاه', 'ثبت شعبه اصلی آموزشگاه', $branchPage);
echo $branchPage;
