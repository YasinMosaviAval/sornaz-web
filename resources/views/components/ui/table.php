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
        <!-- <table class="sn-table">
            <thead>
            <tr>
                <?// foreach($columns as $column): ?>
                    <th>
                        <?//= e($column['title']) ?>
                    </th>
                <?// endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?// if(empty($rows)): ?>
                <tr>
                    <td colspan="<?//= count($columns) ?>">
                        داده‌ای وجود ندارد.
                    </td>
                </tr>
            <?// else: ?>
                <?// foreach($rows as $row): ?>
                    <tr>
                        <?// foreach($columns as $column): ?>
                            <td>
                                <?//= $row[$column['field']] ?? '' ?>
                            </td>
                        <?// endforeach; ?>
                    </tr>
                <?// endforeach; ?>
            <?// endif; ?>
            </tbody>
        </table> -->
    </div>
</div>