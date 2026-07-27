// ==================== داده نمونه دسترسی‌ها (بر اساس تصاویر) ====================
let allPermissions = [
    { id: 1, name: "request_seen_language", title: "درخواست مشاهده زبان", title_en: "Request Seen Language", group: "زبان", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, name: "request_remove_language", title: "درخواست حذف زبان", title_en: "Request Remove Language", group: "زبان", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, name: "request_edit_language", title: "درخواست ویرایش زبان", title_en: "Request Edit Language", group: "زبان", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 4, name: "request_add_language", title: "درخواست اضافه کردن زبان", title_en: "Request Add Language", group: "زبان", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 5, name: "request_seen_lesson", title: "درخواست مشاهده درس", title_en: "Request Seen Lesson", group: "درس", branchId: 2, branchName: "شعبه ونک" },
    { id: 6, name: "request_remove_lesson", title: "درخواست حذف درس", title_en: "Request Remove Lesson", group: "درس", branchId: 2, branchName: "شعبه ونک" },
    { id: 7, name: "request_edit_lesson", title: "درخواست ویرایش درس", title_en: "Request Edit Lesson", group: "درس", branchId: 2, branchName: "شعبه ونک" },
    { id: 8, name: "request_add_lesson", title: "درخواست اضافه کردن درس", title_en: "Request Add Lesson", group: "درس", branchId: 2, branchName: "شعبه ونک" },
    { id: 9, name: "request_seen_instrument", title: "درخواست مشاهده ابزار", title_en: "Request Seen Instrument", group: "ابزار", branchId: 3, branchName: "شعبه سعادت‌آباد" },
    { id: 10, name: "request_remove_instrument", title: "درخواست حذف ابزار", title_en: "Request Remove Instrument", group: "ابزار", branchId: 3, branchName: "شعبه سعادت‌آباد" },
    { id: 11, name: "manage_students", title: "مدیریت هنرجویان", title_en: "Manage Students", group: "هنرجو", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 12, name: "manage_teachers", title: "مدیریت اساتید", title_en: "Manage Teachers", group: "استاد", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 13, name: "manage_finance", title: "مدیریت امور مالی", title_en: "Manage Finance", group: "مالی", branchId: 4, branchName: "شعبه کرج" },
    { id: 14, name: "view_reports", title: "مشاهده گزارش‌ها", title_en: "View Reports", group: "گزارش", branchId: 1, branchName: "شعبه مرکزی" }
];

let currentPermissionBranch = 'all';

window.renderPermissionsBranchTabs = function() {
    const container = document.getElementById('permissionsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.permission-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'permission-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterPermissionsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterPermissionsByBranch = function(branchId) {
    currentPermissionBranch = branchId;

    document.querySelectorAll('.permission-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.permission-branch-tab');
    if (branchId === 'all') {
        if (tabs[0]) {
            tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            tabs[0].classList.remove('border-gray-200');
        }
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }

    renderPermissionsTable();
};

window.renderPermissionsTable = function() {
    const tbody = document.querySelector('#permissionsTable tbody');
    if (!tbody) return;

    const list = currentPermissionBranch === 'all' 
        ? allPermissions 
        : allPermissions.filter(p => p.branchId == currentPermissionBranch);

    tbody.innerHTML = list.length === 0 
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">دسترسی‌ای یافت نشد</td></tr>`
        : list.map(p => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-4 font-medium">${p.name}</td>
                <td class="py-4 px-4">${p.title}</td>
                <td class="py-4 px-4 text-gray-500">${p.title_en}</td>
                <td class="py-4 px-4"><span class="px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">${p.group}</span></td>
                <td class="py-4 px-4">${p.branchName}</td>
                <td class="py-4 px-4 text-left">
                    <button onclick="editPermission(${p.id})" class="text-indigo-600 hover:underline text-sm ml-3">ویرایش</button>
                    <button onclick="deletePermission(${p.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            </tr>
        `).join('');
};

window.openAddPermissionModal = function() {
    if (!document.getElementById('modalContainer')) {
        alert('modalContainer پیدا نشد!');
        return;
    }

    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : [])
        .map(b => `<option value="${b.id}">${b.name}</option>`).join('');

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-md my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن دسترسی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام گروه</label>
                    <input id="permGroup" type="text" placeholder="مثال: درس / زبان / هنرجو" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نام *</label>
                    <input id="permName" type="text" placeholder="مثال: request_add_lesson" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان *</label>
                    <input id="permTitle" type="text" placeholder="مثال: درخواست اضافه کردن درس" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان انگلیسی</label>
                    <input id="permTitleEn" type="text" placeholder="مثال: Request Add Lesson" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="permBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="savePermission()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ثبت مجوز</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.savePermission = function() {
    const name = document.getElementById('permName')?.value.trim();
    const title = document.getElementById('permTitle')?.value.trim();
    if (!name || !title) return alert('نام و عنوان الزامی است');

    const branchId = parseInt(document.getElementById('permBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allPermissions.push({
        id: Date.now(),
        name,
        title,
        title_en: document.getElementById('permTitleEn').value || name,
        group: document.getElementById('permGroup').value || 'عمومی',
        branchId,
        branchName: branch ? branch.name : 'نامشخص'
    });

    filterPermissionsByBranch(currentPermissionBranch);
    closeModal();
    alert('✅ دسترسی ثبت شد');
};

window.editPermission = function(id) {
    alert('ویرایش دسترسی');
};

window.deletePermission = function(id) {
    if (confirm('حذف این دسترسی؟')) {
        allPermissions = allPermissions.filter(p => p.id !== id);
        filterPermissionsByBranch(currentPermissionBranch);
    }
};

// Init
setTimeout(() => {
    if (document.getElementById('permissionsTable')) {
        renderPermissionsBranchTabs();
        filterPermissionsByBranch('all');
    }
}, 200);