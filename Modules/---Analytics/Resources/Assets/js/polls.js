let allPolls = [
    {
        id: 1, title: "رضایت از کیفیت کلاس‌ها", summary: "نظرسنجی ماهانه", description: "لطفاً میزان رضایت خود را از کیفیت تدریس اعلام کنید.",
        type: "single", is_anonymous: true, status: "active", votes_count: 48, expires_at: "۱۴۰۳/۱۰/۳۰",
        branchId: 1, branchName: "شعبه مرکزی", target_type: "students", target_id: null,
        options: [
            { id: 11, text: "عالی", vote_count: 22, sort_order: 1 },
            { id: 12, text: "خوب", vote_count: 18, sort_order: 2 },
            { id: 13, text: "متوسط", vote_count: 6, sort_order: 3 },
            { id: 14, text: "ضعیف", vote_count: 2, sort_order: 4 }
        ]
    },
    {
        id: 2, title: "انتخاب ساز برای کارگاه تابستان", summary: "چند انتخابی", description: "کدام سازها برای کارگاه تابستان پیشنهاد می‌شود؟",
        type: "multiple", is_anonymous: false, status: "active", votes_count: 35, expires_at: "۱۴۰۳/۰۹/۱۵",
        branchId: 2, branchName: "شعبه ونک", target_type: "all", target_id: null,
        options: [
            { id: 21, text: "گیتار", vote_count: 15, sort_order: 1 },
            { id: 22, text: "پیانو", vote_count: 12, sort_order: 2 },
            { id: 23, text: "ویولن", vote_count: 8, sort_order: 3 }
        ]
    },
    {
        id: 3, title: "زمان مناسب کلاس آنلاین", summary: "بسته شده", description: "بهترین بازه زمانی برای کلاس‌های آنلاین.",
        type: "single", is_anonymous: true, status: "closed", votes_count: 60, expires_at: "۱۴۰۳/۰۵/۰۱",
        branchId: 1, branchName: "شعبه مرکزی", target_type: "students", target_id: null,
        options: [
            { id: 31, text: "صبح", vote_count: 10, sort_order: 1 },
            { id: 32, text: "عصر", vote_count: 35, sort_order: 2 },
            { id: 33, text: "شب", vote_count: 15, sort_order: 3 }
        ]
    }
];
let currentPollBranch = 'all';

const pollStatusLabels = { active: 'فعال', deactive: 'غیرفعال', closed: 'بسته شده' };
const pollTypeLabels = { single: 'تک‌انتخابی', multiple: 'چند‌انتخابی' };

window.renderPollsBranchTabs = function() {
    const container = document.getElementById('pollsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.poll-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'poll-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterPollsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterPollsByBranch = function(branchId) {
    currentPollBranch = branchId;
    document.querySelectorAll('.poll-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.poll-branch-tab');
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
    renderPollsTable();
};

window.renderPollsTable = function() {
    const tbody = document.querySelector('#pollsTable tbody');
    if (!tbody) return;
    const list = currentPollBranch === 'all' ? allPolls : allPolls.filter(p => p.branchId == currentPollBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="7" class="py-12 text-center text-gray-400">نظرسنجی‌ای یافت نشد</td></tr>`
        : list.map(p => {
            const statusClass = p.status === 'active' ? 'bg-green-100 text-green-700' : p.status === 'closed' ? 'bg-gray-100 text-gray-600' : 'bg-yellow-100 text-yellow-700';
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${p.title}</td>
                <td class="py-4 px-5">${pollTypeLabels[p.type] || p.type}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${pollStatusLabels[p.status] || p.status}</span></td>
                <td class="py-4 px-5">${p.votes_count || 0}</td>
                <td class="py-4 px-5">${p.expires_at || '—'}</td>
                <td class="py-4 px-5">${p.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewPoll(${p.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editPoll(${p.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deletePoll(${p.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddPollModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن نظرسنجی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="pollTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="pollSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="pollDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="pollType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="single">تک‌انتخابی</option>
                        <option value="multiple">چند‌انتخابی</option>
                    </select>
                    <select id="pollStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="active">فعال</option>
                        <option value="deactive">غیرفعال</option>
                        <option value="closed">بسته شده</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="pollAnonymous"> ناشناس</label>
                    <input id="pollExpires" type="text" placeholder="تاریخ انقضا" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="pollBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div>
                    <label class="block text-sm font-medium mb-2">گزینه‌ها (هر خط یک گزینه)</label>
                    <textarea id="pollOptions" rows="4" placeholder="عالی&#10;خوب&#10;متوسط&#10;ضعیف" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                </div>
                <div class="flex gap-4">
                    <button onclick="savePoll()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.savePoll = function() {
    const title = document.getElementById('pollTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const optsText = document.getElementById('pollOptions').value.trim();
    const options = optsText ? optsText.split('\n').map((t, i) => ({
        id: Date.now() + i, text: t.trim(), vote_count: 0, sort_order: i + 1
    })).filter(o => o.text) : [];
    const branchId = parseInt(document.getElementById('pollBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allPolls.unshift({
        id: Date.now(), title,
        summary: document.getElementById('pollSummary').value.trim(),
        description: document.getElementById('pollDesc').value.trim(),
        type: document.getElementById('pollType').value,
        is_anonymous: document.getElementById('pollAnonymous').checked,
        status: document.getElementById('pollStatus').value,
        votes_count: 0,
        expires_at: document.getElementById('pollExpires').value.trim() || null,
        branchId, branchName: branch ? branch.name : 'نامشخص',
        target_type: 'all', target_id: null,
        options
    });
    filterPollsByBranch(currentPollBranch);
    closeModal();
    alert('✅ نظرسنجی ثبت شد');
};

window.viewPoll = function(id) {
    const p = allPolls.find(x => x.id === id);
    if (!p) return;
    const total = p.votes_count || 1;
    const optionsHtml = (p.options || []).map(o => {
        const pct = total ? Math.round((o.vote_count / total) * 100) : 0;
        return `
        <div class="mb-3">
            <div class="flex justify-between text-sm mb-1"><span>${o.text}</span><span>${o.vote_count} رأی (${pct}٪)</span></div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-indigo-500 rounded-full" style="width:${pct}%"></div></div>
        </div>`;
    }).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${p.title}</h2>
                    <p class="text-sm text-gray-500">${pollTypeLabels[p.type]} — ${pollStatusLabels[p.status]}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editPoll(${p.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${p.summary ? `<p class="text-indigo-600 font-medium">${p.summary}</p>` : ''}
                ${p.description ? `<p class="text-gray-600 text-sm">${p.description}</p>` : ''}
                <div class="text-sm space-y-1 border-b pb-3">
                    <div class="flex justify-between"><span class="text-gray-500">کل آرا</span><span>${p.votes_count}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">ناشناس</span><span>${p.is_anonymous ? 'بله' : 'خیر'}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">انقضا</span><span>${p.expires_at || '—'}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">شعبه</span><span>${p.branchName}</span></div>
                </div>
                <div><h3 class="font-semibold mb-3">نتایج</h3>${optionsHtml || '<p class="text-gray-400 text-sm">گزینه‌ای ثبت نشده</p>'}</div>
            </div>
        </div>
    </div>`;
};

window.editPoll = function(id) {
    const p = allPolls.find(x => x.id === id);
    if (!p) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === p.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    const optsText = (p.options || []).map(o => o.text).join('\n');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش نظرسنجی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editPollTitle" type="text" value="${p.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editPollSummary" type="text" value="${p.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editPollDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${p.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="editPollType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="single" ${p.type==='single'?'selected':''}>تک‌انتخابی</option>
                        <option value="multiple" ${p.type==='multiple'?'selected':''}>چند‌انتخابی</option>
                    </select>
                    <select id="editPollStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="active" ${p.status==='active'?'selected':''}>فعال</option>
                        <option value="deactive" ${p.status==='deactive'?'selected':''}>غیرفعال</option>
                        <option value="closed" ${p.status==='closed'?'selected':''}>بسته شده</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="editPollAnonymous" ${p.is_anonymous?'checked':''}> ناشناس</label>
                    <input id="editPollExpires" type="text" value="${p.expires_at || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="editPollBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <textarea id="editPollOptions" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${optsText}</textarea>
                <div class="flex gap-4">
                    <button onclick="saveEditedPoll(${p.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedPoll = function(id) {
    const title = document.getElementById('editPollTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allPolls.findIndex(x => x.id === id);
    if (index === -1) return;
    const optsText = document.getElementById('editPollOptions').value.trim();
    const oldOpts = allPolls[index].options || [];
    const options = optsText ? optsText.split('\n').map((t, i) => {
        const existing = oldOpts.find(o => o.text === t.trim());
        return { id: existing ? existing.id : Date.now() + i, text: t.trim(), vote_count: existing ? existing.vote_count : 0, sort_order: i + 1 };
    }).filter(o => o.text) : [];
    const branchId = parseInt(document.getElementById('editPollBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allPolls[index] = {
        ...allPolls[index], title,
        summary: document.getElementById('editPollSummary').value.trim(),
        description: document.getElementById('editPollDesc').value.trim(),
        type: document.getElementById('editPollType').value,
        is_anonymous: document.getElementById('editPollAnonymous').checked,
        status: document.getElementById('editPollStatus').value,
        expires_at: document.getElementById('editPollExpires').value.trim() || null,
        branchId, branchName: branch ? branch.name : 'نامشخص',
        options
    };
    filterPollsByBranch(currentPollBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deletePoll = function(id) {
    if (confirm('حذف این نظرسنجی؟')) {
        allPolls = allPolls.filter(p => p.id !== id);
        filterPollsByBranch(currentPollBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#pollsTable tbody')) {
            renderPollsBranchTabs();
            filterPollsByBranch('all');
        }
    }, 200);
})();