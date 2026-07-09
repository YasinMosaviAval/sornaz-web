<div class="sn-form-group">

    <?php if (!empty($label)): ?>
        <label class="sn-label">
            <?= e($label) ?>
        </label>
    <?php endif; ?>

    <textarea name="<?= e($name) ?>" rows="<?= $rows ?? 5 ?>" class="sn-input"><?= e($value ?? '') ?></textarea>

</div>