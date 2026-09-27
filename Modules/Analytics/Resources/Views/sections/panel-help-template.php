<?php if ((int)auth()->id() !== 1) return; ?>
<template id="adminHelpButtonTemplate">
    <button type="button" class="context-help-button" aria-label="راهنمای این صفحه">
        <i class="fas fa-question"></i>
        <span>راهنما</span>
    </button>
</template>
