let allEducations = [
    { id: 1, title: "کارشناسی موسیقی", organization: "دانشگاه هنر تهران", summary: "رشته نوازندگی پیانو", description: "فارغ‌التحصیل با رتبه عالی.", start_date: "۱۳۹۲/۰۷/۰۱", end_date: "۱۳۹۶/۰۶/۳۱", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "کارشناسی ارشد آهنگسازی", organization: "دانشگاه تهران", summary: "گرایش موسیقی کلاسیک", description: "پایان‌نامه در زمینه موسیقی ایرانی.", start_date: "۱۳۹۶/۰۷/۰۱", end_date: "۱۳۹۹/۰۶/۳۱", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, title: "دوره تخصصی گیتار", organization: "آکادمی موسیقی ونک", summary: "گیتار کلاسیک", description: "دوره فشرده دو ساله.", start_date: "۱۳۹۸/۰۱/۱۵", end_date: "۱۴۰۰/۰۱/۱۵", branchId: 2, branchName: "شعبه ونک" }
];
let currentEduBranch = 'all';

window.renderEducationsBranchTabs = function() {
    const container = document.getElementById('educationsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.edu-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'edu-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterEducationsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterEducationsByBranch = function(branchId) {
    currentEduBranch = branchId;
    document.querySelectorAll('.edu-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.edu-branch-tab');
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
    renderEducationsTable();
};

window.renderEducationsTable = function() {
    const tbody = document.querySelector('#educationsTable tbody');
    if (!tbody) return;
    const list = currentEduBranch === 'all' ? allEducations : allEducations.filter(e => e.branchId == currentEduBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">موردی یافت نشد</td></tr>`
        : list.map(e => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${e.title}</td>
                <td class="py-4 px-5">${e.organization}</td>
                <td class="py-4 px-5">${e.start_date || '—'}</td>
                <td class="py-4 px-5">${e.end_date || 'تاکنون'}</td>
                <td class="py-4 px-5">${e.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewEducation(${e.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editEducation(${e.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteEducation(${e.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`).join('');
};

window.openAddEducationModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن تحصیلات</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="eduTitle" type="text" placeholder="عنوان * (مثال: کارشناسی موسیقی)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="eduOrg" type="text" placeholder="سازمان / دانشگاه *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="eduSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="eduDesc" rows="3" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <input id="eduStart" type="text" placeholder="تاریخ شروع" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="eduEnd" type="text" placeholder="تاریخ پایان (خالی=تاکنون)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="eduBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEducation()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEducation = function() {
    const title = document.getElementById('eduTitle')?.value.trim();
    const org = document.getElementById('eduOrg')?.value.trim();
    if (!title || !org) return alert('عنوان و سازمان الزامی است');
    const branchId = parseInt(document.getElementById('eduBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allEducations.unshift({
        id: Date.now(), title, organization: org,
        summary: document.getElementById('eduSummary').value.trim(),
        description: document.getElementById('eduDesc').value.trim(),
        start_date: document.getElementById('eduStart').value.trim() || null,
        end_date: document.getElementById('eduEnd').value.trim() || null,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterEducationsByBranch(currentEduBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewEducation = function(id) {
    const e = allEducations.find(x => x.id === id);
    if (!e) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div><h2 class="text-2xl font-bold">${e.title}</h2><p class="text-sm text-gray-500">${e.organization}</p></div>
                <div class="flex gap-3">
                    <button onclick="editEducation(${e.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${e.summary ? `<p class="text-indigo-600 font-medium">${e.summary}</p>` : ''}
                ${e.description ? `<p class="text-gray-600">${e.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شروع</span><span>${e.start_date || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">پایان</span><span>${e.end_date || 'تاکنون'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${e.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editEducation = function(id) {
    const e = allEducations.find(x => x.id === id);
    if (!e) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === e.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش تحصیلات</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editEduTitle" type="text" value="${e.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editEduOrg" type="text" value="${e.organization}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editEduSummary" type="text" value="${e.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editEduDesc" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${e.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <input id="editEduStart" type="text" value="${e.start_date || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="editEduEnd" type="text" value="${e.end_date || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="editEduBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedEducation(${e.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedEducation = function(id) {
    const title = document.getElementById('editEduTitle')?.value.trim();
    const org = document.getElementById('editEduOrg')?.value.trim();
    if (!title || !org) return alert('عنوان و سازمان الزامی است');
    const index = allEducations.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editEduBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allEducations[index] = {
        ...allEducations[index], title, organization: org,
        summary: document.getElementById('editEduSummary').value.trim(),
        description: document.getElementById('editEduDesc').value.trim(),
        start_date: document.getElementById('editEduStart').value.trim() || null,
        end_date: document.getElementById('editEduEnd').value.trim() || null,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterEducationsByBranch(currentEduBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteEducation = function(id) {
    if (confirm('حذف این مورد؟')) {
        allEducations = allEducations.filter(e => e.id !== id);
        filterEducationsByBranch(currentEduBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#educationsTable tbody')) {
            renderEducationsBranchTabs();
            filterEducationsByBranch('all');
        }
    }, 200);
})();