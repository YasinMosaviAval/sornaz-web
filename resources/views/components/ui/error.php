<?php
$all = errors();
$message = $all[$field] ?? null;
if (is_array($message)) {
    $message = reset($message);
}
if ($message):

?>

<div class="sn-error"><?= e($message) ?></div>
<?php endif; ?>