(function () {
'use strict';
// ==================== قوانین زمان‌بندی (ایزوله) ====================
window.ruleTypesList = ['لغو', 'جبرانی', 'رزرو', 'زمان‌بندی'];
window.ruleStatusesList = ['فعال', 'غیرفعال', 'در انتظار تأیید', 'حذف‌شده'];

const ruleTitlePool = [
    'حداقل زمان لغو کلاس', 'حداکثر جلسات جبرانی در ماه', 'ساعت شروع رزرو آنلاین',
    'ساعت پایان رزرو آنلاین', 'حداقل فاصله بین دو کلاس استاد', 'حداکثر کلاس روزانه استاد',
    'مهلت رزرو کلاس گروهی', 'قفل خودکار کلاس پس از غیبت', 'حداکثر ظرفیت کلاس گروهی',
    'مدت جلسه خصوصی پیش‌فرض', 'مدت جلسه گروهی پیش‌فرض', 'محدودیت تغییر زمان کلاس',
    'الزام تأیید مدیر برای جبرانی', 'حداقل سن ثبت‌نام آنلاین', 'سقف غیبت مجاز ماهانه'
];
const ruleValuePool = [
    '۲۴ ساعت قبل', '۲ جلسه', '۰۸:۰۰', '۲۲:۰۰', '۱۵ دقیقه', '۶ جلسه',
    '۴۸ ساعت قبل', '۳ غیبت متوالی', '۱۲ نفر', '۶۰ دقیقه', '۹۰ دقیقه',
    '۷۲ ساعت قبل', 'بله', '۷ سال', '۲ غیبت'
];

window.getRuleBranches = async function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' },
        { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' },
        { id: 4, name: 'شعبه کرج' }
    ];
};

let allRules = [];
(function buildSampleRules() {
    const branches = window.getRuleBranches();
    for (let i = 1; i <= 30; i++) {
        const title = ruleTitlePool[Math.floor(Math.random() * ruleTitlePool.length)] + (i > 15 ? ' #' + i : '');
        const type = window.ruleTypesList[Math.floor(Math.random() * window.ruleTypesList.length)];
        const value = ruleValuePool[Math.floor(Math.random() * ruleValuePool.length)];
        const status = window.ruleStatusesList[Math.floor(Math.random() * window.ruleStatusesList.length)];
        const branch = branches[Math.floor(Math.random() * branches.length)];
        allRules.push({
            id: i, title: title,
            summary: 'خلاصه قانون ' + title,
            description: 'توضیحات مربوط به «' + title + '» در ' + branch.name + ' با مقدار ' + value,
            branchId: branch.id, branchName: branch.name,
            type: type, value: value, status: status
        });
    }
})();

let currentRuleBranch = 'all';
let rulesCurrentPage = 1;
const rulesPerPage = 10;
let filteredRules = allRules.slice();
let editingRuleRowId = null;
let ruleSortField = '';
let ruleSortDirection = 'asc';

const rulePdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'title', label: 'عنوان' },
    { field: 'branchName', label: 'شعبه' },
    { field: 'type', label: 'نوع' },
    { field: 'value', label: 'مقدار' },
    { field: 'status', label: 'وضعیت' }
];

window.promptAddRuleType = async function (selectId) {
    const name = await AppDialog.prompt('نام نوع قانون جدید را وارد کنید:');
    const sel = document.getElementById(selectId);
    if (!name || !name.trim()) {
        if (sel) sel.value = window.ruleTypesList[0] || '';
        return;
    }
    const t = name.trim();
    if (window.ruleTypesList.indexOf(t) === -1) {
        window.ruleTypesList.push(t);
        window.renderRuleTypeFilter();
    }
    if (sel) {
        const opt = document.createElement('option');
        opt.value = t; opt.textContent = t;
        sel.insertBefore(opt, sel.lastElementChild);
        sel.value = t;
    }
};

window.renderRuleTypeFilter = async function () {
    const sel = document.getElementById('filterRuleType');
    if (!sel) return;
    const cur = sel.value;
    sel.innerHTML = '<option value="">همه انواع</option>' +
        window.ruleTypesList.map(function (t) {
            return '<option value="' + t + '"' + (t === cur ? ' selected' : '') + '>' + t + '</option>';
        }).join('');
};

function sortRuleItems() {
    if (!ruleSortField) return;
    filteredRules.sort(function (a, b) {
        let av = String(a[ruleSortField] || '').toLowerCase();
        let bv = String(b[ruleSortField] || '').toLowerCase();
        if (av < bv) return ruleSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return ruleSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateRuleSortIcons = async function () {
    ['title', 'branchName', 'type', 'value', 'status'].forEach(function (f) {
        const icon = document.getElementById('ruleSortIcon-' + f);
        if (!icon) return;
        icon.textContent = ruleSortField === f ? (ruleSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortRulesBy = async function (field) {
    if (ruleSortField === field) ruleSortDirection = ruleSortDirection === 'asc' ? 'desc' : 'asc';
    else { ruleSortField = field; ruleSortDirection = 'asc'; }
    sortRuleItems();
    window.renderRulesTable(filteredRules);
    window.updateRuleSortIcons();
};

window.renderRulesBranchTabs = async function () {
    const container = document.getElementById('rulesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.rule-branch-tab:not(:first-child)').forEach(function (t) { t.remove(); });
    window.getRuleBranches().forEach(function (b) {
        const active = currentRuleBranch == b.id;
        const btn = document.createElement('button');
        btn.className = 'rule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = b.name;
        btn.onclick = function () { window.filterRulesByBranch(b.id); };
        container.appendChild(btn);
    });
};

window.filterRulesByBranch = async function (branchId) {
    currentRuleBranch = branchId;
    document.querySelectorAll('.rule-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.rule-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        const name = window.getRuleBranches().find(function (b) { return b.id == branchId; });
        tabs.forEach(function (tab) {
            if (name && tab.textContent === name.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    window.filterRules();
};

window.filterRules = async function () {
    const search = (document.getElementById('ruleSearch') && document.getElementById('ruleSearch').value || '').trim().toLowerCase();
    const status = document.getElementById('filterRuleStatus') && document.getElementById('filterRuleStatus').value || '';
    const type = document.getElementById('filterRuleType') && document.getElementById('filterRuleType').value || '';

    filteredRules = allRules.filter(function (item) {
        const matchBranch = currentRuleBranch === 'all' || item.branchId == currentRuleBranch;
        const matchSearch = !search ||
            (item.title || '').toLowerCase().includes(search) ||
            (item.summary || '').toLowerCase().includes(search) ||
            (item.value || '').toLowerCase().includes(search);
        const matchStatus = !status || item.status === status;
        const matchType = !type || item.type === type;
        return matchBranch && matchSearch && matchStatus && matchType;
    });

    rulesCurrentPage = 1;
    sortRuleItems();
    window.renderRulesTable(filteredRules);
};

window.renderRulesTable = async function (list) {
    list = list || filteredRules;
    const tbody = document.querySelector('#rulesTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / rulesPerPage) || 1;
    if (rulesCurrentPage > totalPages) rulesCurrentPage = totalPages;

    const start = (rulesCurrentPage - 1) * rulesPerPage;
    const end = start + rulesPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getRuleEmptyRowHTML ? window.getRuleEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getRuleRowHTML ? window.getRuleRowHTML(item) : '';
            tbody.appendChild(tr);
            if (editingRuleRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50';
                expand.innerHTML = window.getRuleInlineExpandRowHTML ? window.getRuleInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updateRulesPagination(list.length, start, end, totalPages);
    window.updateRuleSortIcons();
};

function updateRulesPagination(total, start, end, totalPages) {
    const info = document.getElementById('rulesPaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' قانون';
    }
    const pagination = document.getElementById('rulesPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeRulesPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (rulesCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeRulesPage(' + (rulesCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (rulesCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, rulesCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeRulesPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === rulesCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeRulesPage(' + (rulesCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (rulesCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeRulesPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (rulesCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeRulesPage = async function (page) {
    const totalPages = Math.ceil(filteredRules.length / rulesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    rulesCurrentPage = page;
    window.renderRulesTable(filteredRules);
};

function readRuleForm(prefix) {
    const f = function (s) { return document.getElementById(prefix ? prefix + s : 'rule' + s); };
    const branchId = parseInt(f('Branch') && f('Branch').value, 10);
    const branch = window.getRuleBranches().find(function (b) { return b.id === branchId; });
    return {
        branchId: branchId,
        branchName: branch ? branch.name : 'نامشخص',
        title: f('Title') && f('Title').value.trim() || '',
        type: f('Type') && f('Type').value || '',
        value: f('Value') && f('Value').value.trim() || '—',
        status: f('Status') && f('Status').value || 'فعال',
        summary: f('Summary') && f('Summary').value.trim() || '',
        description: f('Description') && f('Description').value.trim() || ''
    };
}

window.openAddRuleModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getRuleAddModalHTML ? window.getRuleAddModalHTML() : '';
};

window.saveRule = async function () {
    const data = readRuleForm('');
    if (!data.title) return alert('عنوان قانون الزامی است');
    if (data.type === '__new__') return alert('لطفاً یک نوع قانون انتخاب کنید');
    allRules.unshift(Object.assign({ id: Date.now() }, data));
    window.filterRules();
    closeModal();
    alert('✅ قانون ثبت شد');
};

window.viewRule = async function (id) {
    const item = allRules.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getRuleDetailsModalHTML
        ? window.getRuleDetailsModalHTML(item) : '';
};

window.editRule = async function (id) {
    const item = allRules.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getRuleEditModalHTML
        ? window.getRuleEditModalHTML(item) : '';
};

window.saveEditedRule = async function (id) {
    const data = readRuleForm('editRule');
    if (!data.title) return alert('عنوان قانون الزامی است');
    if (data.type === '__new__') return alert('لطفاً یک نوع قانون انتخاب کنید');
    const index = allRules.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allRules[index] = Object.assign({}, allRules[index], data);
    editingRuleRowId = null;
    window.filterRules();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.toggleRuleInlineEdit = async function (id) {
    editingRuleRowId = editingRuleRowId === id ? null : id;
    window.renderRulesTable(filteredRules);
};

window.saveInlineRule = async function (id) {
    const data = readRuleForm('inlineRule' + id);
    if (!data.title) return alert('عنوان قانون الزامی است');
    if (data.type === '__new__') return alert('لطفاً یک نوع قانون انتخاب کنید');
    const index = allRules.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allRules[index] = Object.assign({}, allRules[index], data);
    editingRuleRowId = null;
    window.filterRules();
    alert('✅ تغییرات ذخیره شد');
};

window.deleteRule = async function (id) {
    if (!(await AppDialog.confirmDelete(allRules, id, 'قانون'))) return;
    allRules = allRules.filter(function (r) { return r.id !== id; });
    if (editingRuleRowId === id) editingRuleRowId = null;
    window.filterRules();
};

window.exportRulesToExcel = async function () {
    const data = filteredRules.length ? filteredRules : allRules;
    let csv = '\uFEFFردیف,عنوان,شعبه,نوع,مقدار,وضعیت,خلاصه\n';
    data.forEach(function (item, i) {
        csv += (i + 1) + ',"' + item.title + '","' + item.branchName + '","' + item.type + '","' +
            item.value + '","' + item.status + '","' + (item.summary || '') + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'قوانین_زمانبندی_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportRulesToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getRulePDFModalHTML
        ? window.getRulePDFModalHTML(rulePdfColumns) : '';
};

window.generateRulesPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('rulePdfTitle') && document.getElementById('rulePdfTitle').value || 'گزارش قوانین زمان‌بندی';
    const subtitle = document.getElementById('rulePdfSubtitle') && document.getElementById('rulePdfSubtitle').value || '';
    const footer = document.getElementById('rulePdfFooter') && document.getElementById('rulePdfFooter').value || '';
    const format = document.getElementById('rulePdfFormat') && document.getElementById('rulePdfFormat').value || 'a4';
    const orientation = document.getElementById('rulePdfOrientation') && document.getElementById('rulePdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('rulePdfIncludeDate') && document.getElementById('rulePdfIncludeDate').checked;
    const headerColor = document.getElementById('rulePdfHeaderColor') && document.getElementById('rulePdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('rulePdfEvenRowColor') && document.getElementById('rulePdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('rulePdfOddRowColor') && document.getElementById('rulePdfOddRowColor').value || '#f8fafc';
    const selectedColumns = rulePdfColumns.filter(function (c) {
        return document.getElementById('rulePdfCol-' + c.field) && document.getElementById('rulePdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredRules.length ? filteredRules : allRules;
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getRulePDFPageHTML(p + 1, pageRows, p === 0, {
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
    doc.save('قوانین_زمانبندی_' + date + '.pdf');
    closeModal();
};

setTimeout(function () {
    if (document.getElementById('rulesTable')) {
        window.renderRulesBranchTabs();
        window.renderRuleTypeFilter();
        window.filterRules();
    }
}, 200);
})();
