(function () {
  'use strict';
  // ==================== گزارش‌های سیستمی ====================

  window.reportTypesList = ['حضور و غیاب', 'مالی', 'ثبت‌نام', 'آموزشی', 'نظرسنجی', 'عملکرد'];
  window.reportStatusesList = ['آماده', 'در حال تهیه'];
  window.reportPeriodLabels = {
    weekly: 'هفتگی',
    monthly: 'ماهانه',
    yearly: 'سالانه',
    custom: 'انتخابی',
  };

  window.getReportBranches = function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [];
  };

  let allReports = [];
  let reportBranches = [];
  let reportRequest = 0;
  async function loadReports() {
    const request = ++reportRequest;
    allReports = [];
    window.filterReports();
    try {
      const query = new URLSearchParams();
      if (currentReportBranch === 'academy') return;
      if (/^\d+$/.test(String(currentReportBranch))) query.set('branchId', currentReportBranch);
      const response = await fetch('/analytics/admin-reports?' + query, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
      const payload = await response.json();
      if (!response.ok || payload.success !== true || !Array.isArray(payload.data?.reports)) throw new Error(payload.message || 'دریافت گزارش‌ها انجام نشد.');
      if (request !== reportRequest) return;
      allReports = payload.data.reports.map(report => ({ ...report,
        date: document.documentElement.lang === 'en' ? report.dateISO : new Date(report.dateISO + 'T12:00:00').toLocaleDateString('fa-IR'),
      }));
      reportBranches = payload.data.branches || [];
      window.renderReportsBranchTabs();
      window.filterReports();
    } catch (error) {
      if (request !== reportRequest) return;
      allReports = [];
      window.filterReports();
      const tbody = document.querySelector('#reportsTable tbody');
      if (tbody) window.AdminPanelTable.setRows('#reportsTable', [{ cells: [{ value: error.message, attributes: 'colspan="7" class="py-12 text-center text-gray-400"' }] }]);
    }
  }

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
    { field: 'status', label: 'وضعیت' },
  ];

  window.onReportPeriodChange = function () {
    const period =
      document.getElementById('filterReportPeriod') &&
      document.getElementById('filterReportPeriod').value;
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
      let av = a[reportSortField],
        bv = b[reportSortField];
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
      if (icon)
        icon.textContent =
          reportSortField === f ? (reportSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
  };

  window.sortReportsBy = function (field) {
    if (reportSortField === field)
      reportSortDirection = reportSortDirection === 'asc' ? 'desc' : 'asc';
    else {
      reportSortField = field;
      reportSortDirection = 'asc';
    }
    sortReportItems();
    window.renderReportsTable(filteredReports);
    window.updateReportSortIcons();
  };

  window.renderReportsBranchTabs = function () {
    window.renderPanelOrganizationTabs?.('reportsBranchTabs', currentReportBranch, window.filterReportsByBranch, { tabClass: 'report-branch-tab', branches: reportBranches, showAcademy: false });
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
    loadReports();
  };

  window.filterReports = function () {
    const search = (
      (document.getElementById('reportSearch') && document.getElementById('reportSearch').value) ||
      ''
    )
      .trim()
      .toLowerCase();
    const type =
      (document.getElementById('filterReportType') &&
        document.getElementById('filterReportType').value) ||
      '';
    const status =
      (document.getElementById('filterReportStatus') &&
        document.getElementById('filterReportStatus').value) ||
      '';
    const period =
      (document.getElementById('filterReportPeriod') &&
        document.getElementById('filterReportPeriod').value) ||
      '';
    const dateFrom =
      (document.getElementById('reportDateFrom') &&
        document.getElementById('reportDateFrom').value) ||
      '';
    const dateTo =
      (document.getElementById('reportDateTo') && document.getElementById('reportDateTo').value) ||
      '';

    const now = new Date();
    const todayISO = now.toISOString().split('T')[0];

    filteredReports = allReports.filter(function (r) {
      const matchBranch = true; // The server enforces the selected branch scope.
      const matchSearch =
        !search ||
        (r.title || '').toLowerCase().includes(search) ||
        (r.type || '').toLowerCase().includes(search);
      const matchType = !type || r.type === type;
      const matchStatus = !status || r.status === status;

      let matchPeriod = true;
      if (period === 'weekly' || period === 'monthly' || period === 'yearly' || period === 'current') {
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
    const pageItems = window.AdminPanelTable.page?.('#reportsTable', list, start, end, () => window.renderReportsTable(list), undefined, row => window.getReportRowHTML(row)) ?? list.slice(start, end);

    const rows = pageItems.length
      ? pageItems.map(item => ({ cells: window.getReportRowCells(item), attributes: ' class="hover:bg-gray-50 transition" data-id="' + Number(item.id) + '"' }))
      : [{ cells: [{ value: document.documentElement.lang.startsWith('en') ? 'No reports found' : 'گزارشی یافت نشد', attributes: 'colspan="7" class="py-12 text-center text-gray-400"' }] }];
    window.AdminPanelTable.setRows('#reportsTable', rows, { pageOffset: start });
    updateReportsPagination(list.length, start, end, totalPages);
    window.updateReportSortIcons();
  };

  function updateReportsPagination(total) {
    window.AdminPanelTable.renderPagination(document.getElementById('reportsPagination'), { total, perPage: 10, page: reportsCurrentPage, onChange: next => window.changeReportsPage(next) });
  }

  window.changeReportsPage = function (page) {
    const totalPages = Math.ceil(filteredReports.length / reportsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    reportsCurrentPage = page;
    window.renderReportsTable(filteredReports);
  };

  window.viewReport = function (id) {
    const item = allReports.find(function (x) {
      return x.id === id;
    });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getReportDetailsModalHTML
      ? window.getReportDetailsModalHTML(item)
      : '';
  };

  window.AcademyExports?.register('reports', () => filteredReports);
window.exportReportsToExcel = async function () {
  if (!window.AcademyExports || !(await window.AcademyExports.authorize(filteredReports))) return;
    const data = filteredReports;
    let csv = '\uFEFFردیف,عنوان,شعبه,نوع,بازه زمانی,از تاریخ,تا تاریخ,تاریخ تولید,وضعیت\n';
    data.forEach(function (r, i) {
      csv +=
        i +
        1 +
        ',"' +
        r.title +
        '","' +
        r.branchName +
        '","' +
        r.type +
        '","' +
        r.periodLabel +
        '","' +
        (r.periodFrom || '') +
        '","' +
        (r.periodTo || '') +
        '","' +
        r.date +
        '","' +
        r.status +
        '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'گزارش‌ها_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
  };

  window.exportReportsToPDF = async function () {
  if (!window.AcademyExports || !(await window.AcademyExports.authorize(filteredReports))) return;
    document.getElementById('modalContainer').innerHTML = window.getReportPDFModalHTML
      ? window.getReportPDFModalHTML(reportPdfColumns)
      : '';
  };

  window.generateReportsPDF = async function () {
  if (!window.AcademyExports || !(await window.AcademyExports.authorize(filteredReports))) return;
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title =
      (document.getElementById('reportPdfTitle') &&
        document.getElementById('reportPdfTitle').value) ||
      'گزارش‌های آموزشگاه';
    const subtitle =
      (document.getElementById('reportPdfSubtitle') &&
        document.getElementById('reportPdfSubtitle').value) ||
      '';
    const footer =
      (document.getElementById('reportPdfFooter') &&
        document.getElementById('reportPdfFooter').value) ||
      '';
    const format =
      (document.getElementById('reportPdfFormat') &&
        document.getElementById('reportPdfFormat').value) ||
      'a4';
    const orientation =
      (document.getElementById('reportPdfOrientation') &&
        document.getElementById('reportPdfOrientation').value) ||
      'landscape';
    const includeDate =
      document.getElementById('reportPdfIncludeDate') &&
      document.getElementById('reportPdfIncludeDate').checked;
    const headerColor =
      (document.getElementById('reportPdfHeaderColor') &&
        document.getElementById('reportPdfHeaderColor').value) ||
      '#eff6ff';
    const evenRowColor =
      (document.getElementById('reportPdfEvenRowColor') &&
        document.getElementById('reportPdfEvenRowColor').value) ||
      '#ffffff';
    const oddRowColor =
      (document.getElementById('reportPdfOddRowColor') &&
        document.getElementById('reportPdfOddRowColor').value) ||
      '#f8fafc';
    const selectedColumns = reportPdfColumns.filter(function (c) {
      return (
        document.getElementById('reportPdfCol-' + c.field) &&
        document.getElementById('reportPdfCol-' + c.field).checked
      );
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredReports;
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
      const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
      const wrap = document.createElement('div');
      wrap.style.cssText =
        'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' +
        (orientation === 'portrait' ? '900' : '1400') +
        'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
      wrap.innerHTML = window.getReportPDFPageHTML(p + 1, pageRows, p === 0, {
        title: title,
        subtitle: subtitle,
        footer: footer,
        includeDate: includeDate,
        date: date,
        headerColor: headerColor,
        evenRowColor: evenRowColor,
        oddRowColor: oddRowColor,
        selectedColumns: selectedColumns,
        rowsPerPage: rowsPerPage,
        totalPages: totalPages,
      });
      document.body.appendChild(wrap);
      canvasPages.push(
        await html2canvas(wrap, { scale: 2, useCORS: true, backgroundColor: '#ffffff' })
      );
      wrap.remove();
    }
    const doc = new window.jspdf.jsPDF({ orientation: orientation, unit: 'pt', format: format });
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 20,
      imgWidth = pageWidth - margin * 2;
    canvasPages.forEach(function (canvas, i) {
      if (i > 0) doc.addPage();
      doc.addImage(
        canvas.toDataURL('image/png'),
        'PNG',
        margin,
        margin,
        imgWidth,
        (canvas.height * imgWidth) / canvas.width
      );
    });
    doc.save('گزارش‌ها_' + date + '.pdf');
    closeModal();
  };

  setTimeout(function () {
    if (document.getElementById('reportsTable')) {
      window.renderReportsBranchTabs();
      loadReports();
    }
  }, 200);
  window.addEventListener('panel-organizations-changed', () => { Promise.resolve(loadReports()).catch(console.error); });
})();
