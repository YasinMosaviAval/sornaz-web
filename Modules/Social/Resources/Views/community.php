<?php
$pageTitle = locale() === 'en' ? 'Sornaz community' : 'جامعه سُرناز';
$pageStyles = ['assets/social/community.css'];
$pageScripts = ['assets/social/community.js'];
$config = json_encode($boot, JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?>
<div class="community-app">
    <div id="community-root"></div>
    <div id="community-toast" role="status" aria-live="polite"></div>
    <script>window.SOCIAL_BOOT = <?= $config ?>;</script>
</div>
