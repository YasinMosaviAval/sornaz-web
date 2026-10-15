<div id="course-levels" class="section hidden">
    <h1 class="hidden">سطح درس‌ها</h1>
    <?php component('lesson-page-tabs', ['active' => 'course-levels']); ?>
    <p class="mb-6 text-gray-500">مدیریت سطح‌های آموزشی قابل انتخاب برای درس‌ها</p>
    <div class="mb-6 flex justify-end"><button onclick="openCourseLevelModal()" class="rounded-2xl bg-indigo-600 px-6 py-3 text-white hover:bg-indigo-700"><i class="fas fa-plus ml-2"></i>افزودن سطح</button></div>
    <div class="overflow-hidden rounded-3xl bg-white shadow"><div class="overflow-x-auto"><?php component('panel-table-template', [
                'attributes' => ' class="w-full min-w-[640px]"',
                'headAttributes' => ' class="border-b bg-gray-50"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' id="courseLevelsBody" class="divide-y"',
                'columns' => [
                    ['html' => 'عنوان', 'attributes' => ' class="px-5 py-4 text-right"'],
                    ['html' => 'خلاصه', 'attributes' => ' class="px-5 py-4 text-right"'],
                    ['html' => '', 'attributes' => ' class="px-5 py-4"'],
                ],
                'rows' => [],
            ]); ?></div></div>
</div>
