const approvalActionLabels = { approve: 'تأیید', reject: 'رد' };

let allApprovals = [
    { id: 1, title: "تأیید مدرس جدید", summary: "مدارک کامل", description: "مدارک تدریس و هویت بررسی و تأیید شد.", by_user_id: 10, entity_id: 55, entity_type: "user", action: "approve", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "رد درخواست دوره", summary: "ظرفیت تکمیل", description: "ظرفیت دوره تکمیل است؛ درخواست رد شد.", by_user_id: 10, entity_id: 12, entity_type: "course", action: "reject", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, title: "تأیید شعبه ونک", summary: "مجوز فعالیت", description: "مجوز فعالیت شعبه ونک تأیید شد.", by_user_id: 5, entity_id: 2, entity_type: "branch", action: "approve", branchId: 2, branchName: "شعبه ونک" }
];
let currentApprBranch = 'all';

window.renderApprovalsBranchTabs = function() {
    const container = document.getElementById('approvalsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.appr-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'appr-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterApprovalsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterApprovalsByBranch = function(branchId) {
    currentApprBranch = branchId;
    document.querySelectorAll('.appr-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.appr-branch-tab');
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
    renderApprovalsTable();
};

window.renderApprovalsTable = function() {
    const tbody = document.querySelector('#approvalsTable tbody');
    if (!tbody) return;
    const list = currentApprBranch === 'all' ? allApprovals : allApprovals.filter(a => a.branchId == currentApprBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">موردی یافت نشد</td></tr>`
        : list.map(a => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${a.title}</td>
                <td class="py-4 px-5">${a.entity_type || '—'}</td>
                <td class="py-4 px-5">
                    <span class="px-3 py-1 rounded-full text-xs ${a.action === 'approve' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${approvalActionLabels[a.action] || a.action}</span>
                </td>
                <td class="py-4 px-5">#${a.by_user_id || '—'}</td>
                <td class="py-4 px-5">${a.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewApproval(${a.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editApproval(${a.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteApproval(${a.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`).join('');
};

window.openAddApprovalModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن تأیید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="apprTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="apprSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="apprDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <input id="apprEntityType" type="text" placeholder="نوع موجودیت (user, course...)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="apprEntityId" type="number" placeholder="شناسه موجودیت" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <select id="apprAction" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="approve">تأیید</option>
                        <option value="reject">رد</option>
                    </select>
                    <input id="apprByUser" type="number" placeholder="تأییدکننده (user_id)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="apprBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveApproval()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveApproval = function() {
    const title = document.getElementById('apprTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const branchId = parseInt(document.getElementById('apprBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allApprovals.unshift({
        id: Date.now(), title,
        summary: document.getElementById('apprSummary').value.trim(),
        description: document.getElementById('apprDesc').value.trim(),
        by_user_id: parseInt(document.getElementById('apprByUser').value) || null,
        entity_id: parseInt(document.getElementById('apprEntityId').value) || null,
        entity_type: document.getElementById('apprEntityType').value.trim(),
        action: document.getElementById('apprAction').value,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterApprovalsByBranch(currentApprBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewApproval = function(id) {
    const a = allApprovals.find(x => x.id === id);
    if (!a) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${a.title}</h2>
                    <p class="text-sm text-gray-500">${approvalActionLabels[a.action]} — ${a.entity_type} #${a.entity_id}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editApproval(${a.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${a.summary ? `<p class="text-indigo-600 font-medium">${a.summary}</p>` : ''}
                ${a.description ? `<p class="text-gray-600">${a.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">عملیات</span><span>${approvalActionLabels[a.action]}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تأییدکننده</span><span>#${a.by_user_id || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${a.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editApproval = function(id) {
    const a = allApprovals.find(x => x.id === id);
    if (!a) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === a.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش تأیید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editApprTitle" type="text" value="${a.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editApprSummary" type="text" value="${a.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editApprDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${a.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <input id="editApprEntityType" type="text" value="${a.entity_type || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="editApprEntityId" type="number" value="${a.entity_id || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <select id="editApprAction" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="approve" ${a.action==='approve'?'selected':''}>تأیید</option>
                        <option value="reject" ${a.action==='reject'?'selected':''}>رد</option>
                    </select>
                    <input id="editApprByUser" type="number" value="${a.by_user_id || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="editApprBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedApproval(${a.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedApproval = function(id) {
    const title = document.getElementById('editApprTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allApprovals.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editApprBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allApprovals[index] = {
        ...allApprovals[index], title,
        summary: document.getElementById('editApprSummary').value.trim(),
        description: document.getElementById('editApprDesc').value.trim(),
        entity_type: document.getElementById('editApprEntityType').value.trim(),
        entity_id: parseInt(document.getElementById('editApprEntityId').value) || null,
        action: document.getElementById('editApprAction').value,
        by_user_id: parseInt(document.getElementById('editApprByUser').value) || null,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterApprovalsByBranch(currentApprBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteApproval = function(id) {
    if (confirm('حذف این مورد؟')) {
        allApprovals = allApprovals.filter(a => a.id !== id);
        filterApprovalsByBranch(currentApprBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#approvalsTable tbody')) {
            renderApprovalsBranchTabs();
            filterApprovalsByBranch('all');
        }
    }, 200);
})();