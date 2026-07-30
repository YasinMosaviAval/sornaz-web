const pointTypeLabels = { general: 'عمومی', professional: 'حرفه‌ای' };

let allPoints = [
    { id: 1, title: "تکمیل پروفایل", summary: "امتیاز خوش‌آمدگویی", description: "بابت تکمیل اطلاعات پروفایل.", type: "general", points: 50, action: "complete_profile", reference_type: "user", reference_id: 1, user_id: 1, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "شرکت در کنسرت", summary: "حضور در رویداد", description: "امتیاز حضور در کنسرت پایان ترم.", type: "professional", points: 100, action: "attend_event", reference_type: "event", reference_id: 5, user_id: 1, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, title: "معرفی هنرجوی جدید", summary: "امتیاز ارجاع", description: "بابت معرفی یک هنرجوی جدید.", type: "general", points: 200, action: "referral", reference_type: "user", reference_id: 12, user_id: 2, branchId: 2, branchName: "شعبه ونک" },
    { id: 4, title: "انتشار مقاله", summary: "تألیف علمی", description: "امتیاز بابت انتشار مقاله داوری‌شده.", type: "professional", points: 300, action: "publish_article", reference_type: "publication", reference_id: 2, user_id: 2, branchId: 1, branchName: "شعبه مرکزی" }
];
let currentPointBranch = 'all';

window.renderPointsBranchTabs = function() {
    const container = document.getElementById('pointsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.point-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'point-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterPointsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterPointsByBranch = function(branchId) {
    currentPointBranch = branchId;
    document.querySelectorAll('.point-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.point-branch-tab');
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
    renderPointsTable();
};

window.renderPointsTable = function() {
    const tbody = document.querySelector('#pointsTable tbody');
    if (!tbody) return;
    const list = currentPointBranch === 'all' ? allPoints : allPoints.filter(p => p.branchId == currentPointBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">امتیازی یافت نشد</td></tr>`
        : list.map(p => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${p.title}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${p.type==='professional'?'bg-purple-100 text-purple-700':'bg-blue-100 text-blue-700'}">${pointTypeLabels[p.type] || p.type}</span></td>
                <td class="py-4 px-5 font-bold text-indigo-600">+${p.points}</td>
                <td class="py-4 px-5 text-sm text-gray-500">${p.action || '—'}</td>
                <td class="py-4 px-5">${p.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewPoint(${p.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editPoint(${p.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deletePoint(${p.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`).join('');
};

window.openAddPointModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن امتیاز</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="pointTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="pointSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="pointDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="pointType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="general">عمومی</option>
                        <option value="professional">حرفه‌ای</option>
                    </select>
                    <input id="pointValue" type="number" min="0" value="10" placeholder="امتیاز" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <input id="pointAction" type="text" placeholder="عملیات (action)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <div class="grid grid-cols-2 gap-4">
                    <input id="pointRefType" type="text" placeholder="نوع مرجع" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="pointRefId" type="number" placeholder="شناسه مرجع" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="pointBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="savePoint()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.savePoint = function() {
    const title = document.getElementById('pointTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const branchId = parseInt(document.getElementById('pointBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allPoints.unshift({
        id: Date.now(), title,
        summary: document.getElementById('pointSummary').value.trim(),
        description: document.getElementById('pointDesc').value.trim(),
        type: document.getElementById('pointType').value,
        points: parseInt(document.getElementById('pointValue').value) || 0,
        action: document.getElementById('pointAction').value.trim(),
        reference_type: document.getElementById('pointRefType').value.trim(),
        reference_id: parseInt(document.getElementById('pointRefId').value) || null,
        user_id: 1, branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterPointsByBranch(currentPointBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewPoint = function(id) {
    const p = allPoints.find(x => x.id === id);
    if (!p) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${p.title}</h2>
                    <p class="text-indigo-600 font-bold text-xl mt-1">+${p.points} امتیاز</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editPoint(${p.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${p.summary ? `<p class="text-indigo-600 font-medium">${p.summary}</p>` : ''}
                ${p.description ? `<p class="text-gray-600">${p.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع</span><span>${pointTypeLabels[p.type]}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">عملیات</span><span>${p.action || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">مرجع</span><span>${p.reference_type || '—'} #${p.reference_id || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${p.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editPoint = function(id) {
    const p = allPoints.find(x => x.id === id);
    if (!p) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === p.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش امتیاز</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editPointTitle" type="text" value="${p.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editPointSummary" type="text" value="${p.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editPointDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${p.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="editPointType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="general" ${p.type==='general'?'selected':''}>عمومی</option>
                        <option value="professional" ${p.type==='professional'?'selected':''}>حرفه‌ای</option>
                    </select>
                    <input id="editPointValue" type="number" value="${p.points || 0}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <input id="editPointAction" type="text" value="${p.action || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <div class="grid grid-cols-2 gap-4">
                    <input id="editPointRefType" type="text" value="${p.reference_type || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="editPointRefId" type="number" value="${p.reference_id || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="editPointBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedPoint(${p.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedPoint = function(id) {
    const title = document.getElementById('editPointTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allPoints.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editPointBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allPoints[index] = {
        ...allPoints[index], title,
        summary: document.getElementById('editPointSummary').value.trim(),
        description: document.getElementById('editPointDesc').value.trim(),
        type: document.getElementById('editPointType').value,
        points: parseInt(document.getElementById('editPointValue').value) || 0,
        action: document.getElementById('editPointAction').value.trim(),
        reference_type: document.getElementById('editPointRefType').value.trim(),
        reference_id: parseInt(document.getElementById('editPointRefId').value) || null,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterPointsByBranch(currentPointBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deletePoint = function(id) {
    if (confirm('حذف این امتیاز؟')) {
        allPoints = allPoints.filter(p => p.id !== id);
        filterPointsByBranch(currentPointBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#pointsTable tbody')) {
            renderPointsBranchTabs();
            filterPointsByBranch('all');
        }
    }, 200);
})();