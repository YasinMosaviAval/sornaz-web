<div id="users" class="section hidden">
 <style>
  #userAccessModal .access-choice[aria-pressed="true"] { background: color-mix(in srgb, var(--theme-500, #6366f1) 25%, transparent); border-color: var(--theme-500, #6366f1); }
  #userAccessModal .access-choice:focus-visible { outline-color: var(--theme-500, #6366f1); }
 </style>
 <h1 class="hidden">نقش‌ها و دسترسی‌ها</h1>
 <?php component('staff-page-tabs', ['active' => 'users']); ?>
 <p class="mb-6 text-gray-500">تعیین نقش‌ها و مجوزهای اختصاصی هر کاربر</p>
 <div class="mb-6 overflow-x-auto rounded-3xl bg-white p-3 shadow-sm"><div id="accessUsersBranchTabs" class="flex min-w-max gap-2"><button type="button" data-value="all" class="rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white">همه</button></div></div>
 <div class="mb-6 rounded-3xl bg-white p-5 shadow-sm"><div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"><input id="accessUserSearch" oninput="searchAccessUsers()" class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:outline-none focus:border-indigo-500" placeholder="جستجو در نام، نام کاربری، موبایل یا ایمیل..."><select id="accessUserBranch" onchange="loadAccessUsers(1)" class="w-full rounded-2xl border border-gray-300 px-4 py-3"><option value="">همه شعبه‌ها</option></select><select id="accessUserStatus" onchange="loadAccessUsers(1)" class="w-full rounded-2xl border border-gray-300 px-4 py-3"><option value="">همه وضعیت‌ها</option><option value="pending">در انتظار</option><option value="approved">تأییدشده</option><option value="rejected">ردشده</option><option value="inactive">غیرفعال</option><option value="banned">مسدود</option></select></div></div>
 <div class="overflow-hidden rounded-3xl bg-white shadow"><div class="overflow-x-auto"><?php component('panel-table-template', [
                'attributes' => ' id="accessUsersTable" data-admin-identity-direction="desc" class="w-full min-w-[1000px]"',
                'headAttributes' => ' class="bg-gray-50"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' id="accessUsersBody" class="divide-y text-sm"',
                'columns' => [
                    ['html' => 'کاربر', 'attributes' => ' class="p-5 text-right"'],
                    ['html' => 'نقش‌ها', 'attributes' => ' class="p-5 text-right"'],
                    ['html' => 'مجوزهای اختصاصی', 'attributes' => ' class="p-5 text-right"'],
                    ['html' => 'وضعیت', 'attributes' => ' class="p-5 text-right"'],
                    ['html' => '', 'attributes' => ' class="p-5"'],
                ],
                'rows' => [],
            ]); ?></div><div class="flex flex-wrap items-center justify-between gap-3 border-t p-4"><div class="flex items-center gap-3"><span id="accessUsersInfo" class="text-sm text-gray-500"></span><select id="accessUsersPerPage" onchange="changeAccessUsersPerPage(this.value)" class="rounded-xl border px-3 py-2"><option>10</option><option selected>20</option><option>30</option><option>50</option><option>100</option></select></div><div id="accessUsersPagination" class="flex gap-2"></div></div></div>
</div>
