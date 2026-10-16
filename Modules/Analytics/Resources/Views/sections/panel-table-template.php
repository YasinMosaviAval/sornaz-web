<?php
if (isset($columns, $rows)) {
    $tableRows = $rows;
    $pageSize = max(1, min(100, (int) ($pagination['perPage'] ?? 10)));
    if (isset($pagination)) {
        $tableRows = array_slice($rows, 0, $pageSize);
        if (!$tableRows) $tableRows = [['cells' => [['value' => $pagination['emptyMessage'] ?? '', 'attributes' => 'colspan="' . count($columns) . '" class="py-12 text-center text-gray-400"']]]];
        echo '<section class="admin-ui-table overflow-hidden rounded-3xl bg-white shadow dark:bg-slate-900"><div data-slot="table" class="overflow-x-auto">';
    }
    echo \Modules\Analytics\Services\PanelTableRenderer::render([
        'columns' => $columns,
        'rows' => $tableRows,
        'attributes' => $attributes ?? '',
        'headAttributes' => $headAttributes ?? '',
        'bodyAttributes' => $bodyAttributes ?? '',
        'headerRowAttributes' => $headerRowAttributes ?? '',
    ]);
    if (isset($pagination)) {
        echo '</div><footer data-slot="pagination" data-panel-pagination class="hidden flex flex-col items-center justify-between gap-4 border-t px-6 py-4 text-sm text-gray-500 sm:flex-row" hidden></footer>';
        if (($pagination['mode'] ?? '') !== 'server') echo '<script type="application/json" data-panel-pagination-source>' . json_encode(['rows' => $rows, 'perPage' => $pageSize, 'emptyMessage' => $pagination['emptyMessage'] ?? ''], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) . '</script>';
        echo '</section>';
    }
    return;
}
?>
<script type="application/json" id="adminPanelTableMarkup">
{"table":"<table {{attributes}} data-panel-table=\"1\"><thead {{headAttributes}}><tr {{headerRowAttributes}}>{{columns}}</tr></thead><tbody {{bodyAttributes}}>{{rows}}</tbody></table>","heading":"<th {{attributes}} scope=\"col\">{{html}}</th>","cell":"<td {{attributes}}>{{html}}</td>","row":"<tr {{attributes}}>{{cells}}</tr>"}
</script>
<template id="adminTableTemplate">
    <section class="admin-ui-table overflow-hidden rounded-3xl bg-white shadow">
        <div data-slot="table" class="overflow-x-auto"></div>
        <footer data-slot="pagination" class="flex flex-col items-center justify-between gap-4 border-t px-6 py-4 text-sm text-gray-500 sm:flex-row"></footer>
    </section>
</template>
