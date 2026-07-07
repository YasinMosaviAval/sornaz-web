<?php
$type = $type ?? 'primary';
$url = $url ?? null;
$text = $text ?? '';
$class = "sn-btn sn-btn-{$type}";
?>

<?php if($url): ?>
    <a href="<?= e($url) ?>" class="<?= $class ?>"> <?= e($text) ?></a>
<?php else: ?>
    <button class="<?= $class ?>"> <?= e($text) ?></button>
<?php endif; ?>