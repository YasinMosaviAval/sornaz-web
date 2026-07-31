(() => {
// نمونه درسها (معادل translation)
const sampleLessons = [
    { id: 1, title: "پیانو" },
    { id: 2, title: "گیتار کلاسیک" },
    { id: 3, title: "گیتار الکتریک" },
    { id: 4, title: "ویولن" },
    { id: 5, title: "ویولا" },
    { id: 6, title: "ویولنسل" },
    { id: 7, title: "فلوت" },
    { id: 8, title: "کلارینت" },
    { id: 9, title: "ساکسوفون" },
    { id: 10, title: "ترومپت" },
    { id: 11, title: "درام" },
    { id: 12, title: "کاخن" },
    { id: 13, title: "سنتور" },
    { id: 14, title: "تار" },
    { id: 15, title: "سه‌تار" },
    { id: 16, title: "کمانچه" },
    { id: 17, title: "نی" },
    { id: 18, title: "عود" },
    { id: 19, title: "آکاردئون" },
    { id: 20, title: "کیبورد" }
];

// سطوح (از جدول levels)
const sampleLevels = [
    { level_id: 1, title: "مبتدی", type: "learning", sort_order: 1 },
    { level_id: 2, title: "متوسط", type: "learning", sort_order: 2 },
    { level_id: 3, title: "پیشرفته", type: "learning", sort_order: 3 },
    { level_id: 4, title: "حرفه‌ای", type: "learning", sort_order: 4 },
    { level_id: 5, title: "کارشناسی", type: "academic", sort_order: 5 },
    { level_id: 6, title: "کارشناسی ارشد", type: "academic", sort_order: 6 }
];

let allUserLessons = [
    { id: 1, title: "پیانو", summary: "درس اصلی", description: "تسلط کامل روی رپرتوار کلاسیک.", lesson_id: 1, level_id: 3, years_of_experience: 12, is_primary: 1, user_id: 1, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "گیتار کلاسیک", summary: "درس دوم", description: "سطح متوسط تا پیشرفته.", lesson_id: 2, level_id: 2, years_of_experience: 5, is_primary: 0, user_id: 1, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, title: "ویولن", summary: "تخصص اصلی", description: "نوازنده ارکستر.", lesson_id: 4, level_id: 4, years_of_experience: 15, is_primary: 1, user_id: 2, branchId: 2, branchName: "شعبه ونک" },
    { id: 4, title: "درام", summary: "ریتم", description: "سبک جاز و راک.", lesson_id: 11, level_id: 3, years_of_experience: 8, is_primary: 1, user_id: 3, branchId: 4, branchName: "شعبه کرج" }
];
let currentInstBranch = 'all';

window.renderLessonsBranchTabs = function() {
    const container = document.getElementById('lessonsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.inst-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'inst-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterLessonsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterLessonsByBranch = function(branchId) {
    currentInstBranch = branchId;
    document.querySelectorAll('.inst-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.inst-branch-tab');
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
    renderLessonsTable();
};

window.renderLessonsTable = function() {
    const tbody = document.querySelector('#lessonsTable tbody');
    if (!tbody) return;
    const list = currentInstBranch === 'all' ? allUserLessons : allUserLessons.filter(i => i.branchId == currentInstBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">موردی یافت نشد</td></tr>`
        : list.map(i => {
            const level = sampleLevels.find(l => l.level_id === i.level_id);
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${i.title}</td>
                <td class="py-4 px-5">${level ? level.title : '—'}</td>
                <td class="py-4 px-5">${i.years_of_experience ?? '—'}</td>
                <td class="py-4 px-5">${i.is_primary ? '<span class="px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">اصلی</span>' : '—'}</td>
                <td class="py-4 px-5">${i.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewLesson(${i.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editLesson(${i.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteLesson(${i.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddLessonModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    const instOptions = sampleLessons.map(i => `<option value="${i.id}">${i.title}</option>`).join('');
    const levelOptions = sampleLevels.map(l => `<option value="${l.level_id}">${l.title} (${l.type})</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن درس</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">درس *</label>
                    <select id="instSelect" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${instOptions}</select>
                </div>
                <input id="instSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="instDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">سطح</label>
                        <select id="instLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${levelOptions}</select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">سابقه (سال)</label>
                        <input id="instYears" type="number" min="0" value="1" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="instPrimary"> درس اصلی (برای این کاربر فقط یکی)</label>
                <select id="instBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveLesson()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveLesson = function() {
    const instId = parseInt(document.getElementById('instSelect').value);
    const inst = sampleLessons.find(i => i.id === instId);
    if (!inst) return alert('درس را انتخاب کنید');
    const branchId = parseInt(document.getElementById('instBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    const isPrimary = document.getElementById('instPrimary').checked ? 1 : 0;
    const userId = 1; // نمونه

    // فقط یک is_primary برای هر user_id
    if (isPrimary) {
        allUserLessons.forEach(i => {
            if (i.user_id === userId) i.is_primary = 0;
        });
    }

    allUserLessons.unshift({
        id: Date.now(),
        title: inst.title,
        summary: document.getElementById('instSummary').value.trim(),
        description: document.getElementById('instDesc').value.trim(),
        lesson_id: instId,
        level_id: parseInt(document.getElementById('instLevel').value),
        years_of_experience: parseInt(document.getElementById('instYears').value) || 0,
        is_primary: isPrimary,
        user_id: userId,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterLessonsByBranch(currentInstBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewLesson = function(id) {
    const i = allUserLessons.find(x => x.id === id);
    if (!i) return;
    const level = sampleLevels.find(l => l.level_id === i.level_id);
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${i.title}</h2>
                    <p class="text-sm text-gray-500">${level ? level.title : ''} ${i.is_primary ? '— درس اصلی' : ''}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editLesson(${i.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${i.summary ? `<p class="text-indigo-600 font-medium">${i.summary}</p>` : ''}
                ${i.description ? `<p class="text-gray-600">${i.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سطح</span><span>${level ? level.title : '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سابقه</span><span>${i.years_of_experience} سال</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">اصلی</span><span>${i.is_primary ? 'بله' : 'خیر'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${i.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editLesson = function(id) {
    const i = allUserLessons.find(x => x.id === id);
    if (!i) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === i.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    const instOptions = sampleLessons.map(s =>
        `<option value="${s.id}" ${s.id === i.lesson_id ? 'selected' : ''}>${s.title}</option>`
    ).join('');
    const levelOptions = sampleLevels.map(l =>
        `<option value="${l.level_id}" ${l.level_id === i.level_id ? 'selected' : ''}>${l.title}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش درس</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <select id="editInstSelect" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${instOptions}</select>
                <input id="editInstSummary" type="text" value="${i.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editInstDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${i.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="editInstLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${levelOptions}</select>
                    <input id="editInstYears" type="number" value="${i.years_of_experience || 0}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="editInstPrimary" ${i.is_primary ? 'checked' : ''}> درس اصلی</label>
                <select id="editInstBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedLesson(${i.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedLesson = function(id) {
    const index = allUserLessons.findIndex(x => x.id === id);
    if (index === -1) return;
    const instId = parseInt(document.getElementById('editInstSelect').value);
    const inst = sampleLessons.find(s => s.id === instId);
    const branchId = parseInt(document.getElementById('editInstBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    const isPrimary = document.getElementById('editInstPrimary').checked ? 1 : 0;
    const userId = allUserLessons[index].user_id;

    if (isPrimary) {
        allUserLessons.forEach(i => {
            if (i.user_id === userId && i.id !== id) i.is_primary = 0;
        });
    }

    allUserLessons[index] = {
        ...allUserLessons[index],
        title: inst ? inst.title : allUserLessons[index].title,
        summary: document.getElementById('editInstSummary').value.trim(),
        description: document.getElementById('editInstDesc').value.trim(),
        lesson_id: instId,
        level_id: parseInt(document.getElementById('editInstLevel').value),
        years_of_experience: parseInt(document.getElementById('editInstYears').value) || 0,
        is_primary: isPrimary,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterLessonsByBranch(currentInstBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteLesson = function(id) {
    if (confirm('حذف این درس؟')) {
        allUserLessons = allUserLessons.filter(i => i.id !== id);
        filterLessonsByBranch(currentInstBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#lessonsTable tbody')) {
            renderLessonsBranchTabs();
            filterLessonsByBranch('all');
        }
    }, 200);
})();

})();
