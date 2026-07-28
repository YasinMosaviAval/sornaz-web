<div id="users" class="section hidden">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">مدیریت کاربران</h1>
        <button onclick="openAddUserModal()" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-3">
            <i class="fas fa-plus"></i> افزودن کاربر جدید
        </button>
    </div>
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="p-6 border-b">
            <input type="text" id="userSearchInput" placeholder="جستجو بر اساس نام یا شماره..." 
                onkeyup="filterUsers()" 
                class="w-full border border-gray-300 rounded-2xl py-3 px-5 focus:outline-none focus:border-indigo-500">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="usersTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-right py-5 px-6 font-medium">نام</th>
                        <th class="text-right py-5 px-6 font-medium">نوع کاربر</th>
                        <th class="text-right py-5 px-6 font-medium">شماره تماس</th>
                        <th class="text-right py-5 px-6 font-medium">وضعیت</th>
                        <th class="w-32 py-5 px-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y"></tbody>
            </table>
        </div>
    </div>
</div>