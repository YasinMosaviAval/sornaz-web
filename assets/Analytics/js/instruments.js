// ==================== داده پایه ====================
window.sampleInstruments = [
    { id: 1, title: "پیانو" }, { id: 2, title: "گیتار کلاسیک" }, { id: 3, title: "گیتار الکتریک" },
    { id: 4, title: "ویولن" }, { id: 5, title: "ویولا" }, { id: 6, title: "ویولنسل" },
    { id: 7, title: "فلوت" }, { id: 8, title: "کلارینت" }, { id: 9, title: "ساکسوفون" },
    { id: 10, title: "ترومپت" }, { id: 11, title: "درام" }, { id: 12, title: "کاخن" },
    { id: 13, title: "سنتور" }, { id: 14, title: "تار" }, { id: 15, title: "سه‌تار" },
    { id: 16, title: "کمانچه" }, { id: 17, title: "نی" }, { id: 18, title: "عود" },
    { id: 19, title: "آکاردئون" }, { id: 20, title: "کیبورد" }
];
window.instrumentLevels = [
    { level_id: 1, title: "مبتدی", type: "learning", sort_order: 1 },
    { level_id: 2, title: "متوسط", type: "learning", sort_order: 2 },
    { level_id: 3, title: "پیشرفته", type: "learning", sort_order: 3 },
    { level_id: 4, title: "حرفه‌ای", type: "learning", sort_order: 4 },
    { level_id: 5, title: "کارشناسی", type: "academic", sort_order: 5 },
    { level_id: 6, title: "کارشناسی ارشد", type: "academic", sort_order: 6 }
];
const instrumentStatuses = ['فعال', 'غیرفعال', 'در انتظار', 'حذف‌شده'];

function getInstBranches() {
    return Array.isArray(window.branchOfferingBranches) ? window.branchOfferingBranches : [];
}

// ۴۰ نمونه
window.allUserInstruments = [];
(function buildSamples() { return;
    const branches = getInstBranches();
    const summaries = ['ساز اصلی', 'ساز دوم', 'تخصص', 'ریتم', 'همراهی'];
    for (let i = 1; i <= 40; i++) {
        const inst = sampleInstruments[Math.floor(Math.random() * sampleInstruments.length)];
        const level = instrumentLevels[Math.floor(Math.random() * instrumentLevels.length)];
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const userId = 1 + (i % 5);
        allUserInstruments.push({
            id: i,
            title: inst.title,
            summary: summaries[Math.floor(Math.random() * summaries.length)],
            description: 'توضیحات مربوط به ' + inst.title + ' در سطح ' + level.title,
            instrument_id: inst.id,
            level_id: level.level_id,
            years_of_experience: 1 + Math.floor(Math.random() * 20),
            is_primary: i % 7 === 0 ? 1 : 0,
            status: instrumentStatuses[Math.floor(Math.random() * instrumentStatuses.length)],
            user_id: userId,
            branchId: branch.id,
            branchName: branch.name
        });
    }
    // enforce one primary per user
    const seen = {};
    allUserInstruments.forEach(function (item) {
        if (item.is_primary) {
            if (seen[item.user_id]) item.is_primary = 0;
            else seen[item.user_id] = true;
        }
    });
})();

let currentInstBranch = 'all';
let instrumentsCurrentPage = 1;
const instrumentsPerPage = 10;
let filteredInstruments = allUserInstruments.slice();
let editingInstrumentRowId = null;
let instrumentSortField = '';
let instrumentSortDirection = 'asc';

const instrumentPdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'title', label: 'ساز' },
    { field: 'levelTitle', label: 'سطح' },
    { field: 'years_of_experience', label: 'سابقه' },
    { field: 'is_primary_label', label: 'اصلی' },
    { field: 'status', label: 'وضعیت' },
    { field: 'branchName', label: 'شعبه' }
];

function getLevelTitle(levelId) {
    const l = instrumentLevels.find(function (x) { return x.level_id === levelId; });
    return l ? l.title : '—';
}

function sortInstrumentItems() {
    if (!instrumentSortField) return;
    filteredInstruments.sort(function (a, b) {
        let av = a[instrumentSortField], bv = b[instrumentSortField];
        if (instrumentSortField === 'levelTitle') {
            av = getLevelTitle(a.level_id); bv = getLevelTitle(b.level_id);
        }
        if (instrumentSortField === 'years_of_experience') {
            av = Number(av) || 0; bv = Number(bv) || 0;
        } else {
            av = String(av || '').toLowerCase(); bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return instrumentSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return instrumentSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateInstrumentSortIcons = async function () {
    ['title', 'levelTitle', 'years_of_experience', 'is_primary', 'status', 'branchName'].forEach(function (f) {
        const icon = document.getElementById('instSortIcon-' + f);
        if (!icon) return;
        icon.textContent = instrumentSortField === f ? (instrumentSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortInstrumentsBy = async function (field) {
    if (instrumentSortField === field) instrumentSortDirection = instrumentSortDirection === 'asc' ? 'desc' : 'asc';
    else { instrumentSortField = field; instrumentSortDirection = 'asc'; }
    sortInstrumentItems();
    renderInstrumentsTable(filteredInstruments);
    updateInstrumentSortIcons();
};

window.renderInstrumentsBranchTabs = async function () {
    const container = document.getElementById('instrumentsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.inst-branch-tab:not(:first-child)').forEach(function (t) { t.remove(); });
    getInstBranches().forEach(function (b) {
        const active = currentInstBranch == b.id;
        const btn = document.createElement('button');
        btn.className = 'inst-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = b.name;
        btn.onclick = function () { filterInstrumentsByBranch(b.id); };
        container.appendChild(btn);
    });
};

window.filterInstrumentsByBranch = async function (branchId) {
    currentInstBranch = branchId;
    document.querySelectorAll('#instrumentsBranchTabs .inst-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('#instrumentsBranchTabs .inst-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        const name = getInstBranches().find(function (b) { return b.id == branchId; });
        tabs.forEach(function (tab) {
            if (name && tab.textContent === name.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    filterInstruments();
};

window.filterInstruments = async function () {
    const search = (document.getElementById('instrumentSearch') && document.getElementById('instrumentSearch').value || '').trim().toLowerCase();
    const status = document.getElementById('filterInstrumentStatus') && document.getElementById('filterInstrumentStatus').value || '';
    const level = document.getElementById('filterInstrumentLevel') && document.getElementById('filterInstrumentLevel').value || '';

    filteredInstruments = allUserInstruments.filter(function (item) {
        const matchBranch = window.matchesOrganizationFilter(item,currentInstBranch);
        const matchSearch = !search || (item.title || '').toLowerCase().includes(search) || (item.summary || '').toLowerCase().includes(search);
        const matchStatus = !status || item.status === status;
        const matchLevel = !level || String(item.level_id) === String(level);
        return matchBranch && matchSearch && matchStatus && matchLevel;
    });
    instrumentsCurrentPage = 1;
    sortInstrumentItems();
    renderInstrumentsTable(filteredInstruments);
};

window.renderInstrumentsTable = async function (list) {
    list = list || filteredInstruments;
    const tbody = document.querySelector('#instrumentsTable tbody');
    if (!tbody) return;
    const totalPages = Math.ceil(list.length / instrumentsPerPage) || 1;
    if (instrumentsCurrentPage > totalPages) instrumentsCurrentPage = totalPages;
    const start = (instrumentsCurrentPage - 1) * instrumentsPerPage;
    const end = start + instrumentsPerPage;
    const pageItems = list.slice(start, end);
    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getInstrumentEmptyRowHTML ? window.getInstrumentEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getInstrumentRowHTML ? window.getInstrumentRowHTML(item, getLevelTitle(item.level_id)) : '';
            tbody.appendChild(tr);
            if (editingInstrumentRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50';
                expand.innerHTML = window.getInstrumentInlineExpandRowHTML ? window.getInstrumentInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updateInstrumentsPagination(list.length, start, end, totalPages);
    updateInstrumentSortIcons();
};

function updateInstrumentsPagination(total, start, end, totalPages) {
    const info = document.getElementById('instrumentsPaginationInfo');
    if (info) info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' مورد';
    const pagination = document.getElementById('instrumentsPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeInstrumentsPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (instrumentsCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeInstrumentsPage(' + (instrumentsCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (instrumentsCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, instrumentsCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeInstrumentsPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === instrumentsCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeInstrumentsPage(' + (instrumentsCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (instrumentsCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeInstrumentsPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (instrumentsCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeInstrumentsPage = async function (page) {
    const totalPages = Math.ceil(filteredInstruments.length / instrumentsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    instrumentsCurrentPage = page;
    renderInstrumentsTable(filteredInstruments);
};

window.promptAddInstrumentType = async function () {
    const name = (await AppDialog.prompt('نام ساز جدید را وارد کنید:') || '').trim();
    if (!name) return;
    if (sampleInstruments.some(function (i) { return i.title === name; })) return alert('این ساز قبلاً وجود دارد');
    const item = { id: Date.now(), title: name };
    sampleInstruments.push(item);
    document.querySelectorAll('select[id$="Select"], select[id*="InstSelect"]').forEach(function (sel) {
        if (!/Select|InstSelect/.test(sel.id)) return;
        const opt = document.createElement('option');
        opt.value = item.id; opt.textContent = name; opt.selected = true;
        sel.appendChild(opt);
    });
};

function readInstrumentForm(prefix) {
    const f = function (s) { return document.getElementById(prefix ? prefix + s : 'inst' + s); };
    const instId = parseInt(f('Select') && f('Select').value, 10);
    const inst = sampleInstruments.find(function (i) { return i.id === instId; });
    const branchId = parseInt(f('Branch') && f('Branch').value, 10);
    const branch = getInstBranches().find(function (b) { return b.id === branchId; });
    return {
        title: inst ? inst.title : '',
        instrument_id: instId,
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
    allUserInstruments.forEach(function (i) {
        if (i.user_id === userId && i.id !== excludeId) i.is_primary = 0;
    });
}

window.openAddInstrumentModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getInstrumentAddModalHTML ? window.getInstrumentAddModalHTML() : '';
};

window.saveInstrument = async function () {
    const data = readInstrumentForm('');
    if (!data.instrument_id) return alert('ساز را انتخاب کنید');
    const userId = 1;
    if (data.is_primary) enforcePrimary(userId, null);
    allUserInstruments.unshift(Object.assign({ id: Date.now(), user_id: userId }, data));
    filterInstruments();
    closeModal();
    alert('✅ ثبت شد');
};

window.viewInstrument = async function (id) {
    const item = allUserInstruments.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getInstrumentDetailsModalHTML
        ? window.getInstrumentDetailsModalHTML(item, getLevelTitle(item.level_id)) : '';
};

window.editInstrument = async function (id) {
    const item = allUserInstruments.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getInstrumentEditModalHTML
        ? window.getInstrumentEditModalHTML(item) : '';
};

window.saveEditedInstrument = async function (id) {
    const data = readInstrumentForm('editInst');
    if (!data.instrument_id) return alert('ساز را انتخاب کنید');
    const index = allUserInstruments.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    if (data.is_primary) enforcePrimary(allUserInstruments[index].user_id, id);
    allUserInstruments[index] = Object.assign({}, allUserInstruments[index], data);
    editingInstrumentRowId = null;
    filterInstruments();
    closeModal();
    alert('✅ ذخیره شد');
};

window.toggleInstrumentInlineEdit = async function (id) {
    editingInstrumentRowId = editingInstrumentRowId === id ? null : id;
    renderInstrumentsTable(filteredInstruments);
};

window.saveInlineInstrument = async function (id) {
    const data = readInstrumentForm('inlineInst' + id);
    if (!data.instrument_id) return alert('ساز را انتخاب کنید');
    const index = allUserInstruments.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    if (data.is_primary) enforcePrimary(allUserInstruments[index].user_id, id);
    allUserInstruments[index] = Object.assign({}, allUserInstruments[index], data);
    editingInstrumentRowId = null;
    filterInstruments();
    alert('✅ ذخیره شد');
};

window.deleteInstrument = async function (id) {
    if (!(await AppDialog.confirmDelete(allUserInstruments, id, 'ساز'))) return;
    await branchOfferingDelete('instrument',id);
    allUserInstruments = allUserInstruments.filter(function (i) { return i.id !== id; });
    if (editingInstrumentRowId === id) editingInstrumentRowId = null;
    filterInstruments();
};

window.applyInstrumentDatabaseData=function(data){window.branchOfferingBranches=data.branches||[];sampleInstruments=data.instruments_catalog||[];instrumentLevels=data.levels||[];allUserInstruments=data.instruments||[];filteredInstruments=allUserInstruments.slice();window.renderInstrumentsBranchTabs();window.filterInstruments();};
window.addEventListener('branch-offerings-loaded',function(e){window.applyInstrumentDatabaseData(e.detail);});
if(window.branchOfferingData)window.applyInstrumentDatabaseData(window.branchOfferingData);

window.exportInstrumentsToExcel = async function () {
    const data = filteredInstruments.length ? filteredInstruments : allUserInstruments;
    let csv = '\uFEFFردیف,ساز,سطح,سابقه,اصلی,وضعیت,شعبه\n';
    data.forEach(function (item, i) {
        csv += (i + 1) + ',"' + item.title + '","' + getLevelTitle(item.level_id) + '",' + (item.years_of_experience || 0) + ',' +
            (item.is_primary ? 'بله' : 'خیر') + ',"' + (item.status || '') + '","' + item.branchName + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'سازها_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportInstrumentsToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getInstrumentPDFModalHTML
        ? window.getInstrumentPDFModalHTML(instrumentPdfColumns) : '';
};

window.generateInstrumentsPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('instPdfTitle') && document.getElementById('instPdfTitle').value || 'گزارش سازها';
    const subtitle = document.getElementById('instPdfSubtitle') && document.getElementById('instPdfSubtitle').value || '';
    const footer = document.getElementById('instPdfFooter') && document.getElementById('instPdfFooter').value || '';
    const format = document.getElementById('instPdfFormat') && document.getElementById('instPdfFormat').value || 'a4';
    const orientation = document.getElementById('instPdfOrientation') && document.getElementById('instPdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('instPdfIncludeDate') && document.getElementById('instPdfIncludeDate').checked;
    const headerColor = document.getElementById('instPdfHeaderColor') && document.getElementById('instPdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('instPdfEvenRowColor') && document.getElementById('instPdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('instPdfOddRowColor') && document.getElementById('instPdfOddRowColor').value || '#f8fafc';
    const selectedColumns = instrumentPdfColumns.filter(function (c) {
        return document.getElementById('instPdfCol-' + c.field) && document.getElementById('instPdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = (filteredInstruments.length ? filteredInstruments : allUserInstruments).map(function (item) {
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
        wrap.innerHTML = window.getInstrumentPDFPageHTML(p + 1, pageRows, p === 0, {
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
    doc.save('سازها_' + date + '.pdf');
    closeModal();
};

(function () {
    setTimeout(function () {
        if (document.querySelector('#instrumentsTable tbody')) {
            renderInstrumentsBranchTabs();
            filterInstruments();
        }
    }, 200);
})();
