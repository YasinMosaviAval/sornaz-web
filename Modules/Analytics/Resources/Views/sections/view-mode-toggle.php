<?php
$viewModeTableId = $viewModeTableId ?? '';
$viewModeCardsId = $viewModeCardsId ?? '';
$viewModeTableAttributes = $viewModeTableAttributes ?? '';
$viewModeCardsAttributes = $viewModeCardsAttributes ?? '';
?>
<div class="view-mode-toggle mb-5 flex flex-wrap gap-2" role="group" aria-label="نوع نمایش">
    <button type="button"<?= $viewModeTableId !== '' ? ' id="'.e($viewModeTableId).'"' : '' ?> <?= $viewModeTableAttributes ?>
            class="view-mode-toggle-button rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 transition hover:border-indigo-300 hover:text-indigo-700">
        <i class="fas fa-table ml-1"></i> نمایش جدولی
    </button>
    <button type="button"<?= $viewModeCardsId !== '' ? ' id="'.e($viewModeCardsId).'"' : '' ?> <?= $viewModeCardsAttributes ?>
            class="view-mode-toggle-button rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 transition hover:border-indigo-300 hover:text-indigo-700">
        <i class="fas fa-th-large ml-1"></i> نمایش کارتی
    </button>
</div>
<?php unset($viewModeTableId,$viewModeCardsId,$viewModeTableAttributes,$viewModeCardsAttributes); ?>
