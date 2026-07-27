// نمونه دروس
const sampleLessons = [
    { id: 1, title: "تئوری موسیقی" },
    { id: 2, title: "سولفژ" },
    { id: 3, title: "هارمونی" },
    { id: 4, title: "کنترپوان" },
    { id: 5, title: "تاریخ موسیقی" },
    { id: 6, title: "آنالیز آثار" },
    { id: 7, title: "آهنگسازی" },
    { id: 8, title: "ارکستراسیون" },
    { id: 9, title: "ریتم و متر" },
    { id: 10, title: "شنوایی‌سنجی" },
    { id: 11, title: "پیانو همراهی" },
    { id: 12, title: "بداهه‌نوازی" }
];

// از همان sampleLevels در instruments استفاده می‌شود؛ اگر جدا لود شد:
if (typeof sampleLevels === 'undefined') {
    var sampleLevels = [
        { level_id: 1, title: "مبتدی", type: "learning" },
        { level_id: 2, title: "متوسط", type: "learning" },
        { level_id: 3, title: "پیشرفته", type: "learning" },
        { level_id: 4, title: "حرفه‌ای", type: "learning" },
        { level_id: 5, title: "کارشناسی", type: "academic" },
        { level_id: 6, title: "کارشناسی ارشد", type: "academic" }
    ];
}

let allUserLessons = [
    { id: 1, title: "تئوری موسیقی", summary: "درس پایه", description: "تسلط بر نت‌خوانی و گام‌ها.", lesson_id: 1, level_id: 3, years_of_experience: 6, is_primary: 1, user_id: 1, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "سولفژ", summary: "شنوایی", description: "تمرین روزانه سولفژ.", lesson_id: 2, level_id: 2, years_of_experience: 4, is_primary: 0, user_id: 1, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, title: "هارمونی", summary: "تخصص", description: "هارمونی کلاسیک و جاز.", lesson_id: 3, level_id: 4, years_of_experience: 8, is_primary: 1, user_id: 2, branchId: 2, branchName: "شعبه ونک" },
    { id: 4, title: "بداهه‌نوازی", summary: "کارگاه", description: "بداهه روی استانداردهای جاز.", lesson_id: 12, level_id: 3, years_of_experience: 3, is_primary: 1, user_id: 3, branchId: 2, branchName: "شعبه ونک" }
];
let currentLessonBranch = 'all';

window.renderLessonsBranchTabs = function() {
    const container = document.getElementById('lessonsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.lesson-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'lesson-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterLessonsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterLessonsByBranch = function(branchId) {
    currentLessonBranch = branchId;
    document.querySelectorAll('.lesson-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.lesson-branch-tab');
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
    const list = currentLessonBranch === 'all' ? allUserLessons : allUserLessons.filter(l => l.branchId == currentLessonBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">موردی یافت نشد</td></tr>`
        : list.map(l => {
            const level = sampleLevels.find(x => x.level_id === l.level_id);
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${l.title}</td>
                <td class="py-4 px-5">${level ? level.title : '—'}</td>
                <td class="py-4 px-5">${l.years_of_experience ?? '—'}</td>
                <td class="py-4 px-5">${l.is_primary ? '<span class="px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">اصلی</span>' : '—'}</td>
                <td class="py-4 px-5">${l.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewLesson(${l.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editLesson(${l.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteLesson(${l.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddLessonModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    const lessonOptions = sampleLessons.map(l => `<option value="${l.id}">${l.title}</option>`).join('');
    const levelOptions = sampleLevels.map(l => `<option value="${l.level_id}">${l.title}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن درس</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <select id="lessonSelect" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${lessonOptions}</select>
                <input id="lessonSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="lessonDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="lessonLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${levelOptions}</select>
                    <input id="lessonYears" type="number" min="0" value="1" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="سابقه (سال)">
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="lessonPrimary"> درس اصلی (فقط یکی برای هر کاربر)</label>
                <select id="lessonBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveLesson()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveLesson = function() {
    const lessonId = parseInt(document.getElementById('lessonSelect').value);
    const lesson = sampleLessons.find(l => l.id === lessonId);
    if (!lesson) return alert('درس را انتخاب کنید');
    const branchId = parseInt(document.getElementById('lessonBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    const isPrimary = document.getElementById('lessonPrimary').checked ? 1 : 0;
    const userId = 1;

    if (isPrimary) {
        allUserLessons.forEach(l => {
            if (l.user_id === userId) l.is_primary = 0;
        });
    }

    allUserLessons.unshift({
        id: Date.now(),
        title: lesson.title,
        summary: document.getElementById('lessonSummary').value.trim(),
        description: document.getElementById('lessonDesc').value.trim(),
        lesson_id: lessonId,
        level_id: parseInt(document.getElementById('lessonLevel').value),
        years_of_experience: parseInt(document.getElementById('lessonYears').value) || 0,
        is_primary: isPrimary,
        user_id: userId,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterLessonsByBranch(currentLessonBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewLesson = function(id) {
    const l = allUserLessons.find(x => x.id === id);
    if (!l) return;
    const level = sampleLevels.find(x => x.level_id === l.level_id);
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${l.title}</h2>
                    <p class="text-sm text-gray-500">${level ? level.title : ''} ${l.is_primary ? '— درس اصلی' : ''}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editLesson(${l.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${l.summary ? `<p class="text-indigo-600 font-medium">${l.summary}</p>` : ''}
                ${l.description ? `<p class="text-gray-600">${l.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سطح</span><span>${level ? level.title : '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سابقه</span><span>${l.years_of_experience} سال</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">اصلی</span><span>${l.is_primary ? 'بله' : 'خیر'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${l.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editLesson = function(id) {
    const l = allUserLessons.find(x => x.id === id);
    if (!l) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === l.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    const lessonOptions = sampleLessons.map(s =>
        `<option value="${s.id}" ${s.id === l.lesson_id ? 'selected' : ''}>${s.title}</option>`
    ).join('');
    const levelOptions = sampleLevels.map(x =>
        `<option value="${x.level_id}" ${x.level_id === l.level_id ? 'selected' : ''}>${x.title}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش درس</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <select id="editLessonSelect" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${lessonOptions}</select>
                <input id="editLessonSummary" type="text" value="${l.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editLessonDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${l.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="editLessonLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${levelOptions}</select>
                    <input id="editLessonYears" type="number" value="${l.years_of_experience || 0}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="editLessonPrimary" ${l.is_primary ? 'checked' : ''}> درس اصلی</label>
                <select id="editLessonBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedLesson(${l.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedLesson = function(id) {
    const index = allUserLessons.findIndex(x => x.id === id);
    if (index === -1) return;
    const lessonId = parseInt(document.getElementById('editLessonSelect').value);
    const lesson = sampleLessons.find(s => s.id === lessonId);
    const branchId = parseInt(document.getElementById('editLessonBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    const isPrimary = document.getElementById('editLessonPrimary').checked ? 1 : 0;
    const userId = allUserLessons[index].user_id;

    if (isPrimary) {
        allUserLessons.forEach(l => {
            if (l.user_id === userId && l.id !== id) l.is_primary = 0;
        });
    }

    allUserLessons[index] = {
        ...allUserLessons[index],
        title: lesson ? lesson.title : allUserLessons[index].title,
        summary: document.getElementById('editLessonSummary').value.trim(),
        description: document.getElementById('editLessonDesc').value.trim(),
        lesson_id: lessonId,
        level_id: parseInt(document.getElementById('editLessonLevel').value),
        years_of_experience: parseInt(document.getElementById('editLessonYears').value) || 0,
        is_primary: isPrimary,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterLessonsByBranch(currentLessonBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteLesson = function(id) {
    if (confirm('حذف این درس؟')) {
        allUserLessons = allUserLessons.filter(l => l.id !== id);
        filterLessonsByBranch(currentLessonBranch);
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