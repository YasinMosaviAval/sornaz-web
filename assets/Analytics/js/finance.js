// ==================== امور مالی ====================
const financeStatuses = ['تأیید شده', 'در انتظار تأیید', 'رد شده', 'حذف‌شده'];
const financeTypes = ['درآمد', 'هزینه'];

const financeTitleTemplates = {
    'درآمد': [
        'پرداخت شهریه هنرجو', 'پرداخت شهریه گروهی', 'ثبت‌نام دوره جدید',
        'فروش بلیت کنسرت', 'درآمد کارگاه', 'پرداخت قسط شهریه', 'هدیه حامی مالی'
    ],
    'هزینه': [
        'حقوق استاد', 'اجاره ماهیانه شعبه', 'خرید تجهیزات صوتی',
        'هزینه تبلیغات', 'تعمیرات کلاس', 'خرید نت و کتاب', 'قبوض آب و برق'
    ]
};

function getFinanceBranches() {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' },
        { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' },
        { id: 4, name: 'شعبه کرج' }
    ];
}

function toIsoDate(d) {
    return d.toISOString().split('T')[0];
}

function formatDisplayDate(iso) {
    if (!iso) return '—';
    try {
        return new Date(iso).toLocaleDateString('fa-IR');
    } catch (e) {
        return iso;
    }
}

// ۵۰ تراکنش نمونه
let allTransactions = [];
(function buildSampleTransactions() {
    const branches = getFinanceBranches();
    for (let i = 1; i <= 50; i++) {
        const type = financeTypes[Math.floor(Math.random() * financeTypes.length)];
        const titles = financeTitleTemplates[type];
        const title = titles[Math.floor(Math.random() * titles.length)] + ' #' + i;
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const amount = type === 'درآمد'
            ? (5 + Math.floor(Math.random() * 40)) * 100000
            : (8 + Math.floor(Math.random() * 80)) * 100000;
        const daysAgo = Math.floor(Math.random() * 120);
        const d = new Date();
        d.setDate(d.getDate() - daysAgo);
        const iso = toIsoDate(d);
        allTransactions.push({
            id: i,
            title: title,
            summary: 'خلاصه ' + title,
            description: 'توضیحات مربوط به ' + title + ' در ' + branch.name,
            branchId: branch.id,
            branchName: branch.name,
            type: type,
            amount: amount,
            dateIso: iso,
            date: formatDisplayDate(iso),
            status: financeStatuses[Math.floor(Math.random() * financeStatuses.length)]
        });
    }
})();

let currentFinanceBranch = 'all';
let financeCurrentPage = 1;
const financePerPage = 10;
let filteredTransactions = allTransactions.slice();
let editingFinanceRowId = null;
let financeSortField = '';
let financeSortDirection = 'asc';

const financePdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'title', label: 'شرح' },
    { field: 'branchName', label: 'شعبه' },
    { field: 'type', label: 'نوع' },
    { field: 'amountLabel', label: 'مبلغ' },
    { field: 'date', label: 'تاریخ' },
    { field: 'status', label: 'وضعیت' }
];

// ==================== مرتب‌سازی ====================
function sortFinanceItems() {
    if (!financeSortField) return;
    filteredTransactions.sort(function (a, b) {
        let av = a[financeSortField], bv = b[financeSortField];
        if (financeSortField === 'amount') {
            av = Number(av) || 0; bv = Number(bv) || 0;
        } else if (financeSortField === 'date') {
            av = a.dateIso || ''; bv = b.dateIso || '';
        } else {
            av = String(av || '').toLowerCase();
            bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return financeSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return financeSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateFinanceSortIcons = async function () {
    ['title', 'branchName', 'type', 'amount', 'date', 'status'].forEach(function (f) {
        const icon = document.getElementById('financeSortIcon-' + f);
        if (!icon) return;
        icon.textContent = financeSortField === f
            ? (financeSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortFinanceBy = async function (field) {
    if (financeSortField === field) {
        financeSortDirection = financeSortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        financeSortField = field;
        financeSortDirection = 'asc';
    }
    sortFinanceItems();
    renderFinanceTable(filteredTransactions);
    updateFinanceSortIcons();
};

// ==================== تب شعبه‌ها ====================
window.renderFinanceBranchTabs = async function () {
    const container = document.getElementById('financeBranchTabs');
    if (!container) return;
    container.querySelectorAll('.finance-branch-tab:not(:first-child)').forEach(function (t) { t.remove(); });
    getFinanceBranches().forEach(function (b) {
        const active = currentFinanceBranch == b.id;
        const btn = document.createElement('button');
        btn.className = 'finance-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = b.name;
        btn.onclick = function () { filterFinanceByBranch(b.id); };
        container.appendChild(btn);
    });
};

window.filterFinanceByBranch = async function (branchId) {
    currentFinanceBranch = branchId;
    document.querySelectorAll('.finance-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.finance-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        const name = getFinanceBranches().find(function (b) { return b.id == branchId; });
        tabs.forEach(function (tab) {
            if (name && tab.textContent === name.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    filterFinance();
};

// ==================== محدوده زمانی ====================
window.onFinanceRangeChange = async function () {
    const range = document.getElementById('filterFinanceRange') && document.getElementById('filterFinanceRange').value;
    const customBox = document.getElementById('financeCustomRangeBox');
    if (customBox) {
        customBox.classList.toggle('hidden', range !== 'custom');
    }
    filterFinance();
};

function matchesDateRange(item) {
    const range = document.getElementById('filterFinanceRange') && document.getElementById('filterFinanceRange').value || '';
    if (!range || !item.dateIso) return true;
    const d = new Date(item.dateIso);
    if (isNaN(d.getTime())) return true;
    const now = new Date();
    const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());

    if (range === 'weekly') {
        const from = new Date(startOfToday);
        from.setDate(from.getDate() - 7);
        return d >= from;
    }
    if (range === 'monthly') {
        const from = new Date(startOfToday);
        from.setDate(from.getDate() - 30);
        return d >= from;
    }
    if (range === 'yearly') {
        const from = new Date(startOfToday);
        from.setFullYear(from.getFullYear() - 1);
        return d >= from;
    }
    if (range === 'custom') {
        const fromVal = document.getElementById('filterFinanceFrom') && document.getElementById('filterFinanceFrom').value;
        const toVal = document.getElementById('filterFinanceTo') && document.getElementById('filterFinanceTo').value;
        if (fromVal) {
            const from = new Date(fromVal);
            if (d < from) return false;
        }
        if (toVal) {
            const to = new Date(toVal);
            to.setHours(23, 59, 59, 999);
            if (d > to) return false;
        }
        return true;
    }
    return true;
}

// ==================== فیلتر ====================
window.filterFinance = async function () {
    const search = (document.getElementById('financeSearch') && document.getElementById('financeSearch').value || '').trim().toLowerCase();
    const status = document.getElementById('filterFinanceStatus') && document.getElementById('filterFinanceStatus').value || '';
    const type = document.getElementById('filterFinanceType') && document.getElementById('filterFinanceType').value || '';

    filteredTransactions = allTransactions.filter(function (item) {
        if (item.status === 'حذف‌شده' && status !== 'حذف‌شده') {
            // still show if explicitly filtered; otherwise hide deleted by default? user asked for filter by status so show all unless filtered
        }
        const matchBranch = currentFinanceBranch === 'all' || item.branchId == currentFinanceBranch;
        const matchSearch = !search || (item.title || '').toLowerCase().includes(search) || (item.summary || '').toLowerCase().includes(search);
        const matchStatus = !status || item.status === status;
        const matchType = !type || item.type === type;
        const matchDate = matchesDateRange(item);
        return matchBranch && matchSearch && matchStatus && matchType && matchDate;
    });

    financeCurrentPage = 1;
    sortFinanceItems();
    renderFinanceTable(filteredTransactions);
    renderFinanceSummary();
};

// ==================== خلاصه مالی ====================
window.renderFinanceSummary = async function () {
    const container = document.getElementById('financeSummaryCards');
    if (!container) return;

    // فقط تراکنش‌های تأیید شده در خلاصه
    const list = filteredTransactions.filter(function (t) { return t.status === 'تأیید شده'; });
    const income = list.filter(function (t) { return t.type === 'درآمد'; }).reduce(function (s, t) { return s + (Number(t.amount) || 0); }, 0);
    const expense = list.filter(function (t) { return t.type === 'هزینه'; }).reduce(function (s, t) { return s + (Number(t.amount) || 0); }, 0);
    const balance = income - expense;

    container.innerHTML = `
        <div class="bg-white rounded-3xl p-6 shadow">
            <p class="text-gray-500 text-sm">کل درآمد (تأیید شده)</p>
            <p class="text-2xl font-bold text-green-600 mt-2">${income.toLocaleString('fa-IR')} تومان</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow">
            <p class="text-gray-500 text-sm">کل هزینه (تأیید شده)</p>
            <p class="text-2xl font-bold text-red-600 mt-2">${expense.toLocaleString('fa-IR')} تومان</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow">
            <p class="text-gray-500 text-sm">مانده</p>
            <p class="text-2xl font-bold ${balance >= 0 ? 'text-indigo-600' : 'text-red-600'} mt-2">${balance.toLocaleString('fa-IR')} تومان</p>
        </div>`;
};

// ==================== جدول ====================
window.renderFinanceTable = async function (list) {
    list = list || filteredTransactions;
    const tbody = document.querySelector('#financeTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / financePerPage) || 1;
    if (financeCurrentPage > totalPages) financeCurrentPage = totalPages;

    const start = (financeCurrentPage - 1) * financePerPage;
    const end = start + financePerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getFinanceEmptyRowHTML ? window.getFinanceEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getFinanceRowHTML ? window.getFinanceRowHTML(item) : '';
            tbody.appendChild(tr);
            if (editingFinanceRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50';
                expand.innerHTML = window.getFinanceInlineExpandRowHTML ? window.getFinanceInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updateFinancePagination(list.length, start, end, totalPages);
    updateFinanceSortIcons();
};

function updateFinancePagination(total, start, end, totalPages) {
    const info = document.getElementById('financePaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' تراکنش';
    }
    const pagination = document.getElementById('financePaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeFinancePage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (financeCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeFinancePage(' + (financeCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (financeCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, financeCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeFinancePage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === financeCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeFinancePage(' + (financeCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (financeCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeFinancePage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (financeCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeFinancePage = async function (page) {
    const totalPages = Math.ceil(filteredTransactions.length / financePerPage) || 1;
    if (page < 1 || page > totalPages) return;
    financeCurrentPage = page;
    renderFinanceTable(filteredTransactions);
};

// ==================== خواندن فرم ====================
function readFinanceForm(prefix) {
    const f = function (s) { return document.getElementById(prefix ? prefix + s : 'trans' + s); };
    const title = f('Title') && f('Title').value.trim();
    const amount = parseInt(f('Amount') && f('Amount').value, 10);
    const branchId = parseInt(f('Branch') && f('Branch').value, 10);
    const branch = getFinanceBranches().find(function (b) { return b.id === branchId; });
    const dateIso = f('Date') && f('Date').value || '';
    return {
        title: title,
        amount: amount,
        branchId: branchId,
        branchName: branch ? branch.name : 'نامشخص',
        type: f('Type') && f('Type').value || 'درآمد',
        status: f('Status') && f('Status').value || 'تأیید شده',
        summary: f('Summary') && f('Summary').value.trim() || '',
        description: f('Description') && f('Description').value.trim() || '',
        dateIso: dateIso,
        date: dateIso ? formatDisplayDate(dateIso) : '—'
    };
}

// ==================== CRUD ====================
window.openAddTransactionModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getFinanceAddModalHTML ? window.getFinanceAddModalHTML() : '';
};

window.saveTransaction = async function () {
    const data = readFinanceForm('');
    if (!data.title || !data.amount) return alert('شرح و مبلغ الزامی است');
    allTransactions.unshift(Object.assign({ id: Date.now() }, data));
    filterFinance();
    closeModal();
    alert('✅ تراکنش ثبت شد');
};

window.viewTransaction = async function (id) {
    const item = allTransactions.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getFinanceDetailsModalHTML
        ? window.getFinanceDetailsModalHTML(item) : '';
};

window.editTransaction = async function (id) {
    const item = allTransactions.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getFinanceEditModalHTML
        ? window.getFinanceEditModalHTML(item) : '';
};

window.saveEditedTransaction = async function (id) {
    const data = readFinanceForm('editTrans');
    if (!data.title || !data.amount) return alert('شرح و مبلغ الزامی است');
    const index = allTransactions.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allTransactions[index] = Object.assign({}, allTransactions[index], data);
    editingFinanceRowId = null;
    filterFinance();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.toggleFinanceInlineEdit = async function (id) {
    editingFinanceRowId = editingFinanceRowId === id ? null : id;
    renderFinanceTable(filteredTransactions);
};

window.saveInlineTransaction = async function (id) {
    const data = readFinanceForm('inlineTrans' + id);
    if (!data.title || !data.amount) return alert('شرح و مبلغ الزامی است');
    const index = allTransactions.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allTransactions[index] = Object.assign({}, allTransactions[index], data);
    editingFinanceRowId = null;
    filterFinance();
    alert('✅ تغییرات ذخیره شد');
};

window.deleteTransaction = async function (id) {
    if (!(await AppDialog.confirmDelete(allTransactions, id, 'تراکنش'))) return;
    allTransactions = allTransactions.filter(function (t) { return t.id !== id; });
    if (editingFinanceRowId === id) editingFinanceRowId = null;
    filterFinance();
};

// ==================== اکسل / PDF ====================
window.exportFinanceToExcel = async function () {
    const data = filteredTransactions.length ? filteredTransactions : allTransactions;
    let csv = '\uFEFFردیف,شرح,شعبه,نوع,مبلغ,تاریخ,وضعیت,خلاصه\n';
    data.forEach(function (item, i) {
        csv += (i + 1) + ',"' + item.title + '","' + item.branchName + '","' + item.type + '",' +
            item.amount + ',"' + (item.date || '') + '","' + item.status + ',"' + (item.summary || '') + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'امور_مالی_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportFinance = async function () {
    exportFinanceToExcel();
};

window.exportFinanceToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getFinancePDFModalHTML
        ? window.getFinancePDFModalHTML(financePdfColumns) : '';
};

window.generateFinancePDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('financePdfTitle') && document.getElementById('financePdfTitle').value || 'گزارش امور مالی';
    const subtitle = document.getElementById('financePdfSubtitle') && document.getElementById('financePdfSubtitle').value || '';
    const footer = document.getElementById('financePdfFooter') && document.getElementById('financePdfFooter').value || '';
    const format = document.getElementById('financePdfFormat') && document.getElementById('financePdfFormat').value || 'a4';
    const orientation = document.getElementById('financePdfOrientation') && document.getElementById('financePdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('financePdfIncludeDate') && document.getElementById('financePdfIncludeDate').checked;
    const headerColor = document.getElementById('financePdfHeaderColor') && document.getElementById('financePdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('financePdfEvenRowColor') && document.getElementById('financePdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('financePdfOddRowColor') && document.getElementById('financePdfOddRowColor').value || '#f8fafc';
    const selectedColumns = financePdfColumns.filter(function (c) {
        return document.getElementById('financePdfCol-' + c.field) && document.getElementById('financePdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = (filteredTransactions.length ? filteredTransactions : allTransactions).map(function (item) {
        return Object.assign({}, item, {
            amountLabel: Number(item.amount || 0).toLocaleString('fa-IR') + ' تومان'
        });
    });
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getFinancePDFPageHTML(p + 1, pageRows, p === 0, {
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
    doc.save('امور_مالی_' + date + '.pdf');
    closeModal();
};

// ==================== Init ====================
(function initFinance() {
    setTimeout(function () {
        if (document.getElementById('financeTable')) {
            renderFinanceBranchTabs();
            filterFinance();
        }
    }, 200);
})();
