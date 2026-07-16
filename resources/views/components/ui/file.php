<div class="sn-form-group">

<label>

<?=e($label)?>

</label>

<input
type="file"
name="<?=e($name)?>"
class="sn-input"

    <?php if(!empty($multiple)): ?>
        multiple
    <?php endif; ?>
/>

</div>