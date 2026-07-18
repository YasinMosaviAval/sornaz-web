<?php

$value=$value ?? [];

?>

<div class="sn-form-group">
    <label>
        اسناد
    </label>
    <input
        type="file"
        name="<?=e($name)?>[]"
        multiple>
</div>
<?php if(!empty($value)): ?>
<table class="sn-table">
    <thead>
        <tr>
            <th>نام فایل</th>
            <th>دانلود</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($value as $item): ?>
        <tr>
            <td>
                <?=e($item['original_filename'])?>
            </td>
            <td>
                <a
                    target="_blank"
                    href="/<?=e($item['path'])?>">
                    دانلود
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>