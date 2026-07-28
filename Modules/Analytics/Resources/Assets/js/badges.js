const badgeStatusLabels = { active: 'فعال', expired: 'منقضی', revoked: 'لغو شده' };

// نمونه سطوح تأیید
const verificationLevels = [
    { id: 1, title: "تأیید هویت پایه" },
    { id: 2, title: "مدرس تأییدشده" },
    { id: 3, title: "مدرس ویژه (VIP)" },
    { id: 4, title: "مدیر شعبه" },
    { id: 5, title: "مالک آموزشگاه" }
];

let allBadges = [
    { id: 1, title: "مدرس تأییدشده", summary: "تأیید رسمی تدریس", description: "پس از بررسی مدارک و سابقه تدریس اعطا شد.", user_id: 1, verification_level_id: 2, granted_by: 10, granted_at: "۱۴۰۲/۰۵/۱۲", expires_at: null, status: "active", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "تأیید هویت پایه", summary: "احراز هویت", description: "مدارک شناسایی تأیید شد.", user_id: 2, verification_level_id: 1, granted_by: 10, granted_at: "۱۴۰۱/۱۱/۰۱", expires_at: null, status: "active", branchId: 2, branchName: "شعبه ونک" },
    { id: 3, title: "مدرس ویژه", summary: "سطح VIP", description: "به دلیل عملکرد عالی و رضایت هنرجویان.", user_id: 1, verification_level_id: 3, granted_by: 5, granted_at: "۱۴۰۳/۰۱/۲۰", expires_at: "۱۴۰۵/۰۱/۲۰", status: "active", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 4, title: "نشان قدیمی", summary: "منقضی", description: "نشان آزمایشی قبلی.", user_id: 3, verification_level_id: 2, granted_by: 10, granted_at: "۱۳۹۹/۰۶/۰۱", expires_at: "۱۴۰۱/۰۶/۰۱", status: "expired", branchId: 4, branchName: "شعبه کرج" }
];
let currentBadgeBranch = 'all';

window.renderBadgesBranchTabs = function() {
    const container = document.getElementById('badgesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.badge-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'badge-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterBadgesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterBadgesByBranch = function(branchId) {
    currentBadgeBranch = branchId;
    document.querySelectorAll('.badge-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.badge-branch-tab');
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
    renderBadgesTable();
};

window.renderBadgesTable = function() {
    const tbody = document.querySelector('#badgesTable tbody');
    if (!tbody) return;
    const list = currentBadgeBranch === 'all' ? allBadges : allBadges.filter(b => b.branchId == currentBadgeBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">نشانی یافت نشد</td></tr>`
        : list.map(b => {
            const level = verificationLevels.find(v => v.id === b.verification_level_id);
            const statusClass = b.status === 'active' ? 'bg-green-100 text-green-700' : b.status === 'expired' ? 'bg-gray-100 text-gray-600' : 'bg-red-100 text-red-700';
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${b.title}</td>
                <td class="py-4 px-5">${level ? level.title : '—'}</td>
                <td class="py-4 px-5">${b.granted_at || '—'}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${badgeStatusLabels[b.status] || b.status}</span></td>
                <td class="py-4 px-5">${b.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewBadge(${b.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editBadge(${b.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteBadge(${b.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddBadgeModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    const levelOptions = verificationLevels.map(v => `<option value="${v.id}">${v.title}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن نشان</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="badgeTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="badgeSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="badgeDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <select id="badgeLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${levelOptions}</select>
                <div class="grid grid-cols-2 gap-4">
                    <input id="badgeGrantedAt" type="text" placeholder="تاریخ اعطا" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="badgeExpiresAt" type="text" placeholder="تاریخ انقضا (اختیاری)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="badgeStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <option value="active">فعال</option>
                    <option value="expired">منقضی</option>
                    <option value="revoked">لغو شده</option>
                </select>
                <select id="badgeBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveBadge()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveBadge = function() {
    const title = document.getElementById('badgeTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const branchId = parseInt(document.getElementById('badgeBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    const levelId = parseInt(document.getElementById('badgeLevel').value);
    allBadges.unshift({
        id: Date.now(), title,
        summary: document.getElementById('badgeSummary').value.trim(),
        description: document.getElementById('badgeDesc').value.trim(),
        user_id: 1,
        verification_level_id: levelId,
        granted_by: 10,
        granted_at: document.getElementById('badgeGrantedAt').value.trim() || 'همین الان',
        expires_at: document.getElementById('badgeExpiresAt').value.trim() || null,
        status: document.getElementById('badgeStatus').value,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterBadgesByBranch(currentBadgeBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewBadge = function(id) {
    const b = allBadges.find(x => x.id === id);
    if (!b) return;
    const level = verificationLevels.find(v => v.id === b.verification_level_id);
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${b.title}</h2>
                    <p class="text-sm text-gray-500">${level ? level.title : ''} — ${badgeStatusLabels[b.status]}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editBadge(${b.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${b.summary ? `<p class="text-indigo-600 font-medium">${b.summary}</p>` : ''}
                ${b.description ? `<p class="text-gray-600">${b.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ اعطا</span><span>${b.granted_at || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">انقضا</span><span>${b.expires_at || 'نامحدود'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">اعطاکننده (ID)</span><span>${b.granted_by || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${b.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editBadge = function(id) {
    const b = allBadges.find(x => x.id === id);
    if (!b) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(br =>
        `<option value="${br.id}" ${br.id === b.branchId ? 'selected' : ''}>${br.name}</option>`
    ).join('');
    const levelOptions = verificationLevels.map(v =>
        `<option value="${v.id}" ${v.id === b.verification_level_id ? 'selected' : ''}>${v.title}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش نشان</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editBadgeTitle" type="text" value="${b.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editBadgeSummary" type="text" value="${b.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editBadgeDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${b.description || ''}</textarea>
                <select id="editBadgeLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${levelOptions}</select>
                <div class="grid grid-cols-2 gap-4">
                    <input id="editBadgeGrantedAt" type="text" value="${b.granted_at || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="editBadgeExpiresAt" type="text" value="${b.expires_at || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="editBadgeStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <option value="active" ${b.status==='active'?'selected':''}>فعال</option>
                    <option value="expired" ${b.status==='expired'?'selected':''}>منقضی</option>
                    <option value="revoked" ${b.status==='revoked'?'selected':''}>لغو شده</option>
                </select>
                <select id="editBadgeBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedBadge(${b.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedBadge = function(id) {
    const title = document.getElementById('editBadgeTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allBadges.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editBadgeBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allBadges[index] = {
        ...allBadges[index], title,
        summary: document.getElementById('editBadgeSummary').value.trim(),
        description: document.getElementById('editBadgeDesc').value.trim(),
        verification_level_id: parseInt(document.getElementById('editBadgeLevel').value),
        granted_at: document.getElementById('editBadgeGrantedAt').value.trim() || null,
        expires_at: document.getElementById('editBadgeExpiresAt').value.trim() || null,
        status: document.getElementById('editBadgeStatus').value,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterBadgesByBranch(currentBadgeBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteBadge = function(id) {
    if (confirm('حذف این نشان؟')) {
        allBadges = allBadges.filter(b => b.id !== id);
        filterBadgesByBranch(currentBadgeBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#badgesTable tbody')) {
            renderBadgesBranchTabs();
            filterBadgesByBranch('all');
        }
    }, 200);
})();