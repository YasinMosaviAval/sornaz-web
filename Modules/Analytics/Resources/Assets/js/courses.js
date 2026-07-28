// ==================== داده نمونه دوره‌ها ====================
let allCourses = [
    { id: 1, name: "دوره پیانو مبتدی", branchId: 1, branchName: "شعبه مرکزی", instrument: "پیانو", capacity: 12, enrolled: 9, status: "فعال" },
    { id: 2, name: "دوره گیتار متوسط", branchId: 1, branchName: "شعبه مرکزی", instrument: "گیتار", capacity: 10, enrolled: 10, status: "تکمیل‌شده" },
    { id: 3, name: "دوره ویولن پیشرفته", branchId: 2, branchName: "شعبه ونک", instrument: "ویولن", capacity: 8, enrolled: 3, status: "در انتظار" },
    { id: 4, name: "دوره آواز کلاسیک", branchId: 3, branchName: "شعبه سعادت‌آباد", instrument: "آواز", capacity: 15, enrolled: 7, status: "فعال" },
    { id: 5, name: "دوره درام کودکان", branchId: 4, branchName: "شعبه کرج", instrument: "درام", capacity: 8, enrolled: 0, status: "غیرفعال" },
    { id: 6, name: "دوره سنتور", branchId: 1, branchName: "شعبه مرکزی", instrument: "سنتور", capacity: 6, enrolled: 4, status: "فعال" },
    { id: 7, name: "دوره کمانچه", branchId: 2, branchName: "شعبه ونک", instrument: "کمانچه", capacity: 5, enrolled: 2, status: "در انتظار" },
    { id: 8, name: "دوره تئوری موسیقی", branchId: 3, branchName: "شعبه سعادت‌آباد", instrument: "تئوری", capacity: 20, enrolled: 14, status: "فعال" }
];

let currentCourseBranch = 'all';
let coursesCurrentPage = 1;
const coursesPerPage = 5;
let filteredCourses = [...allCourses];

// ==================== تاپ‌بار شعبه‌ها ====================
window.renderCoursesBranchTabs = function() {
    const container = document.getElementById('coursesBranchTabs');
    if (!container) return;

    // پاک کردن تب‌های قبلی (به جز اولی)
    const oldTabs = container.querySelectorAll('.course-branch-tab:not(:first-child)');
    oldTabs.forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'course-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterCoursesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterCoursesByBranch = function(branchId) {
    currentCourseBranch = branchId;

    // استایل تب‌ها
    document.querySelectorAll('.course-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.course-branch-tab');
    if (branchId === 'all') {
        if (tabs[0]) {
            tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            tabs[0].classList.remove('border-gray-200');
        }
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }

    // فیلتر کردن
    filteredCourses = branchId === 'all' 
        ? [...allCourses] 
        : allCourses.filter(c => c.branchId == branchId);

    coursesCurrentPage = 1;
    renderCoursesTable();
};

// ==================== رندر جدول + صفحه‌بندی ====================
window.renderCoursesTable = function() {
    const tbody = document.querySelector('#coursesTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(filteredCourses.length / coursesPerPage) || 1;
    if (coursesCurrentPage > totalPages) coursesCurrentPage = totalPages;

    const start = (coursesCurrentPage - 1) * coursesPerPage;
    const pageData = filteredCourses.slice(start, start + coursesPerPage);

    tbody.innerHTML = '';

    if (pageData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-gray-400">دوره‌ای یافت نشد</td></tr>`;
    } else {
        pageData.forEach(c => {
            const statusClass = {
                'فعال': 'bg-green-100 text-green-700',
                'غیرفعال': 'bg-gray-100 text-gray-600',
                'در انتظار': 'bg-yellow-100 text-yellow-700',
                'تکمیل‌شده': 'bg-blue-100 text-blue-700'
            }[c.status] || 'bg-gray-100 text-gray-600';

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = `
                <td class="py-4 px-5 font-medium">${c.name}</td>
                <td class="py-4 px-5">${c.branchName}</td>
                <td class="py-4 px-5">${c.instrument}</td>
                <td class="py-4 px-5">${c.capacity}</td>
                <td class="py-4 px-5">${c.enrolled}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${c.status}</span></td>
                <td class="py-4 px-5 text-left">
                    <button onclick="editCourse(${c.id})" class="text-indigo-600 hover:underline text-sm ml-3">ویرایش</button>
                    <button onclick="deleteCourse(${c.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // صفحه‌بندی ساده
    updateCoursesPagination(filteredCourses.length, start, totalPages);
};

function updateCoursesPagination(total, start, totalPages) {
    // اگر المان صفحه‌بندی در HTML نداری، می‌توانی این بخش را نادیده بگیری
    // یا در HTML یک div با id="coursesPagination" اضافه کن
}

window.changeCoursesPage = function(page) {
    const totalPages = Math.ceil(filteredCourses.length / coursesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    coursesCurrentPage = page;
    renderCoursesTable();
};

// ==================== افزودن دوره ====================
window.openAddCourseModal = function() {
    if (!document.getElementById('modalContainer')) {
        alert('خطا: المان modalContainer در صفحه اصلی وجود ندارد!');
        return;
    }

    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : [])
        .map(b => `<option value="${b.id}">${b.name}</option>`).join('');

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن دوره جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام دوره *</label>
                    <input id="courseName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="courseBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${branchOptions || '<option>شعبه‌ای تعریف نشده</option>'}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">ساز / تخصص</label>
                    <input id="courseInstrument" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">ظرفیت</label>
                        <input id="courseCapacity" type="number" value="10" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">وضعیت</label>
                        <select id="courseStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            <option value="فعال">فعال</option>
                            <option value="در انتظار">در انتظار</option>
                            <option value="غیرفعال">غیرفعال</option>
                            <option value="تکمیل‌شده">تکمیل‌شده</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveCourse()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-medium">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveCourse = function() {
    const name = document.getElementById('courseName')?.value.trim();
    if (!name) return alert('نام دوره الزامی است');

    const branchId = parseInt(document.getElementById('courseBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allCourses.unshift({
        id: Date.now(),
        name,
        branchId,
        branchName: branch ? branch.name : 'نامشخص',
        instrument: document.getElementById('courseInstrument').value || '—',
        capacity: parseInt(document.getElementById('courseCapacity').value) || 10,
        enrolled: 0,
        status: document.getElementById('courseStatus').value
    });

    filterCoursesByBranch(currentCourseBranch);
    closeModal();
    alert('✅ دوره با موفقیت اضافه شد');
};

window.deleteCourse = function(id) {
    if (confirm('آیا از حذف این دوره مطمئن هستید؟')) {
        allCourses = allCourses.filter(c => c.id !== id);
        filterCoursesByBranch(currentCourseBranch);
    }
};

window.editCourse = function(id) {
    alert('ویرایش دوره (می‌توانید مشابه بخش هنرجویان کامل کنید)');
};

// ==================== Init ====================
(function initCourses() {
    setTimeout(() => {
        if (document.getElementById('coursesTable')) {
            renderCoursesBranchTabs();
            filterCoursesByBranch('all');
        }
    }, 200);
})();
