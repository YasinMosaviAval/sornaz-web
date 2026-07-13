<div class="sn-form-group">

    <?php if (!empty($label)): ?>
        <label class="sn-label">
            <?= e($label) ?>
        </label>
    <?php endif; ?>

    <textarea
        class="sn-input"
        name="<?= e($name) ?>"
        rows="<?= $rows ?? 4 ?>"
    ><?= e($value ?? '') ?></textarea>

    <?php if (!empty($error)): ?>
        <div class="sn-error">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

</div>