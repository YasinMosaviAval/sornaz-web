<div id="branches" class="section hidden" data-csrf="<?= e(csrf_token()) ?>">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold">مدیریت شعبه‌ها</h1>
            <p class="text-gray-500 mt-1">شعبه‌های مختلف آموزشگاه و اطلاعات آن‌ها</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddBranchModal()"
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

    <!-- فیلتر -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <select id="filterBranchPhysicalType" onchange="filterBranches()"
                        class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه انواع ارائه</option>
                </select>
            </div>
        </div>
    </div>

    <!-- کارت‌های شعبه‌ها -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="branchesCards"></div>
</div>
