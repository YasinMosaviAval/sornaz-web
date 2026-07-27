<div id="experiences" class="section">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">تجربه‌ها</h1>
            <p class="text-gray-500 mt-1">سوابق و تجربیات آموزشی و حرفه‌ای</p>
        </div>
        <button onclick="openAddExperienceModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن تجربه
        </button>
    </div>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="experiencesBranchTabs">
            <button onclick="filterExperiencesByBranch('all')" class="exp-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]" id="experiencesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">عنوان</th>
                        <th class="text-right py-5 px-5 font-medium">سازمان</th>
                        <th class="text-right py-5 px-5 font-medium">تاریخ شروع</th>
                        <th class="text-right py-5 px-5 font-medium">تاریخ پایان</th>
                        <th class="text-right py-5 px-5 font-medium">شعبه</th>
                        <th class="w-32 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
    </div>
</div>