<?php if (\Modules\System\Services\SiteAdminAccess::allows(auth()->user())): ?>
<div id="national-holidays" class="section hidden">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div><h1 class="text-3xl font-bold">تعطیلات رسمی کشور</h1><p class="mt-1 text-gray-500">تاریخ‌هایی که به‌صورت پیش‌فرض از برنامه‌ریزی خودکار جلسات حذف می‌شوند</p></div>
        <button type="button" onclick="openNationalHolidayModal()" class="rounded-2xl bg-indigo-600 px-6 py-3 text-white"><i class="fas fa-plus ml-2"></i>افزودن تعطیل رسمی</button>
    </div>
    <div class="overflow-hidden rounded-3xl bg-white shadow">
        <div class="overflow-x-auto"><table class="w-full min-w-[760px]"><thead class="border-b bg-gray-50"><tr><th class="p-5 text-right">عنوان</th><th class="p-5 text-right">تاریخ</th><th class="p-5 text-right">وضعیت</th><th class="p-5"></th></tr></thead><tbody id="nationalHolidaysBody"></tbody></table></div>
    </div>
</div>
<?php endif; ?>
