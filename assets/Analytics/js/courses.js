// ==================== سطوح، درس‌ها، وضعیت‌ها ====================
window.allCourseLevels = [];
window.courseLessons = [];
const courseStatuses = ['در انتظار', 'باز', 'در حال برگزاری', 'پایان‌یافته'];

function getCourseBranches() {
    return Array.isArray(window.courseBranches) ? window.courseBranches : [];
}

// داده‌های این بخش فقط از API و پایگاه داده دریافت می‌شوند.
window.allCourses = [];

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

window.updateCourseSortIcons = async function () {
    const fields = ['name', 'level', 'branchName', 'instrument', 'capacity', 'enrolled', 'status'];
    fields.forEach(field => {
        const icon = document.getElementById(`courseSortIcon-${field}`);
        if (!icon) return;
        icon.textContent = courseSortField === field
            ? (courseSortDirection === 'asc' ? '↑' : '↓')
            : '↕';
    });
};

window.sortCoursesBy = async function (field) {
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
window.renderCoursesBranchTabs = async function () {
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

window.filterCoursesByBranch = async function (branchId) {
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
window.renderCourseInstrumentFilter = async function () {
    const select = document.getElementById('filterCourseInstrument');
    if (!select) return;
    const names = new Set(allCourses.map(c => c.instrument).filter(Boolean));
    const current = select.value;
    select.innerHTML = '<option value="">همه سازها</option>' +
        [...names].sort().map(n => `<option value="${n}" ${n === current ? 'selected' : ''}>${n}</option>`).join('');
};

window.filterCourses = async function () {
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
window.renderCoursesTable = async function (list = filteredCourses) {
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
                'در انتظار': 'bg-yellow-100 text-yellow-700',
                'باز': 'bg-green-100 text-green-700',
                'در حال برگزاری': 'bg-indigo-100 text-indigo-700',
                'پایان‌یافته': 'bg-blue-100 text-blue-700'
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

window.changeCoursesPage = async function (page) {
    const totalPages = Math.ceil(filteredCourses.length / coursesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    coursesCurrentPage = page;
    renderCoursesTable(filteredCourses);
};

// ==================== سطح دوره ====================
window.promptAddCourseLevel = async function () {
    const name = await AppDialog.prompt('نام سطح دوره جدید را وارد کنید:')?.trim();
    if (!name) return;
    if (allCourseLevels.some(l => l.name === name)) return alert('این سطح قبلاً وجود دارد');
    await courseApi('/academy/admin/course-levels',{name,summary:'سطح آموزشی دوره',description:'سطح آموزشی قابل انتخاب برای دوره‌های شعبه.'});
    await loadCourses();
};

window.refreshCourseLessons=function(prefix){const f=s=>document.getElementById(prefix?prefix+s:'course'+s),branchId=Number(f('Branch')?.value||0),select=f('Instrument');if(!select)return;const items=window.courseLessons.filter(x=>x.branchId===branchId);select.disabled=!branchId;select.innerHTML='<option value="">درس / تخصص را انتخاب کنید</option>'+items.map(x=>`<option value="${x.id}">${x.name}</option>`).join('');};

// ==================== خواندن فرم ====================
function readCourseForm(prefix) {
    const field = (suffix) => document.getElementById(prefix ? `${prefix}${suffix}` : `course${suffix}`);
    const name = field('Name')?.value.trim();
    const levelId = parseInt(field('Level')?.value,10);
    const branchId = parseInt(field('Branch')?.value, 10);
    const lessonId = parseInt(field('Instrument')?.value,10);
    const capacity = parseInt(field('Capacity')?.value || '10', 10) || 10;
    const status = field('Status')?.value || 'pending';
    const teacher = field('Teacher')?.value.trim() || '';
    const summary = field('Summary')?.value.trim() || '';
    const description = field('Description')?.value.trim() || '';
    const branch = getCourseBranches().find(b => b.id === branchId);
    return {
        name, level_id:levelId, branchId,
        branchName: branch ? branch.name : 'نامشخص',
        lesson_id:lessonId, capacity, status, teacher, summary, description
    };
}

function courseEncode(data){const bytes=new TextEncoder().encode(JSON.stringify(data));let s='';bytes.forEach(b=>s+=String.fromCharCode(b));return btoa(s).replace(/\+/g,'-').replace(/\//g,'_').replace(/=+$/,'');}
async function courseApi(url,data=null){const token=window.adminCsrfToken||'',o={method:data?'POST':'GET',credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}};if(data){o.headers['Content-Type']='application/x-www-form-urlencoded;charset=UTF-8';o.headers['X-CSRF-TOKEN']=token;o.body=new URLSearchParams({_token:token,payload_b64:courseEncode(data)}).toString();}const r=await fetch(url,o),raw=await r.text();let p;try{p=JSON.parse(raw)}catch(e){throw new Error('پاسخ معتبر JSON از سرور دریافت نشد.')}const env=p.data??p;if(!r.ok||env.success===false)throw new Error(env.message||'عملیات دوره ناموفق بود.');return env.data??env;}
window.loadCourses=async function(){const d=await courseApi('/academy/admin/courses');window.courseBranches=d.branches||[];window.allCourseLevels=d.levels||[];window.courseLessons=d.lessons||[];window.allCourses=d.courses||[];filteredCourses=allCourses.slice();renderCoursesBranchTabs();renderCourseInstrumentFilter();filterCourses();if(typeof renderCourseLevels==='function')renderCourseLevels();return d;};

// ==================== CRUD ====================
window.openAddCourseModal = async function () {
    if (!document.getElementById('modalContainer')) {
        alert('خطا: المان modalContainer در صفحه اصلی وجود ندارد!');
        return;
    }
    document.getElementById('modalContainer').innerHTML = window.getCourseAddModalHTML
        ? window.getCourseAddModalHTML() : '';
};

window.saveCourse = async function () {
    const data = readCourseForm('');
    if (!data.name) return alert('نام دوره الزامی است');
    if (!data.branchId) return alert('شعبه الزامی است');

    if(!data.level_id)return alert('سطح دوره الزامی است');if(!data.lesson_id)return alert('درس / تخصص الزامی است');
    try{await courseApi('/academy/admin/courses',data);await loadCourses();closeModal();alert('✅ دوره با موفقیت اضافه شد');}catch(e){alert(e.message);}
};

window.viewCourse = async function (id) {
    const item = allCourses.find(x => x.id === id);
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getCourseDetailsModalHTML
        ? window.getCourseDetailsModalHTML(item) : '';
};

window.editCourse = async function (id) {
    const item = allCourses.find(x => x.id === id);
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getCourseEditModalHTML
        ? window.getCourseEditModalHTML(item) : '';
};

window.saveEditedCourse = async function (id) {
    const data = readCourseForm('editCourse');
    if (!data.name) return alert('نام دوره الزامی است');
    try{await courseApi(`/academy/admin/courses/${id}/update`,data);await loadCourses();editingCourseRowId=null;closeModal();alert('✅ تغییرات ذخیره شد');}catch(e){alert(e.message);}
};

window.toggleCourseInlineEdit = async function (id) {
    editingCourseRowId = editingCourseRowId === id ? null : id;
    renderCoursesTable(filteredCourses);
};

window.saveInlineCourse = async function (id) {
    const data = readCourseForm(`inlineCourse${id}`);
    if (!data.name) return alert('نام دوره الزامی است');
    try{await courseApi(`/academy/admin/courses/${id}/update`,data);await loadCourses();editingCourseRowId=null;alert('✅ تغییرات با موفقیت ذخیره شد');}catch(e){alert(e.message);}
};

window.deleteCourse = async function (id) {
    if (!(await AppDialog.confirmDelete(allCourses, id, 'دوره'))) return;
    try{await courseApi(`/academy/admin/courses/${id}/delete`,{});await loadCourses();if(editingCourseRowId===id)editingCourseRowId=null;}catch(e){alert(e.message);}
};

// ==================== خروجی اکسل ====================
window.exportCoursesToExcel = async function () {
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
window.exportCoursesToPDF = async function () {
    openCoursesPDFOptionsModal();
};

window.openCoursesPDFOptionsModal = async function () {
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
    setTimeout(async () => {
        if (document.getElementById('coursesTable')) {
            try { await loadCourses(); } catch (error) { console.error(error); alert(error.message); }
        }
    }, 200);
})();
