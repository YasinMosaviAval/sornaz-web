// ==================== داده نمونه اساتید ====================
const teacherFirstNames = ["محمد", "علی", "رضا", "حسین", "امیر", "سعید", "مهدی", "احمد", "کامران", "بهرام"];
const teacherLastNames = ["موسوی", "رضایی", "بهرامی", "کاظمی", "نوری", "جعفری", "احمدی", "حسینی", "کریمی", "محمدی"];
const teacherInstruments = ["پیانو", "گیتار", "ویولن", "آواز", "درام", "سنتور", "کمانچه"];
const teacherLevels = ["مبتدی تا پیشرفته", "متوسط و پیشرفته", "فقط پیشرفته"];
const teacherStatuses = ["فعال", "مرخصی", "غیرفعال"];

let allTeachers = [];
for (let i = 1; i <= 28; i++) {
    const first = teacherFirstNames[Math.floor(Math.random() * teacherFirstNames.length)];
    const last = teacherLastNames[Math.floor(Math.random() * teacherLastNames.length)];
    const instrument = teacherInstruments[Math.floor(Math.random() * teacherInstruments.length)];
    
    allTeachers.push({
        id: i,
        name: `استاد ${first} ${last}`,
        instrument: instrument,
        level: teacherLevels[Math.floor(Math.random() * teacherLevels.length)],
        studentsCount: Math.floor(Math.random() * 25) + 3,
        hourlyRate: (150 + Math.floor(Math.random() * 200)) * 1000,
        status: teacherStatuses[Math.floor(Math.random() * teacherStatuses.length)],
        phone: `۰۹۱${Math.floor(10000000 + Math.random() * 89999999)}`,
        experience: Math.floor(Math.random() * 20) + 2,
        rating: (4 + Math.random()).toFixed(1)
    });
}

// ==================== متغیرهای صفحه‌بندی ====================
let teachersCurrentPage = 1;
const teachersPerPage = 10;
let filteredTeachers = [...allTeachers];

// ==================== رندر جدول ====================
window.renderTeachersTable = function(teachers = filteredTeachers) {
    const tbody = document.querySelector('#teachersTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(teachers.length / teachersPerPage) || 1;
    if (teachersCurrentPage > totalPages) teachersCurrentPage = totalPages;

    const start = (teachersCurrentPage - 1) * teachersPerPage;
    const end = start + teachersPerPage;
    const pageTeachers = teachers.slice(start, end);

    tbody.innerHTML = '';

    if (pageTeachers.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-gray-400">هیچ استادی یافت نشد</td></tr>`;
    } else {
        pageTeachers.forEach(teacher => {
            const statusClass = teacher.status === 'فعال' 
                ? 'bg-green-100 text-green-700' 
                : teacher.status === 'مرخصی' 
                    ? 'bg-yellow-100 text-yellow-700' 
                    : 'bg-red-100 text-red-700';

            const tr = document.createElement('tr');
            tr.className = "hover:bg-gray-50 transition";
            tr.innerHTML = `
                <td class="py-4 px-5 font-medium">${teacher.name}</td>
                <td class="py-4 px-5">${teacher.instrument}</td>
                <td class="py-4 px-5">${teacher.level}</td>
                <td class="py-4 px-5">
                    <span class="px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                        ${teacher.studentsCount} نفر
                    </span>
                </td>
                <td class="py-4 px-5">${teacher.hourlyRate.toLocaleString('fa-IR')} تومان</td>
                <td class="py-4 px-5">
                    <span class="px-3 py-1 rounded-full text-xs ${statusClass}">${teacher.status}</span>
                </td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewTeacher(${teacher.id})" class="text-indigo-600 hover:underline text-sm ml-3">جزئیات</button>
                    <button onclick="editTeacher(${teacher.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    updateTeachersPagination(teachers.length, start, end, totalPages);
};

// ==================== صفحه‌بندی ====================
function updateTeachersPagination(total, start, end, totalPages) {
    const info = document.getElementById('teachersPaginationInfo');
    if (info) {
        const from = total === 0 ? 0 : start + 1;
        const to = Math.min(end, total);
        info.textContent = `نمایش ${from} تا ${to} از ${total} استاد`;
    }

    const pagination = document.getElementById('teachersPaginationButtons');
    if (!pagination) return;

    let html = `
        <button onclick="changeTeachersPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${teachersCurrentPage === 1 ? 'disabled' : ''}>اول</button>
        <button onclick="changeTeachersPage(${teachersCurrentPage - 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${teachersCurrentPage === 1 ? 'disabled' : ''}>قبلی</button>
    `;

    let startPage = Math.max(1, teachersCurrentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        html += `<button onclick="changeTeachersPage(${i})" class="px-3 py-1.5 rounded-lg ${i === teachersCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50'}">${i}</button>`;
    }

    html += `
        <button onclick="changeTeachersPage(${teachersCurrentPage + 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${teachersCurrentPage === totalPages ? 'disabled' : ''}>بعدی</button>
        <button onclick="changeTeachersPage(${totalPages})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${teachersCurrentPage === totalPages ? 'disabled' : ''}>آخر</button>
    `;

    pagination.innerHTML = html;
}

window.changeTeachersPage = function(page) {
    const totalPages = Math.ceil(filteredTeachers.length / teachersPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    teachersCurrentPage = page;
    renderTeachersTable(filteredTeachers);
};

// ==================== فیلترها ====================
window.filterTeachers = function() {
    const search = (document.getElementById('teacherSearch')?.value || '').trim().toLowerCase();
    const instrument = document.getElementById('filterTeacherInstrument')?.value || '';
    const status = document.getElementById('filterTeacherStatus')?.value || '';
    const level = document.getElementById('filterTeacherLevel')?.value || '';

    filteredTeachers = allTeachers.filter(t => {
        const matchSearch = !search || t.name.toLowerCase().includes(search) || (t.phone && t.phone.includes(search));
        const matchInstrument = !instrument || t.instrument === instrument;
        const matchStatus = !status || t.status === status;
        const matchLevel = !level || t.level === level;
        return matchSearch && matchInstrument && matchStatus && matchLevel;
    });

    teachersCurrentPage = 1;
    renderTeachersTable(filteredTeachers);
};

// ==================== خروجی اکسل ====================
window.exportTeachersToExcel = function() {
    const data = filteredTeachers.length ? filteredTeachers : allTeachers;
    let csv = '\uFEFF';
    csv += 'ردیف,نام,تخصص,سطح تدریس,تعداد هنرجو,نرخ ساعتی,وضعیت,شماره تماس,سابقه (سال)\n';

    data.forEach((t, index) => {
        csv += `${index + 1},"${t.name}","${t.instrument}","${t.level}",${t.studentsCount},${t.hourlyRate},"${t.status}","${t.phone}",${t.experience}\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `اساتید_${new Date().toLocaleDateString('fa-IR')}.csv`;
    link.click();
};

// ==================== Modal افزودن استاد ====================
window.openAddTeacherModal = function() {
    const modalHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">افزودن استاد جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                        <input id="teacherName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                        <input id="teacherPhone" type="tel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تخصص اصلی *</label>
                        <select id="teacherInstrument" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${teacherInstruments.map(i => `<option value="${i}">${i}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">سطح تدریس</label>
                        <select id="teacherLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${teacherLevels.map(l => `<option value="${l}">${l}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نرخ ساعتی (تومان)</label>
                        <input id="teacherRate" type="number" value="250000" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">سابقه کار (سال)</label>
                        <input id="teacherExp" type="number" value="5" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="saveTeacher()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره استاد</button>
                    <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
    
    document.getElementById('modalContainer').innerHTML = modalHTML;
};

window.saveTeacher = function() {
    const name = document.getElementById('teacherName')?.value.trim();
    const phone = document.getElementById('teacherPhone')?.value.trim();
    
    if (!name || !phone) {
        alert('نام و شماره تماس الزامی است');
        return;
    }

    allTeachers.unshift({
        id: Date.now(),
        name: name.startsWith('استاد') ? name : `استاد ${name}`,
        phone: phone,
        instrument: document.getElementById('teacherInstrument').value,
        level: document.getElementById('teacherLevel').value,
        hourlyRate: parseInt(document.getElementById('teacherRate').value) || 250000,
        experience: parseInt(document.getElementById('teacherExp').value) || 5,
        studentsCount: 0,
        status: "فعال",
        rating: "4.5"
    });

    filterTeachers();
    closeModal();
    alert('✅ استاد با موفقیت ثبت شد');
};

// ==================== جزئیات استاد ====================
window.viewTeacher = function(id) {
    const t = allTeachers.find(x => x.id === id);
    if (!t) return;

    const modalHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <div>
                    <h2 class="text-2xl font-bold">${t.name}</h2>
                    <p class="text-sm text-gray-500 mt-1">کد استاد: #${t.id}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="editTeacher(${t.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
            </div>
            
            <div class="p-8 space-y-8">
                <div>
                    <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-user"></i> اطلاعات شخصی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام</span><span class="font-medium">${t.name}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شماره تماس</span><span class="font-medium">${t.phone}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سابقه کار</span><span class="font-medium">${t.experience} سال</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">امتیاز هنرجویان</span><span class="font-medium">${t.rating} از ۵</span></div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-music"></i> اطلاعات تدریس</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تخصص</span><span class="font-medium">${t.instrument}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سطح تدریس</span><span class="font-medium">${t.level}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تعداد هنرجویان</span><span class="font-medium">${t.studentsCount} نفر</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span>
                            <span class="px-3 py-1 rounded-full text-xs ${t.status === 'فعال' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">${t.status}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-money-bill-wave"></i> اطلاعات مالی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-2xl p-4 text-center">
                            <p class="text-sm text-gray-500">نرخ ساعتی</p>
                            <p class="text-lg font-bold mt-1">${t.hourlyRate.toLocaleString('fa-IR')} تومان</p>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 text-center">
                            <p class="text-sm text-gray-500">درآمد تخمینی ماهانه</p>
                            <p class="text-lg font-bold mt-1">${(t.hourlyRate * t.studentsCount * 4).toLocaleString('fa-IR')} تومان</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>`;
    
    document.getElementById('modalContainer').innerHTML = modalHTML;
};

// ==================== ویرایش استاد ====================
window.editTeacher = function(id) {
    const t = allTeachers.find(x => x.id === id);
    if (!t) return;

    const modalHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">ویرایش استاد</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                        <input id="editTeacherName" type="text" value="${t.name}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                        <input id="editTeacherPhone" type="tel" value="${t.phone}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تخصص اصلی</label>
                        <select id="editTeacherInstrument" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${teacherInstruments.map(i => `<option value="${i}" ${i === t.instrument ? 'selected' : ''}>${i}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">سطح تدریس</label>
                        <select id="editTeacherLevel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${teacherLevels.map(l => `<option value="${l}" ${l === t.level ? 'selected' : ''}>${l}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نرخ ساعتی</label>
                        <input id="editTeacherRate" type="number" value="${t.hourlyRate}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">وضعیت</label>
                        <select id="editTeacherStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            <option value="فعال" ${t.status === 'فعال' ? 'selected' : ''}>فعال</option>
                            <option value="مرخصی" ${t.status === 'مرخصی' ? 'selected' : ''}>مرخصی</option>
                            <option value="غیرفعال" ${t.status === 'غیرفعال' ? 'selected' : ''}>غیرفعال</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="saveEditedTeacher(${t.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                    <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
    
    document.getElementById('modalContainer').innerHTML = modalHTML;
};

window.saveEditedTeacher = function(id) {
    const name = document.getElementById('editTeacherName')?.value.trim();
    const phone = document.getElementById('editTeacherPhone')?.value.trim();
    
    if (!name || !phone) {
        alert('نام و شماره تماس الزامی است');
        return;
    }

    const index = allTeachers.findIndex(x => x.id === id);
    if (index === -1) return;

    allTeachers[index] = {
        ...allTeachers[index],
        name: name,
        phone: phone,
        instrument: document.getElementById('editTeacherInstrument').value,
        level: document.getElementById('editTeacherLevel').value,
        hourlyRate: parseInt(document.getElementById('editTeacherRate').value) || 250000,
        status: document.getElementById('editTeacherStatus').value
    };

    filterTeachers();
    closeModal();
    alert('✅ تغییرات با موفقیت ذخیره شد');
};

// ==================== اجرای اولیه ====================
(function initTeachers() {
    if (document.querySelector('#teachersTable tbody')) {
        renderTeachersTable();
    } else {
        setTimeout(() => {
            if (document.querySelector('#teachersTable tbody')) {
                renderTeachersTable();
            }
        }, 150);
    }
})();