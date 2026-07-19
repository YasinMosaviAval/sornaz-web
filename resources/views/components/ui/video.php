<?php

pushStyle('assets/css/components/video.css');
pushScript('assets/js/components/video.js');

$value = $value ?? '';

?>

<div class="sn-form-group">
    <label><?= e($label) ?></label>
    <input type="file" name="<?= e($name) ?>" id="<?= e($name) ?>" class="sn-input" accept="video/mp4,video/webm,video/ogg">
    <input type="hidden" name="<?= e($name) ?>_current" value="<?= e($value) ?>">
    <div class="sn-video-preview">
        <?php if(!empty($value)): ?>
            <video controls>
                <source src="<?= upload_url($value) ?>">
            </video>
        <?php endif; ?>
    </div>
</div>