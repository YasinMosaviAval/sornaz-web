// ==================== نمونه داده پرسنل و قرارداد ====================
const staffFirstNames = ["محمد", "علی", "رضا", "حسین", "امیر", "سعید", "مهدی", "احمد", "کامران", "بهرام"];
const staffLastNames = ["موسوی", "رضایی", "بهرامی", "کاظمی", "نوری", "جعفری", "احمدی", "حسینی", "کریمی", "محمدی"];
const staffTypes = [
    { value: 'teacher', label: 'استاد' },
    { value: 'receptionist', label: 'پذیرش' },
    { value: 'manager', label: 'مدیر' },
    { value: 'other', label: 'سایر' }
];
const branches = ["شعبه مرکزی", "شعبه ونک", "شعبه سعادت‌آباد", "شعبه کرج"];
const contractCurrencies = [
    { id: 1, label: 'تومان' },
    { id: 2, label: 'دلار' },
    { id: 3, label: 'یورو' }
];
const contractTitles = [
    'قرارداد آموزش تخصصی',
    'قرارداد خدمات اداری',
    'قرارداد مدیریت داخلی',
    'قرارداد پشتیبانی پذیرش',
    'قرارداد همکاری آموزش'
];
const contractDescriptions = [
    'این قرارداد به منظور انجام امور آموزشی و پشتیبانی آموزشی تنظیم شده است.',
    'پرسنل موظف است در طول مدت قرارداد وظایف محوله را مطابق دستور مدیریت انجام دهد.',
    'شرح قرارداد شامل ارائه کلاس‌های تخصصی، نظارت بر روند آموزشی و پشتیبانی اداری است.',
    'این قرارداد برای انجام خدمات پذیرش و هماهنگی دانشجویان در نظر گرفته شده است.',
    'قرارداد شامل انجام وظایف مدیریتی، گزارش‌دهی و هماهنگی داخلی می‌باشد.'
];
const staffStatuses = ["فعال", "مرخصی", "غیرفعال"];
const lessonTypes = [
    { value: 'piano', label: 'پیانو' },
    { value: 'guitar', label: 'گیتار' },
    { value: 'violin', label: 'ویولن' },
    { value: 'vocal', label: 'آواز' },
    { value: 'drums', label: 'درام' },
    { value: 'flute', label: 'فلوت' },
    { value: 'theory', label: 'تئوری موسیقی' },
    { value: 'other', label: 'سایر' }
];
const lessonLevels = [
    { value: 'beginner', label: 'مبتدی' },
    { value: 'intermediate', label: 'متوسط' },
    { value: 'advanced', label: 'پیشرفته' },
    { value: 'professional', label: 'حرفه‌ای' }
];
const profileVisibilities = [
    { value: 'public', label: 'عمومی' },
    { value: 'private', label: 'خصوصی' }
];
window.staffTypes = staffTypes;
window.branches = branches;
window.contractCurrencies = contractCurrencies;
window.staffStatuses = staffStatuses;
window.lessonTypes = lessonTypes;
window.lessonLevels = lessonLevels;
window.profileVisibilities = profileVisibilities;

const pdfExportColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'name', label: 'نام' },
    { field: 'typeLabel', label: 'نوع پرسنل' },
    { field: 'contractTitle', label: 'عنوان قرارداد' },
    { field: 'branch', label: 'شعبه' },
    { field: 'startDate', label: 'تاریخ شروع' },
    { field: 'endDate', label: 'تاریخ خاتمه' },
    { field: 'price', label: 'مبلغ قرارداد' },
    { field: 'status', label: 'وضعیت' }
];

function getRandomDate(start, end) {
    const startDate = new Date(start);
    const endDate = new Date(end);
    const date = new Date(startDate.getTime() + Math.random() * (endDate.getTime() - startDate.getTime()));
    return date.toISOString().split('T')[0];
}

let allStaff = [];
/* Legacy random fixtures removed: staff is hydrated from academy-data-loaded. */
if (false) for (let i = 1; i <= 28; i++) {
    const first = staffFirstNames[Math.floor(Math.random() * staffFirstNames.length)];
    const last = staffLastNames[Math.floor(Math.random() * staffLastNames.length)];
    const type = staffTypes[Math.floor(Math.random() * staffTypes.length)];
    const branch = branches[Math.floor(Math.random() * branches.length)];
    const startDate = getRandomDate('2024-01-01', '2024-09-01');
    const endDate = getRandomDate(startDate, '2025-12-31');
    const currency = contractCurrencies[Math.floor(Math.random() * contractCurrencies.length)];

    const lessons = type.value === 'teacher' ? [{
        type: lessonTypes[Math.floor(Math.random() * lessonTypes.length)].value,
        level: lessonLevels[Math.floor(Math.random() * lessonLevels.length)].value
    }] : [];
    if (type.value === 'teacher' && Math.random() > 0.5) {
        lessons.push({
            type: lessonTypes[Math.floor(Math.random() * lessonTypes.length)].value,
            level: lessonLevels[Math.floor(Math.random() * lessonLevels.length)].value
        });
    }
    allStaff.push({
        id: i,
        name: `${first} ${last}`,
        type: type.value,
        typeLabel: type.label,
        contractTitle: contractTitles[Math.floor(Math.random() * contractTitles.length)],
        contractDescription: contractDescriptions[Math.floor(Math.random() * contractDescriptions.length)],
        branch: branch,
        startDate: startDate,
        endDate: endDate,
        price: Math.floor(Math.random() * 12000000) + 1200000,
        currencyId: currency.id,
        currency: currency.label,
        status: staffStatuses[Math.floor(Math.random() * staffStatuses.length)],
        phone: `۰۹۱${Math.floor(10000000 + Math.random() * 89999999)}`,
        activityStart: getRandomDate('2018-01-01', '2023-12-31'),
        profileVisibility: Math.random() > 0.3 ? 'public' : 'private',
        lessons: lessons
    });
}
window.addEventListener('academy-data-loaded',function(event){allStaff=(event.detail.members||[]).filter(item=>item.type!=='student');filteredStaff=allStaff.slice();if(document.getElementById('staffTable')){window.renderStaffBranchTabs?.();window.filterStaff();}});

// ==================== متغیرهای صفحه‌بندی ====================
let staffCurrentPage = 1;
const staffPerPage = 10;
let filteredStaff = [...allStaff];
let editingRowId = null;
let staffSortField = '';
let staffSortDirection = 'asc';
let currentStaffBranch = 'all';

function sortStaffItems() {
    if (!staffSortField) return;

    filteredStaff.sort((a, b) => {
        let aValue = a[staffSortField];
        let bValue = b[staffSortField];

        if (staffSortField === 'price') {
            aValue = Number(aValue);
            bValue = Number(bValue);
        } else if (staffSortField === 'startDate' || staffSortField === 'endDate') {
            aValue = new Date(aValue);
            bValue = new Date(bValue);
        } else {
            aValue = String(aValue || '').toLowerCase();
            bValue = String(bValue || '').toLowerCase();
        }

        if (aValue < bValue) return staffSortDirection === 'asc' ? -1 : 1;
        if (aValue > bValue) return staffSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateSortIcons = async function () {
    const fields = ['name', 'typeLabel', 'contractTitle', 'branch', 'startDate', 'endDate', 'price', 'status'];
    fields.forEach(field => {
        const icon = document.getElementById(`sortIcon-${field}`);
        if (!icon) return;
        if (staffSortField === field) {
            icon.textContent = staffSortDirection === 'asc' ? '↑' : '↓';
        } else {
            icon.textContent = '↕';
        }
    });
};

window.sortStaffBy = async function (field) {
    if (staffSortField === field) {
        staffSortDirection = staffSortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        staffSortField = field;
        staffSortDirection = 'asc';
    }
    sortStaffItems();
    renderStaffTable(filteredStaff);
    updateSortIcons();
};


function getLessonTypeLabel(value) {
    const t = lessonTypes.find(x => x.value === value);
    return t ? t.label : value || '—';
}
function getLessonLevelLabel(value) {
    const t = lessonLevels.find(x => x.value === value);
    return t ? t.label : value || '—';
}
function formatLessonsSummary(lessons) {
    if (!lessons || !lessons.length) return '—';
    return lessons.map(l => getLessonTypeLabel(l.type) + ' / ' + getLessonLevelLabel(l.level)).join(' · ');
}
function readLessonsFromPrefix(prefix) {
    const container = document.getElementById(prefix + 'LessonsContainer');
    if (!container) return [];
    const rows = container.querySelectorAll('[data-lesson-row]');
    const list = [];
    rows.forEach(row => {
        const typeSel = row.querySelector('[data-lesson-type]');
        const levelSel = row.querySelector('[data-lesson-level]');
        if (!typeSel || !levelSel) return;
        list.push({ type: typeSel.value, level: levelSel.value });
    });
    return list;
}
window.toggleStaffLessonFields = async function (selectId, containerId) {
    const sel = document.getElementById(selectId);
    const box = document.getElementById(containerId);
    if (!sel || !box) return;
    const isTeacher = sel.value === 'teacher';
    box.classList.toggle('hidden', !isTeacher);
};
window.addStaffLessonRow = async function (prefix) {
    const container = document.getElementById(prefix + 'LessonsContainer');
    if (!container || !window.getStaffLessonRowHTML) return;
    const idx = container.querySelectorAll('[data-lesson-row]').length;
    container.insertAdjacentHTML('beforeend', window.getStaffLessonRowHTML(prefix, idx, null, idx === 0));
};
window.removeStaffLessonRow = async function (btn) {
    const row = btn.closest('[data-lesson-row]');
    if (!row) return;
    const container = row.parentElement;
    if (!container) return;
    if (container.querySelectorAll('[data-lesson-row]').length <= 1) {
        alert('حداقل یک درس باید باقی بماند.');
        return;
    }
    row.remove();
};

// ==================== رندر جدول ====================
window.renderStaffTable = async function (staff = filteredStaff) {
    const tbody = document.querySelector('#staffTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(staff.length / staffPerPage) || 1;
    if (staffCurrentPage > totalPages) staffCurrentPage = totalPages;

    const start = (staffCurrentPage - 1) * staffPerPage;
    const end = start + staffPerPage;
    const pageStaff = staff.slice(start, end);

    tbody.innerHTML = '';

    if (pageStaff.length === 0) {
        tbody.innerHTML = window.getStaffEmptyRowHTML ? window.getStaffEmptyRowHTML() : '';
    } else {
        pageStaff.forEach(item => {
            const statusClass = item.status === 'فعال'
                ? 'bg-green-100 text-green-700'
                : item.status === 'مرخصی'
                    ? 'bg-yellow-100 text-yellow-700'
                    : 'bg-red-100 text-red-700';

            const tr = document.createElement('tr');
            tr.className = "hover:bg-gray-50 transition";
            tr.innerHTML = window.getStaffRowHTML ? window.getStaffRowHTML(item, statusClass) : '';
            tbody.appendChild(tr);

            if (editingRowId === item.id) {
                const expandRow = document.createElement('tr');
                expandRow.className = 'bg-gray-50 staff-inline-expand';
                expandRow.innerHTML = window.getStaffInlineExpandRowHTML ? window.getStaffInlineExpandRowHTML(item) : '';
                tbody.appendChild(expandRow);
            }
        });
    }

    updateStaffPagination(staff.length, start, end, totalPages);
};

// ==================== صفحه‌بندی ====================
function updateStaffPagination(total, start, end, totalPages) {
    const info = document.getElementById('staffPaginationInfo');
    if (info) {
        const from = total === 0 ? 0 : start + 1;
        const to = Math.min(end, total);
        info.textContent = `نمایش ${from} تا ${to} از ${total} پرسنل`;
    }

    const pagination = document.getElementById('staffPaginationButtons');
    if (!pagination) return;

    let html = `
        <button onclick="changeStaffPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${staffCurrentPage === 1 ? 'disabled' : ''}>اول</button>
        <button onclick="changeStaffPage(${staffCurrentPage - 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${staffCurrentPage === 1 ? 'disabled' : ''}>قبلی</button>
    `;

    let startPage = Math.max(1, staffCurrentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        html += `<button onclick="changeStaffPage(${i})" class="px-3 py-1.5 rounded-lg ${i === staffCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50'}">${i}</button>`;
    }

    html += `
        <button onclick="changeStaffPage(${staffCurrentPage + 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${staffCurrentPage === totalPages ? 'disabled' : ''}>بعدی</button>
        <button onclick="changeStaffPage(${totalPages})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${staffCurrentPage === totalPages ? 'disabled' : ''}>آخر</button>
    `;

    pagination.innerHTML = html;
}

window.changeStaffPage = async function (page) {
    const totalPages = Math.ceil(filteredStaff.length / staffPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    staffCurrentPage = page;
    renderStaffTable(filteredStaff);
};

// ==================== فیلترها ====================
window.renderStaffBranchTabs = async function () {
    const container = document.getElementById('staffBranchTabs');
    if (!container) return;
    container.querySelectorAll('.staff-branch-tab:not(:first-child)').forEach(function (tab) { tab.remove(); });
    const branchesList = (typeof allBranches !== 'undefined' && allBranches.length) ? allBranches : [{ id: 1, name: 'شعبه مرکزی' }, { id: 2, name: 'شعبه ونک' }, { id: 3, name: 'شعبه سعادت‌آباد' }, { id: 4, name: 'شعبه کرج' }];
    branchesList.forEach(function (branch) {
        const active = currentStaffBranch === branch.name;
        const btn = document.createElement('button');
        btn.className = 'staff-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' + (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = branch.name;
        btn.onclick = function () { window.filterStaffByBranch(branch.name); };
        container.appendChild(btn);
    });
};

window.filterStaffByBranch = async function (branchName) {
    currentStaffBranch = branchName;
    document.querySelectorAll('.staff-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.staff-branch-tab');
    if (branchName === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        tabs.forEach(function (tab) {
            if (tab.textContent === branchName) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    window.filterStaff();
};

window.filterStaff = async function () {
    const search = (document.getElementById('staffSearch')?.value || '').trim().toLowerCase();
    const type = document.getElementById('filterStaffType')?.value || '';
    const status = document.getElementById('filterStaffStatus')?.value || '';
    const currency = document.getElementById('filterStaffCurrency')?.value || '';
    const branch = currentStaffBranch === 'all' ? '' : currentStaffBranch;

    filteredStaff = allStaff.filter(item => {
        const matchSearch = !search || item.name.toLowerCase().includes(search) || item.contractTitle.toLowerCase().includes(search) || (item.phone && item.phone.includes(search));
        const matchType = !type || item.type === type;
        const matchBranch = !branch || item.branch === branch;
        const matchStatus = !status || item.status === status;
        const matchCurrency = !currency || item.currency === currency;
        return matchSearch && matchType && matchBranch && matchStatus && matchCurrency;
    });

    staffCurrentPage = 1;
    sortStaffItems();
    renderStaffTable(filteredStaff);
};

window.toggleStaffInlineEdit = async function (id) {
    editingRowId = editingRowId === id ? null : id;
    renderStaffTable(filteredStaff);
};

window.deleteStaff = async function (id) {
    if (!(await AppDialog.confirmDelete(allStaff, id, 'عضو'))) return;
    await branchRequest(`/academy/admin/members/${id}/delete`,{});
    allStaff = allStaff.filter(item => item.id !== id);
    filteredStaff = filteredStaff.filter(item => item.id !== id);
    if (editingRowId === id) editingRowId = null;
    staffCurrentPage = 1;
    renderStaffTable(filteredStaff);
};

window.getInlineEditRowHTML = async function (item) {
    return window.getStaffInlineEditRowHTML ? window.getStaffInlineEditRowHTML(item) : '';
};

window.saveInlineStaff = async function (id) {
    const name = document.getElementById(`inlineStaffName-${id}`)?.value.trim();
    const phone = document.getElementById(`inlineStaffPhone-${id}`)?.value.trim();
    const contractTitle = document.getElementById(`inlineStaffContractTitle-${id}`)?.value.trim();
    const contractDescription = document.getElementById(`inlineStaffContractDescription-${id}`)?.value.trim();
    const startDate = document.getElementById(`inlineStaffStartDate-${id}`)?.value;
    const endDate = document.getElementById(`inlineStaffEndDate-${id}`)?.value;
    const price = parseFloat(document.getElementById(`inlineStaffPrice-${id}`)?.value || 0);

    if (!name || !phone || !contractTitle || !startDate || !endDate || price <= 0) {
        alert('لطفاً تمام فیلدهای الزامی قرارداد را پر کنید.');
        return;
    }

    const typeValue = document.getElementById(`inlineStaffType-${id}`).value;
    const type = staffTypes.find(t => t.value === typeValue) || staffTypes[0];
    const branch = document.getElementById(`inlineStaffBranch-${id}`).value;
    const currencyId = parseInt(document.getElementById(`inlineStaffCurrency-${id}`).value, 10);
    const currency = contractCurrencies.find(c => c.id === currencyId)?.label || 'تومان';
    const status = document.getElementById(`inlineStaffStatus-${id}`).value;
    const activityStart = document.getElementById(`inlineStaffActivityStart-${id}`)?.value || '';
    const profileVisibility = document.getElementById(`inlineStaffProfileVisibility-${id}`)?.value || 'public';
    let lessons = [];
    if (type.value === 'teacher') {
        lessons = readLessonsFromPrefix('inlineStaff' + id);
        if (!lessons.length) {
            alert('برای استاد حداقل یک نوع درس و سطح باید انتخاب شود.');
            return;
        }
    }

    const index = allStaff.findIndex(x => x.id === id);
    if (index === -1) return;

    allStaff[index] = {
        ...allStaff[index],
        name: name,
        phone: phone,
        type: type.value,
        typeLabel: type.label,
        contractTitle: contractTitle,
        contractDescription: contractDescription,
        branch: branch,
        branchId: (typeof allBranches !== 'undefined' && allBranches.find(item => item.name === branch)?.id) || allStaff[index].branchId,
        startDate: startDate,
        endDate: endDate,
        price: price,
        currencyId: currencyId,
        currency: currency,
        status: status,
        activityStart: activityStart,
        profileVisibility: profileVisibility,
        lessons: lessons
    };
    await branchRequest(`/academy/admin/members/${id}/update`,{payload_b64:encodeBranchPayload(allStaff[index])});

    editingRowId = null;
    filterStaff();
    alert('✅ تغییرات با موفقیت ذخیره شد');
};

// ==================== خروجی اکسل ====================
window.exportStaffToExcel = async function () {
    const data = filteredStaff.length ? filteredStaff : allStaff;
    let csv = '\uFEFF';
    csv += 'ردیف,نام,نوع پرسنل,عنوان قرارداد,شعبه,تاریخ شروع,تاریخ خاتمه,مبلغ قرارداد,واحد پول,وضعیت,شماره تماس,شروع فعالیت,نمایش پروفایل,دروس\n';

    data.forEach((item, index) => {
        const lessonsText = formatLessonsSummary(item.lessons).replace(/"/g, '""');
        const vis = item.profileVisibility === 'private' ? 'خصوصی' : 'عمومی';
        csv += `${index + 1},"${item.name}","${item.typeLabel}","${item.contractTitle}","${item.branch}",${item.startDate},${item.endDate},${item.price},"${item.currency}","${item.status}","${item.phone}","${item.activityStart || ''}","${vis}","${lessonsText}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `پرسنل_${new Date().toLocaleDateString('fa-IR')}.csv`;
    link.click();
};

window.exportStaffToPDF = async function () {
    openPDFOptionsModal();
};

window.openPDFOptionsModal = async function () {
    const modalHTML = window.getStaffPDFModalHTML ? window.getStaffPDFModalHTML(pdfExportColumns) : '';
    document.getElementById('modalContainer').innerHTML = modalHTML;
};

window.generateStaffPDF = async function() {
    if (!window.html2canvas) {
        alert('ابزار تولید PDF بارگذاری نشده است. لطفاً صفحه را مجدداً بارگذاری کنید.');
        return;
    }

    const title = document.getElementById('pdfTitle')?.value || 'گزارش پرسنل آموزشگاه';
    const subtitle = document.getElementById('pdfSubtitle')?.value || 'لیست پرسنل و وضعیت قراردادها';
    const footer = document.getElementById('pdfFooter')?.value || '';
    const format = document.getElementById('pdfFormat')?.value || 'a4';
    const orientation = document.getElementById('pdfOrientation')?.value || 'landscape';
    const includeDate = document.getElementById('pdfIncludeDate')?.checked;
    const headerColor = document.getElementById('pdfHeaderColor')?.value || '#eff6ff';
    const evenRowColor = document.getElementById('pdfEvenRowColor')?.value || '#ffffff';
    const oddRowColor = document.getElementById('pdfOddRowColor')?.value || '#f8fafc';
    const selectedColumns = pdfExportColumns.filter(col => document.getElementById(`pdfCol-${col.field}`)?.checked);
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredStaff.length ? filteredStaff : allStaff;

    if (!selectedColumns.length) {
        alert('لطفاً حداقل یک ستون برای خروجی PDF انتخاب کنید.');
        return;
    }

    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));

    const renderPageHTML = (pageNumber, rows, isFirstPage) => window.getStaffPDFPageHTML ? window.getStaffPDFPageHTML(pageNumber, rows, isFirstPage, {
        title,
        subtitle,
        footer,
        includeDate,
        date,
        headerColor,
        evenRowColor,
        oddRowColor,
        selectedColumns,
        rowsPerPage,
        totalPages
    }) : '';

    const canvasPages = [];
    for (let pageIndex = 0; pageIndex < totalPages; pageIndex++) {
        const pageRows = data.slice(pageIndex * rowsPerPage, (pageIndex + 1) * rowsPerPage);
        const pageWrapper = document.createElement('div');
        pageWrapper.style.direction = 'rtl';
        pageWrapper.style.position = 'fixed';
        pageWrapper.style.top = '-9999px';
        pageWrapper.style.left = '-9999px';
        pageWrapper.style.width = orientation === 'portrait' ? '900px' : '1400px';
        pageWrapper.style.padding = pageIndex === 0 ? '20px 30px 30px' : '30px';
        pageWrapper.style.backgroundColor = '#ffffff';
        pageWrapper.style.fontFamily = 'Vazirmatn, Tahoma, sans-serif';
        pageWrapper.innerHTML = renderPageHTML(pageIndex + 1, pageRows, pageIndex === 0);
        document.body.appendChild(pageWrapper);

        const canvas = await html2canvas(pageWrapper, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            scrollY: -window.scrollY
        });
        canvasPages.push(canvas);
        pageWrapper.remove();
    }

    const doc = new window.jspdf.jsPDF({ orientation, unit: 'pt', format });
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const margin = 20;
    const imgWidth = pageWidth - margin * 2;

    canvasPages.forEach((canvas, index) => {
        if (index > 0) {
            doc.addPage();
        }
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        doc.addImage(canvas.toDataURL('image/png'), 'PNG', margin, margin, imgWidth, imgHeight);
    });

    doc.save(`پرسنل_${date}.pdf`);
    closeModal();
};

// ==================== Modal افزودن پرسنل ====================
window.openAddStaffModal = async function () {
    const modalHTML = window.getStaffAddModalHTML ? window.getStaffAddModalHTML() : '';
    document.getElementById('modalContainer').innerHTML = modalHTML;
};

window.saveStaff = async function () {
    const name = document.getElementById('staffName')?.value.trim();
    const phone = document.getElementById('staffPhone')?.value.trim();
    const contractTitle = document.getElementById('staffContractTitle')?.value.trim();
    const contractDescription = document.getElementById('staffContractDescription')?.value.trim();
    const startDate = document.getElementById('staffStartDate')?.value;
    const endDate = document.getElementById('staffEndDate')?.value;
    const price = parseFloat(document.getElementById('staffPrice')?.value || 0);

    if (!name || !phone || !contractTitle || !startDate || !endDate || price <= 0) {
        alert('لطفاً تمام فیلدهای الزامی قرارداد را پر کنید.');
        return;
    }

    const typeValue = document.getElementById('staffType').value;
    const type = staffTypes.find(t => t.value === typeValue) || staffTypes[0];
    const branch = document.getElementById('staffBranch').value;
    const currencyId = parseInt(document.getElementById('staffCurrency').value, 10);
    const currency = contractCurrencies.find(c => c.id === currencyId)?.label || 'تومان';

    const activityStart = document.getElementById('staffActivityStart')?.value || '';
    const profileVisibility = document.getElementById('staffProfileVisibility')?.value || 'public';
    let lessons = [];
    if (type.value === 'teacher') {
        lessons = readLessonsFromPrefix('staff');
        if (!lessons.length) {
            alert('برای استاد حداقل یک نوع درس و سطح باید انتخاب شود.');
            return;
        }
    }

    allStaff.unshift({
        id: Date.now(),
        name: name,
        phone: phone,
        type: type.value,
        typeLabel: type.label,
        contractTitle: contractTitle,
        contractDescription: contractDescription,
        branch: branch,
        startDate: startDate,
        endDate: endDate,
        price: price,
        currencyId: currencyId,
        currency: currency,
        status: 'فعال',
        activityStart: activityStart,
        profileVisibility: profileVisibility,
        lessons: lessons
    });

    filterStaff();
    closeModal();
    alert('✅ پرسنل با موفقیت ثبت شد');
};

// ==================== نمایش جزئیات پرسنل ====================
window.viewStaff = async function (id) {
    const item = allStaff.find(x => x.id === id);
    if (!item) return;

    const modalHTML = window.getStaffDetailsModalHTML ? window.getStaffDetailsModalHTML(item) : '';
    document.getElementById('modalContainer').innerHTML = modalHTML;
};

// ==================== ویرایش پرسنل ====================
window.editStaff = async function (id) {
    const item = allStaff.find(x => x.id === id);
    if (!item) return;

    const modalHTML = window.getStaffEditModalHTML ? window.getStaffEditModalHTML(item) : '';
    document.getElementById('modalContainer').innerHTML = modalHTML;
};

window.saveEditedStaff = async function (id) {
    const name = document.getElementById('editStaffName')?.value.trim();
    const phone = document.getElementById('editStaffPhone')?.value.trim();
    const contractTitle = document.getElementById('editStaffContractTitle')?.value.trim();
    const contractDescription = document.getElementById('editStaffContractDescription')?.value.trim();
    const startDate = document.getElementById('editStaffStartDate')?.value;
    const endDate = document.getElementById('editStaffEndDate')?.value;
    const price = parseFloat(document.getElementById('editStaffPrice')?.value || 0);

    if (!name || !phone || !contractTitle || !startDate || !endDate || price <= 0) {
        alert('لطفاً تمام فیلدهای الزامی قرارداد را پر کنید.');
        return;
    }

    const index = allStaff.findIndex(x => x.id === id);
    if (index === -1) return;

    const typeValue = document.getElementById('editStaffType').value;
    const type = staffTypes.find(t => t.value === typeValue) || staffTypes[0];
    const branch = document.getElementById('editStaffBranch').value;
    const currencyId = parseInt(document.getElementById('editStaffCurrency').value, 10);
    const currency = contractCurrencies.find(c => c.id === currencyId)?.label || 'تومان';
    const status = document.getElementById('editStaffStatus').value;
    const activityStart = document.getElementById('editStaffActivityStart')?.value || '';
    const profileVisibility = document.getElementById('editStaffProfileVisibility')?.value || 'public';
    let lessons = [];
    if (type.value === 'teacher') {
        lessons = readLessonsFromPrefix('editStaff');
        if (!lessons.length) {
            alert('برای استاد حداقل یک نوع درس و سطح باید انتخاب شود.');
            return;
        }
    }

    allStaff[index] = {
        ...allStaff[index],
        name: name,
        phone: phone,
        type: type.value,
        typeLabel: type.label,
        contractTitle: contractTitle,
        contractDescription: contractDescription,
        branch: branch,
        branchId: (typeof allBranches !== 'undefined' && allBranches.find(item => item.name === branch)?.id) || allStaff[index].branchId,
        startDate: startDate,
        endDate: endDate,
        price: price,
        currencyId: currencyId,
        currency: currency,
        status: status,
        activityStart: activityStart,
        profileVisibility: profileVisibility,
        lessons: lessons
    };

    await branchRequest(`/academy/admin/members/${id}/update`,{payload_b64:encodeBranchPayload(allStaff[index])});

    filterStaff();
    closeModal();
    alert('✅ تغییرات با موفقیت ذخیره شد');
};

// ==================== اجرای اولیه ====================
(function initStaff() {
    if (document.querySelector('#staffTable tbody')) {
        window.renderStaffBranchTabs();
        renderStaffTable();
    } else {
        setTimeout(function () {
            if (document.querySelector('#staffTable tbody')) {
                window.renderStaffBranchTabs();
                renderStaffTable();
            }
        }, 150);
    }
})();
