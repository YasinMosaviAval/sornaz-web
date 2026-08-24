(function () {
'use strict';
// ==================== گزارش‌های سیستمی ====================

window.reportTypesList = ['حضور و غیاب', 'مالی', 'ثبت‌نام', 'آموزشی', 'نظرسنجی', 'عملکرد'];
window.reportStatusesList = ['آماده', 'در حال تهیه'];
window.reportPeriodLabels = { weekly: 'هفتگی', monthly: 'ماهانه', yearly: 'سالانه', custom: 'انتخابی' };

window.getReportBranches = function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' },
        { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' },
        { id: 4, name: 'شعبه کرج' }
    ];
};

const reportTitleTemplates = [
    { title: 'گزارش حضور و غیاب', type: 'حضور و غیاب', period: 'monthly' },
    { title: 'گزارش درآمد', type: 'مالی', period: 'monthly' },
    { title: 'آمار ثبت‌نام هنرجویان جدید', type: 'ثبت‌نام', period: 'weekly' },
    { title: 'گزارش عملکرد اساتید', type: 'آموزشی', period: 'monthly' },
    { title: 'گزارش بدهی هنرجویان', type: 'مالی', period: 'monthly' },
    { title: 'آمار کلاس‌های برگزار شده', type: 'آموزشی', period: 'weekly' },
    { title: 'گزارش رضایت هنرجویان', type: 'نظرسنجی', period: 'yearly' },
    { title: 'خلاصه عملکرد شعبه', type: 'عملکرد', period: 'monthly' },
    { title: 'گزارش شهریه‌های وصول‌شده', type: 'مالی', period: 'weekly' },
    { title: 'آمار جلسات لغو شده', type: 'حضور و غیاب', period: 'weekly' },
    { title: 'گزارش سالانه مالی', type: 'مالی', period: 'yearly' },
    { title: 'آمار ارتقای سطح هنرجویان', type: 'آموزشی', period: 'yearly' }
];

let allReports = [];
(function buildSample() {
    const branches = window.getReportBranches();
    let id = 1;
    for (let i = 0; i < 80; i++) {
        const tpl = reportTitleTemplates[i % reportTitleTemplates.length];
        const branch = branches[i % branches.length];
        const d = new Date();
        d.setDate(d.getDate() - Math.floor(Math.random() * 120));
        const dateISO = d.toISOString().split('T')[0];
        const period = tpl.period;
        let periodFrom = dateISO, periodTo = dateISO;
        if (period === 'weekly') {
            const from = new Date(d); from.setDate(from.getDate() - 6);
            periodFrom = from.toISOString().split('T')[0];
            periodTo = dateISO;
        } else if (period === 'monthly') {
            periodFrom = dateISO.slice(0, 8) + '01';
            periodTo = dateISO;
        } else if (period === 'yearly') {
            periodFrom = dateISO.slice(0, 4) + '-01-01';
            periodTo = dateISO;
        }
        allReports.push({
            id: id++,
            title: tpl.title + ' — ' + branch.name,
            branchId: branch.id,
            branchName: branch.name,
            type: tpl.type,
            period: period,
            periodLabel: window.reportPeriodLabels[period] || period,
            periodFrom: periodFrom,
            periodTo: periodTo,
            date: d.toLocaleDateString('fa-IR'),
            dateISO: dateISO,
            status: Math.random() > 0.25 ? 'آماده' : 'در حال تهیه',
            summary: 'این گزارش به‌صورت خودکار توسط سیستم تولید شده و شامل خلاصه آماری بازه زمانی مشخص است.',
            metrics: {
                total: 40 + Math.floor(Math.random() * 200),
                success: 30 + Math.floor(Math.random() * 150),
                pending: Math.floor(Math.random() * 30)
            }
        });
    }
})();

let currentReportBranch = 'all';
let reportsCurrentPage = 1;
const reportsPerPage = 10;
let filteredReports = allReports.slice();
let reportSortField = '';
let reportSortDirection = 'asc';

const reportPdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'title', label: 'عنوان' },
    { field: 'branchName', label: 'شعبه' },
    { field: 'type', label: 'نوع' },
    { field: 'periodLabel', label: 'بازه زمانی' },
    { field: 'date', label: 'تاریخ' },
    { field: 'status', label: 'وضعیت' }
];

window.onReportPeriodChange = function () {
    const period = document.getElementById('filterReportPeriod') && document.getElementById('filterReportPeriod').value;
    const fromWrap = document.getElementById('reportCustomDateFromWrap');
    const toWrap = document.getElementById('reportCustomDateToWrap');
    const show = period === 'custom';
    if (fromWrap) fromWrap.classList.toggle('hidden', !show);
    if (toWrap) toWrap.classList.toggle('hidden', !show);
    window.filterReports();
};

function sortReportItems() {
    if (!reportSortField) return;
    filteredReports.sort(function (a, b) {
        let av = a[reportSortField], bv = b[reportSortField];
        if (reportSortField === 'date') {
            av = a.dateISO || '';
            bv = b.dateISO || '';
        } else {
            av = String(av || '').toLowerCase();
            bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return reportSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return reportSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateReportSortIcons = function () {
    ['title', 'branchName', 'type', 'periodLabel', 'date', 'status'].forEach(function (f) {
        const icon = document.getElementById('reportSortIcon-' + f);
        if (icon) icon.textContent = reportSortField === f ? (reportSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortReportsBy = function (field) {
    if (reportSortField === field) reportSortDirection = reportSortDirection === 'asc' ? 'desc' : 'asc';
    else { reportSortField = field; reportSortDirection = 'asc'; }
    sortReportItems();
    window.renderReportsTable(filteredReports);
    window.updateReportSortIcons();
};

window.renderReportsBranchTabs = function () {
    const container = document.getElementById('reportsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.report-branch-tab:not([data-value="all"])').forEach(function (t) { t.remove(); });
    window.getReportBranches().forEach(function (b) {
        const active = String(currentReportBranch) === String(b.id);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'report-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50');
        btn.dataset.value = b.id;
        btn.textContent = b.name;
        btn.onclick = function () { window.filterReportsByBranch(b.id); };
        container.appendChild(btn);
    });
    const allTab = container.querySelector('[data-value="all"]');
    if (allTab) {
        const isAll = currentReportBranch === 'all';
        allTab.classList.toggle('bg-indigo-600', isAll);
        allTab.classList.toggle('text-white', isAll);
        allTab.classList.toggle('border-indigo-600', isAll);
        if (!isAll) {
            allTab.classList.add('border', 'border-gray-200');
            allTab.classList.remove('bg-indigo-600', 'text-white');
        }
    }
};

window.filterReportsByBranch = function (branchId) {
    currentReportBranch = branchId;
    document.querySelectorAll('.report-branch-tab').forEach(function (tab) {
        const active = String(tab.dataset.value) === String(branchId);
        tab.classList.toggle('bg-indigo-600', active);
        tab.classList.toggle('text-white', active);
        tab.classList.toggle('border-indigo-600', active);
        if (!active) {
            tab.classList.add('border', 'border-gray-200');
            tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        } else tab.classList.remove('border-gray-200');
    });
    window.filterReports();
};

window.filterReports = function () {
    const search = (document.getElementById('reportSearch') && document.getElementById('reportSearch').value || '').trim().toLowerCase();
    const type = document.getElementById('filterReportType') && document.getElementById('filterReportType').value || '';
    const status = document.getElementById('filterReportStatus') && document.getElementById('filterReportStatus').value || '';
    const period = document.getElementById('filterReportPeriod') && document.getElementById('filterReportPeriod').value || '';
    const dateFrom = document.getElementById('reportDateFrom') && document.getElementById('reportDateFrom').value || '';
    const dateTo = document.getElementById('reportDateTo') && document.getElementById('reportDateTo').value || '';

    const now = new Date();
    const todayISO = now.toISOString().split('T')[0];

    filteredReports = allReports.filter(function (r) {
        const matchBranch = window.matchesOrganizationFilter(r,currentReportBranch);
        const matchSearch = !search || (r.title || '').toLowerCase().includes(search) || (r.type || '').toLowerCase().includes(search);
        const matchType = !type || r.type === type;
        const matchStatus = !status || r.status === status;

        let matchPeriod = true;
        if (period === 'weekly' || period === 'monthly' || period === 'yearly') {
            matchPeriod = r.period === period;
        } else if (period === 'custom') {
            if (dateFrom && r.dateISO < dateFrom) matchPeriod = false;
            if (dateTo && r.dateISO > dateTo) matchPeriod = false;
        }

        return matchBranch && matchSearch && matchType && matchStatus && matchPeriod;
    });

    reportsCurrentPage = 1;
    sortReportItems();
    window.renderReportsTable(filteredReports);
};

window.renderReportsTable = function (list) {
    list = list || filteredReports;
    const tbody = document.querySelector('#reportsTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / reportsPerPage) || 1;
    if (reportsCurrentPage > totalPages) reportsCurrentPage = totalPages;

    const start = (reportsCurrentPage - 1) * reportsPerPage;
    const end = start + reportsPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getReportEmptyRowHTML ? window.getReportEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getReportRowHTML ? window.getReportRowHTML(item) : '';
            tbody.appendChild(tr);
        });
    }
    updateReportsPagination(list.length, start, end, totalPages);
    window.updateReportSortIcons();
};

function updateReportsPagination(total, start, end, totalPages) {
    const info = document.getElementById('reportsPaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' گزارش';
    }
    const pagination = document.getElementById('reportsPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeReportsPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (reportsCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeReportsPage(' + (reportsCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (reportsCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, reportsCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeReportsPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === reportsCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeReportsPage(' + (reportsCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (reportsCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeReportsPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (reportsCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeReportsPage = function (page) {
    const totalPages = Math.ceil(filteredReports.length / reportsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    reportsCurrentPage = page;
    window.renderReportsTable(filteredReports);
};

window.viewReport = function (id) {
    const item = allReports.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getReportDetailsModalHTML
        ? window.getReportDetailsModalHTML(item) : '';
};

window.exportReportsToExcel = function () {
    const data = filteredReports.length ? filteredReports : allReports;
    let csv = '\uFEFFردیف,عنوان,شعبه,نوع,بازه زمانی,از تاریخ,تا تاریخ,تاریخ تولید,وضعیت\n';
    data.forEach(function (r, i) {
        csv += (i + 1) + ',"' + r.title + '","' + r.branchName + '","' + r.type + '","' + r.periodLabel + '","' +
            (r.periodFrom || '') + '","' + (r.periodTo || '') + '","' + r.date + '","' + r.status + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'گزارش‌ها_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportReportsToPDF = function () {
    document.getElementById('modalContainer').innerHTML = window.getReportPDFModalHTML
        ? window.getReportPDFModalHTML(reportPdfColumns) : '';
};

window.generateReportsPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('reportPdfTitle') && document.getElementById('reportPdfTitle').value || 'گزارش‌های آموزشگاه';
    const subtitle = document.getElementById('reportPdfSubtitle') && document.getElementById('reportPdfSubtitle').value || '';
    const footer = document.getElementById('reportPdfFooter') && document.getElementById('reportPdfFooter').value || '';
    const format = document.getElementById('reportPdfFormat') && document.getElementById('reportPdfFormat').value || 'a4';
    const orientation = document.getElementById('reportPdfOrientation') && document.getElementById('reportPdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('reportPdfIncludeDate') && document.getElementById('reportPdfIncludeDate').checked;
    const headerColor = document.getElementById('reportPdfHeaderColor') && document.getElementById('reportPdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('reportPdfEvenRowColor') && document.getElementById('reportPdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('reportPdfOddRowColor') && document.getElementById('reportPdfOddRowColor').value || '#f8fafc';
    const selectedColumns = reportPdfColumns.filter(function (c) {
        return document.getElementById('reportPdfCol-' + c.field) && document.getElementById('reportPdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredReports.length ? filteredReports : allReports;
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getReportPDFPageHTML(p + 1, pageRows, p === 0, {
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
    doc.save('گزارش‌ها_' + date + '.pdf');
    closeModal();
};

setTimeout(function () {
    if (document.getElementById('reportsTable')) {
        window.renderReportsBranchTabs();
        window.filterReports();
    }
}, 200);
})();
