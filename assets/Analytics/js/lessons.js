// ==================== داده پایه ====================
window.sampleLessons = [
    { id: 1, title: "پیانو" }, { id: 2, title: "گیتار کلاسیک" }, { id: 3, title: "گیتار الکتریک" },
    { id: 4, title: "ویولن" }, { id: 5, title: "ویولا" }, { id: 6, title: "ویولنسل" },
    { id: 7, title: "فلوت" }, { id: 8, title: "کلارینت" }, { id: 9, title: "ساکسوفون" },
    { id: 10, title: "ترومپت" }, { id: 11, title: "درام" }, { id: 12, title: "کاخن" },
    { id: 13, title: "سنتور" }, { id: 14, title: "تار" }, { id: 15, title: "سه‌تار" },
    { id: 16, title: "کمانچه" }, { id: 17, title: "نی" }, { id: 18, title: "عود" },
    { id: 19, title: "آکاردئون" }, { id: 20, title: "کیبورد" }
];
window.lessonLevels = [
    { level_id: 1, title: "مبتدی", type: "learning", sort_order: 1 },
    { level_id: 2, title: "متوسط", type: "learning", sort_order: 2 },
    { level_id: 3, title: "پیشرفته", type: "learning", sort_order: 3 },
    { level_id: 4, title: "حرفه‌ای", type: "learning", sort_order: 4 },
    { level_id: 5, title: "کارشناسی", type: "academic", sort_order: 5 },
    { level_id: 6, title: "کارشناسی ارشد", type: "academic", sort_order: 6 }
];
const lessonStatuses = ['فعال', 'غیرفعال', 'در انتظار', 'حذف‌شده'];

function getLesnBranches() {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' }, { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' }, { id: 4, name: 'شعبه کرج' }
    ];
}

// ۴۰ نمونه
window.allUserLessons = [];
(function buildSamples() {
    const branches = getLesnBranches();
    const summaries = ['درس اصلی', 'درس دوم', 'تخصص', 'ریتم', 'همراهی'];
    for (let i = 1; i <= 40; i++) {
        const lesn = sampleLessons[Math.floor(Math.random() * sampleLessons.length)];
        const level = lessonLevels[Math.floor(Math.random() * lessonLevels.length)];
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const userId = 1 + (i % 5);
        allUserLessons.push({
            id: i,
            title: lesn.title,
            summary: summaries[Math.floor(Math.random() * summaries.length)],
            description: 'توضیحات مربوط به ' + lesn.title + ' در سطح ' + level.title,
            lesson_id: lesn.id,
            level_id: level.level_id,
            years_of_experience: 1 + Math.floor(Math.random() * 20),
            is_primary: i % 7 === 0 ? 1 : 0,
            status: lessonStatuses[Math.floor(Math.random() * lessonStatuses.length)],
            user_id: userId,
            branchId: branch.id,
            branchName: branch.name
        });
    }
    // enforce one primary per user
    const seen = {};
    allUserLessons.forEach(function (item) {
        if (item.is_primary) {
            if (seen[item.user_id]) item.is_primary = 0;
            else seen[item.user_id] = true;
        }
    });
})();

let currentLesnBranch = 'all';
let lessonsCurrentPage = 1;
const lessonsPerPage = 10;
let filteredLessons = allUserLessons.slice();
let editingLessonRowId = null;
let lessonSortField = '';
let lessonSortDirection = 'asc';

const lessonPdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'title', label: 'درس' },
    { field: 'levelTitle', label: 'سطح' },
    { field: 'years_of_experience', label: 'سابقه' },
    { field: 'is_primary_label', label: 'اصلی' },
    { field: 'status', label: 'وضعیت' },
    { field: 'branchName', label: 'شعبه' }
];

function getLevelTitle(levelId) {
    const l = lessonLevels.find(function (x) { return x.level_id === levelId; });
    return l ? l.title : '—';
}

function sortLessonItems() {
    if (!lessonSortField) return;
    filteredLessons.sort(function (a, b) {
        let av = a[lessonSortField], bv = b[lessonSortField];
        if (lessonSortField === 'levelTitle') {
            av = getLevelTitle(a.level_id); bv = getLevelTitle(b.level_id);
        }
        if (lessonSortField === 'years_of_experience') {
            av = Number(av) || 0; bv = Number(bv) || 0;
        } else {
            av = String(av || '').toLowerCase(); bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return lessonSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return lessonSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateLessonSortIcons = async function () {
    ['title', 'levelTitle', 'years_of_experience', 'is_primary', 'status', 'branchName'].forEach(function (f) {
        const icon = document.getElementById('lesnSortIcon-' + f);
        if (!icon) return;
        icon.textContent = lessonSortField === f ? (lessonSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortLessonsBy = async function (field) {
    if (lessonSortField === field) lessonSortDirection = lessonSortDirection === 'asc' ? 'desc' : 'asc';
    else { lessonSortField = field; lessonSortDirection = 'asc'; }
    sortLessonItems();
    renderLessonsTable(filteredLessons);
    updateLessonSortIcons();
};

window.renderLessonsBranchTabs = async function () {
    const container = document.getElementById('lessonsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.lesn-branch-tab:not(:first-child)').forEach(function (t) { t.remove(); });
    getLesnBranches().forEach(function (b) {
        const active = currentLesnBranch == b.id;
        const btn = document.createElement('button');
        btn.className = 'lesn-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = b.name;
        btn.onclick = function () { filterLessonsByBranch(b.id); };
        container.appendChild(btn);
    });
};

window.filterLessonsByBranch = async function (branchId) {
    currentLesnBranch = branchId;
    document.querySelectorAll('#lessonsBranchTabs .lesn-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('#lessonsBranchTabs .lesn-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        const name = getLesnBranches().find(function (b) { return b.id == branchId; });
        tabs.forEach(function (tab) {
            if (name && tab.textContent === name.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    filterLessons();
};

window.filterLessons = async function () {
    const search = (document.getElementById('lessonSearch') && document.getElementById('lessonSearch').value || '').trim().toLowerCase();
    const status = document.getElementById('filterLessonStatus') && document.getElementById('filterLessonStatus').value || '';
    const level = document.getElementById('filterLessonLevel') && document.getElementById('filterLessonLevel').value || '';

    filteredLessons = allUserLessons.filter(function (item) {
        const matchBranch = currentLesnBranch === 'all' || item.branchId == currentLesnBranch;
        const matchSearch = !search || (item.title || '').toLowerCase().includes(search) || (item.summary || '').toLowerCase().includes(search);
        const matchStatus = !status || item.status === status;
        const matchLevel = !level || String(item.level_id) === String(level);
        return matchBranch && matchSearch && matchStatus && matchLevel;
    });
    lessonsCurrentPage = 1;
    sortLessonItems();
    renderLessonsTable(filteredLessons);
};

window.renderLessonsTable = async function (list) {
    list = list || filteredLessons;
    const tbody = document.querySelector('#lessonsTable tbody');
    if (!tbody) return;
    const totalPages = Math.ceil(list.length / lessonsPerPage) || 1;
    if (lessonsCurrentPage > totalPages) lessonsCurrentPage = totalPages;
    const start = (lessonsCurrentPage - 1) * lessonsPerPage;
    const end = start + lessonsPerPage;
    const pageItems = list.slice(start, end);
    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getLessonEmptyRowHTML ? window.getLessonEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getLessonRowHTML ? window.getLessonRowHTML(item, getLevelTitle(item.level_id)) : '';
            tbody.appendChild(tr);
            if (editingLessonRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50';
                expand.innerHTML = window.getLessonInlineExpandRowHTML ? window.getLessonInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updateLessonsPagination(list.length, start, end, totalPages);
    updateLessonSortIcons();
};

function updateLessonsPagination(total, start, end, totalPages) {
    const info = document.getElementById('lessonsPaginationInfo');
    if (info) info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' مورد';
    const pagination = document.getElementById('lessonsPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeLessonsPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (lessonsCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeLessonsPage(' + (lessonsCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (lessonsCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, lessonsCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeLessonsPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === lessonsCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeLessonsPage(' + (lessonsCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (lessonsCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeLessonsPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (lessonsCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeLessonsPage = async function (page) {
    const totalPages = Math.ceil(filteredLessons.length / lessonsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    lessonsCurrentPage = page;
    renderLessonsTable(filteredLessons);
};

window.promptAddLessonType = async function () {
    const name = (await AppDialog.prompt('نام درس جدید را وارد کنید:') || '').trim();
    if (!name) return;
    if (sampleLessons.some(function (i) { return i.title === name; })) return alert('این درس قبلاً وجود دارد');
    const item = { id: Date.now(), title: name };
    sampleLessons.push(item);
    document.querySelectorAll('select[id$="Select"], select[id*="LesnSelect"]').forEach(function (sel) {
        if (!/Select|LesnSelect/.test(sel.id)) return;
        const opt = document.createElement('option');
        opt.value = item.id; opt.textContent = name; opt.selected = true;
        sel.appendChild(opt);
    });
};

function readLessonForm(prefix) {
    const f = function (s) { return document.getElementById(prefix ? prefix + s : 'lesn' + s); };
    const lesnId = parseInt(f('Select') && f('Select').value, 10);
    const lesn = sampleLessons.find(function (i) { return i.id === lesnId; });
    const branchId = parseInt(f('Branch') && f('Branch').value, 10);
    const branch = getLesnBranches().find(function (b) { return b.id === branchId; });
    return {
        title: lesn ? lesn.title : '',
        lesson_id: lesnId,
        summary: f('Summary') && f('Summary').value.trim() || '',
        description: f('Desc') && f('Desc').value.trim() || '',
        level_id: parseInt(f('Level') && f('Level').value, 10),
        years_of_experience: parseInt(f('Years') && f('Years').value, 10) || 0,
        is_primary: f('Primary') && f('Primary').checked ? 1 : 0,
        status: f('Status') && f('Status').value || 'فعال',
        branchId: branchId,
        branchName: branch ? branch.name : 'نامشخص'
    };
}

function enforcePrimary(userId, excludeId) {
    allUserLessons.forEach(function (i) {
        if (i.user_id === userId && i.id !== excludeId) i.is_primary = 0;
    });
}

window.openAddLessonModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getLessonAddModalHTML ? window.getLessonAddModalHTML() : '';
};

window.saveLesson = async function () {
    const data = readLessonForm('');
    if (!data.lesson_id) return alert('درس را انتخاب کنید');
    const userId = 1;
    if (data.is_primary) enforcePrimary(userId, null);
    allUserLessons.unshift(Object.assign({ id: Date.now(), user_id: userId }, data));
    filterLessons();
    closeModal();
    alert('✅ ثبت شد');
};

window.viewLesson = async function (id) {
    const item = allUserLessons.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getLessonDetailsModalHTML
        ? window.getLessonDetailsModalHTML(item, getLevelTitle(item.level_id)) : '';
};

window.editLesson = async function (id) {
    const item = allUserLessons.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getLessonEditModalHTML
        ? window.getLessonEditModalHTML(item) : '';
};

window.saveEditedLesson = async function (id) {
    const data = readLessonForm('editLesn');
    if (!data.lesson_id) return alert('درس را انتخاب کنید');
    const index = allUserLessons.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    if (data.is_primary) enforcePrimary(allUserLessons[index].user_id, id);
    allUserLessons[index] = Object.assign({}, allUserLessons[index], data);
    editingLessonRowId = null;
    filterLessons();
    closeModal();
    alert('✅ ذخیره شد');
};

window.toggleLessonInlineEdit = async function (id) {
    editingLessonRowId = editingLessonRowId === id ? null : id;
    renderLessonsTable(filteredLessons);
};

window.saveInlineLesson = async function (id) {
    const data = readLessonForm('inlineLesn' + id);
    if (!data.lesson_id) return alert('درس را انتخاب کنید');
    const index = allUserLessons.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    if (data.is_primary) enforcePrimary(allUserLessons[index].user_id, id);
    allUserLessons[index] = Object.assign({}, allUserLessons[index], data);
    editingLessonRowId = null;
    filterLessons();
    alert('✅ ذخیره شد');
};

window.deleteLesson = async function (id) {
    if (!(await AppDialog.confirmDelete(allUserLessons, id, 'درس'))) return;
    allUserLessons = allUserLessons.filter(function (i) { return i.id !== id; });
    if (editingLessonRowId === id) editingLessonRowId = null;
    filterLessons();
};

window.exportLessonsToExcel = async function () {
    const data = filteredLessons.length ? filteredLessons : allUserLessons;
    let csv = '\uFEFFردیف,درس,سطح,سابقه,اصلی,وضعیت,شعبه\n';
    data.forEach(function (item, i) {
        csv += (i + 1) + ',"' + item.title + '","' + getLevelTitle(item.level_id) + '",' + (item.years_of_experience || 0) + ',' +
            (item.is_primary ? 'بله' : 'خیر') + ',"' + (item.status || '') + '","' + item.branchName + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'درس‌ها_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportLessonsToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getLessonPDFModalHTML
        ? window.getLessonPDFModalHTML(lessonPdfColumns) : '';
};

window.generateLessonsPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('lesnPdfTitle') && document.getElementById('lesnPdfTitle').value || 'گزارش درس‌ها';
    const subtitle = document.getElementById('lesnPdfSubtitle') && document.getElementById('lesnPdfSubtitle').value || '';
    const footer = document.getElementById('lesnPdfFooter') && document.getElementById('lesnPdfFooter').value || '';
    const format = document.getElementById('lesnPdfFormat') && document.getElementById('lesnPdfFormat').value || 'a4';
    const orientation = document.getElementById('lesnPdfOrientation') && document.getElementById('lesnPdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('lesnPdfIncludeDate') && document.getElementById('lesnPdfIncludeDate').checked;
    const headerColor = document.getElementById('lesnPdfHeaderColor') && document.getElementById('lesnPdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('lesnPdfEvenRowColor') && document.getElementById('lesnPdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('lesnPdfOddRowColor') && document.getElementById('lesnPdfOddRowColor').value || '#f8fafc';
    const selectedColumns = lessonPdfColumns.filter(function (c) {
        return document.getElementById('lesnPdfCol-' + c.field) && document.getElementById('lesnPdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = (filteredLessons.length ? filteredLessons : allUserLessons).map(function (item) {
        return Object.assign({}, item, {
            levelTitle: getLevelTitle(item.level_id),
            is_primary_label: item.is_primary ? 'بله' : 'خیر'
        });
    });
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getLessonPDFPageHTML(p + 1, pageRows, p === 0, {
            title: title, subtitle: subtitle, footer: footer, includeDate: includeDate, date: date,
            headerColor: headerColor, evenRowColor: evenRowColor, oddRowColor: oddRowColor,
            selectedColumns: selectedColumns, rowsPerPage: rowsPerPage, totalPages: totalPages
        });
        document.body.appendChild(wrap);
        canvasPages.push(await html2canvas(wrap, { scale: 2, useCORS: true, backgroundColor: '#ffffff' }));
        wrap.remove();
    }
    const doc = new window.jspdf.jsPDF({ orientation: orientation, unit: 'pt', format: format });
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 20, imgWidth = pageWidth - margin * 2;
    canvasPages.forEach(function (canvas, i) {
        if (i > 0) doc.addPage();
        doc.addImage(canvas.toDataURL('image/png'), 'PNG', margin, margin, imgWidth, (canvas.height * imgWidth) / canvas.width);
    });
    doc.save('درس‌ها_' + date + '.pdf');
    closeModal();
};

(function () {
    setTimeout(function () {
        if (document.querySelector('#lessonsTable tbody')) {
            renderLessonsBranchTabs();
            filterLessons();
        }
    }, 200);
})();
