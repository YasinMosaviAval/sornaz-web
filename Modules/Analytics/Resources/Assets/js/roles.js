// ==================== داده نمونه نقش‌ها (بر اساس تصاویر) ====================
let allRoles = [
    { id: 1, name: "user", title: "کاربر عادی", title_en: "user", type: "سیستم", color: "#6B7280", order: 1, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, name: "verified_teacher", title: "استاد تأییدشده", title_en: "verified_teacher", type: "سیستم", color: "#8B5CF6", order: 2, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, name: "vip_member", title: "عضو ویژه", title_en: "vip_member", type: "سیستم", color: "#F59E0B", order: 3, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 4, name: "academy_student", title: "دانش‌آموز", title_en: "academy_student", type: "سیستم", color: "#10B981", order: 4, branchId: 2, branchName: "شعبه ونک" },
    { id: 5, name: "academy_teacher", title: "استاد", title_en: "academy_teacher", type: "سیستم", color: "#06B6D4", order: 5, branchId: 2, branchName: "شعبه ونک" },
    { id: 6, name: "academy_receptionist", title: "منشی آموزشگاه", title_en: "academy_receptionist", type: "سیستم", color: "#3B82F6", order: 6, branchId: 3, branchName: "شعبه سعادت‌آباد" },
    { id: 7, name: "academy_manager", title: "مدیر آموزشگاه", title_en: "academy_manager", type: "سیستم", color: "#4B5563", order: 7, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 8, name: "academy_owner", title: "مالک آموزشگاه", title_en: "academy_owner", type: "سیستم", color: "#A855F7", order: 8, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 9, name: "financial_manager", title: "مدیر مالی", title_en: "financial_manager", type: "سیستم", color: "#14B8A6", order: 9, branchId: 4, branchName: "شعبه کرج" },
    { id: 10, name: "content_manager", title: "مدیر محتوا", title_en: "content_manager", type: "سیستم", color: "#EC4899", order: 10, branchId: 3, branchName: "شعبه سعادت‌آباد" },
    { id: 11, name: "support", title: "پشتیبانی", title_en: "support", type: "سیستم", color: "#F97316", order: 11, branchId: 2, branchName: "شعبه ونک" },
    { id: 12, name: "admin", title: "مدیر سایت", title_en: "admin", type: "سیستم", color: "#EF4444", order: 12, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 13, name: "superadmin", title: "مدیر کل پلتفرم", title_en: "superadmin", type: "سیستم", color: "#DC2626", order: 13, branchId: 1, branchName: "شعبه مرکزی" }
];

let currentRoleBranch = 'all';

window.renderRolesBranchTabs = function() {
    const container = document.getElementById('rolesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.role-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'role-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterRolesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterRolesByBranch = function(branchId) {
    currentRoleBranch = branchId;

    document.querySelectorAll('.role-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.role-branch-tab');
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

    renderRolesTable();
};

window.renderRolesTable = function() {
    const tbody = document.querySelector('#rolesTable tbody');
    if (!tbody) return;

    const list = currentRoleBranch === 'all' 
        ? allRoles 
        : allRoles.filter(r => r.branchId == currentRoleBranch);

    tbody.innerHTML = list.length === 0 
        ? `<tr><td colspan="8" class="py-12 text-center text-gray-400">نقشی یافت نشد</td></tr>`
        : list.map(r => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-4 font-medium">${r.name}</td>
                <td class="py-4 px-4">${r.title}</td>
                <td class="py-4 px-4 text-gray-500">${r.title_en}</td>
                <td class="py-4 px-4"><span class="px-3 py-1 rounded-full text-xs bg-gray-100">${r.type}</span></td>
                <td class="py-4 px-4">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full border" style="background-color: ${r.color}"></span>
                        <span class="text-xs text-gray-500">${r.color}</span>
                    </div>
                </td>
                <td class="py-4 px-4">${r.order}</td>
                <td class="py-4 px-4">${r.branchName}</td>
                <td class="py-4 px-4 text-left">
                    <button onclick="editRole(${r.id})" class="text-indigo-600 hover:underline text-sm ml-3">ویرایش</button>
                    <button onclick="deleteRole(${r.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            </tr>
        `).join('');
};

window.openAddRoleModal = function() {
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
                <h2 class="text-2xl font-bold">افزودن نقش</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام *</label>
                    <input id="roleName" type="text" placeholder="مثال: academy_teacher" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع</label>
                    <select id="roleType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="سیستم">سیستم</option>
                        <option value="سفارشی">سفارشی</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان *</label>
                    <input id="roleTitle" type="text" placeholder="مثال: استاد" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان انگلیسی</label>
                    <input id="roleTitleEn" type="text" placeholder="مثال: academy_teacher" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">رنگ</label>
                    <input id="roleColor" type="color" value="#4F46E5" class="w-full h-12 border border-gray-300 rounded-2xl px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">ترتیب</label>
                    <input id="roleOrder" type="number" value="1" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="roleBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveRole()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ثبت نقش</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveRole = function() {
    const name = document.getElementById('roleName')?.value.trim();
    const title = document.getElementById('roleTitle')?.value.trim();
    if (!name || !title) return alert('نام و عنوان الزامی است');

    const branchId = parseInt(document.getElementById('roleBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allRoles.push({
        id: Date.now(),
        name,
        title,
        title_en: document.getElementById('roleTitleEn').value || name,
        type: document.getElementById('roleType').value,
        color: document.getElementById('roleColor').value,
        order: parseInt(document.getElementById('roleOrder').value) || 1,
        branchId,
        branchName: branch ? branch.name : 'نامشخص'
    });

    filterRolesByBranch(currentRoleBranch);
    closeModal();
    alert('✅ نقش ثبت شد');
};

window.editRole = function(id) {
    alert('ویرایش نقش (می‌توانید مشابه فرم افزودن کامل کنید)');
};

window.deleteRole = function(id) {
    if (confirm('حذف این نقش؟')) {
        allRoles = allRoles.filter(r => r.id !== id);
        filterRolesByBranch(currentRoleBranch);
    }
};

// Init
setTimeout(() => {
    if (document.getElementById('rolesTable')) {
        renderRolesBranchTabs();
        filterRolesByBranch('all');
    }
}, 200);