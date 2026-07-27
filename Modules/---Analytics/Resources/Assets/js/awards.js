let allAwards = [
    { id: 1, title: "بهترین آموزشگاه موسیقی سال", organization: "وزارت فرهنگ و ارشاد", summary: "جایزه ملی", description: "انتخاب به‌عنوان برترین آموزشگاه موسیقی در سطح کشور.", date: "۱۴۰۲/۰۹/۱۵", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "مدال طلای جشنواره موسیقی جوان", organization: "جشنواره موسیقی جوان", summary: "مدال طلا", description: "کسب مدال طلا در بخش گروهی.", date: "۱۴۰۱/۱۱/۲۰", branchId: 2, branchName: "شعبه ونک" },
    { id: 3, title: "لوح تقدیر آموزش برتر", organization: "سازمان فنی‌وحرفه‌ای", summary: "تقدیرنامه", description: "لوح تقدیر بابت کیفیت آموزش خیاطی.", date: "۱۴۰۳/۰۲/۱۰", branchId: 4, branchName: "شعبه کرج" }
];

let currentAwardBranch = 'all';

window.renderAwardsBranchTabs = function() {
    const container = document.getElementById('awardsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.award-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'award-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterAwardsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterAwardsByBranch = function(branchId) {
    currentAwardBranch = branchId;
    document.querySelectorAll('.award-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.award-branch-tab');
    if (branchId === 'all') {
        if (tabs[0]) { tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600'); tabs[0].classList.remove('border-gray-200'); }
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    renderAwardsTable();
};

window.renderAwardsTable = function() {
    const tbody = document.querySelector('#awardsTable tbody');
    if (!tbody) return;
    const list = currentAwardBranch === 'all' ? allAwards : allAwards.filter(a => a.branchId == currentAwardBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="5" class="py-12 text-center text-gray-400">جایزه‌ای یافت نشد</td></tr>`
        : list.map(a => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${a.title}</td>
                <td class="py-4 px-5">${a.organization}</td>
                <td class="py-4 px-5">${a.date || '—'}</td>
                <td class="py-4 px-5">${a.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewAward(${a.id})" class="text-indigo-600 hover:underline text-sm ml-3">جزئیات</button>
                    <button onclick="editAward(${a.id})" class="text-indigo-600 hover:underline text-sm ml-3">ویرایش</button>
                    <button onclick="deleteAward(${a.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            </tr>`).join('');
};

window.openAddAwardModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن جایزه</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان *</label>
                    <input id="awardTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">سازمان *</label>
                    <input id="awardOrg" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">خلاصه</label>
                    <input id="awardSummary" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="awardDesc" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ</label>
                        <input id="awardDate" type="text" placeholder="۱۴۰۲/۰۹/۱۵" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شعبه</label>
                        <select id="awardBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                    </div>
                </div>
                <div class="flex gap-4">
                    <button onclick="saveAward()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveAward = function() {
    const title = document.getElementById('awardTitle')?.value.trim();
    const org = document.getElementById('awardOrg')?.value.trim();
    if (!title || !org) return alert('عنوان و سازمان الزامی است');
    const branchId = parseInt(document.getElementById('awardBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allAwards.unshift({
        id: Date.now(), title, organization: org,
        summary: document.getElementById('awardSummary').value.trim(),
        description: document.getElementById('awardDesc').value.trim(),
        date: document.getElementById('awardDate').value.trim() || null,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterAwardsByBranch(currentAwardBranch);
    closeModal();
    alert('✅ جایزه ثبت شد');
};

window.viewAward = function(id) {
    const a = allAwards.find(x => x.id === id);
    if (!a) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${a.title}</h2>
                    <p class="text-sm text-gray-500">${a.organization}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editAward(${a.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${a.summary ? `<p class="text-indigo-600 font-medium">${a.summary}</p>` : ''}
                ${a.description ? `<p class="text-gray-600">${a.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ</span><span>${a.date || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${a.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editAward = function(id) {
    const a = allAwards.find(x => x.id === id);
    if (!a) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === a.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش جایزه</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editAwardTitle" type="text" value="${a.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="عنوان">
                <input id="editAwardOrg" type="text" value="${a.organization}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="سازمان">
                <input id="editAwardSummary" type="text" value="${a.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="خلاصه">
                <textarea id="editAwardDesc" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${a.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <input id="editAwardDate" type="text" value="${a.date || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="تاریخ">
                    <select id="editAwardBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <div class="flex gap-4">
                    <button onclick="saveEditedAward(${a.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedAward = function(id) {
    const title = document.getElementById('editAwardTitle')?.value.trim();
    const org = document.getElementById('editAwardOrg')?.value.trim();
    if (!title || !org) return alert('عنوان و سازمان الزامی است');
    const index = allAwards.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editAwardBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allAwards[index] = {
        ...allAwards[index], title, organization: org,
        summary: document.getElementById('editAwardSummary').value.trim(),
        description: document.getElementById('editAwardDesc').value.trim(),
        date: document.getElementById('editAwardDate').value.trim() || null,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterAwardsByBranch(currentAwardBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteAward = function(id) {
    if (confirm('حذف این جایزه؟')) {
        allAwards = allAwards.filter(a => a.id !== id);
        filterAwardsByBranch(currentAwardBranch);
    }
};

setTimeout(() => {
    if (document.getElementById('awardsTable')) {
        renderAwardsBranchTabs();
        filterAwardsByBranch('all');
    }
}, 200);