const exceptionTypeLabels = {
    holiday: 'تعطیل رسمی', closed: 'بسته', unavailable: 'خارج از دسترس',
    busy: 'مشغول', vacation: 'مرخصی', blocked: 'مسدود'
};

let allExceptions = [
    { id: 1, title: "تعطیلات نوروز", summary: "تعطیلی کامل", description: "شعبه در ایام نوروز فعالیت ندارد.", user_id: 1, date: "۱۴۰۴/۰۱/۰۱", start_time: "00:00", end_time: "23:59", type: "holiday", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "مرخصی استاد", summary: "سفر کاری", description: "عدم حضور استاد از ۱۰ تا ۱۵ مهر.", user_id: 2, date: "۱۴۰۳/۰۷/۱۰", start_time: "08:00", end_time: "22:00", type: "vacation", branchId: 2, branchName: "شعبه ونک" },
    { id: 3, title: "تعمیر سالن", summary: "بسته موقت", description: "سالن اصلی در حال تعمیر.", user_id: 1, date: "۱۴۰۳/۰۸/۰۵", start_time: "00:00", end_time: "23:59", type: "closed", branchId: 1, branchName: "شعبه مرکزی" }
];
let currentExcBranch = 'all';

window.renderExceptionsBranchTabs = function() {
    const container = document.getElementById('exceptionsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.exc-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'exc-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterExceptionsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterExceptionsByBranch = function(branchId) {
    currentExcBranch = branchId;
    document.querySelectorAll('.exc-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.exc-branch-tab');
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
    renderExceptionsTable();
};

window.renderExceptionsTable = function() {
    const tbody = document.querySelector('#exceptionsTable tbody');
    if (!tbody) return;
    const list = currentExcBranch === 'all' ? allExceptions : allExceptions.filter(e => e.branchId == currentExcBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">موردی یافت نشد</td></tr>`
        : list.map(e => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${e.title}</td>
                <td class="py-4 px-5">${e.date || '—'}</td>
                <td class="py-4 px-5">${e.start_time || '—'} – ${e.end_time || '—'}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs bg-orange-100 text-orange-700">${exceptionTypeLabels[e.type] || e.type}</span></td>
                <td class="py-4 px-5">${e.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewException(${e.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editException(${e.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteException(${e.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`).join('');
};

window.openAddExceptionModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    const typeOptions = Object.entries(exceptionTypeLabels).map(([k, v]) => `<option value="${k}">${v}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن استثنا</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="excTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="excSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="excDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <input id="excDate" type="text" placeholder="تاریخ" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <div class="grid grid-cols-2 gap-4">
                    <input id="excStart" type="text" placeholder="شروع" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="excEnd" type="text" placeholder="پایان" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="excType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${typeOptions}</select>
                <select id="excBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveException()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveException = function() {
    const title = document.getElementById('excTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const branchId = parseInt(document.getElementById('excBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allExceptions.unshift({
        id: Date.now(), title,
        summary: document.getElementById('excSummary').value.trim(),
        description: document.getElementById('excDesc').value.trim(),
        user_id: 1,
        date: document.getElementById('excDate').value.trim() || null,
        start_time: document.getElementById('excStart').value.trim() || null,
        end_time: document.getElementById('excEnd').value.trim() || null,
        type: document.getElementById('excType').value,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterExceptionsByBranch(currentExcBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewException = function(id) {
    const e = allExceptions.find(x => x.id === id);
    if (!e) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${e.title}</h2>
                    <p class="text-sm text-gray-500">${exceptionTypeLabels[e.type]} — ${e.date || ''}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editException(${e.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${e.summary ? `<p class="text-indigo-600 font-medium">${e.summary}</p>` : ''}
                ${e.description ? `<p class="text-gray-600">${e.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ساعت</span><span>${e.start_time || '—'} تا ${e.end_time || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${e.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editException = function(id) {
    const e = allExceptions.find(x => x.id === id);
    if (!e) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === e.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    const typeOptions = Object.entries(exceptionTypeLabels).map(([k, v]) =>
        `<option value="${k}" ${e.type === k ? 'selected' : ''}>${v}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش استثنا</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editExcTitle" type="text" value="${e.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editExcSummary" type="text" value="${e.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editExcDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${e.description || ''}</textarea>
                <input id="editExcDate" type="text" value="${e.date || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <div class="grid grid-cols-2 gap-4">
                    <input id="editExcStart" type="text" value="${e.start_time || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="editExcEnd" type="text" value="${e.end_time || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="editExcType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${typeOptions}</select>
                <select id="editExcBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedException(${e.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedException = function(id) {
    const title = document.getElementById('editExcTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allExceptions.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editExcBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allExceptions[index] = {
        ...allExceptions[index], title,
        summary: document.getElementById('editExcSummary').value.trim(),
        description: document.getElementById('editExcDesc').value.trim(),
        date: document.getElementById('editExcDate').value.trim() || null,
        start_time: document.getElementById('editExcStart').value.trim() || null,
        end_time: document.getElementById('editExcEnd').value.trim() || null,
        type: document.getElementById('editExcType').value,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterExceptionsByBranch(currentExcBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteException = function(id) {
    if (confirm('حذف این استثنا؟')) {
        allExceptions = allExceptions.filter(e => e.id !== id);
        filterExceptionsByBranch(currentExcBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#exceptionsTable tbody')) {
            renderExceptionsBranchTabs();
            filterExceptionsByBranch('all');
        }
    }, 200);
})();