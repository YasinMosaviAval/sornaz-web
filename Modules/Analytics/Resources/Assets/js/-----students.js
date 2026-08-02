// ==================== داده نمونه (۷۶ هنرجو) ====================
const firstNames = ["سارا", "امیر", "زهرا", "علی", "نگار", "پارسا", "مهسا", "رضا", "نیلوفر", "محمد", "فاطمه", "حسین", "مریم", "آرین", "هستی", "کیان", "یاسمن", "آرش", "ستایش", "دانیال"];
const lastNames = ["احمدی", "حسینی", "کریمی", "محمدی", "رضایی", "نوری", "موسوی", "جعفری", "کاظمی", "حیدری", "صادقی", "اکبری", "میرزایی", "نظری", "رحیمی", "باقری", "شریفی", "طاهری", "قاسمی", "ابراهیمی"];
const instruments = ["پیانو", "گیتار", "ویولن", "آواز", "درام", "سنتور", "کمانچه"];
const levels = ["مبتدی", "متوسط", "پیشرفته"];
const teachers = ["موسوی", "رضایی", "بهرامی", "کاظمی", "نوری"];
const financials = ["تسویه", "بدهکار"];

let allStudents = [];
for (let i = 1; i <= 76; i++) {
    const first = firstNames[Math.floor(Math.random() * firstNames.length)];
    const last = lastNames[Math.floor(Math.random() * lastNames.length)];
    allStudents.push({
        id: i,
        name: `${first} ${last}`,
        instrument: instruments[Math.floor(Math.random() * instruments.length)],
        level: levels[Math.floor(Math.random() * levels.length)],
        teacher: teachers[Math.floor(Math.random() * teachers.length)],
        remaining: Math.floor(Math.random() * 12),
        financial: financials[Math.floor(Math.random() * financials.length)],
        attendance: `${70 + Math.floor(Math.random() * 30)}٪`,
        phone: `۰۹۱${Math.floor(10000000 + Math.random() * 89999999)}`
    });
}

// ==================== متغیرهای صفحه‌بندی ====================
let currentPage = 1;
const itemsPerPage = 10;
let filteredStudents = [...allStudents];

// ==================== رندر جدول با صفحه‌بندی ====================
window.renderStudentsTable = function(students = filteredStudents) {
    const tbody = document.querySelector('#studentsTable tbody');
    if (!tbody) return;

    // محاسبه صفحه‌بندی
    const totalPages = Math.ceil(students.length / itemsPerPage) || 1;
    if (currentPage > totalPages) currentPage = totalPages;

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageStudents = students.slice(start, end);

    tbody.innerHTML = '';

    if (pageStudents.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="py-12 text-center text-gray-400">هیچ هنرجویی یافت نشد</td></tr>`;
    } else {
        pageStudents.forEach(stu => {
            const financialClass = stu.financial === 'تسویه' 
                ? 'bg-green-100 text-green-700' 
                : 'bg-red-100 text-red-700';

            const tr = document.createElement('tr');
            tr.className = "hover:bg-gray-50 transition";
            tr.innerHTML = `
                <td class="py-4 px-5 font-medium">${stu.name}</td>
                <td class="py-4 px-5">${stu.instrument}</td>
                <td class="py-4 px-5">${stu.level}</td>
                <td class="py-4 px-5">${stu.teacher}</td>
                <td class="py-4 px-5">
                    <span class="px-3 py-1 rounded-full text-xs ${stu.remaining <= 2 ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'}">
                        ${stu.remaining} جلسه
                    </span>
                </td>
                <td class="py-4 px-5">
                    <span class="px-3 py-1 rounded-full text-xs ${financialClass}">${stu.financial}</span>
                </td>
                <td class="py-4 px-5">${stu.attendance}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewStudent(${stu.id})" class="text-indigo-600 hover:underline text-sm ml-3">جزئیات</button>
                    <button onclick="editStudent(${stu.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // به‌روزرسانی اطلاعات صفحه‌بندی
    updatePaginationInfo(students.length, start, end, totalPages);
};

// ==================== به‌روزرسانی اطلاعات صفحه‌بندی ====================
/*
    function updatePaginationInfo(total, start, end, totalPages) {
        const info = document.getElementById('paginationInfo');
        if (info) {
            const from = total === 0 ? 0 : start + 1;
            const to = Math.min(end, total);
            info.textContent = `نمایش ${from} تا ${to} از ${total} هنرجو`;
        }

        // دکمه‌های صفحه‌بندی
        const pagination = document.getElementById('paginationButtons');
        if (!pagination) return;

        let html = `
            <button onclick="changePage(${currentPage - 1})" 
                    class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
                    ${currentPage === 1 ? 'disabled' : ''}>
                قبلی
            </button>
        `;

        // نمایش حداکثر ۵ صفحه
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <button onclick="changePage(${i})" 
                        class="px-3 py-1.5 rounded-lg ${i === currentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50'}">
                    ${i}
                </button>
            `;
        }

        html += `
            <button onclick="changePage(${currentPage + 1})" 
                    class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
                    ${currentPage === totalPages ? 'disabled' : ''}>
                بعدی
            </button>
        `;

        pagination.innerHTML = html;
    }
*/
// ==================== به‌روزرسانی اطلاعات صفحه‌بندی ====================
function updatePaginationInfo(total, start, end, totalPages) {
    const info = document.getElementById('paginationInfo');
    if (info) {
        const from = total === 0 ? 0 : start + 1;
        const to = Math.min(end, total);
        info.textContent = `نمایش ${from} تا ${to} از ${total} هنرجو`;
    }

    const pagination = document.getElementById('paginationButtons');
    if (!pagination) return;

    let html = `
        <!-- دکمه اول -->
        <button onclick="changePage(1)" 
                class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
                ${currentPage === 1 ? 'disabled' : ''}>
            اول
        </button>

        <!-- دکمه قبلی -->
        <button onclick="changePage(${currentPage - 1})" 
                class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
                ${currentPage === 1 ? 'disabled' : ''}>
            قبلی
        </button>
    `;

    // نمایش حداکثر ۵ صفحه
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `
            <button onclick="changePage(${i})" 
                    class="px-3 py-1.5 rounded-lg ${i === currentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50'}">
                ${i}
            </button>
        `;
    }

    html += `
        <!-- دکمه بعدی -->
        <button onclick="changePage(${currentPage + 1})" 
                class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
                ${currentPage === totalPages ? 'disabled' : ''}>
            بعدی
        </button>

        <!-- دکمه آخر -->
        <button onclick="changePage(${totalPages})" 
                class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
                ${currentPage === totalPages ? 'disabled' : ''}>
            آخر
        </button>
    `;

    pagination.innerHTML = html;
}

// ==================== تغییر صفحه ====================
window.changePage = function(page) {
    const totalPages = Math.ceil(filteredStudents.length / itemsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderStudentsTable(filteredStudents);
};

// ==================== فیلترها ====================
window.filterStudents = function() {
    const search = (document.getElementById('studentSearch')?.value || '').trim().toLowerCase();
    const instrument = document.getElementById('filterInstrument')?.value || '';
    const level = document.getElementById('filterLevel')?.value || '';
    const status = document.getElementById('filterStatus')?.value || '';
    const teacher = document.getElementById('filterTeacher')?.value || '';

    filteredStudents = allStudents.filter(stu => {
        const matchSearch = !search || 
            stu.name.toLowerCase().includes(search) || 
            (stu.phone && stu.phone.includes(search));

        const matchInstrument = !instrument || stu.instrument === instrument;
        const matchLevel = !level || stu.level === level;
        const matchTeacher = !teacher || stu.teacher === teacher;

        let matchStatus = true;
        if (status === 'فعال') matchStatus = stu.financial === 'تسویه';
        if (status === 'بدهکار') matchStatus = stu.financial === 'بدهکار';
        if (status === 'غیرفعال') matchStatus = stu.remaining === 0;

        return matchSearch && matchInstrument && matchLevel && matchTeacher && matchStatus;
    });

    currentPage = 1; // برگشت به صفحه اول بعد از فیلتر
    renderStudentsTable(filteredStudents);
};

// ==================== خروجی اکسل (CSV) ====================
window.exportToExcel = function() {
    // فقط داده‌های فیلتر شده را خروجی می‌گیریم
    const data = filteredStudents.length ? filteredStudents : allStudents;

    let csv = '\uFEFF'; // BOM برای پشتیبانی از فارسی در اکسل
    csv += 'ردیف,نام,ساز,سطح,استاد,جلسات باقی‌مانده,وضعیت مالی,حضور,شماره تماس\n';

    data.forEach((stu, index) => {
        csv += `${index + 1},"${stu.name}","${stu.instrument}","${stu.level}","${stu.teacher}",${stu.remaining},"${stu.financial}","${stu.attendance}","${stu.phone}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `هنرجویان_${new Date().toLocaleDateString('fa-IR')}.csv`;
    link.click();
};

// ==================== Modal افزودن هنرجو ====================
window.openAddStudentModal = function() {
    const modalHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">افزودن هنرجو جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
            </div>
            
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2">نوع کاربر</label>
                    <select disabled class="w-full border border-gray-200 bg-gray-100 rounded-2xl py-3.5 px-5 text-gray-500 cursor-not-allowed">
                        <option selected>هنرجو</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                        <input id="stuName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                        <input id="stuPhone" type="tel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="font-semibold mb-4 text-indigo-700">اطلاعات آموزشی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">ترم آموزشی *</label>
                            <select id="stuInstrument" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                ${instruments.map(i => `<option value="${i}">${i}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">سطح</label>
                            <select id="stuLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                ${levels.map(l => `<option value="${l}">${l}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">استاد</label>
                            <select id="stuTeacher" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                ${teachers.map(t => `<option value="${t}">استاد ${t}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">تعداد جلسات پکیج</label>
                            <input id="stuRemaining" type="number" value="8" min="1" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="saveStudent()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium transition">
                        ذخیره هنرجو
                    </button>
                    <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50 transition">
                        انصراف
                    </button>
                </div>
            </div>
        </div>
    </div>`;
    
    const container = document.getElementById('modalContainer');
    if (!container) {
        alert('خطا: modalContainer در صفحه پیدا نشد!');
        return;
    }
    container.innerHTML = modalHTML;
};

window.saveStudent = function() {
    const name = document.getElementById('stuName')?.value.trim();
    const phone = document.getElementById('stuPhone')?.value.trim();
    
    if (!name || !phone) {
        alert('لطفاً نام و شماره تماس را وارد کنید');
        return;
    }

    const newStudent = {
        id: Date.now(),
        name: name,
        phone: phone,
        instrument: document.getElementById('stuInstrument').value,
        level: document.getElementById('stuLevel').value,
        teacher: document.getElementById('stuTeacher').value,
        remaining: parseInt(document.getElementById('stuRemaining').value) || 8,
        financial: "تسویه",
        attendance: "—"
    };

    allStudents.unshift(newStudent);
    filterStudents();
    closeModal();
    alert('✅ هنرجو با موفقیت ثبت شد');
};


// ==================== صفحه جزئیات هنرجو ====================
window.viewStudent = function(id) {
    const stu = allStudents.find(s => s.id === id);
    if (!stu) return;

    const modalHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <div>
                    <h2 class="text-2xl font-bold">${stu.name}</h2>
                    <p class="text-sm text-gray-500 mt-1">کد هنرجو: #${stu.id}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="editStudent(${stu.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">
                        ویرایش
                    </button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
                </div>
            </div>
            
            <div class="p-8 space-y-8">
                <div>
                    <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-user"></i> اطلاعات شخصی
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">نام و نام خانوادگی</span>
                            <span class="font-medium">${stu.name}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">شماره تماس</span>
                            <span class="font-medium">${stu.phone}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">تاریخ ثبت‌نام</span>
                            <span class="font-medium">۱۴۰۴/۰۵/۱۲</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">وضعیت</span>
                            <span class="px-3 py-1 rounded-full text-xs ${stu.financial === 'تسویه' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                                ${stu.financial === 'تسویه' ? 'فعال' : 'بدهکار'}
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-music"></i> اطلاعات آموزشی
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">ترم آموزشی</span>
                            <span class="font-medium">${stu.instrument}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">سطح</span>
                            <span class="font-medium">${stu.level}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">استاد</span>
                            <span class="font-medium">استاد ${stu.teacher}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">جلسات باقی‌مانده</span>
                            <span class="font-medium">${stu.remaining} جلسه</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">درصد حضور</span>
                            <span class="font-medium">${stu.attendance}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">نوع کلاس</span>
                            <span class="font-medium">خصوصی</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-money-bill-wave"></i> خلاصه مالی
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-2xl p-4 text-center">
                            <p class="text-sm text-gray-500">وضعیت</p>
                            <p class="text-lg font-bold mt-1 ${stu.financial === 'تسویه' ? 'text-green-600' : 'text-red-600'}">${stu.financial}</p>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 text-center">
                            <p class="text-sm text-gray-500">آخرین پرداخت</p>
                            <p class="text-lg font-bold mt-1">۱,۲۰۰,۰۰۰ تومان</p>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 text-center">
                            <p class="text-sm text-gray-500">بدهی فعلی</p>
                            <p class="text-lg font-bold mt-1">${stu.financial === 'بدهکار' ? '۸۵۰,۰۰۰' : '۰'} تومان</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-indigo-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-sticky-note"></i> یادداشت‌های استاد
                    </h3>
                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 text-sm text-gray-700 leading-relaxed">
                        هنرجو پیشرفت خوبی در تکنیک داشته. پیشنهاد می‌شود قطعات کلاسیک بیشتری تمرین کند.
                        <div class="text-xs text-gray-400 mt-2">آخرین به‌روزرسانی: ۲ روز پیش</div>
                    </div>
                </div>
            </div>
        </div>
    </div>`;
    
    const container = document.getElementById('modalContainer');
    if (!container) {
        console.error('modalContainer پیدا نشد');
        alert('خطا: المان modalContainer در صفحه وجود ندارد');
        return;
    }
    container.innerHTML = modalHTML;
};

// ==================== صفحه ویرایش هنرجو ====================
window.editStudent = function(id) {
    const stu = allStudents.find(s => s.id === id);
    if (!stu) return;

    const modalHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">ویرایش هنرجو</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
            </div>
            
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2">نوع کاربر</label>
                    <select disabled class="w-full border border-gray-200 bg-gray-100 rounded-2xl py-3.5 px-5 text-gray-500 cursor-not-allowed">
                        <option selected>هنرجو</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                        <input id="editName" type="text" value="${stu.name}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                        <input id="editPhone" type="tel" value="${stu.phone}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="font-semibold mb-4 text-indigo-700">اطلاعات آموزشی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">ترم آموزشی *</label>
                            <select id="editInstrument" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                ${instruments.map(i => `<option value="${i}" ${i === stu.instrument ? 'selected' : ''}>${i}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">سطح</label>
                            <select id="editLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                ${levels.map(l => `<option value="${l}" ${l === stu.level ? 'selected' : ''}>${l}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">استاد</label>
                            <select id="editTeacher" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                ${teachers.map(t => `<option value="${t}" ${t === stu.teacher ? 'selected' : ''}>استاد ${t}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">جلسات باقی‌مانده</label>
                            <input id="editRemaining" type="number" value="${stu.remaining}" min="0" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">وضعیت مالی</label>
                            <select id="editFinancial" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                <option value="تسویه" ${stu.financial === 'تسویه' ? 'selected' : ''}>تسویه</option>
                                <option value="بدهکار" ${stu.financial === 'بدهکار' ? 'selected' : ''}>بدهکار</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">درصد حضور</label>
                            <input id="editAttendance" type="text" value="${stu.attendance}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="saveEditedStudent(${stu.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium transition">
                        ذخیره تغییرات
                    </button>
                    <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50 transition">
                        انصراف
                    </button>
                </div>
            </div>
        </div>
    </div>`;
    
    const container = document.getElementById('modalContainer');
    if (!container) {
        console.error('modalContainer پیدا نشد');
        alert('خطا: المان modalContainer در صفحه وجود ندارد');
        return;
    }
    container.innerHTML = modalHTML;
};

window.saveEditedStudent = function(id) {
    const name = document.getElementById('editName')?.value.trim();
    const phone = document.getElementById('editPhone')?.value.trim();

    if (!name || !phone) {
        alert('نام و شماره تماس الزامی است');
        return;
    }

    const index = allStudents.findIndex(s => s.id === id);
    if (index === -1) return;

    allStudents[index] = {
        ...allStudents[index],
        name: name,
        phone: phone,
        instrument: document.getElementById('editInstrument').value,
        level: document.getElementById('editLevel').value,
        teacher: document.getElementById('editTeacher').value,
        remaining: parseInt(document.getElementById('editRemaining').value) || 0,
        financial: document.getElementById('editFinancial').value,
        attendance: document.getElementById('editAttendance').value
    };

    filterStudents();
    closeModal();
    alert('✅ تغییرات با موفقیت ذخیره شد');
};


// ==================== اجرای اولیه ====================
// این بخش باعث می‌شود جدول در بارگذاری اول صفحه نمایش داده شود
(function initStudents() {
    // اگر جدول در صفحه وجود دارد، فوراً رندر کن
    if (document.querySelector('#studentsTable tbody')) {
        renderStudentsTable();
    } else {
        // اگر هنوز لود نشده (به خاطر لود داینامیک)، کمی صبر کن
        setTimeout(() => {
            if (document.querySelector('#studentsTable tbody')) {
                renderStudentsTable();
            }
        }, 100);
    }
})();