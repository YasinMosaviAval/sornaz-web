<div id="<?=e($profileContentEntity)?>" class="section hidden" data-profile-content="<?=e($profileContentEntity)?>">
 <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"><div><h1 class="text-3xl font-bold"><?=e($profileContentTitle)?></h1><p class="mt-1 text-gray-500"><?=e($profileContentDescription)?></p></div><button type="button" data-profile-add class="flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3 text-white transition hover:bg-indigo-700"><i class="fas fa-plus"></i> افزودن مورد جدید</button></div>
 <div class="mb-5 overflow-x-auto rounded-3xl bg-white p-3 shadow-sm"><div data-profile-organization-tabs class="flex min-w-max gap-2"></div></div>
 <?php
 $viewModeTableAttributes='data-profile-view="table"';
 $viewModeCardsAttributes='data-profile-view="cards"';
 require __DIR__.'/view-mode-toggle.php';
 ?>
 <div class="mb-5 rounded-3xl bg-white p-4 shadow-sm"><div class="relative"><i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i><input data-profile-search class="w-full rounded-2xl border py-3 pr-11 pl-4" placeholder="جستجو در اطلاعات..."></div></div>
 <div data-profile-loading class="rounded-3xl bg-white p-12 text-center text-gray-400">در حال دریافت اطلاعات...</div>
 <div data-profile-table-wrap class="overflow-hidden rounded-3xl bg-white shadow-sm"><div class="overflow-x-auto"><table data-no-identity-column class="w-full min-w-[980px]"><thead data-profile-head class="border-b bg-gray-50"></thead><tbody data-profile-body class="divide-y text-sm"></tbody></table></div></div>
 <div data-profile-cards class="hidden grid gap-4 lg:grid-cols-2"></div><div data-profile-pagination class="mt-5 hidden items-center justify-between rounded-2xl bg-white p-4 shadow-sm"></div>
</div>
