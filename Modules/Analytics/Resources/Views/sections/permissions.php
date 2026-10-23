<div id="permissions" class="section hidden"><?php component('access-page-tabs', ['active' => 'permissions']); ?><div class="mb-6 flex flex-wrap items-center justify-between gap-4"><button onclick="openAccessPermission()" class="rounded-xl bg-indigo-600 px-5 py-3 text-white"><i class="fas fa-plus ml-2"></i>مجوز جدید</button></div><div class="mb-5 overflow-x-auto rounded-3xl bg-white p-4 shadow-sm"><div class="grid min-w-[720px] grid-cols-4 gap-3"><input id="accessPermissionSearch" oninput="renderAccessPermissions()" class="min-w-0 rounded-xl border px-4 py-3" placeholder="جستجوی مجوز..."><select id="accessPermissionScope" onchange="renderAccessPermissions()" class="min-w-0 rounded-xl border px-4"><option value="">همه دامنه‌ها</option><option value="platform">پلتفرم</option><option value="website">سایت</option><option value="academy">آموزشگاه</option><option value="branch">شعبه</option><option value="self">شخصی</option></select><select id="accessPermissionRisk" onchange="renderAccessPermissions()" class="min-w-0 rounded-xl border px-4"><option value="">همه سطوح خطر</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select><div id="accessPermissionCount" class="flex items-center text-sm text-gray-500"></div></div></div><div class="overflow-hidden rounded-3xl bg-white shadow"><div class="overflow-x-auto"><?php component('panel-table-template', [
                'attributes' => ' class="w-full min-w-[1050px]"',
                'headAttributes' => ' class="bg-gray-50"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' id="accessPermissionsBody" class="divide-y text-sm"',
                'columns' => [
                    ['html' => 'عنوان و نام', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'منبع', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'عملیات', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'دامنه', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'خطر', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'نمایش در پنل', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => '', 'attributes' => ' class="p-4"'],
                ],
                'rows' => [],
            ]); ?></div></div></div>
