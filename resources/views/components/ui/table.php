<div class="sn-card">
    <?php if(!empty($title)): ?>
        <div class="sn-card-header">
            <h3><?= e($title) ?></h3>
        </div>
    <?php endif; ?>
    <div class="sn-table-wrapper">
        <?php
            component(
                'ui.table',
                [
                    'title'   => 'لیست آموزشگاه‌ها',
                    'columns' => $columns,
                    'rows'    => $rows
                ]
            );
        ?>
    </div>
</div>