<?php

/*
|--------------------------------------------------------------------------
| ساعات کاری
|--------------------------------------------------------------------------
*/

ob_start();

component(
    'ui.availability',
    [
        'name'  => 'availability',
        'value' => $availability ?? []
    ]
);

$availabilityForm = ob_get_clean();

component(
    'ui.card',
    [
        'title' => 'ساعات کاری',
        'slot'  => $availabilityForm
    ]
);



/*
|--------------------------------------------------------------------------
| استثناهای ساعات کاری
|--------------------------------------------------------------------------
*/

ob_start();

component(
    'ui.availability_exceptions',
    [
        'name'  => 'availability_exceptions',
        'value' => $availabilityExceptions ?? []
    ]
);

$exceptionForm = ob_get_clean();

component(
    'ui.card',
    [
        'title' => 'استثناهای ساعات کاری',
        'slot'  => $exceptionForm
    ]
);