<?
$type = $type ?? 'primary';
$class = "sn-btn {$type}";
if (!empty($submit)):
?>

<button type="submit" class="<?= $class ?>"><?= e($text) ?></button>
<? else: ?>
<a href="<?= e($url ?? '#') ?>" class="<?= $class ?>"><?= e($text) ?></a>
<? endif; ?>