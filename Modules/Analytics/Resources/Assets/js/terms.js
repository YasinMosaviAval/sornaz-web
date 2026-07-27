// ==================== داده نمونه ترم‌ها ====================
let allTerms = [
    { id: 1, name: "ترم پاییز ۱۴۰۴", branchId: 1, branchName: "شعبه مرکزی", course: "دوره پیانو مبتدی", start: "۱۴۰۴/۰۷/۰۱", end: "۱۴۰۴/۰۹/۳۰", status: "در حال برگزاری" },
    { id: 2, name: "ترم تابستان ۱۴۰۴", branchId: 1, branchName: "شعبه مرکزی", course: "دوره گیتار متوسط", start: "۱۴۰۴/۰۴/۰۱", end: "۱۴۰۴/۰۶/۳۱", status: "پایان‌یافته" },
    { id: 3, name: "ترم زمستان ۱۴۰۴", branchId: 2, branchName: "شعبه ونک", course: "دوره ویولن پیشرفته", start: "۱۴۰۴/۱۰/۰۱", end: "۱۴۰۴/۱۲/۲۹", status: "در انتظار" },
    { id: 4, name: "ترم بهار ۱۴۰۵", branchId: 3, branchName: "شعبه سعادت‌آباد", course: "دوره آواز کلاسیک", start: "۱۴۰۵/۰۱/۱۵", end: "۱۴۰۵/۰۳/۳۱", status: "تعلیق‌شده" },
    { id: 5, name: "ترم پاییز ۱۴۰۴", branchId: 4, branchName: "شعبه کرج", course: "دوره درام کودکان", start: "۱۴۰۴/۰۷/۱۰", end: "۱۴۰۴/۰۹/۲۵", status: "در حال برگزاری" },
    { id: 6, name: "ترم ویژه نوروز", branchId: 1, branchName: "شعبه مرکزی", course: "دوره تئوری موسیقی", start: "۱۴۰۴/۱۲/۲۰", end: "۱۴۰۵/۰۱/۱۰", status: "پایان‌یافته" },
    { id: 7, name: "ترم فشرده تابستان", branchId: 2, branchName: "شعبه ونک", course: "دوره گیتار", start: "۱۴۰۴/۰۵/۰۱", end: "۱۴۰۴/۰۶/۱۵", status: "پایان‌یافته" },
    { id: 8, name: "ترم زمستان پیشرفته", branchId: 3, branchName: "شعبه سعادت‌آباد", course: "دوره سنتور", start: "۱۴۰۴/۱۰/۱۵", end: "۱۴۰۵/۰۱/۱۵", status: "در حال برگزاری" }
];

let currentTermBranch = 'all';
let termsCurrentPage = 1;
const termsPerPage = 5;
let filteredTerms = [...allTerms];

// ==================== تاپ‌بار شعبه‌ها ====================
window.renderTermsBranchTabs = function() {
    const container = document.getElementById('termsBranchTabs');
    if (!container) return;

    container.querySelectorAll('.term-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'term-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterTermsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterTermsByBranch = function(branchId) {
    currentTermBranch = branchId;

    document.querySelectorAll('.term-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.term-branch-tab');
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

    filteredTerms = branchId === 'all' 
        ? [...allTerms] 
        : allTerms.filter(t => t.branchId == branchId);

    termsCurrentPage = 1;
    renderTermsTable();
};

// ==================== رندر جدول ====================
window.renderTermsTable = function() {
    const tbody = document.querySelector('#termsTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(filteredTerms.length / termsPerPage) || 1;
    if (termsCurrentPage > totalPages) termsCurrentPage = totalPages;

    const start = (termsCurrentPage - 1) * termsPerPage;
    const pageData = filteredTerms.slice(start, start + termsPerPage);

    tbody.innerHTML = '';

    if (pageData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-gray-400">ترمی یافت نشد</td></tr>`;
    } else {
        pageData.forEach(t => {
            const statusClass = {
                'در حال برگزاری': 'bg-green-100 text-green-700',
                'پایان‌یافته': 'bg-gray-100 text-gray-600',
                'در انتظار': 'bg-yellow-100 text-yellow-700',
                'تعلیق‌شده': 'bg-red-100 text-red-700'
            }[t.status] || 'bg-gray-100 text-gray-600';

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = `
                <td class="py-4 px-5 font-medium">${t.name}</td>
                <td class="py-4 px-5">${t.branchName}</td>
                <td class="py-4 px-5">${t.course}</td>
                <td class="py-4 px-5">${t.start}</td>
                <td class="py-4 px-5">${t.end}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${t.status}</span></td>
                <td class="py-4 px-5 text-left">
                    <button onclick="editTerm(${t.id})" class="text-indigo-600 hover:underline text-sm ml-3">ویرایش</button>
                    <button onclick="deleteTerm(${t.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
};

window.changeTermsPage = function(page) {
    const totalPages = Math.ceil(filteredTerms.length / termsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    termsCurrentPage = page;
    renderTermsTable();
};

// ==================== افزودن ترم ====================
window.openAddTermModal = function() {
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
                <h2 class="text-2xl font-bold">افزودن ترم جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام ترم *</label>
                    <input id="termName" type="text" placeholder="مثال: ترم پاییز ۱۴۰۴" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="termBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${branchOptions || '<option>شعبه‌ای تعریف نشده</option>'}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">دوره مرتبط</label>
                    <input id="termCourse" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ شروع</label>
                        <input id="termStart" type="text" placeholder="۱۴۰۴/۰۷/۰۱" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ پایان</label>
                        <input id="termEnd" type="text" placeholder="۱۴۰۴/۰۹/۳۰" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="termStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="در حال برگزاری">در حال برگزاری</option>
                        <option value="در انتظار">در انتظار</option>
                        <option value="پایان‌یافته">پایان‌یافته</option>
                        <option value="تعلیق‌شده">تعلیق‌شده</option>
                    </select>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveTerm()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-medium">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveTerm = function() {
    const name = document.getElementById('termName')?.value.trim();
    if (!name) return alert('نام ترم الزامی است');

    const branchId = parseInt(document.getElementById('termBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allTerms.unshift({
        id: Date.now(),
        name,
        branchId,
        branchName: branch ? branch.name : 'نامشخص',
        course: document.getElementById('termCourse').value || '—',
        start: document.getElementById('termStart').value || '—',
        end: document.getElementById('termEnd').value || '—',
        status: document.getElementById('termStatus').value
    });

    filterTermsByBranch(currentTermBranch);
    closeModal();
    alert('✅ ترم با موفقیت اضافه شد');
};

window.deleteTerm = function(id) {
    if (confirm('آیا از حذف این ترم مطمئن هستید؟')) {
        allTerms = allTerms.filter(t => t.id !== id);
        filterTermsByBranch(currentTermBranch);
    }
};

window.editTerm = function(id) {
    alert('ویرایش ترم (مشابه بخش هنرجویان قابل پیاده‌سازی است)');
};

// ==================== Init ====================
(function initTerms() {
    setTimeout(() => {
        if (document.getElementById('termsTable')) {
            renderTermsBranchTabs();
            filterTermsByBranch('all');
        }
    }, 200);
})();


