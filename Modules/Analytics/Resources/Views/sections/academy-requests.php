<div id="academy-requests" class="section hidden">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-xl font-bold mb-4">درخواست‌های در انتظار بررسی</h2>
        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <?php component('panel-table-template', [
                'attributes' => ' class="w-full min-w-[700px]" id="academyRequestsTable"',
                'headAttributes' => ' class="bg-gray-50 border-b"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' class="divide-y text-sm"',
                'columns' => [
                    ['html' => 'آموزشگاه', 'attributes' => ' class="text-right py-4 px-5 font-medium"'],
                    ['html' => 'ایمیل / کاربر', 'attributes' => ' class="text-right py-4 px-5 font-medium"'],
                    ['html' => 'وضعیت', 'attributes' => ' class="text-right py-4 px-5 font-medium"'],
                    ['html' => '', 'attributes' => ' class="w-40"'],
                ],
                'rows' => [],
            ]); ?>
        </div>
    </div>
</div>
