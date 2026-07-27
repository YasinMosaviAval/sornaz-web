const dayLabels = {
    saturday: 'شنبه', sunday: 'یکشنبه', monday: 'دوشنبه', tuesday: 'سه‌شنبه',
    wednesday: 'چهارشنبه', thursday: 'پنج‌شنبه', friday: 'جمعه'
};
const repeatLabels = {
    week: 'هفتگی', '2-week': 'دو هفته‌ای', '3-week': 'سه هفته‌ای',
    '4-week': 'چهار هفته‌ای', month: 'ماهانه', year: 'سالانه'
};
const availTypeLabels = { available: 'در دسترس', unavailable: 'خارج از دسترس' };

let allAvailabilities = [
    { id: 1, title: "کلاس‌های عصر شنبه", summary: "بازه اصلی تدریس", description: "زمان ثابت کلاس‌های پیانو.", user_id: 1, date: null, day_of_week: "saturday", start_time: "16:00", end_time: "20:00", timezone: "Asia/Tehran", type: "available", is_repeating: 1, repeat_period: "week", is_closed: 0, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "صبح‌های دوشنبه", summary: "تمرین گروهی", description: "آماده‌سازی گروه کر.", user_id: 2, date: null, day_of_week: "monday", start_time: "09:00", end_time: "12:00", timezone: "Asia/Tehran", type: "available", is_repeating: 1, repeat_period: "week", is_closed: 0, branchId: 2, branchName: "شعبه ونک" },
    { id: 3, title: "پنج‌شنبه تعطیل", summary: "روز استراحت", description: "عدم پذیرش کلاس در پنج‌شنبه.", user_id: 1, date: null, day_of_week: "thursday", start_time: "00:00", end_time: "23:59", timezone: "Asia/Tehran", type: "unavailable", is_repeating: 1, repeat_period: "week", is_closed: 1, branchId: 1, branchName: "شعبه مرکزی" }
];
let currentAvailBranch = 'all';

window.renderAvailabilitiesBranchTabs = function() {
    const container = document.getElementById('availabilitiesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.avail-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'avail-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterAvailabilitiesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterAvailabilitiesByBranch = function(branchId) {
    currentAvailBranch = branchId;
    document.querySelectorAll('.avail-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.avail-branch-tab');
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
    renderAvailabilitiesTable();
};

window.renderAvailabilitiesTable = function() {
    const tbody = document.querySelector('#availabilitiesTable tbody');
    if (!tbody) return;
    const list = currentAvailBranch === 'all' ? allAvailabilities : allAvailabilities.filter(a => a.branchId == currentAvailBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="7" class="py-12 text-center text-gray-400">موردی یافت نشد</td></tr>`
        : list.map(a => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${a.title}</td>
                <td class="py-4 px-5">${dayLabels[a.day_of_week] || a.day_of_week || '—'}</td>
                <td class="py-4 px-5">${a.start_time || '—'} – ${a.end_time || '—'}</td>
                <td class="py-4 px-5">
                    <span class="px-3 py-1 rounded-full text-xs ${a.type === 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${availTypeLabels[a.type] || a.type}</span>
                </td>
                <td class="py-4 px-5 text-sm">${a.is_repeating ? (repeatLabels[a.repeat_period] || a.repeat_period) : 'بدون تکرار'}</td>
                <td class="py-4 px-5">${a.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewAvailability(${a.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editAvailability(${a.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteAvailability(${a.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`).join('');
};

window.openAddAvailabilityModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    const dayOptions = Object.entries(dayLabels).map(([k, v]) => `<option value="${k}">${v}</option>`).join('');
    const repeatOptions = Object.entries(repeatLabels).map(([k, v]) => `<option value="${k}">${v}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن زمان در دسترس</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="availTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="availSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="availDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="availDay" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${dayOptions}</select>
                    <select id="availType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="available">در دسترس</option>
                        <option value="unavailable">خارج از دسترس</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input id="availStart" type="text" placeholder="شروع (مثلاً 16:00)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="availEnd" type="text" placeholder="پایان (مثلاً 20:00)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <input id="availTimezone" type="text" value="Asia/Tehran" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="availRepeating" checked> تکرارشونده</label>
                    <select id="availRepeat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${repeatOptions}</select>
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="availClosed"> بسته (is_closed)</label>
                <select id="availBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveAvailability()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveAvailability = function() {
    const title = document.getElementById('availTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const branchId = parseInt(document.getElementById('availBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allAvailabilities.unshift({
        id: Date.now(), title,
        summary: document.getElementById('availSummary').value.trim(),
        description: document.getElementById('availDesc').value.trim(),
        user_id: 1, date: null,
        day_of_week: document.getElementById('availDay').value,
        start_time: document.getElementById('availStart').value.trim() || null,
        end_time: document.getElementById('availEnd').value.trim() || null,
        timezone: document.getElementById('availTimezone').value.trim() || 'Asia/Tehran',
        type: document.getElementById('availType').value,
        is_repeating: document.getElementById('availRepeating').checked ? 1 : 0,
        repeat_period: document.getElementById('availRepeat').value,
        is_closed: document.getElementById('availClosed').checked ? 1 : 0,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterAvailabilitiesByBranch(currentAvailBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewAvailability = function(id) {
    const a = allAvailabilities.find(x => x.id === id);
    if (!a) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${a.title}</h2>
                    <p class="text-sm text-gray-500">${dayLabels[a.day_of_week]} — ${a.start_time} تا ${a.end_time}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editAvailability(${a.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${a.summary ? `<p class="text-indigo-600 font-medium">${a.summary}</p>` : ''}
                ${a.description ? `<p class="text-gray-600">${a.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع</span><span>${availTypeLabels[a.type]}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تکرار</span><span>${a.is_repeating ? repeatLabels[a.repeat_period] : 'خیر'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">منطقه زمانی</span><span>${a.timezone}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">بسته</span><span>${a.is_closed ? 'بله' : 'خیر'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${a.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editAvailability = function(id) {
    const a = allAvailabilities.find(x => x.id === id);
    if (!a) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === a.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    const dayOptions = Object.entries(dayLabels).map(([k, v]) =>
        `<option value="${k}" ${a.day_of_week === k ? 'selected' : ''}>${v}</option>`
    ).join('');
    const repeatOptions = Object.entries(repeatLabels).map(([k, v]) =>
        `<option value="${k}" ${a.repeat_period === k ? 'selected' : ''}>${v}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش زمان</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editAvailTitle" type="text" value="${a.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editAvailSummary" type="text" value="${a.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editAvailDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${a.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="editAvailDay" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${dayOptions}</select>
                    <select id="editAvailType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="available" ${a.type==='available'?'selected':''}>در دسترس</option>
                        <option value="unavailable" ${a.type==='unavailable'?'selected':''}>خارج از دسترس</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input id="editAvailStart" type="text" value="${a.start_time || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="editAvailEnd" type="text" value="${a.end_time || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <input id="editAvailTimezone" type="text" value="${a.timezone || 'Asia/Tehran'}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="editAvailRepeating" ${a.is_repeating?'checked':''}> تکرارشونده</label>
                    <select id="editAvailRepeat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${repeatOptions}</select>
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="editAvailClosed" ${a.is_closed?'checked':''}> بسته</label>
                <select id="editAvailBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedAvailability(${a.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedAvailability = function(id) {
    const title = document.getElementById('editAvailTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allAvailabilities.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editAvailBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allAvailabilities[index] = {
        ...allAvailabilities[index], title,
        summary: document.getElementById('editAvailSummary').value.trim(),
        description: document.getElementById('editAvailDesc').value.trim(),
        day_of_week: document.getElementById('editAvailDay').value,
        start_time: document.getElementById('editAvailStart').value.trim() || null,
        end_time: document.getElementById('editAvailEnd').value.trim() || null,
        timezone: document.getElementById('editAvailTimezone').value.trim(),
        type: document.getElementById('editAvailType').value,
        is_repeating: document.getElementById('editAvailRepeating').checked ? 1 : 0,
        repeat_period: document.getElementById('editAvailRepeat').value,
        is_closed: document.getElementById('editAvailClosed').checked ? 1 : 0,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterAvailabilitiesByBranch(currentAvailBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteAvailability = function(id) {
    if (confirm('حذف این مورد؟')) {
        allAvailabilities = allAvailabilities.filter(a => a.id !== id);
        filterAvailabilitiesByBranch(currentAvailBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#availabilitiesTable tbody')) {
            renderAvailabilitiesBranchTabs();
            filterAvailabilitiesByBranch('all');
        }
    }, 200);
})();