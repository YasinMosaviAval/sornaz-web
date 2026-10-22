<?php
$classroomTypeUser=auth()->user();
$classroomTypeUserId=(int)($classroomTypeUser['user_id']??0);
$classroomTypeAdmin=\Modules\System\Services\SiteAdminAccess::allows($classroomTypeUser);
$classroomTypeAcademyOwner=$classroomTypeUserId&&(\Core\database\DB::table('academies')->where('user_id',$classroomTypeUserId)->whereNull('deleted_at')->first()||\Core\database\DB::table('academies')->where('created_by',$classroomTypeUserId)->whereNull('deleted_at')->first());
$classroomTypeAcademyManager=$classroomTypeUserId&&(bool)\Core\database\DB::table('academy_branch_members')->join('academy_branch_member_roles','academy_branch_member_roles.member_id','=','academy_branch_members.member_id')->join('access_system_roles','access_system_roles.role_id','=','academy_branch_member_roles.role_id')->where('academy_branch_members.user_id',$classroomTypeUserId)->whereRaw("access_system_roles.name LIKE 'academy_%manager%' AND access_system_roles.name NOT LIKE '%branch%'")->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
$canViewClassroomTypes=$classroomTypeAdmin||$classroomTypeAcademyOwner||$classroomTypeAcademyManager||($classroomTypeUserId&&(new \Modules\System\Services\AccessControl())->allows($classroomTypeUserId,\Modules\Academy\Services\AcademyClassroomService::CREATE_TYPE_PERMISSION));
$canViewClassroomTypes = $canViewClassroomTypes || (new \Modules\Analytics\Services\MobilePanelAccess())->capabilities($classroomTypeUser)['branchContext'];
if(!$canViewClassroomTypes)return;
?>
<section id="classroom-types" class="section hidden">
    <h1 class="hidden">انواع کلاس</h1>
    <?php component('classroom-page-tabs', ['active' => 'classroom-types']); ?>
    <p class="mb-6 text-gray-500">مدیریت انواع کلاس‌های فیزیکی</p>
    <div class="mb-6 flex justify-end"><button id="addClassroomTypeButton" onclick="openClassroomTypeAdmin()" class="rounded-xl bg-indigo-600 px-5 py-2 text-white">+ نوع جدید</button></div>
    <div class="mb-6 overflow-x-auto rounded-3xl bg-white p-3 shadow-sm"><div id="classroomTypesBranchTabs" class="flex min-w-max gap-2"><button type="button" data-value="all" class="rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white">همه</button></div></div>
    <div class="mb-6 rounded-3xl bg-white p-5 shadow-sm">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label class="mb-1 block text-xs text-gray-500">جست‌وجو</label>
            <input id="classroomTypeSearch" type="search" oninput="filterClassroomTypes()" placeholder="عنوان یا نام ایجادکننده" class="w-full rounded-2xl border border-gray-300 px-4 py-3">
        </div>
        <div id="classroomTypeStatusFilterWrapper">
            <label class="mb-1 block text-xs text-gray-500">وضعیت</label>
            <select id="classroomTypeStatusFilter" onchange="filterClassroomTypes()" class="w-full rounded-2xl border border-gray-300 px-4 py-3">
                <option value="">همه وضعیت‌ها</option><option value="approved">تأیید شده</option><option value="pending">در انتظار تأیید</option><option value="rejected">رد شده</option>
            </select>
        </div>
        <div id="classroomTypeCategoryFilterWrapper">
            <label class="mb-1 block text-xs text-gray-500">دسته</label>
            <div class="flex gap-2"><select id="classroomTypeCategoryFilter" onchange="filterClassroomTypes()" class="min-w-0 flex-1 rounded-2xl border border-gray-300 px-4 py-3"><option value="">همه دسته‌ها</option></select><button id="editClassroomTypeCategoryButton" type="button" onclick="editSelectedClassroomTypeCategory('classroomTypeCategoryFilter')" class="hidden rounded-xl border border-indigo-200 px-3 text-indigo-600" title="ویرایش دسته انتخاب‌شده"><i class="fas fa-pen"></i></button><button id="deleteClassroomTypeCategoryButton" type="button" onclick="deleteSelectedClassroomTypeCategory('classroomTypeCategoryFilter')" class="hidden rounded-xl border border-red-200 px-3 text-red-600" title="حذف دسته انتخاب‌شده"><i class="fas fa-trash"></i></button></div>
        </div>
      </div>
    </div>
    <div class="overflow-x-auto rounded-3xl bg-white shadow">
        <?php component('panel-table-template', [
                'attributes' => ' class="w-full"',
                'headAttributes' => ' class="bg-gray-50"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' id="classroomTypesBody"',
                'columns' => [
                    ['html' => '<button onclick="sortClassroomTypes(\'createdByName\')">ایجادکننده <span id="classroomTypeSort-createdByName">↕</span></button>', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => '<button onclick="sortClassroomTypes(\'title\')">عنوان <span id="classroomTypeSort-title">↕</span></button>', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => '<button onclick="sortClassroomTypes(\'summary\')">خلاصه <span id="classroomTypeSort-summary">↕</span></button>', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => '<button onclick="sortClassroomTypes(\'typeLabel\')">دسته <span id="classroomTypeSort-typeLabel">↕</span></button>', 'attributes' => ' id="classroomTypeCategoryHeading" class="p-4 text-right"'],
                    ['html' => '<button onclick="sortClassroomTypes(\'statusLabel\')">وضعیت <span id="classroomTypeSort-statusLabel">↕</span></button>', 'attributes' => ' id="classroomTypeStatusHeading" class="p-4 text-right"'],
                    ['html' => '', 'attributes' => ' class="p-4"'],
                ],
                'rows' => [],
            ]); ?>
    </div>
</section>
