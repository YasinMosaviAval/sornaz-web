let allRatingSummaries = [
    { id: 1, title: "دوره پیانو مبتدی", summary: "امتیاز دوره", description: "میانگین نظرات هنرجویان این دوره.", target_id: 101, target_type: "course", avg_rating: 4.75, total_votes: 48, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "استاد محمد موسوی", summary: "امتیاز مدرس", description: "میانگین امتیاز از هنرجویان.", target_id: 55, target_type: "user", avg_rating: 4.90, total_votes: 120, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, title: "شعبه ونک", summary: "امتیاز شعبه", description: "رضایت کلی از امکانات شعبه.", target_id: 2, target_type: "branch", avg_rating: 4.20, total_votes: 35, branchId: 2, branchName: "شعبه ونک" },
    { id: 4, title: "درس تئوری موسیقی", summary: "امتیاز درس", description: "ارزیابی محتوای آموزشی.", target_id: 12, target_type: "lesson", avg_rating: 4.50, total_votes: 22, branchId: 1, branchName: "شعبه مرکزی" }
];
let currentRsBranch = 'all';

window.renderRatingSummariesBranchTabs = function() {
    const container = document.getElementById('ratingSummariesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.rs-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'rs-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterRatingSummariesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterRatingSummariesByBranch = function(branchId) {
    currentRsBranch = branchId;
    document.querySelectorAll('.rs-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.rs-branch-tab');
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
    renderRatingSummariesTable();
};

window.renderRatingSummariesTable = function() {
    const tbody = document.querySelector('#ratingSummariesTable tbody');
    if (!tbody) return;
    const list = currentRsBranch === 'all' ? allRatingSummaries : allRatingSummaries.filter(r => r.branchId == currentRsBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">موردی یافت نشد</td></tr>`
        : list.map(r => {
            const stars = '★'.repeat(Math.round(r.avg_rating || 0)) + '☆'.repeat(5 - Math.round(r.avg_rating || 0));
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${r.title}</td>
                <td class="py-4 px-5">${r.target_type || '—'}</td>
                <td class="py-4 px-5 text-amber-500">${stars} <span class="text-gray-700 font-medium">${Number(r.avg_rating).toFixed(2)}</span></td>
                <td class="py-4 px-5">${r.total_votes || 0}</td>
                <td class="py-4 px-5">${r.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewRatingSummary(${r.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editRatingSummary(${r.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteRatingSummary(${r.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddRatingSummaryModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن خلاصه رتبه‌بندی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="rsTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="rsSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="rsDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <input id="rsTargetType" type="text" placeholder="نوع هدف (course, user...)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="rsTargetId" type="number" placeholder="شناسه هدف" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input id="rsAvg" type="number" step="0.01" min="0" max="5" value="5" placeholder="میانگین" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="rsVotes" type="number" min="0" value="0" placeholder="تعداد رأی" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="rsBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveRatingSummary()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveRatingSummary = function() {
    const title = document.getElementById('rsTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const branchId = parseInt(document.getElementById('rsBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allRatingSummaries.unshift({
        id: Date.now(), title,
        summary: document.getElementById('rsSummary').value.trim(),
        description: document.getElementById('rsDesc').value.trim(),
        target_type: document.getElementById('rsTargetType').value.trim(),
        target_id: parseInt(document.getElementById('rsTargetId').value) || null,
        avg_rating: parseFloat(document.getElementById('rsAvg').value) || 0,
        total_votes: parseInt(document.getElementById('rsVotes').value) || 0,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterRatingSummariesByBranch(currentRsBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewRatingSummary = function(id) {
    const r = allRatingSummaries.find(x => x.id === id);
    if (!r) return;
    const stars = '★'.repeat(Math.round(r.avg_rating || 0)) + '☆'.repeat(5 - Math.round(r.avg_rating || 0));
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${r.title}</h2>
                    <p class="text-amber-500 text-lg mt-1">${stars} ${Number(r.avg_rating).toFixed(2)}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editRatingSummary(${r.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${r.summary ? `<p class="text-indigo-600 font-medium">${r.summary}</p>` : ''}
                ${r.description ? `<p class="text-gray-600">${r.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع هدف</span><span>${r.target_type}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شناسه هدف</span><span>${r.target_id}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تعداد رأی</span><span>${r.total_votes}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${r.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editRatingSummary = function(id) {
    const r = allRatingSummaries.find(x => x.id === id);
    if (!r) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === r.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش خلاصه رتبه‌بندی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editRsTitle" type="text" value="${r.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editRsSummary" type="text" value="${r.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editRsDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${r.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <input id="editRsTargetType" type="text" value="${r.target_type || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="editRsTargetId" type="number" value="${r.target_id || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input id="editRsAvg" type="number" step="0.01" value="${r.avg_rating || 0}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="editRsVotes" type="number" value="${r.total_votes || 0}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="editRsBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedRatingSummary(${r.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedRatingSummary = function(id) {
    const title = document.getElementById('editRsTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allRatingSummaries.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editRsBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allRatingSummaries[index] = {
        ...allRatingSummaries[index], title,
        summary: document.getElementById('editRsSummary').value.trim(),
        description: document.getElementById('editRsDesc').value.trim(),
        target_type: document.getElementById('editRsTargetType').value.trim(),
        target_id: parseInt(document.getElementById('editRsTargetId').value) || null,
        avg_rating: parseFloat(document.getElementById('editRsAvg').value) || 0,
        total_votes: parseInt(document.getElementById('editRsVotes').value) || 0,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterRatingSummariesByBranch(currentRsBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteRatingSummary = function(id) {
    if (confirm('حذف این خلاصه؟')) {
        allRatingSummaries = allRatingSummaries.filter(r => r.id !== id);
        filterRatingSummariesByBranch(currentRsBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#ratingSummariesTable tbody')) {
            renderRatingSummariesBranchTabs();
            filterRatingSummariesByBranch('all');
        }
    }, 200);
})();