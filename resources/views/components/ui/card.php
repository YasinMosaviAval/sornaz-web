<?php

$title = $title ?? null;
$actions = $actions ?? '';

?>

<div class="sn-card">
    <?php if($title): ?>
        <div class="sn-card-header">
            <div class="sn-card-title">
                <?= e($title) ?>
            </div>
            <div class="sn-card-actions">
                <?= $actions ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="sn-card-body">
        <?= $slot ?? '' ?>
    </div>
</div>