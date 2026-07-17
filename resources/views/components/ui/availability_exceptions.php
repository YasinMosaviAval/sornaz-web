<?php

pushStyle('assets/css/components/availability-exceptions.css');
pushScript('assets/js/components/availability-exceptions.js');

$value = $value ?? [];

?>

<div class="availability-exceptions">

    <div id="availability-exception-list">

        <?php foreach($value as $index => $item): ?>

            <div class="availability-exception-row">

                <input
                    type="date"
                    name="exceptions[<?= $index ?>][date]"
                    value="<?= e($item['date'] ?? '') ?>">

                <input
                    type="time"
                    name="exceptions[<?= $index ?>][start_time]"
                    value="<?= e($item['start_time'] ?? '') ?>">

                <input
                    type="time"
                    name="exceptions[<?= $index ?>][end_time]"
                    value="<?= e($item['end_time'] ?? '') ?>">

                <select
                    name="exceptions[<?= $index ?>][type]">

                    <?php

                    foreach([
                        'holiday'      => 'تعطیل',
                        'closed'       => 'بسته',
                        'busy'         => 'مشغول',
                        'vacation'     => 'مرخصی',
                        'blocked'      => 'مسدود',
                        'unavailable'  => 'در دسترس نیست'
                    ] as $key=>$title):

                    ?>

                        <option
                            value="<?= $key ?>"
                            <?= (($item['type'] ?? '') == $key) ? 'selected' : '' ?>>
                            <?= $title ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <input
                    type="text"
                    name="exceptions[<?= $index ?>][note]"
                    value="<?= e($item['note'] ?? '') ?>"
                    placeholder="توضیح">

                <button
                    type="button"
                    class="exception-remove">
                    حذف
                </button>

            </div>

        <?php endforeach; ?>

    </div>

    <button
        type="button"
        id="add-exception">
        + افزودن استثناء
    </button>

</div>