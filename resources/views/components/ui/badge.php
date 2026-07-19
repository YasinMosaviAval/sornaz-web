<?php
$class='secondary';
if(($type ?? '')=='success'){
    $class='success';
}
?>

<span class="sn-badge sn-badge-<?= $class ?>"><?= e($text) ?></span>