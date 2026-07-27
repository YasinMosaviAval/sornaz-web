<div id="addUserModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-lg mx-4 modal">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-2xl font-bold">افزودن کاربر جدید</h2>
            <button onclick="closeModal()" class="text-3xl text-gray-400 hover:text-gray-600">×</button>
        </div>
        
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm mb-2 font-medium">نوع کاربر</label>
                <select id="userType" onchange="updateFormFields()" 
                        class="w-full border border-gray-300 rounded-2xl py-3 px-5 focus:outline-none focus:border-indigo-500">
                    <option value="student">هنرجو</option>
                    <option value="teacher">استاد</option>
                    <option value="secretary">منشی</option>
                    <option value="admin">مدیر</option>
                    <option value="staff">پرسنل</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-2">نام و نام خانوادگی</label>
                    <input id="fullName" type="text" class="w-full border border-gray-300 rounded-2xl py-3 px-5">
                </div>
                <div>
                    <label class="block text-sm mb-2">شماره تماس</label>
                    <input id="phone" type="tel" class="w-full border border-gray-300 rounded-2xl py-3 px-5">
                </div>
            </div>

            <!-- فیلدهای داینامیک -->
            <div id="dynamicFields"></div>

            <div class="flex gap-4 pt-4">
                <button onclick="saveUser()" 
                        class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl hover:bg-indigo-700">
                    ذخیره کاربر
                </button>
                <button onclick="closeModal()" 
                        class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">
                    انصراف
                </button>
            </div>
        </div>
    </div>
</div>
