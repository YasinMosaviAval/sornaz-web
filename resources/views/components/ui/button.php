<?
// $type = $type ?? 'primary';
// $url = $url ?? null;
// $text = $text ?? '';
// $class = "sn-btn sn-btn-{$type}";
?>

<?// if($url): ?>
    <!-- <a href="<?//= e($url) ?>" class="<?//= $class ?>"> <?//= e($text) ?></a> -->
<?// else: ?>
    <!-- <button class="<?//= $class ?>"> <?//= e($text) ?></button> -->
<?// endif; ?>



<?
$type = $type ?? 'primary';
$class = "sn-btn {$type}";
if (!empty($submit)):
?>

<button type="submit" class="<?= $class ?>">
    <?= e($text) ?>
</button>
<? else: ?>
<a href="<?= e($url ?? '#') ?>" class="<?= $class ?>">
    <?= e($text) ?>
</a>
<? endif; ?>