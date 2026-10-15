<div id="ratings" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">رتبه‌بندی‌ها</h1>
            <p class="text-gray-500 mt-1">امتیاز و نظرات کاربران به آیتم‌ها</p>
        </div>
        <button onclick="openAddRatingModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن رتبه‌بندی
        </button>
    </div>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="ratingsBranchTabs">
            <button onclick="filterRatingsByBranch('all')" class="rating-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">همه شعبه‌ها</button>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <?php component('panel-table-template', [
                'attributes' => ' class="w-full min-w-[1100px]" id="ratingsTable"',
                'headAttributes' => ' class="bg-gray-50 border-b"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' class="divide-y text-sm"',
                'columns' => [
                    ['html' => 'عنوان', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'نوع آیتم', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'امتیاز', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'ناشناس / خصوصی', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'شعبه', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '', 'attributes' => ' class="w-36 py-5 px-5"'],
                ],
                'rows' => [],
            ]); ?>
        </div>
    </div>
</div>