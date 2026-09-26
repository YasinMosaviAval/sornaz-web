<?php
$chatLabels = [
    'گفتگوها'=>'Conversations', 'پیام خصوصی و گروهی'=>'Private and group messages',
    'جستجوی گفتگو...'=>'Search conversations...', 'یک گفتگو را انتخاب کنید'=>'Select a conversation',
    'در حال ویرایش پیام'=>'Editing message', 'پیام بنویسید...'=>'Write a message...',
    'گفتگویی وجود ندارد.'=>'No conversations yet.', 'گفتگوی خصوصی'=>'Private conversation',
    'گفتگوی جدید'=>'New conversation', 'نام گروه (برای گفتگوهای چندنفره)'=>'Group name (for group conversations)',
    'جستجوی کاربر...'=>'Search users...', 'ایجاد گفتگو'=>'Create conversation',
    'امکانات پیام'=>'Message actions', 'ارسال در چت دیگر'=>'Forward to another chat',
    'پاک کردن پیام'=>'Delete message', 'ارسال در گفتگوهای دیگر'=>'Forward to conversations',
    'اعضای گروه'=>'Group members', 'اطلاعات گفتگو'=>'Conversation details',
    'تصویر گروه'=>'Group image', 'انتخاب تصویر'=>'Select image', 'ویرایش نام'=>'Rename',
    'افزودن عضو جدید'=>'Add members', 'افزودن افراد انتخاب‌شده'=>'Add selected members',
    'حذف گفتگو'=>'Delete conversation', 'حذف گروه'=>'Delete group', 'ترک گروه'=>'Leave group',
    'پیام صوتی'=>'Voice message', 'ویرایش‌شده'=>'Edited', 'ضبط صدا'=>'Record voice',
    'حذف صدای ضبط‌شده'=>'Discard recording', 'خصوصی'=>'Private', 'گروهی'=>'Groups',
    'امروز'=>'Today', 'دیروز'=>'Yesterday', 'لغو'=>'Cancel', 'شما'=>'You', 'عضو'=>'Member',
    'حذف'=>'Remove', 'ارسال'=>'Send', 'گفتگو'=>'Conversation',
];
$adminUiMap = array_replace($chatLabels, $adminUiMap ?? []);
?>
<!DOCTYPE html>
<html lang="<?= e(locale()) ?>" dir="<?= e(direction()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= locale() === 'en' ? 'Conversations' : 'گفتگوها' ?></title>
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <script>tailwind.config={darkMode:['class','[data-mode="dark"]']};</script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <link rel="stylesheet" href="/assets/theme/theme.css?v=<?= filemtime(base_path('assets/theme/theme.css')) ?>">
    <style>html,body{margin:0;height:100%;overflow:hidden}#chat{display:block;height:100dvh}#chat>div{border-radius:0;box-shadow:none}#chatBody{min-width:0}#chatRoomTitle{text-align:start}</style>
    <script src="/assets/social/chat-frame.js?v=<?= filemtime(base_path('assets/social/chat-frame.js')) ?>" defer></script>
</head>
<body>
    <?php component('Analytics::sections.chat'); ?>
    <div id="modalContainer"></div>
    <script>
        window.adminCsrfToken = <?= json_encode(csrf_token()) ?>;
        window.adminLocale = <?= json_encode(locale()) ?>;
        window.adminUiMap = <?= json_encode($adminUiMap ?? [], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE) ?>;
        window.closeModal = () => document.getElementById('modalContainer').replaceChildren();
    </script>
    <script src="/assets/theme/dialog.js?v=<?= filemtime(base_path('assets/theme/dialog.js')) ?>"></script>
    <script src="/assets/Analytics/js/chat.js?v=<?= filemtime(base_path('assets/Analytics/js/chat.js')) ?>"></script>
    <script src="/assets/Analytics/js/admin-i18n.js?v=<?= filemtime(base_path('assets/Analytics/js/admin-i18n.js')) ?>"></script>
</body>
</html>
