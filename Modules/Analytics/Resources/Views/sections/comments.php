<div id="comments" class="section hidden">
    <?php component('posts-page-tabs', ['active'=>'comments']); ?>
    <?php
    $viewModeTableId='commentsTableViewButton'; $viewModeCardsId='commentsCardViewButton';
    $viewModeTableAttributes='onclick="setCommentsView(\'table\')"';
    $viewModeCardsAttributes='onclick="setCommentsView(\'cards\')"';
    require __DIR__.'/view-mode-toggle.php';
    ?>

    <div class="mb-4 flex flex-wrap gap-2 rounded-3xl bg-white p-3 shadow-sm"><?php foreach(['all'=>'همه','pending'=>'در انتظار','approved'=>'تأییدشده','rejected'=>'ردشده']as$key=>$label): ?><button data-comment-status="<?= $key ?>" onclick="filterCommentsByStatus('<?= $key ?>')" class="comment-status-tab rounded-xl px-4 py-2 text-sm <?= $key==='all'?'bg-indigo-600 text-white':'border' ?>"><?= $label ?> <span data-comment-status-count="<?= $key ?>" class="opacity-60">0</span></button><?php endforeach; ?></div>
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm"><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><input id="commentSearch" oninput="filterAdminComments()" placeholder="<?= e(locale()==='en' ? 'Search comments, author or email...' : 'جستجو در دیدگاه، نام یا ایمیل...') ?>" class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"></div></div>
    <div id="commentsCards" class="hidden grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6"></div>
    <div id="commentsTableWrap" class="overflow-hidden rounded-3xl bg-white shadow"><div class="overflow-x-auto"><?php component('panel-table-template', [
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
            ]); ?></div></div><?php component('panel-table-pagination', ['id'=>'commentsPagination']); ?>
</div>
