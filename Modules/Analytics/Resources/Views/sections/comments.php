<div id="comments" class="section hidden">
    <div class="mb-6"><h1 class="text-3xl font-bold">دیدگاه‌ها</h1><p class="mt-1 text-gray-500">بررسی، پاسخ و مدیریت دیدگاه‌های نوشته‌ها</p></div>
    <div class="mb-4 flex flex-wrap gap-2 rounded-3xl bg-white p-3 shadow-sm"><?php foreach(['all'=>'همه','pending'=>'در انتظار','approved'=>'تأییدشده','rejected'=>'ردشده']as$key=>$label): ?><button data-comment-status="<?= $key ?>" onclick="filterCommentsByStatus('<?= $key ?>')" class="comment-status-tab rounded-xl px-4 py-2 text-sm <?= $key==='all'?'bg-gray-900 text-white':'border' ?>"><?= $label ?> <span data-comment-status-count="<?= $key ?>" class="opacity-60">0</span></button><?php endforeach; ?></div>
    <div class="mb-6 grid grid-cols-1 gap-4 rounded-3xl bg-white p-4 shadow-sm md:grid-cols-2"><input id="commentSearch" onkeyup="filterAdminComments()" placeholder="جستجو در دیدگاه، نام یا ایمیل..." class="rounded-2xl border px-4 py-3"><select id="commentPostFilter" onchange="filterAdminComments()" class="rounded-2xl border px-4 py-3"><option value="">همه نوشته‌ها</option></select></div>
    <div class="overflow-hidden rounded-3xl bg-white shadow"><div class="overflow-x-auto"><?php component('panel-table-template', [
                'attributes' => ' id="adminCommentsTable" class="w-full min-w-[1050px]"',
                'headAttributes' => ' class="border-b bg-gray-50"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' class="divide-y text-sm"',
                'columns' => [
                    ['html' => 'نویسنده', 'attributes' => ' class="p-5 text-right"'],
                    ['html' => 'دیدگاه', 'attributes' => ' class="p-5 text-right"'],
                    ['html' => 'در پاسخ به', 'attributes' => ' class="p-5 text-right"'],
                    ['html' => 'وضعیت', 'attributes' => ' class="p-5 text-right"'],
                    ['html' => 'تاریخ', 'attributes' => ' class="p-5 text-right"'],
                    ['html' => '', 'attributes' => ' class="p-5"'],
                ],
                'rows' => [],
            ]); ?></div><div class="flex flex-col gap-3 border-t p-4 text-sm text-gray-500 md:flex-row md:items-center md:justify-between"><div class="flex items-center gap-3"><span id="commentsPaginationInfo"></span><select id="commentsPerPage" onchange="changeCommentsPerPage(this.value)" class="rounded-xl border px-3 py-2"><option>10</option><option selected>20</option><option>30</option><option>50</option><option>100</option></select></div><div id="commentsPagination" class="flex gap-2"></div></div></div>
</div>
