<h2>شعب آموزشگاه</h2>

<div class="mb-3">
    <a href="/academies/<?=e($academyId)?>/branches/create" class="btn btn-primary">
        ثبت شعبه جدید
    </a>
</div>
<hr>

<?php if(empty($branches)): ?>
    <div class="alert alert-info">
        هنوز شعبه‌ای ثبت نشده است.
    </div>
<?php endif; ?>

<?php foreach($branches as $branch): ?>
<div class="card mb-3 p-3">
    <h5>
        <?=e($branch['name_fa'] ?? '-')?>
    </h5>
    <div>
        <a href="/academies/<?=e($academyId)?>/branches/<?=e($branch['branch_id'])?>/edit">
            ویرایش
        </a>
    </div>
</div>
<?php endforeach; ?>