<div id="roles" class="section hidden"><?php component('access-page-tabs', ['active' => 'roles']); ?><div class="mb-6 flex flex-wrap items-center justify-between gap-4"><button onclick="openAccessRole()" class="rounded-xl bg-indigo-600 px-5 py-3 text-white"><i class="fas fa-plus ml-2"></i>نقش جدید</button></div><div class="mb-5 overflow-x-auto rounded-3xl bg-white p-4 shadow-sm"><div class="grid min-w-[720px] grid-cols-4 gap-3"><input id="accessRoleSearch" oninput="renderAccessRoles()" class="min-w-0 rounded-xl border px-4 py-3" placeholder="جستجوی نقش..."><select id="accessRoleScope" onchange="renderAccessRoles()" class="min-w-0 rounded-xl border px-4"><option value="">همه دامنه‌ها</option><option value="platform">پلتفرم</option><option value="website">سایت</option><option value="academy">آموزشگاه</option><option value="branch">شعبه</option><option value="self">شخصی</option></select><select id="accessRoleLevel" onchange="renderAccessRoles()" class="min-w-0 rounded-xl border px-4"><option value="">همه سطوح</option><option value="1">درجه ۱</option><option value="2">درجه ۲</option><option value="3">درجه ۳</option></select><div id="accessRoleCount" class="flex items-center text-sm text-gray-500"></div></div></div><div class="overflow-hidden rounded-3xl bg-white shadow"><div class="overflow-x-auto"><?php component('panel-table-template', [
                'attributes' => ' class="w-full min-w-[1000px]"',
                'headAttributes' => ' class="bg-gray-50"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' id="accessRolesBody" class="divide-y text-sm"',
                'columns' => [
                    ['html' => 'عنوان و نام', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'دامنه', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'درجه', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'والد', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'مجوزها', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'نمایش در پنل', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => '', 'attributes' => ' class="p-4"'],
                ],
                'rows' => [],
            ]); ?></div></div></div>
