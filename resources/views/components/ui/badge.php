<?php
$type = $type ?? 'primary';
$text = $text ?? '';
?>

<span class="sn-badge sn-badge-<?= e($type) ?>"><?= e($text) ?></span>