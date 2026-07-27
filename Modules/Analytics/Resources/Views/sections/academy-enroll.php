<div id="academy-enroll" class="section hidden">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold">ثبت‌نام در کلاس</h1>
            <p class="text-gray-500 mt-2">پس از ثبت‌نام، با شما تماس گرفته می‌شود تا زمان کلاس نهایی شود.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 space-y-5">
            <div>
                <label class="block text-sm font-medium mb-2">آموزشگاه *</label>
                <select id="enrollAcademy" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">دوره / کلاس مورد نظر *</label>
                <select id="enrollCourse" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <option value="">انتخاب کنید</option>
                    <option value="piano-beginner">پیانو مبتدی</option>
                    <option value="guitar">گیتار کلاسیک</option>
                    <option value="violin">ویولن</option>
                    <option value="theory">تئوری موسیقی</option>
                    <option value="vocal">آواز</option>
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                    <input id="enrollName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شماره موبایل *</label>
                    <input id="enrollPhone" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">ایمیل</label>
                <input id="enrollEmail" type="email" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">سطح فعلی</label>
                <select id="enrollLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <option value="beginner">مبتدی</option>
                    <option value="intermediate">متوسط</option>
                    <option value="advanced">پیشرفته</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">توضیحات / درخواست خاص</label>
                <textarea id="enrollNote" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"
                          placeholder="زمان ترجیحی، اهداف یادگیری و ..."></textarea>
            </div>
            <button onclick="submitAcademyEnroll()"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">
                ثبت‌نام در کلاس
            </button>
        </div>

        <div class="mt-10">
            <h2 class="text-xl font-bold mb-4">درخواست‌های ثبت‌نام اخیر</h2>
            <div class="bg-white rounded-3xl shadow overflow-hidden">
                <table class="w-full min-w-[600px]" id="enrollmentsTable">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-right py-4 px-5 font-medium">نام</th>
                            <th class="text-right py-4 px-5 font-medium">آموزشگاه / دوره</th>
                            <th class="text-right py-4 px-5 font-medium">وضعیت</th>
                            <th class="w-28"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>