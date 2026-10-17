<div data-panel-content data-term-panel="discounts" class="hidden">
    <div class="mb-6 flex justify-end">
        <button type="button" onclick="openAddTermDiscountModal()" class="flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3 text-white"><i class="fas fa-plus" aria-hidden="true"></i><?= e(locale()==='en' ? 'Add discount' : 'افزودن تخفیف جدید') ?></button>
    </div>
    <div class="mb-6 rounded-3xl bg-white p-3 shadow-sm"><div id="termDiscountsBranchTabs" class="flex gap-2 overflow-x-auto"></div></div>
    <div class="mb-6 grid gap-4 rounded-3xl bg-white p-5 shadow-sm md:grid-cols-2">
        <input id="termDiscountSearch" type="search" oninput="filterTermDiscounts()" aria-label="<?= e(locale()==='en' ? 'Search discounts' : 'جستجوی تخفیف‌ها') ?>" placeholder="<?= e(locale()==='en' ? 'Search discounts' : 'جستجوی تخفیف‌ها') ?>" class="w-full rounded-2xl border px-4 py-3">
        <select id="termDiscountTypeFilter" onchange="filterTermDiscounts()" aria-label="<?= e(locale()==='en' ? 'Discount type' : 'نوع تخفیف') ?>" class="w-full rounded-2xl border px-4 py-3">
            <option value=""><?= e(locale()==='en' ? 'All discount types' : 'همه انواع تخفیف') ?></option>
            <option value="percentage"><?= e(locale()==='en' ? 'Percentage' : 'درصدی') ?></option>
            <option value="fixed"><?= e(locale()==='en' ? 'Fixed amount' : 'مبلغ ثابت') ?></option>
        </select>
    </div>
    <?php component('panel-table-template', [
        'attributes'=>'id="termDiscountsTable" class="w-full min-w-[700px]"',
        'headAttributes'=>'class="bg-gray-50 border-b"', 'bodyAttributes'=>'class="divide-y text-sm"',
        'columns'=>array_map(static fn ($label)=>['value'=>$label,'attributes'=>'class="px-5 py-5 text-start font-medium"'], locale()==='en' ? ['Title','Type','Value','Organization','Actions'] : ['عنوان تخفیف','نوع تخفیف','مقدار','سازمان','عملیات']),
        'rows'=>[], 'pagination'=>['mode'=>'server'],
    ]); ?>
</div>
