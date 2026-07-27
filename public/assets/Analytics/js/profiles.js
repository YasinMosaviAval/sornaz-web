// نمونه سطوح هنرجو
const studentLevels = [
    { id: 1, title: "مبتدی" },
    { id: 2, title: "متوسط" },
    { id: 3, title: "پیشرفته" },
    { id: 4, title: "حرفه‌ای" }
];

let allProfiles = [
    { id: 1, title: "پروفایل علی رضایی", summary: "هنرجوی پیانو", description: "هنرجوی فعال شعبه مرکزی از سال ۱۳۹۸.", user_id: 1, student_level_id: 3, start_career_date: "۱۳۹۸/۰۷/۰۱", picture_media_id: 101, show_in_public: 1, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "پروفایل سارا موسوی", summary: "مدرس گیتار", description: "مدرس رسمی گیتار کلاسیک.", user_id: 2, student_level_id: 4, start_career_date: "۱۳۹۵/۰۱/۱۵", picture_media_id: 102, show_in_public: 1, branchId: 2, branchName: "شعبه ونک" },
    { id: 3, title: "پروفایل خصوصی", summary: "عدم نمایش عمومی", description: "پروفایل محدود به اعضا.", user_id: 3, student_level_id: 1, start_career_date: "۱۴۰۲/۰۴/۰۱", picture_media_id: null, show_in_public: 0, branchId: 1, branchName: "شعبه مرکزی" }
];
let currentProfBranch = 'all';

window.renderProfilesBranchTabs = function() {
    const container = document.getElementById('profilesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.prof-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'prof-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterProfilesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterProfilesByBranch = function(branchId) {
    currentProfBranch = branchId;
    document.querySelectorAll('.prof-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.prof-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    renderProfilesTable();
};

window.renderProfilesTable = function() {
    const tbody = document.querySelector('#profilesTable tbody');
    if (!tbody) return;
    const list = currentProfBranch === 'all' ? allProfiles : allProfiles.filter(p => p.branchId == currentProfBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">پروفایلی یافت نشد</td></tr>`
        : list.map(p => {
            const level = studentLevels.find(l => l.id === p.student_level_id);
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${p.title}</td>
                <td class="py-4 px-5">${level ? level.title : '—'}</td>
                <td class="py-4 px-5">${p.start_career_date || '—'}</td>
                <td class="py-4 px-5">${p.show_in_public ? '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">عمومی</span>' : '<span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-600">خصوصی</span>'}</td>
                <td class="py-4 px-5">${p.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewProfile(${p.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editProfile(${p.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteProfile(${p.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddProfileModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    const levelOptions = studentLevels.map(l => `<option value="${l.id}">${l.title}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن پروفایل</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="profTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="profSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="profDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="profLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${levelOptions}</select>
                    <input id="profStartDate" type="text" placeholder="شروع فعالیت" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <input id="profMediaId" type="number" placeholder="شناسه تصویر (picture_media_id)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="profPublic" checked> نمایش در پروفایل عمومی</label>
                <select id="profBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveProfile()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveProfile = function() {
    const title = document.getElementById('profTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const branchId = parseInt(document.getElementById('profBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allProfiles.unshift({
        id: Date.now(), title,
        summary: document.getElementById('profSummary').value.trim(),
        description: document.getElementById('profDesc').value.trim(),
        user_id: 1,
        student_level_id: parseInt(document.getElementById('profLevel').value),
        start_career_date: document.getElementById('profStartDate').value.trim() || null,
        picture_media_id: parseInt(document.getElementById('profMediaId').value) || null,
        show_in_public: document.getElementById('profPublic').checked ? 1 : 0,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterProfilesByBranch(currentProfBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewProfile = function(id) {
    const p = allProfiles.find(x => x.id === id);
    if (!p) return;
    const level = studentLevels.find(l => l.id === p.student_level_id);
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${p.title}</h2>
                    <p class="text-sm text-gray-500">${level ? level.title : ''} — کاربر #${p.user_id}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editProfile(${p.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${p.summary ? `<p class="text-indigo-600 font-medium">${p.summary}</p>` : ''}
                ${p.description ? `<p class="text-gray-600">${p.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شروع فعالیت</span><span>${p.start_career_date || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تصویر (media_id)</span><span>${p.picture_media_id || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نمایش عمومی</span><span>${p.show_in_public ? 'بله' : 'خیر'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${p.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editProfile = function(id) {
    const p = allProfiles.find(x => x.id === id);
    if (!p) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === p.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    const levelOptions = studentLevels.map(l =>
        `<option value="${l.id}" ${l.id === p.student_level_id ? 'selected' : ''}>${l.title}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش پروفایل</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editProfTitle" type="text" value="${p.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editProfSummary" type="text" value="${p.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editProfDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${p.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="editProfLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${levelOptions}</select>
                    <input id="editProfStartDate" type="text" value="${p.start_career_date || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <input id="editProfMediaId" type="number" value="${p.picture_media_id || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="editProfPublic" ${p.show_in_public ? 'checked' : ''}> نمایش عمومی</label>
                <select id="editProfBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedProfile(${p.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedProfile = function(id) {
    const title = document.getElementById('editProfTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allProfiles.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editProfBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allProfiles[index] = {
        ...allProfiles[index], title,
        summary: document.getElementById('editProfSummary').value.trim(),
        description: document.getElementById('editProfDesc').value.trim(),
        student_level_id: parseInt(document.getElementById('editProfLevel').value),
        start_career_date: document.getElementById('editProfStartDate').value.trim() || null,
        picture_media_id: parseInt(document.getElementById('editProfMediaId').value) || null,
        show_in_public: document.getElementById('editProfPublic').checked ? 1 : 0,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterProfilesByBranch(currentProfBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteProfile = function(id) {
    if (confirm('حذف این پروفایل؟')) {
        allProfiles = allProfiles.filter(p => p.id !== id);
        filterProfilesByBranch(currentProfBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#profilesTable tbody')) {
            renderProfilesBranchTabs();
            filterProfilesByBranch('all');
        }
    }, 200);
})();