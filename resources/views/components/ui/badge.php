<?php
// $type = $type ?? 'primary';
// $text = $text ?? '';
?>

<!-- <span class="sn-badge sn-badge-<?//= e($type) ?>"><?//= e($text) ?></span> -->

<?php

$class='secondary';

if(($type ?? '')=='success'){

$class='success';

}

?>

<span class="sn-badge sn-badge-<?= $class ?>">

<?= e($text) ?>

</span>