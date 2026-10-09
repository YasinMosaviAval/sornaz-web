<section id="notation" class="section hidden">
    <h1 class="hidden"><?= e(locale() === 'en' ? 'Music sheets' : 'نت‌نویسی') ?></h1>
    <nav id="panelNotationTabs" class="mb-5 flex flex-wrap gap-3" aria-label="<?= e(locale() === 'en' ? 'Music sheet filters' : 'دسته‌بندی نت‌ها') ?>">
        <?php foreach (['all' => ['همه', 'All'], 'mine' => ['نت‌های من', 'Mine'], 'saved' => ['ذخیره‌شده‌ها', 'Saved']] as $mode => $labels): ?>
            <button type="button" data-notation-mode="<?= e($mode) ?>" aria-pressed="<?= $mode === 'all' ? 'true' : 'false' ?>" class="rounded-2xl px-5 py-3 shadow-sm transition <?= $mode === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-indigo-50 dark:bg-slate-800 dark:text-white' ?>"><?= e($labels[locale() === 'en' ? 1 : 0]) ?></button>
        <?php endforeach; ?>
        <button type="button" data-notation-new aria-pressed="false" class="rounded-2xl bg-white px-5 py-3 text-gray-700 shadow-sm transition hover:bg-indigo-50 dark:bg-slate-800 dark:text-white"><?= e(locale() === 'en' ? 'Write a new sheet' : 'نوشتن نت جدید') ?></button>
    </nav>
    <?php if (!empty($previewPanelMode)): ?>
    <div class="rounded-3xl bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center justify-between"><strong>نت‌های نمونه</strong><span class="text-sm text-gray-500">۳ نت</span></div>
        <div class="grid gap-4 md:grid-cols-3">
            <?php foreach (['تمرین مقدماتی پیانو', 'ملودی نمونه', 'اتود ریتم'] as $previewTitle): ?>
            <article class="rounded-2xl border border-gray-200 p-5"><i class="fas fa-music mb-4 text-2xl text-indigo-600"></i><h2 class="font-bold"><?= e($previewTitle) ?></h2><p class="mt-2 text-sm text-gray-500">نت نمونه برای طراحی رابط</p></article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <iframe id="panelNotationFrame" loading="lazy" src="/music-sheets?panel=1" title="<?= e(locale() === 'en' ? 'Music notation editor' : 'ویرایشگر نت‌نویسی') ?>" class="block w-full border-0 bg-transparent" style="height:600px;min-height:600px"></iframe>
    <script>
    (() => {
        const frame = document.getElementById('panelNotationFrame');
        const tabs = document.getElementById('panelNotationTabs');
        if (!frame || !tabs) return;
        tabs.addEventListener('click', (event) => {
            const button = event.target.closest('[data-notation-mode]');
            if (button) frame.contentWindow?.postMessage({type:'sornaz-notation-filter',mode:button.dataset.notationMode}, location.origin);
            if (event.target.closest('[data-notation-new]')) frame.contentWindow?.postMessage({type:'sornaz-notation-new'}, location.origin);
        });
        window.addEventListener('message', (event) => {
            if (event.origin !== location.origin || event.source !== frame.contentWindow) return;
            if (event.data?.type === 'sornaz-notation-dialog') frame.scrollIntoView({block:'start'});
            if (event.data?.type === 'sornaz-notation-height') {
                const height = Number(event.data.height);
                if (Number.isFinite(height) && height >= 600 && height <= 100000) frame.style.height = `${height}px`;
            }
            if (event.data?.type === 'sornaz-notation-state') {
                tabs.querySelectorAll('[data-notation-mode], [data-notation-new]').forEach((button) => {
                    const active = button.hasAttribute('data-notation-new') ? event.data.route !== 'list' : event.data.route === 'list' && button.dataset.notationMode === event.data.mode;
                    button.setAttribute('aria-pressed', String(active));
                    button.classList.toggle('bg-indigo-600', active);
                    button.classList.toggle('text-white', active);
                    button.classList.toggle('bg-white', !active);
                    button.classList.toggle('text-gray-700', !active);
                });
            }
        });
    })();
    </script>
    <?php endif; ?>
</section>
