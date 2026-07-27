<div id="academy-requests" class="section hidden">
    <div class="max-w-xl mx-auto mb-12">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold">فرم درخواست ثبت آموزشگاه</h1>
            <p class="text-gray-500 mt-2">پس از بررسی، نتیجه از طریق ایمیل اعلام می‌شود.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">ایمیل *</label>
                    <input id="reqEmail" type="email" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نام کاربری *</label>
                    <input id="reqUsername" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">رمز عبور *</label>
                    <input id="reqPassword" type="password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تکرار رمز عبور *</label>
                    <input id="reqPassword2" type="password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">نام آموزشگاه *</label>
                <input id="reqAcademyName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">توضیح کوتاه</label>
                <input id="reqShortDesc" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"
                       placeholder="یک جمله درباره آموزشگاه">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">بیوگرافی</label>
                <textarea id="reqBio" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"
                          placeholder="تاریخچه، سبک تدریس، شعب و ..."></textarea>
            </div>
            <div class="flex gap-4">
                <button onclick="submitAcademyRequest()"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">
                    ثبت آموزشگاه
                </button>
                <button onclick="showSection('academies')"
                        class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">
                    لغو
                </button>
            </div>
        </div>
    </div>

    <!-- صف بررسی درخواست‌ها (ادمین) -->
    <div class="max-w-4xl mx-auto">
        <h2 class="text-xl font-bold mb-4">درخواست‌های در انتظار بررسی</h2>
        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <table class="w-full min-w-[700px]" id="academyRequestsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-4 px-5 font-medium">آموزشگاه</th>
                        <th class="text-right py-4 px-5 font-medium">ایمیل / کاربر</th>
                        <th class="text-right py-4 px-5 font-medium">وضعیت</th>
                        <th class="w-40"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
    </div>
</div>