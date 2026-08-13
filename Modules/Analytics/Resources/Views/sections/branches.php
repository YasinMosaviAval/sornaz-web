<div id="branches" class="section hidden" data-branches-root data-csrf="<?= e(csrf_token()) ?>">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold">مدیریت شعبه‌ها</h1>
            <p id="branchesScopeDescription" class="text-gray-500 mt-1">شعبه‌های مختلف آموزشگاه و اطلاعات آن‌ها</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button id="addBranchButton" onclick="openAddBranchModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن شعبه جدید
            </button>
            <button onclick="exportBranchesToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="openBranchesPDFOptionsModal()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="rounded-3xl border border-violet-100 bg-violet-50 p-5"><span class="text-sm text-violet-700">تعداد آموزشگاه‌ها</span><strong id="branchesAcademiesCount" class="mt-2 block text-3xl text-violet-900">۰</strong></div>
        <div class="rounded-3xl border border-indigo-100 bg-indigo-50 p-5"><span class="text-sm text-indigo-700">تعداد شعبه‌ها</span><strong id="branchesCount" class="mt-2 block text-3xl text-indigo-900">۰</strong></div>
    </div>

    <div class="mb-5 flex gap-2">
        <button id="branchesTableViewButton" onclick="setBranchesView('table')" class="rounded-xl border px-4 py-2 text-sm"><i class="fas fa-table ml-1"></i>نمایش جدولی</button>
        <button id="branchesCardViewButton" onclick="setBranchesView('cards')" class="rounded-xl border px-4 py-2 text-sm"><i class="fas fa-th-large ml-1"></i>نمایش کارتی</button>
    </div>

    <!-- فیلتر -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            <div>
                <input type="text" id="branchSearch" placeholder="جستجو نام شعبه / مدیر..."
                       onkeyup="filterBranches()"
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <select id="filterBranchType" onchange="filterBranches()"
                        class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه انواع آموزشی</option>
                </select>
            </div>
            <div>
                <select id="filterBranchAcademy" onchange="filterBranches()"
                        class="hidden w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه آموزشگاه‌ها</option>
                </select>
            </div>
            <div>
                <select id="filterBranchPhysicalType" onchange="filterBranches()"
                        class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه انواع ارائه</option>
                </select>
            </div>
            <div>
                <select id="filterBranchStatus" onchange="filterBranches()"
                        class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="فعال">فعال</option>
                    <option value="غیرفعال">غیرفعال</option>
                </select>
            </div>
        </div>
    </div>

    <!-- کارت‌های شعبه‌ها -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="branchesCards"></div>
    <div id="branchesTableWrap" class="hidden overflow-x-auto rounded-3xl border border-gray-100 bg-white shadow-sm">
        <table class="w-full min-w-[900px] text-sm"><thead class="bg-gray-50"><tr>
            <?php foreach ([['academy_name','آموزشگاه'],['name','شعبه'],['type','نوع'],['physical_type','ارائه'],['manager','مدیر'],['status','وضعیت']] as [$field,$label]): ?>
                <th class="p-4 text-right"><button type="button" onclick="sortBranchesBy('<?= $field ?>')" class="flex items-center gap-2 font-semibold"><?= $label ?><span id="branchSortIcon-<?= $field ?>">↕</span></button></th>
            <?php endforeach; ?>
            <th class="p-4 text-right"><button type="button" onclick="sortBranchesBy('id')" class="flex items-center gap-2 font-semibold">عملیات<span id="branchSortIcon-id">↕</span></button></th></tr></thead><tbody id="branchesTableBody"></tbody></table>
    </div>
    <div id="branchesPagination" class="mt-5 flex flex-col items-center justify-between gap-3 rounded-2xl border border-gray-100 bg-white p-4 sm:flex-row"><div id="branchesPaginationSummary" class="text-sm text-gray-500"></div><div class="flex items-center gap-2"><select id="branchesPageSize" onchange="changeBranchesPageSize(this.value)" class="rounded-xl border px-3 py-2"><option>10</option><option selected>20</option><option>50</option><option>100</option></select><button onclick="changeBranchesPage(-1)" id="branchesPrevPage" class="rounded-xl border px-4 py-2">قبلی</button><span id="branchesPageLabel" class="text-sm"></span><button onclick="changeBranchesPage(1)" id="branchesNextPage" class="rounded-xl border px-4 py-2">بعدی</button></div></div>
</div>
