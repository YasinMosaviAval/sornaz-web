<section id="notation" class="section hidden">
    <h1 class="hidden"><?= e(locale() === 'en' ? 'Music sheets' : 'نت‌نویسی') ?></h1>
    <div id="panelNotationToolbar" class="mb-5 flex flex-wrap items-center justify-between gap-4">
    <nav id="panelNotationTabs" class="flex flex-wrap gap-3" aria-label="<?= e(locale() === 'en' ? 'Music sheet filters' : 'دسته‌بندی نت‌ها') ?>">
        <?php foreach (['all' => ['همه', 'All'], 'mine' => ['نت‌های من', 'Mine'], 'saved' => ['ذخیره‌شده‌ها', 'Saved'], 'lists' => ['لیست‌ها', 'Lists']] as $mode => $labels): ?>
            <button type="button" data-notation-mode="<?= e($mode) ?>" aria-pressed="<?= $mode === 'all' ? 'true' : 'false' ?>" class="rounded-2xl px-5 py-3 shadow-sm transition <?= $mode === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-indigo-50 dark:bg-slate-800 dark:text-white' ?>"><?= e($labels[locale() === 'en' ? 1 : 0]) ?></button>
        <?php endforeach; ?>
    </nav>
        <div class="flex flex-wrap items-center gap-3">
            <button type="button" data-notation-import class="rounded-2xl bg-white px-5 py-3 text-gray-700 shadow-sm transition hover:bg-indigo-50 dark:bg-slate-800 dark:text-white"><?= e(locale() === 'en' ? 'Import score' : 'وارد کردن نت') ?></button>
            <button type="button" data-notation-omr style="display:none" class="rounded-2xl bg-white px-5 py-3 text-gray-700 shadow-sm transition hover:bg-indigo-50 dark:bg-slate-800 dark:text-white"><?= e(locale() === 'en' ? 'Recognize notes from image' : 'تشخیص نت از تصویر') ?></button>
            <button type="button" data-notation-settings aria-pressed="false" aria-label="<?= e(locale() === 'en' ? 'Settings' : 'تنظیمات') ?>" title="<?= e(locale() === 'en' ? 'Settings' : 'تنظیمات') ?>" class="rounded-2xl bg-white w-12 h-12 inline-flex items-center justify-center text-xl text-gray-700 shadow-sm transition hover:bg-indigo-50 dark:bg-slate-800 dark:text-white"><i class="fas fa-gear" aria-hidden="true"></i></button>
            <button type="button" data-notation-create-list style="display:none" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl items-center gap-2 shadow-sm transition"><i class="fas fa-plus" aria-hidden="true"></i><?= e(locale() === 'en' ? 'New list' : 'لیست جدید') ?></button>
            <button type="button" data-notation-new aria-pressed="false" aria-label="<?= e(locale() === 'en' ? 'Write a new sheet' : 'نوشتن نت جدید') ?>" title="<?= e(locale() === 'en' ? 'Write a new sheet' : 'نوشتن نت جدید') ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white w-12 h-12 rounded-2xl inline-flex items-center justify-center text-xl shadow-sm transition"><i class="fas fa-pen" aria-hidden="true"></i></button>
        </div>
    </div>
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
    <style>#mainContent:has(> #notation:not(.hidden)){padding-bottom:0}</style>
    <iframe id="panelNotationFrame" loading="lazy" src="/music-sheets?panel=1" title="<?= e(locale() === 'en' ? 'Music notation editor' : 'ویرایشگر نت‌نویسی') ?>" class="block w-full border-0 bg-transparent" style="height:1px"></iframe>
    <script>
    (() => {
        const frame = document.getElementById('panelNotationFrame');
        const tabs = document.getElementById('panelNotationTabs');
        const toolbar = document.getElementById('panelNotationToolbar');
        if (!frame || !tabs) return;
        let route = 'list', contentHeight = 1;
        const resize = () => {
            let height = contentHeight + 10;
            if (route === 'editor') {
                const main = frame.closest('main');
                if (main && /auto|scroll/.test(getComputedStyle(main).overflowY)) height = main.clientHeight - parseFloat(getComputedStyle(main).paddingTop || 0);
                else height = window.innerHeight - Math.max(0, frame.getBoundingClientRect().top + window.scrollY);
            }
            frame.style.height = Math.max(1, Math.ceil(height)) + 'px';
            const bounds = frame.getBoundingClientRect(), main = frame.closest('main');
            const clipped = main && /auto|scroll/.test(getComputedStyle(main).overflowY) ? main.getBoundingClientRect() : {top:0,bottom:window.innerHeight};
            const top = Math.max(bounds.top, clipped.top, 0), bottom = Math.min(bounds.bottom, clipped.bottom, window.innerHeight);
            frame.contentWindow?.postMessage({type:'sornaz-notation-viewport',route,center:Math.max(0,(top+bottom)/2-bounds.top),height:Math.max(1,bottom-top)}, location.origin);
        };
        window.addEventListener('resize', resize);
        window.addEventListener('scroll', resize, {capture:true,passive:true});
        if (frame.closest('main')) new ResizeObserver(resize).observe(frame.closest('main'));
        toolbar.addEventListener('click', (event) => {
            const button = event.target.closest('[data-notation-mode]');
            if (button) frame.contentWindow?.postMessage({type:'sornaz-notation-filter',mode:button.dataset.notationMode}, location.origin);
            if (event.target.closest('[data-notation-new]')) frame.contentWindow?.postMessage({type:'sornaz-notation-new'}, location.origin);
            if (event.target.closest('[data-notation-settings]')) frame.contentWindow?.postMessage({type:'sornaz-notation-settings'}, location.origin);
            if (event.target.closest('[data-notation-create-list]')) frame.contentWindow?.postMessage({type:'sornaz-notation-create-list'}, location.origin);
            if (event.target.closest('[data-notation-import]')) frame.contentWindow?.postMessage({type:'sornaz-notation-command',action:'import-score'}, location.origin);
            if (event.target.closest('[data-notation-omr]')) frame.contentWindow?.postMessage({type:'sornaz-notation-command',action:'web-omr'}, location.origin);
        });
        window.addEventListener('message', (event) => {
            if (event.origin !== location.origin || event.source !== frame.contentWindow) return;
            if (event.data?.type === 'sornaz-notation-dialog') frame.scrollIntoView({block:'start'});
            if (event.data?.type === 'sornaz-notation-height') {
                const height = Number(event.data.height);
                if (Number.isFinite(height) && height >= 1 && height <= 100000) { contentHeight = height; resize(); }
            }
            if (event.data?.type === 'sornaz-notation-state') {
                route = event.data.route;
                toolbar.style.display = ['form', 'editor'].includes(event.data.route) ? 'none' : '';
                resize();
                toolbar.querySelector('[data-notation-omr]').style.display = event.data.omrEnabled === true ? 'inline-flex' : 'none';
                toolbar.querySelector('[data-notation-create-list]').style.display = event.data.route === 'list' && event.data.mode === 'lists' ? 'inline-flex' : 'none';
                toolbar.querySelector('[data-notation-new]').setAttribute('aria-pressed', String(['form', 'editor'].includes(event.data.route)));
                toolbar.querySelectorAll('[data-notation-mode], [data-notation-settings]').forEach((button) => {
                    const active = button.hasAttribute('data-notation-settings') ? event.data.route === 'settings' : event.data.route === 'list' && button.dataset.notationMode === event.data.mode;
                    button.setAttribute('aria-pressed', String(active));
                    button.classList.toggle('bg-indigo-600', active);
                    button.classList.toggle('text-white', active);
                    button.classList.toggle('bg-white', !active);
                    button.classList.toggle('text-gray-700', !active);
                    ['hover:bg-indigo-50', 'dark:bg-slate-800', 'dark:text-white'].forEach(name => button.classList.toggle(name, !active));
                });
            }
        });
    })();
    </script>
    <?php endif; ?>
</section>
