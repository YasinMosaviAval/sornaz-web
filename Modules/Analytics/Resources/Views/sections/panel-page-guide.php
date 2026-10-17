<?php
/** Shared collapsible page guidance. Both inputs contain plain text only. */
$guideTitle = (string) ($title ?? '');
$guideParagraphs = (array) ($paragraphs ?? []);
if (isset($page)) {
    $guideFile = dirname(__DIR__, 2) . '/panel-page-guidance.php';
    $defaults = is_file($guideFile) ? require $guideFile : [];
    $guide = $defaults[$page] ?? [];
    $guideTitle = $guide['title'][locale()] ?? $guide['title']['fa'] ?? '';
    $guideParagraphs = preg_split('/\n\s*\n/', $guide['body'][locale()] ?? $guide['body']['fa'] ?? '') ?: [];
}
?>
<?php if (!empty($asTemplate)): ?><template id="panelPageGuideTemplate"><?php endif; ?>
<details class="panel-page-guide group mb-6 rounded-2xl border border-indigo-200 bg-indigo-50 text-sm leading-7 text-indigo-900 dark:border-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-100">
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 rounded-2xl px-6 py-4 font-bold hover:bg-indigo-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 dark:hover:bg-indigo-900/40" style="text-align:start">
        <span data-guide-title><?= e($guideTitle) ?></span>
        <span aria-hidden="true" class="shrink-0 text-indigo-500"><span class="group-open:hidden">+</span><span class="hidden group-open:inline">−</span></span>
    </summary>
    <div data-guide-paragraphs class="space-y-3 px-6 pb-5">
        <?php foreach ($guideParagraphs as $guideParagraph): ?>
            <p><?= e((string) $guideParagraph) ?></p>
        <?php endforeach; ?>
    </div>
</details>
<?php if (!empty($asTemplate)): ?></template><?php endif; ?>
