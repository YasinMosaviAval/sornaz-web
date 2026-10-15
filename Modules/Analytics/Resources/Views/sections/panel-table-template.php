<?php
if (isset($columns, $rows)) {
    echo \Modules\Analytics\Services\PanelTableRenderer::render([
        'columns' => $columns,
        'rows' => $rows,
        'attributes' => $attributes ?? '',
        'headAttributes' => $headAttributes ?? '',
        'bodyAttributes' => $bodyAttributes ?? '',
        'headerRowAttributes' => $headerRowAttributes ?? '',
    ]);
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
