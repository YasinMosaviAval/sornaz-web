<div id="contact-us" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold">تماس با ما</h1>
            <p class="text-gray-500 mt-1">تنظیمات صفحه تماس و پیام‌های دریافتی</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- تنظیمات صفحه -->
        <div class="lg:col-span-1 bg-white rounded-3xl p-6 shadow-sm h-fit">
            <h2 class="font-bold text-lg mb-4">تنظیمات صفحه</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-500 block mb-1">عنوان صفحه</label>
                    <input id="contactPageTitle" type="text" value="ارتباط با ما - ارسال پیام جدید"
                           class="w-full border border-gray-200 rounded-2xl py-3 px-4">
                </div>
                <div>
                    <label class="text-sm text-gray-500 block mb-1">متن راهنما</label>
                    <textarea id="contactPageHint" rows="3" class="w-full border border-gray-200 rounded-2xl py-3 px-4">بعد از مشاهده پیام، در اولین فرصت پاسخ شما را می‌دهیم.</textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-500 block mb-1">ایمیل دریافت</label>
                    <input id="contactReceiveEmail" type="email" placeholder="info@academy.com"
                           class="w-full border border-gray-200 rounded-2xl py-3 px-4">
                </div>
                <button onclick="saveContactSettings()" class="w-full bg-indigo-600 text-white py-3 rounded-2xl">ذخیره تنظیمات</button>
            </div>
        </div>

        <!-- پیام‌های دریافتی -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h2 class="font-bold text-lg">پیام‌های دریافتی</h2>
                    <span class="text-sm text-gray-400" id="contactMsgCount">0 پیام</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px]" id="contactMessagesTable">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-right py-4 px-5 font-medium">فرستنده</th>
                                <th class="text-right py-4 px-5 font-medium">موضوع</th>
                                <th class="text-right py-4 px-5 font-medium">تاریخ</th>
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
</div>