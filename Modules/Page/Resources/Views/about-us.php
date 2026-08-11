<div id="page-about" class="">
    <div class="max-w-3xl mx-auto px-4 py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-bold text-center mb-4"><?= e(trans('public.about.title', 'درباره برنامه موسیقی سُرناز')) ?></h1>
        <div class="bg-white rounded-3xl p-8 shadow-sm mb-6 leading-relaxed text-gray-600 text-justify">
            <p class="pb-5 text-gray-600 leading-relaxed text-justify"><?= e(trans('public.about.description')) ?></p>
        </div>
        <div class="space-y-3" id="aboutAccordions">
            <?php for ($item = 1; $item <= 8; $item++): ?>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <button type="button" onclick="toggleAccordion(this)" class="w-full flex items-center justify-between px-6 py-4 font-bold hover:bg-gray-50" style="text-align:start">
                        <span><?= e(trans('public.about.section_'.$item.'.title')) ?></span>
                        <i class="fas fa-plus text-indigo-500 accordion-icon text-sm"></i>
                    </button>
                    <div class="accordion-body px-6">
                        <div class="pb-5 text-gray-600 leading-relaxed text-justify"><?= trans('public.about.section_'.$item.'.description') ?></div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>
