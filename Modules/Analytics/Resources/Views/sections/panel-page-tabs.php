<?php /** Shared in-flow navigation; item attributes are trusted template constants. */ ?>
<?php $tabHandler = isset($onSelect) && preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/D', $onSelect) ? $onSelect : 'showSection'; ?>
<nav class="panel-page-tabs mb-5 flex flex-wrap gap-3" aria-label="<?= e($label ?? '') ?>">
    <?php foreach (($tabs ?? []) as $tabId => $tab): ?>
        <button type="button" onclick="<?= e($tabHandler) ?>(<?= e(json_encode((string) $tabId, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)" <?= ($active ?? '') === $tabId ? 'aria-current="page"' : '' ?> <?= $tab['attributes'] ?? '' ?> class="<?= !empty($tab['hidden']) ? 'hidden ' : '' ?>rounded-2xl px-5 py-3 shadow-sm transition <?= ($active ?? '') === $tabId ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-indigo-50 dark:bg-slate-800 dark:text-white' ?>"><?= e($tab['labels'][locale() === 'en' ? 1 : 0]) ?></button>
    <?php endforeach; ?>
</nav>
