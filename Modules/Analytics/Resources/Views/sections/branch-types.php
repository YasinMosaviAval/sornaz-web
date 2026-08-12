<?php if (\Modules\System\Services\SiteAdminAccess::allows(auth()->user())): ?>
<section id="branch-types" class="section hidden" data-csrf="<?= e(csrf_token()) ?>">
    <div class="mb-7 flex flex-wrap items-center justify-between gap-4"><div><h1 class="text-3xl font-bold">انواع آموزشی</h1><p class="mt-2 text-gray-500">مدیریت عنوان، خلاصه و شرح انواع آموزشی شعبه‌ها</p></div><button type="button" onclick="openBranchTypeAdminModal()" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-white"><i class="fas fa-plus ml-2"></i>نوع آموزشی جدید</button></div>
    <div class="overflow-x-auto rounded-3xl border border-gray-100 bg-white shadow-sm"><table class="w-full min-w-[850px]"><thead class="bg-gray-50 text-right text-sm text-gray-500"><tr><th class="p-4">عنوان</th><th class="p-4">خلاصه</th><th class="p-4">شرح</th><th class="p-4">عملیات</th></tr></thead><tbody id="branchTypesAdminBody"><tr><td colspan="4" class="p-12 text-center text-gray-400">در حال بارگذاری...</td></tr></tbody></table></div>
</section>
<?php endif; ?>
