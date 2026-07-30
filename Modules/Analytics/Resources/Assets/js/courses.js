// ==================== سطوح، سازها، وضعیت‌ها ====================
let allCourseLevels = [
    { id: 1, name: 'مبتدی' },
    { id: 2, name: 'متوسط' },
    { id: 3, name: 'پیشرفته' },
    { id: 4, name: 'حرفه‌ای' },
    { id: 5, name: 'کودکان' },
    { id: 6, name: 'همه سطوح' }
];

const courseInstruments = [
    'پیانو', 'گیتار', 'ویولن', 'آواز', 'درام', 'سنتور', 'کمانچه', 'تار', 'سه‌تار', 'تئوری', 'فلوت', 'سایر'
];

const courseStatuses = ['فعال', 'در انتظار', 'غیرفعال', 'تکمیل‌شده'];

const courseNameTemplates = [
    'دوره پیانو', 'دوره گیتار', 'دوره ویولن', 'دوره آواز', 'دوره درام',
    'دوره سنتور', 'دوره کمانچه', 'دوره تئوری موسیقی', 'دوره تار', 'دوره سلفژ'
];

const teacherNames = [
    'علی رضایی', 'سارا موسوی', 'رضا کریمی', 'مینا احمدی', 'حسین مهدوی', 'مریم نوری'
];

function getCourseBranches() {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' },
        { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' },
        { id: 4, name: 'شعبه کرج' }
    ];
}

// ==================== ۴۰ دوره نمونه ====================
let allCourses = [];
(function buildSampleCourses() {
    const branches = getCourseBranches();
    for (let i = 1; i <= 40; i++) {
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const level = allCourseLevels[Math.floor(Math.random() * allCourseLevels.length)];
        const instrument = courseInstruments[Math.floor(Math.random() * courseInstruments.length)];
        const capacity = 6 + Math.floor(Math.random() * 16);
        const enrolled = Math.floor(Math.random() * (capacity + 1));
        let status = courseStatuses[Math.floor(Math.random() * courseStatuses.length)];
        if (enrolled >= capacity) status = 'تکمیل‌شده';
        else if (enrolled === 0 && Math.random() > 0.6) status = 'در انتظار';

        const base = courseNameTemplates[Math.floor(Math.random() * courseNameTemplates.length)];
        allCourses.push({
            id: i,
            name: `${base} ${level.name} ${i}`,
            level: level.name,
            branchId: branch.id,
            branchName: branch.name,
            instrument,
            capacity,
            enrolled,
            status,
            teacher: teacherNames[Math.floor(Math.random() * teacherNames.length)],
            description: `دوره ${level.name} در زمینه ${instrument} — شعبه ${branch.name}`
        });
    }
})();

// ==================== صفحه‌بندی / مرتب‌سازی / فیلتر ====================
let coursesCurrentPage = 1;
const coursesPerPage = 10;
let filteredCourses = [...allCourses];
let currentCourseBranch = 'all';
let editingCourseRowId = null;
let courseSortField = '';
let courseSortDirection = 'asc';

const coursePdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'name', label: 'نام دوره' },
    { field: 'level', label: 'سطح' },
    { field: 'branchName', label: 'شعبه' },
    { field: 'instrument', label: 'ساز / تخصص' },
    { field: 'capacity', label: 'ظرفیت' },
    { field: 'enrolled', label: 'ثبت‌نام‌شده' },
    { field: 'status', label: 'وضعیت' },
    { field: 'teacher', label: 'مدرس' }
];

function sortCourseItems() {
    if (!courseSortField) return;
    filteredCourses.sort((a, b) => {
        let aValue = a[courseSortField];
        let bValue = b[courseSortField];
        if (courseSortField === 'capacity' || courseSortField === 'enrolled') {
            aValue = Number(aValue);
            bValue = Number(bValue);
        } else {
            aValue = String(aValue || '').toLowerCase();
            bValue = String(bValue || '').toLowerCase();
        }
        if (aValue < bValue) return courseSortDirection === 'asc' ? -1 : 1;
        if (aValue > bValue) return courseSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateCourseSortIcons = function () {
    const fields = ['name', 'level', 'branchName', 'instrument', 'capacity', 'enrolled', 'status'];
    fields.forEach(field => {
        const icon = document.getElementById(`courseSortIcon-${field}`);
        if (!icon) return;
        icon.textContent = courseSortField === field
            ? (courseSortDirection === 'asc' ? '↑' : '↓')
            : '↕';
    });
};

window.sortCoursesBy = function (field) {
    if (courseSortField === field) {
        courseSortDirection = courseSortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        courseSortField = field;
        courseSortDirection = 'asc';
    }
    sortCourseItems();
    renderCoursesTable(filteredCourses);
    updateCourseSortIcons();
};

// ==================== تب شعبه‌ها ====================
window.renderCoursesBranchTabs = function () {
    const container = document.getElementById('coursesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.course-branch-tab:not(:first-child)').forEach(t => t.remove());
    getCourseBranches().forEach(b => {
        const active = currentCourseBranch == b.id;
        const btn = document.createElement('button');
        btn.className = `course-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ${active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50'} transition`;
        btn.textContent = b.name;
        btn.onclick = () => filterCoursesByBranch(b.id);
        container.appendChild(btn);
    });
};

window.filterCoursesByBranch = function (branchId) {
    currentCourseBranch = branchId;
    document.querySelectorAll('.course-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.course-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        const name = getCourseBranches().find(b => b.id == branchId)?.name;
        tabs.forEach(tab => {
            if (tab.textContent === name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    filterCourses();
};

// ==================== فیلتر ساز ====================
window.renderCourseInstrumentFilter = function () {
    const select = document.getElementById('filterCourseInstrument');
    if (!select) return;
    const names = new Set(allCourses.map(c => c.instrument).filter(Boolean));
    const current = select.value;
    select.innerHTML = '<option value="">همه سازها</option>' +
        [...names].sort().map(n => `<option value="${n}" ${n === current ? 'selected' : ''}>${n}</option>`).join('');
};

window.filterCourses = function () {
    const search = (document.getElementById('courseSearch')?.value || '').trim().toLowerCase();
    const status = document.getElementById('filterCourseStatus')?.value || '';
    const instrument = document.getElementById('filterCourseInstrument')?.value || '';

    filteredCourses = allCourses.filter(item => {
        const matchBranch = currentCourseBranch === 'all' || item.branchId == currentCourseBranch;
        const matchSearch = !search || (item.name || '').toLowerCase().includes(search) ||
            (item.teacher || '').toLowerCase().includes(search);
        const matchStatus = !status || item.status === status;
        const matchInstrument = !instrument || item.instrument === instrument;
        return matchBranch && matchSearch && matchStatus && matchInstrument;
    });

    coursesCurrentPage = 1;
    sortCourseItems();
    renderCoursesTable(filteredCourses);
};

// ==================== رندر جدول ====================
window.renderCoursesTable = function (list = filteredCourses) {
    const tbody = document.querySelector('#coursesTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / coursesPerPage) || 1;
    if (coursesCurrentPage > totalPages) coursesCurrentPage = totalPages;

    const start = (coursesCurrentPage - 1) * coursesPerPage;
    const end = start + coursesPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';

    if (!pageItems.length) {
        tbody.innerHTML = window.getCourseEmptyRowHTML ? window.getCourseEmptyRowHTML() : '';
    } else {
        pageItems.forEach(item => {
            const statusClass = {
                'فعال': 'bg-green-100 text-green-700',
                'غیرفعال': 'bg-gray-100 text-gray-600',
                'در انتظار': 'bg-yellow-100 text-yellow-700',
                'تکمیل‌شده': 'bg-blue-100 text-blue-700'
            }[item.status] || 'bg-gray-100 text-gray-600';

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getCourseRowHTML ? window.getCourseRowHTML(item, statusClass) : '';
            tbody.appendChild(tr);

            if (editingCourseRowId === item.id) {
                const expandRow = document.createElement('tr');
                expandRow.className = 'bg-gray-50 course-inline-expand';
                expandRow.innerHTML = window.getCourseInlineExpandRowHTML
                    ? window.getCourseInlineExpandRowHTML(item) : '';
                tbody.appendChild(expandRow);
            }
        });
    }

    updateCoursesPagination(list.length, start, end, totalPages);
    updateCourseSortIcons();
};

function updateCoursesPagination(total, start, end, totalPages) {
    const info = document.getElementById('coursesPaginationInfo');
    if (info) {
        const from = total === 0 ? 0 : start + 1;
        const to = Math.min(end, total);
        info.textContent = `نمایش ${from} تا ${to} از ${total} دوره`;
    }

    const pagination = document.getElementById('coursesPaginationButtons');
    if (!pagination) return;

    let html = `
        <button onclick="changeCoursesPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${coursesCurrentPage === 1 ? 'disabled' : ''}>اول</button>
        <button onclick="changeCoursesPage(${coursesCurrentPage - 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${coursesCurrentPage === 1 ? 'disabled' : ''}>قبلی</button>
    `;

    let startPage = Math.max(1, coursesCurrentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        html += `<button onclick="changeCoursesPage(${i})" class="px-3 py-1.5 rounded-lg ${i === coursesCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50'}">${i}</button>`;
    }

    html += `
        <button onclick="changeCoursesPage(${coursesCurrentPage + 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${coursesCurrentPage === totalPages ? 'disabled' : ''}>بعدی</button>
        <button onclick="changeCoursesPage(${totalPages})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${coursesCurrentPage === totalPages ? 'disabled' : ''}>آخر</button>
    `;
    pagination.innerHTML = html;
}

window.changeCoursesPage = function (page) {
    const totalPages = Math.ceil(filteredCourses.length / coursesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    coursesCurrentPage = page;
    renderCoursesTable(filteredCourses);
};

// ==================== سطح دوره ====================
window.promptAddCourseLevel = function () {
    const name = prompt('نام سطح دوره جدید را وارد کنید:')?.trim();
    if (!name) return;
    if (allCourseLevels.some(l => l.name === name)) return alert('این سطح قبلاً وجود دارد');
    allCourseLevels.push({ id: Date.now(), name });

    const updateSelect = (sel) => {
        if (!sel) return;
        const current = sel.value;
        sel.innerHTML = allCourseLevels.map(l =>
            `<option value="${l.name}" ${l.name === name || l.name === current ? 'selected' : ''}>${l.name}</option>`
        ).join('');
        sel.value = name;
    };

    ['courseLevel', 'editCourseLevel'].forEach(id => updateSelect(document.getElementById(id)));
    document.querySelectorAll('[id^="inlineCourse"][id$="Level"]').forEach(updateSelect);
};

// ==================== خواندن فرم ====================
function readCourseForm(prefix) {
    const field = (suffix) => document.getElementById(prefix ? `${prefix}${suffix}` : `course${suffix}`);
    const name = field('Name')?.value.trim();
    const level = field('Level')?.value;
    const branchId = parseInt(field('Branch')?.value, 10);
    const instrument = field('Instrument')?.value || '—';
    const capacity = parseInt(field('Capacity')?.value || '10', 10) || 10;
    const enrolled = parseInt(field('Enrolled')?.value || '0', 10) || 0;
    const status = field('Status')?.value || 'فعال';
    const teacher = field('Teacher')?.value.trim() || '';
    const description = field('Description')?.value.trim() || '';
    const branch = getCourseBranches().find(b => b.id === branchId);
    return {
        name, level, branchId,
        branchName: branch ? branch.name : 'نامشخص',
        instrument, capacity, enrolled, status, teacher, description
    };
}

// ==================== CRUD ====================
window.openAddCourseModal = function () {
    if (!document.getElementById('modalContainer')) {
        alert('خطا: المان modalContainer در صفحه اصلی وجود ندارد!');
        return;
    }
    document.getElementById('modalContainer').innerHTML = window.getCourseAddModalHTML
        ? window.getCourseAddModalHTML() : '';
};

window.saveCourse = function () {
    const data = readCourseForm('');
    if (!data.name) return alert('نام دوره الزامی است');
    if (!data.branchId) return alert('شعبه الزامی است');

    allCourses.unshift({ id: Date.now(), ...data });
    renderCourseInstrumentFilter();
    filterCourses();
    closeModal();
    alert('✅ دوره با موفقیت اضافه شد');
};

window.viewCourse = function (id) {
    const item = allCourses.find(x => x.id === id);
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getCourseDetailsModalHTML
        ? window.getCourseDetailsModalHTML(item) : '';
};

window.editCourse = function (id) {
    const item = allCourses.find(x => x.id === id);
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getCourseEditModalHTML
        ? window.getCourseEditModalHTML(item) : '';
};

window.saveEditedCourse = function (id) {
    const data = readCourseForm('editCourse');
    if (!data.name) return alert('نام دوره الزامی است');
    const index = allCourses.findIndex(x => x.id === id);
    if (index === -1) return;
    allCourses[index] = { ...allCourses[index], ...data };
    editingCourseRowId = null;
    renderCourseInstrumentFilter();
    filterCourses();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.toggleCourseInlineEdit = function (id) {
    editingCourseRowId = editingCourseRowId === id ? null : id;
    renderCoursesTable(filteredCourses);
};

window.saveInlineCourse = function (id) {
    const data = readCourseForm(`inlineCourse${id}`);
    if (!data.name) return alert('نام دوره الزامی است');
    const index = allCourses.findIndex(x => x.id === id);
    if (index === -1) return;
    allCourses[index] = { ...allCourses[index], ...data };
    editingCourseRowId = null;
    renderCourseInstrumentFilter();
    filterCourses();
    alert('✅ تغییرات با موفقیت ذخیره شد');
};

window.deleteCourse = function (id) {
    if (!confirm('آیا از حذف این دوره مطمئن هستید؟')) return;
    allCourses = allCourses.filter(c => c.id !== id);
    if (editingCourseRowId === id) editingCourseRowId = null;
    renderCourseInstrumentFilter();
    filterCourses();
};

// ==================== خروجی اکسل ====================
window.exportCoursesToExcel = function () {
    const data = filteredCourses.length ? filteredCourses : allCourses;
    let csv = '\uFEFF';
    csv += 'ردیف,نام دوره,سطح,شعبه,ساز,ظرفیت,ثبت‌نام‌شده,وضعیت,مدرس\n';
    data.forEach((item, index) => {
        csv += `${index + 1},"${item.name}","${item.level || ''}","${item.branchName}","${item.instrument}",${item.capacity},${item.enrolled},"${item.status}","${item.teacher || ''}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `دوره‌ها_${new Date().toLocaleDateString('fa-IR')}.csv`;
    link.click();
};

// ==================== خروجی PDF ====================
window.exportCoursesToPDF = function () {
    openCoursesPDFOptionsModal();
};

window.openCoursesPDFOptionsModal = function () {
    document.getElementById('modalContainer').innerHTML = window.getCoursePDFModalHTML
        ? window.getCoursePDFModalHTML(coursePdfColumns) : '';
};

window.generateCoursesPDF = async function () {
    if (!window.html2canvas) {
        alert('ابزار تولید PDF بارگذاری نشده است. لطفاً صفحه را مجدداً بارگذاری کنید.');
        return;
    }

    const title = document.getElementById('coursePdfTitle')?.value || 'گزارش دوره‌های آموزشگاه';
    const subtitle = document.getElementById('coursePdfSubtitle')?.value || 'لیست دوره‌ها، سطح و وضعیت ثبت‌نام';
    const footer = document.getElementById('coursePdfFooter')?.value || '';
    const format = document.getElementById('coursePdfFormat')?.value || 'a4';
    const orientation = document.getElementById('coursePdfOrientation')?.value || 'landscape';
    const includeDate = document.getElementById('coursePdfIncludeDate')?.checked;
    const headerColor = document.getElementById('coursePdfHeaderColor')?.value || '#eff6ff';
    const evenRowColor = document.getElementById('coursePdfEvenRowColor')?.value || '#ffffff';
    const oddRowColor = document.getElementById('coursePdfOddRowColor')?.value || '#f8fafc';
    const selectedColumns = coursePdfColumns.filter(col =>
        document.getElementById(`coursePdfCol-${col.field}`)?.checked
    );
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredCourses.length ? filteredCourses : allCourses;

    if (!selectedColumns.length) {
        alert('لطفاً حداقل یک ستون برای خروجی PDF انتخاب کنید.');
        return;
    }

    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
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
        pageWrapper.innerHTML = window.getCoursePDFPageHTML
            ? window.getCoursePDFPageHTML(pageIndex + 1, pageRows, pageIndex === 0, {
                title, subtitle, footer, includeDate, date,
                headerColor, evenRowColor, oddRowColor,
                selectedColumns, rowsPerPage, totalPages
            }) : '';
        document.body.appendChild(pageWrapper);
        const canvas = await html2canvas(pageWrapper, {
            scale: 2, useCORS: true, backgroundColor: '#ffffff', scrollY: -window.scrollY
        });
        canvasPages.push(canvas);
        pageWrapper.remove();
    }

    const doc = new window.jspdf.jsPDF({ orientation, unit: 'pt', format });
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 20;
    const imgWidth = pageWidth - margin * 2;

    canvasPages.forEach((canvas, index) => {
        if (index > 0) doc.addPage();
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        doc.addImage(canvas.toDataURL('image/png'), 'PNG', margin, margin, imgWidth, imgHeight);
    });

    doc.save(`دوره‌ها_${date}.pdf`);
    closeModal();
};

// ==================== Init ====================
(function initCourses() {
    setTimeout(() => {
        if (document.getElementById('coursesTable')) {
            renderCoursesBranchTabs();
            renderCourseInstrumentFilter();
            filterCourses();
        }
    }, 200);
})();
