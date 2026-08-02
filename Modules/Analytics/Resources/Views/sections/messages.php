<div id="messages" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مدیریت پیام‌ها</h1>
            <p class="text-gray-500 mt-1">پیام‌های داخلی شعبه‌ها و سیستم</p>
        </div>
        <button onclick="openAddMessageModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> ارسال پیام جدید
        </button>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="messagesBranchTabs">
            <button type="button" onclick="filterMessagesByBranch('all')"
                    class="message-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white"
                    data-value="all">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="text" id="messageSearch" placeholder="جستجو عنوان / فرستنده / گیرنده..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterMessages()">
            <select id="filterMessageStatus" onchange="filterMessages()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="خوانده‌نشده">خوانده‌نشده</option>
                <option value="خوانده‌شده">خوانده‌شده</option>
            </select>
            <select id="filterMessagePriority" onchange="filterMessages()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه اولویت‌ها</option>
                <option value="عادی">عادی</option>
                <option value="مهم">مهم</option>
                <option value="فوری">فوری</option>
            </select>
            <select id="filterMessageType" onchange="filterMessages()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
                <option value="اطلاعیه">اطلاعیه</option>
                <option value="یادآوری">یادآوری</option>
                <option value="هشدار">هشدار</option>
                <option value="شخصی">شخصی</option>
            </select>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="messagesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMessagesBy('title')" class="flex items-center gap-1">عنوان <span id="msgSortIcon-title">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMessagesBy('sender')" class="flex items-center gap-1">فرستنده <span id="msgSortIcon-sender">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMessagesBy('branchName')" class="flex items-center gap-1">شعبه <span id="msgSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMessagesBy('receiver')" class="flex items-center gap-1">گیرنده <span id="msgSortIcon-receiver">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMessagesBy('type')" class="flex items-center gap-1">نوع <span id="msgSortIcon-type">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMessagesBy('date')" class="flex items-center gap-1">تاریخ <span id="msgSortIcon-date">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMessagesBy('status')" class="flex items-center gap-1">وضعیت <span id="msgSortIcon-status">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="messagesPaginationInfo">نمایش ۱ تا ۱۰ از ۰ پیام</span>
            <div class="flex items-center gap-2" id="messagesPaginationButtons"></div>
        </div>
    </div>
</div>
