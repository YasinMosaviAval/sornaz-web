<div id="approvals" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">تأییدها</h1>
            <p class="text-gray-500 mt-1">سوابق تأیید و رد آیتم‌ها توسط مدیران</p>
        </div>
        <button onclick="openAddApprovalModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن تأیید
        </button>
    </div>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="approvalsBranchTabs">
            <button onclick="filterApprovalsByBranch('all')" class="appr-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">همه شعبه‌ها</button>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <?php component('panel-table-template', [
                'attributes' => ' class="w-full min-w-[1100px]" id="approvalsTable"',
                'headAttributes' => ' class="bg-gray-50 border-b"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' class="divide-y text-sm"',
                'columns' => [
                    ['html' => 'عنوان', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'نوع موجودیت', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'عملیات', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'تأییدکننده', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'شعبه', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '', 'attributes' => ' class="w-36 py-5 px-5"'],
                ],
                'rows' => [],
            ]); ?>
        </div>
    </div>
</div>