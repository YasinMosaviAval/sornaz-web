<label
    class="sn-label <?= e($class ?? '') ?>"
    <?= !empty($for) ? 'for="'.e($for).'"' : '' ?>
>
    <?= e($text ?? '') ?>
    <?php if(!empty($required)): ?>
        <span class="sn-required">*</span>
    <?php endif; ?>
</label>