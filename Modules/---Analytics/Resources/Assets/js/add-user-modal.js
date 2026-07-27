let users = [];

function showSection(id) {
    document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'));
    document.getElementById(id).classList.remove('hidden');
}

function openAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
    updateFormFields();
}

function closeModal() {
    document.getElementById('addUserModal').classList.add('hidden');
}

function updateFormFields() {
    const type = document.getElementById('userType').value;
    const container = document.getElementById('dynamicFields');
    container.innerHTML = '';

    if (type === 'student') {
        container.innerHTML = `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-2">ساز</label>
                    <select class="w-full border border-gray-300 rounded-2xl py-3 px-5"><option>پیانو</option><option>گیتار</option><option>ویولن</option></select>
                </div>
                <div>
                    <label class="block text-sm mb-2">سطح</label>
                    <select class="w-full border border-gray-300 rounded-2xl py-3 px-5"><option>مبتدی</option><option>متوسط</option><option>پیشرفته</option></select>
                </div>
            </div>
        `;
    } else if (type === 'teacher') {
        container.innerHTML = `
            <div>
                <label class="block text-sm mb-2">تخصص‌ها (سازها)</label>
                <input type="text" placeholder="پیانو، گیتار، ..." class="w-full border border-gray-300 rounded-2xl py-3 px-5">
            </div>
        `;
    }
    // می‌توانی فیلدهای بیشتری برای منشی، مدیر و ... اضافه کنی
}

function saveUser() {
    const name = document.getElementById('fullName').value || 'کاربر جدید';
    const typeText = document.getElementById('userType').options[document.getElementById('userType').selectedIndex].text;
    
    users.push({ name, type: typeText, phone: '۰۹۱۲۳۴۵۶۷۸۹', status: 'فعال' });
    renderUsersTable();
    closeModal();
    alert('کاربر با موفقیت اضافه شد!');
}

function renderUsersTable() {
    const tbody = document.querySelector('#usersTable tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    users.forEach(user => {
        const tr = document.createElement('tr');
        tr.className = "border-b hover:bg-gray-50";
        tr.innerHTML = `
            <td class="py-4 font-medium">${user.name}</td>
            <td class="py-4">${user.type}</td>
            <td class="py-4">${user.phone}</td>
            <td class="py-4"><span class="px-4 py-1 bg-green-100 text-green-700 rounded-full text-xs">فعال</span></td>
            <td><button class="text-indigo-600 hover:underline">ویرایش</button></td>
        `;
        tbody.appendChild(tr);
    });
}

// Init
window.onload = () => {
    showSection('users');
    // داده نمونه
    users = [
        {name: "سارا احمدی", type: "هنرجو", phone: "۰۹۱۲۳۴۵۶۷۸۹", status: "فعال"},
        {name: "استاد موسوی", type: "استاد", phone: "۰۹۱۲۹۸۷۶۵۴۳", status: "فعال"}
    ];
    renderUsersTable();
};