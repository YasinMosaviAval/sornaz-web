<!-- <h1 class="page-title">
ویرایش آموزشگاه
</h1> -->

<div class="page-toolbar">

<?php

component(
    'ui.button',
    [
        'url'=>'/academy',
        'text'=>'بازگشت',
        'type'=>'secondary'
    ]
);

?>

</div>

<form
method="post"
action="/academy/<?= $academy['academy_id'] ?>"
enctype="multipart/form-data">

<input
type="hidden"
name="_method"
value="PUT">

<?php

require __DIR__.'/partials/account.php';

require __DIR__.'/partials/academy.php';

require __DIR__.'/partials/media.php';

require __DIR__.'/partials/availability.php';

require __DIR__.'/partials/address.php';

require __DIR__.'/partials/contact.php';

?>

<div style="margin-top:30px">

<?php

component(
    'ui.button',
    [
        'submit'=>true,
        'text'=>'ذخیره تغییرات',
        'type'=>'success'
    ]
);

?>

</div>

</form>

<script src="/assets/js/world.js"></script>
