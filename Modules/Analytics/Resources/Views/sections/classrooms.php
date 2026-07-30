<div id="classrooms" class="section hidden">
    <style>
        .classroom-inline-expand { animation: classroomSlideDown 220ms ease-out forwards; }
        @keyframes classroomSlideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مدیریت کلاس‌های فیزیکی</h1>
            <p class="text-gray-500 mt-1">لیست کلاس‌ها، نوع، تجهیزات و وضعیت هر شعبه</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddClassroomModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2 transition">
                <i class="fas fa-plus"></i> افزودن کلاس جدید
            </button>
            <button onclick="exportClassroomsToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportClassroomsToPDF()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="classroomBranchTabs">
            <button onclick="filterClassroomsByBranch('all')"
                    class="branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" id="classroomSearch" placeholder="جستجو نام کلاس..."
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                       onkeyup="filterClassrooms()">
            </div>
            <div>
                <select id="filterClassroomStatus" onchange="filterClassrooms()"
                        class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="فعال">فعال</option>
                    <option value="تعمیر">تعمیر</option>
                    <option value="غیرفعال">غیرفعال</option>
                </select>
            </div>
            <div>
                <select id="filterClassroomEquipment" onchange="filterClassrooms()"
                        class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه تجهیزات</option>
                </select>
            </div>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="classroomsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortClassroomsBy('name')" class="flex items-center gap-1">
                                نام کلاس <span id="classroomSortIcon-name">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortClassroomsBy('typeLabel')" class="flex items-center gap-1">
                                نوع کلاس <span id="classroomSortIcon-typeLabel">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortClassroomsBy('branchName')" class="flex items-center gap-1">
                                شعبه <span id="classroomSortIcon-branchName">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortClassroomsBy('capacity')" class="flex items-center gap-1">
                                ظرفیت <span id="classroomSortIcon-capacity">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortClassroomsBy('equipment')" class="flex items-center gap-1">
                                تجهیزات <span id="classroomSortIcon-equipment">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortClassroomsBy('status')" class="flex items-center gap-1">
                                وضعیت <span id="classroomSortIcon-status">↕</span>
                            </button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>

        <!-- صفحه‌بندی -->
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="classroomPaginationInfo">نمایش ۱ تا ۱۰ از ۴۰ کلاس</span>
            <div class="flex items-center gap-2" id="classroomPaginationButtons"></div>
        </div>
    </div>
</div>
