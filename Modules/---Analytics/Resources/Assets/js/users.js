// js/users.js
export function loadUsersSection() {
    return `
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">مدیریت کاربران</h1>
            <button onclick="openAddUserModal()" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-3 transition">
                <i class="fas fa-plus"></i> 
                افزودن کاربر جدید
            </button>
        </div>

        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <div class="p-6 border-b">
                <input type="text" id="userSearchInput" 
                       placeholder="جستجو بر اساس نام یا شماره تماس..." 
                       onkeyup="filterUsers()"
                       class="w-full border border-gray-300 rounded-2xl py-3 px-5 focus:outline-none focus:border-indigo-500">
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="usersTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-right py-5 px-6 font-medium">نام و نام خانوادگی</th>
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
    `;
}

// داده‌های نمونه
let allUsers = [
    { id: 1, name: "سارا احمدی", type: "هنرجو", phone: "۰۹۱۲۳۴۵۶۷۸۹", status: "فعال" },
    { id: 2, name: "استاد محمد موسوی", type: "استاد", phone: "۰۹۱۲۹۸۷۶۵۴۳", status: "فعال" },
    { id: 3, name: "زهرا کریمی", type: "منشی", phone: "۰۹۱۳۴۵۶۷۸۹۰", status: "فعال" },
    { id: 4, name: "امیر رضایی", type: "هنرجو", phone: "۰۹۱۴۵۶۷۸۹۰۱", status: "بدهکار" }
];

export function renderUsersTable(users = allUsers) {
    const tbody = document.querySelector('#usersTable tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    users.forEach(user => {
        const tr = document.createElement('tr');
        tr.className = "hover:bg-gray-50 transition";
        tr.innerHTML = `
            <td class="py-5 px-6 font-medium">${user.name}</td>
            <td class="py-5 px-6">
                <span class="px-4 py-1.5 text-sm rounded-2xl bg-indigo-100 text-indigo-700">${user.type}</span>
            </td>
            <td class="py-5 px-6">${user.phone}</td>
            <td class="py-5 px-6">
                <span class="px-4 py-1.5 text-sm rounded-2xl ${user.status === 'فعال' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                    ${user.status}
                </span>
            </td>
            <td class="py-5 px-6 text-left">
                <button onclick="editUser(${user.id})" class="text-indigo-600 hover:text-indigo-800 ml-4">ویرایش</button>
                <button onclick="deleteUser(${user.id})" class="text-red-500 hover:text-red-700">حذف</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

window.filterUsers = function() {
    const term = document.getElementById('userSearchInput').value.trim().toLowerCase();
    const filtered = allUsers.filter(user => 
        user.name.toLowerCase().includes(term) || 
        user.phone.includes(term)
    );
    renderUsersTable(filtered);
};

window.openAddUserModal = function() {
    const modalHTML = `
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50" onclick="if(event.target === this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-lg mx-4 shadow-2xl" onclick="event.stopImmediatePropagation()">
                <div class="px-8 pt-6 pb-4 border-b flex items-center justify-between">
                    <h2 class="text-2xl font-bold">افزودن کاربر جدید</h2>
                    <button onclick="closeModal()" class="text-4xl leading-none text-gray-300 hover:text-gray-500">×</button>
                </div>
                
                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع کاربر</label>
                        <select id="modalUserType" onchange="updateModalFields()" 
                                class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:border-indigo-500 focus:outline-none">
                            <option value="student">هنرجو</option>
                            <option value="teacher">استاد</option>
                            <option value="secretary">منشی</option>
                            <option value="admin">مدیر</option>
                            <option value="staff">پرسنل</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">نام و نام خانوادگی</label>
                            <input id="modalFullName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">شماره تماس</label>
                            <input id="modalPhone" type="tel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        </div>
                    </div>

                    <div id="modalDynamicFields"></div>

                    <div class="flex gap-4 pt-6">
                        <button onclick="saveNewUser()" 
                                class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl hover:bg-indigo-700 font-medium">
                            ذخیره کاربر
                        </button>
                        <button onclick="closeModal()" 
                                class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50 font-medium">
                            انصراف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('modalContainer').innerHTML = modalHTML;
    updateModalFields();
};

function updateModalFields() {
    const type = document.getElementById('modalUserType').value;
    const container = document.getElementById('modalDynamicFields');
    
    if (type === 'student') {
        container.innerHTML = `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">ساز اصلی</label>
                    <select class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option>پیانو</option><option>گیتار</option><option>ویولن</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">سطح</label>
                    <select class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option>مبتدی</option><option>متوسط</option><option>پیشرفته</option>
                    </select>
                </div>
            </div>
        `;
    } else if (type === 'teacher') {
        container.innerHTML = `
            <div>
                <label class="block text-sm font-medium mb-2">تخصص‌ها</label>
                <input type="text" placeholder="پیانو، گیتار، تئوری موسیقی" 
                       class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
            </div>
        `;
    } else {
        container.innerHTML = '';
    }
}

window.saveNewUser = function() {
    const name = document.getElementById('modalFullName').value || "کاربر جدید";
    const typeText = document.getElementById('modalUserType').options[document.getElementById('modalUserType').selectedIndex].text;
    
    allUsers.unshift({
        id: Date.now(),
        name: name,
        type: typeText,
        phone: document.getElementById('modalPhone').value || "۰۹xxxxxxxxx",
        status: "فعال"
    });

    renderUsersTable();
    closeModal();
    alert('✅ کاربر با موفقیت ثبت شد');
};


window.editUser = function(id) {
    alert(`ویرایش کاربر با آیدی ${id} (بعداً کامل می‌شود)`);
};

window.deleteUser = function(id) {
    if (confirm('آیا از حذف این کاربر مطمئن هستید؟')) {
        allUsers = allUsers.filter(u => u.id !== id);
        renderUsersTable();
    }
};