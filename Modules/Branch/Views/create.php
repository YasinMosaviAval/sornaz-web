<form method="post" enctype="multipart/form-data">
    <?=csrf()?>
    <?php
    component('academy.partials.account',$data);
    component('branch.partials.branch',$data);
    component('academy.partials.address',$data);
    component('academy.partials.contact',$data);
    component('academy.partials.media',$data);
    component('academy.partials.document',$data);
    component('academy.partials.google_map',$data);
    component('academy.partials.availability',$data);
    ?>
    <button type="submit" class="btn btn-success">ثبت شعبه</button>
</form>