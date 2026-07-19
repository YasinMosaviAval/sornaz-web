<div class="sn-form-group">
    <?php if (!empty($label)): ?>
        <label class="sn-label"><?= e($label) ?></label>
    <?php endif; ?>
    <input
        type="<?= $type ?? 'text' ?>"
        name="<?= e($name) ?>"
        value="<?= e($value ?? '') ?>"
        placeholder="<?= e($placeholder ?? '') ?>"
        class="sn-input"
        autocomplete="off"
    >
    <?php if (!empty($error)): ?>
        <div class="sn-error"><?= e($error) ?></div>
    <?php endif; ?>
</div>