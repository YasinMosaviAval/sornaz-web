<?php

ob_start();
?>

<div id="availability-exceptions">

    <p class="text-muted">
        هنوز استثنایی ثبت نشده است.
    </p>

</div>

<?php

$exceptionForm = ob_get_clean();

component(
    'ui.card',
    [
        'title' => 'استثناهای ساعات کاری',
        'slot'  => $exceptionForm
    ]
);