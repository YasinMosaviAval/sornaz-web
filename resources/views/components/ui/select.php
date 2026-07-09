<?php

$current = old(
    $name ?? '',
    $value ?? null
);

?>

<div class="sn-form-group">

    <?php if (!empty($label)): ?>
        <label class="sn-label">
            <?= e($label) ?>
        </label>
    <?php endif; ?>
    
    <select name="<?= e($name) ?>" class="sn-input">
        <?php foreach($options as $key=>$text): ?>
            <option value="<?= e($key) ?>" <?= $current == $key ? 'selected' : '' ?>>
                <?= e($text) ?>
            </option>
        <?php endforeach; ?>
    </select>

</div>