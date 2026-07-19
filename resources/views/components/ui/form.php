<form
    method="<?= e($method ?? 'POST') ?>"
    action="<?= e($action ?? '') ?>"
    class="<?= e($class ?? 'sn-form') ?>"
    <?= !empty($id) ? 'id="'.e($id).'"' : '' ?>
    <?= !empty($enctype) ? 'enctype="'.e($enctype).'"' : '' ?>
>
    <?php
        if (
            strtoupper($method ?? 'POST') !== 'GET'
            && strtoupper($method ?? 'POST') !== 'POST'
        ):
    ?>
        <input
            type="hidden"
            name="_method"
            value="<?= strtoupper($method) ?>"
        >
    <?php endif; ?>
    <?= $slot ?? '' ?>
</form>