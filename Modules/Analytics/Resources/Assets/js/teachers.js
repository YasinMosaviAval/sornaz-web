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
for (let i = 1; i <= 28; i++) {
    const first = staffFirstNames[Math.floor(Math.random() * staffFirstNames.length)];
    const last = staffLastNames[Math.floor(Math.random() * staffLastNames.length)];
    const type = staffTypes[Math.floor(Math.random() * staffTypes.length)];
    const branch = branches[Math.floor(Math.random() * branches.length)];
    const startDate = getRandomDate('2024-01-01', '2024-09-01');
    const endDate = getRandomDate(startDate, '2025-12-31');
    const currency = contractCurrencies[Math.floor(Math.random() * contractCurrencies.length)];

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
        phone: `۰۹۱${Math.floor(10000000 + Math.random() * 89999999)}`
    });
}

// ==================== متغیرهای صفحه‌بندی ====================
let staffCurrentPage = 1;
const staffPerPage = 10;
let filteredStaff = [...allStaff];
let editingRowId = null;
let staffSortField = '';
let staffSortDirection = 'asc';

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

window.updateSortIcons = function() {
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

window.sortStaffBy = function(field) {
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

// ==================== رندر جدول ====================
window.renderStaffTable = function(staff = filteredStaff) {
    const tbody = document.querySelector('#staffTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(staff.length / staffPerPage) || 1;
    if (staffCurrentPage > totalPages) staffCurrentPage = totalPages;

    const start = (staffCurrentPage - 1) * staffPerPage;
    const end = start + staffPerPage;
    const pageStaff = staff.slice(start, end);

    tbody.innerHTML = '';

    if (pageStaff.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" class="py-12 text-center text-gray-400">هیچ پرسنلی یافت نشد</td></tr>`;
    } else {
        pageStaff.forEach(item => {
            const statusClass = item.status === 'فعال'
                ? 'bg-green-100 text-green-700'
                : item.status === 'مرخصی'
                    ? 'bg-yellow-100 text-yellow-700'
                    : 'bg-red-100 text-red-700';

            const tr = document.createElement('tr');
            tr.className = "hover:bg-gray-50 transition";
            tr.innerHTML = `
                <td class="py-4 px-5 font-medium">${item.name}</td>
                <td class="py-4 px-5">${item.typeLabel}</td>
                <td class="py-4 px-5">${item.contractTitle}</td>
                <td class="py-4 px-5">${item.branch}</td>
                <td class="py-4 px-5">${item.startDate}</td>
                <td class="py-4 px-5">${item.endDate}</td>
                <td class="py-4 px-5">${item.price.toLocaleString('fa-IR')} ${item.currency}</td>
                <td class="py-4 px-5">
                    <span class="px-3 py-1 rounded-full text-xs ${statusClass}">${item.status}</span>
                </td>
                <td class="py-4 px-5 text-left">
                    <div class="inline-flex flex-nowrap items-center gap-3 whitespace-nowrap">
                        <button onclick="viewStaff(${item.id})" class="text-indigo-600 hover:underline text-sm leading-6 align-middle">جزئیات</button>
                        <button onclick="toggleStaffInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm leading-6 align-middle">ویرایش</button>
                        <button onclick="deleteStaff(${item.id})" class="text-red-500 hover:text-red-700 text-sm leading-6 align-middle">حذف</button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);

            if (editingRowId === item.id) {
                const expandRow = document.createElement('tr');
                expandRow.className = 'bg-gray-50 staff-inline-expand';
                expandRow.innerHTML = `
                    <td colspan="9" class="p-5 border-t">
                        ${getInlineEditRowHTML(item)}
                    </td>
                `;
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

window.changeStaffPage = function(page) {
    const totalPages = Math.ceil(filteredStaff.length / staffPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    staffCurrentPage = page;
    renderStaffTable(filteredStaff);
};

// ==================== فیلترها ====================
window.filterStaff = function() {
    const search = (document.getElementById('staffSearch')?.value || '').trim().toLowerCase();
    const type = document.getElementById('filterStaffType')?.value || '';
    const branch = document.getElementById('filterStaffBranch')?.value || '';
    const status = document.getElementById('filterStaffStatus')?.value || '';

    filteredStaff = allStaff.filter(item => {
        const matchSearch = !search || item.name.toLowerCase().includes(search) || item.contractTitle.toLowerCase().includes(search) || (item.phone && item.phone.includes(search));
        const matchType = !type || item.type === type;
        const matchBranch = !branch || item.branch === branch;
        const matchStatus = !status || item.status === status;
        return matchSearch && matchType && matchBranch && matchStatus;
    });

    staffCurrentPage = 1;
    sortStaffItems();
    renderStaffTable(filteredStaff);
};

window.toggleStaffInlineEdit = function(id) {
    editingRowId = editingRowId === id ? null : id;
    renderStaffTable(filteredStaff);
};

window.deleteStaff = function(id) {
    if (!confirm('آیا از حذف این عضو مطمئن هستید؟')) return;
    allStaff = allStaff.filter(item => item.id !== id);
    filteredStaff = filteredStaff.filter(item => item.id !== id);
    if (editingRowId === id) editingRowId = null;
    staffCurrentPage = 1;
    renderStaffTable(filteredStaff);
};

window.getInlineEditRowHTML = function(item) {
    return `
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                    <input id="inlineStaffName-${item.id}" type="text" value="${item.name}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                    <input id="inlineStaffPhone-${item.id}" type="tel" value="${item.phone}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع قرارداد *</label>
                    <select id="inlineStaffType-${item.id}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${staffTypes.map(type => `<option value="${type.value}" ${type.value === item.type ? 'selected' : ''}>${type.label}</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه آموزشگاه *</label>
                    <select id="inlineStaffBranch-${item.id}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${branches.map(branch => `<option value="${branch}" ${branch === item.branch ? 'selected' : ''}>${branch}</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان قرارداد *</label>
                    <input id="inlineStaffContractTitle-${item.id}" type="text" value="${item.contractTitle}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع پول پرداختی *</label>
                    <select id="inlineStaffCurrency-${item.id}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${contractCurrencies.map(currency => `<option value="${currency.id}" ${currency.id === item.currencyId ? 'selected' : ''}>${currency.label}</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">مبلغ قرارداد *</label>
                    <input id="inlineStaffPrice-${item.id}" type="number" value="${item.price}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تاریخ شروع قرارداد *</label>
                    <input id="inlineStaffStartDate-${item.id}" type="date" value="${item.startDate}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تاریخ خاتمه قرارداد *</label>
                    <input id="inlineStaffEndDate-${item.id}" type="date" value="${item.endDate}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">شرح قرارداد</label>
                    <textarea id="inlineStaffContractDescription-${item.id}" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${item.contractDescription}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="inlineStaffStatus-${item.id}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${staffStatuses.map(status => `<option value="${status}" ${status === item.status ? 'selected' : ''}>${status}</option>`).join('')}
                    </select>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button onclick="saveInlineStaff(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="toggleStaffInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div>
        </div>
    `;
};

window.saveInlineStaff = function(id) {
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
        startDate: startDate,
        endDate: endDate,
        price: price,
        currencyId: currencyId,
        currency: currency,
        status: status
    };

    editingRowId = null;
    filterStaff();
    alert('✅ تغییرات با موفقیت ذخیره شد');
};

// ==================== خروجی اکسل ====================
window.exportStaffToExcel = function() {
    const data = filteredStaff.length ? filteredStaff : allStaff;
    let csv = '\uFEFF';
    csv += 'ردیف,نام,نوع پرسنل,عنوان قرارداد,شعبه,تاریخ شروع,تاریخ خاتمه,مبلغ قرارداد,واحد پول,وضعیت,شماره تماس\n';

    data.forEach((item, index) => {
        csv += `${index + 1},"${item.name}","${item.typeLabel}","${item.contractTitle}","${item.branch}",${item.startDate},${item.endDate},${item.price},"${item.currency}","${item.status}","${item.phone}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `پرسنل_${new Date().toLocaleDateString('fa-IR')}.csv`;
    link.click();
};

window.exportStaffToPDF = function() {
    openPDFOptionsModal();
};

window.openPDFOptionsModal = function() {
    const modalHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">تنظیمات خروجی PDF</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
            </div>
            <div class="p-8 space-y-6" style="max-height: calc(100vh - 10rem); overflow-y: auto;">
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">عنوان گزارش</label>
                        <input id="pdfTitle" type="text" value="گزارش پرسنل آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">زیرعنوان</label>
                        <input id="pdfSubtitle" type="text" value="لیست پرسنل و وضعیت قراردادها" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">فرمت صفحه</label>
                            <select id="pdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                <option value="a4">A4</option>
                                <option value="letter">Letter</option>
                                <option value="legal">Legal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">جهت صفحه</label>
                            <select id="pdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                <option value="landscape">افقی</option>
                                <option value="portrait">عمودی</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">متن یادداشت پایین صفحه</label>
                        <input id="pdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
<div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">ستون‌های خروجی PDF</label>
                        <div class="grid grid-cols-2 gap-2">
                            ${pdfExportColumns.map(col => `
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" id="pdfCol-${col.field}" value="${col.field}" checked class="text-indigo-600 border-gray-300 rounded">
                                    ${col.label}
                                </label>
                            `).join('')}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">رنگ سطر عنوان</label>
                            <input id="pdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">رنگ سطرهای زوج</label>
                            <input id="pdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">رنگ سطرهای فرد</label>
                            <input id="pdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                        <input id="pdfIncludeDate" type="checkbox" checked class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="pdfIncludeDate" class="text-sm text-gray-700">نمایش تاریخ استخراج در بالای گزارش</label>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="generateStaffPDF()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ایجاد PDF</button>
                    <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;

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

    const renderPageHTML = (pageNumber, rows, isFirstPage) => `
        <div style="width:100%; padding: 24px; border-radius: 20px; box-shadow: 0 10px 30px rgba(15,23,42,.08); background: #fff;">
            ${isFirstPage ? `
            <div style="text-align: right; direction: rtl;">
                <h1 style="margin: 0 0 6px; font-size: 28px; font-weight: 700;">${title}</h1>
                <p style="margin: 0 0 16px; color: #4b5563; font-size: 14px;">${subtitle}</p>
                ${includeDate ? `<p style="margin: 0 0 16px; color: #6b7280; font-size: 12px;">تاریخ استخراج: ${date}</p>` : ''}
            </div>
            ` : ''}
            <div style="width: 100%; overflow-x: auto;">
                <table style="width:100%; border-collapse: collapse; direction: rtl;">
                    <thead style="background: ${headerColor}; color: #000000;">
                        <tr>
                            ${selectedColumns.map(col => `<th style="padding: 12px 14px; text-align: right; font-weight: 600;">${col.label}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${rows.map((item, index) => `
                            <tr style="background: ${index % 2 === 0 ? evenRowColor : oddRowColor};">
                                ${selectedColumns.map(col => {
                                    const value = col.field === 'index' ? (pageNumber - 1) * rowsPerPage + index + 1 : (col.field === 'price' ? `${item.price.toLocaleString('fa-IR')} ${item.currency}` : item[col.field]);
                                    return `<td style="padding: 12px 14px; text-align: right;">${value}</td>`;
                                }).join('')}
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            ${isFirstPage && footer ? `<p style="margin-top: 16px; color: #6b7280; font-size: 12px;">${footer}</p>` : ''}
            <div style="margin-top: 16px; display: flex; justify-content: flex-end; color: #6b7280; font-size: 12px;">صفحه ${pageNumber} / ${totalPages}</div>
        </div>
    `;

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
window.openAddStaffModal = function() {
    const modalHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
            <div class="bg-white px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن پرسنل جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                        <input id="staffName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                        <input id="staffPhone" type="tel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع قرارداد *</label>
                        <select id="staffType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${staffTypes.map(type => `<option value="${type.value}">${type.label}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شعبه آموزشگاه *</label>
                        <select id="staffBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${branches.map(branch => `<option value="${branch}">${branch}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">عنوان قرارداد *</label>
                        <input id="staffContractTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع پول پرداختی *</label>
                        <select id="staffCurrency" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${contractCurrencies.map(currency => `<option value="${currency.id}">${currency.label}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">مبلغ قرارداد *</label>
                        <input id="staffPrice" type="number" value="5000000" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ شروع قرارداد *</label>
                        <input id="staffStartDate" type="date" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ خاتمه قرارداد *</label>
                        <input id="staffEndDate" type="date" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-medium mb-2">شرح قرارداد</label>
                        <textarea id="staffContractDescription" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="saveStaff()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره پرسنل</button>
                    <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;

    document.getElementById('modalContainer').innerHTML = modalHTML;
};

window.saveStaff = function() {
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
        status: 'فعال'
    });

    filterStaff();
    closeModal();
    alert('✅ پرسنل با موفقیت ثبت شد');
};

// ==================== نمایش جزئیات پرسنل ====================
window.viewStaff = function(id) {
    const item = allStaff.find(x => x.id === id);
    if (!item) return;

    const modalHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <div>
                    <h2 class="text-2xl font-bold">${item.name}</h2>
                    <p class="text-sm text-gray-500 mt-1">کد پرسنل: #${item.id}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="editStaff(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
            </div>
            
            <div class="p-8 space-y-8">
                <div>
                    <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-user"></i> اطلاعات شخصی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام</span><span class="font-medium">${item.name}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شماره تماس</span><span class="font-medium">${item.phone}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع پرسنل</span><span class="font-medium">${item.typeLabel}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${item.status}</span></div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-file-contract"></i> اطلاعات قرارداد</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">عنوان قرارداد</span><span class="font-medium">${item.contractTitle}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${item.branch}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ شروع</span><span class="font-medium">${item.startDate}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ خاتمه</span><span class="font-medium">${item.endDate}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">مبلغ قرارداد</span><span class="font-medium">${item.price.toLocaleString('fa-IR')} ${item.currency}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">واحد پول</span><span class="font-medium">${item.currency}</span></div>
                    </div>
                    <div class="mt-4 border rounded-2xl p-5 bg-gray-50 text-sm text-gray-700">
                        <div class="font-medium mb-2">شرح قرارداد</div>
                        <div class="leading-relaxed">${item.contractDescription}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

    document.getElementById('modalContainer').innerHTML = modalHTML;
};

// ==================== ویرایش پرسنل ====================
window.editStaff = function(id) {
    const item = allStaff.find(x => x.id === id);
    if (!item) return;

    const modalHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
            <div class="bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">ویرایش پرسنل</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                        <input id="editStaffName" type="text" value="${item.name}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                        <input id="editStaffPhone" type="tel" value="${item.phone}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع قرارداد *</label>
                        <select id="editStaffType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${staffTypes.map(type => `<option value="${type.value}" ${type.value === item.type ? 'selected' : ''}>${type.label}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شعبه آموزشگاه *</label>
                        <select id="editStaffBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${branches.map(branch => `<option value="${branch}" ${branch === item.branch ? 'selected' : ''}>${branch}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">عنوان قرارداد *</label>
                        <input id="editStaffContractTitle" type="text" value="${item.contractTitle}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع پول پرداختی *</label>
                        <select id="editStaffCurrency" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${contractCurrencies.map(currency => `<option value="${currency.id}" ${currency.id === item.currencyId ? 'selected' : ''}>${currency.label}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">مبلغ قرارداد *</label>
                        <input id="editStaffPrice" type="number" value="${item.price}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ شروع قرارداد *</label>
                        <input id="editStaffStartDate" type="date" value="${item.startDate}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ خاتمه قرارداد *</label>
                        <input id="editStaffEndDate" type="date" value="${item.endDate}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-medium mb-2">شرح قرارداد</label>
                        <textarea id="editStaffContractDescription" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${item.contractDescription}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">وضعیت</label>
                        <select id="editStaffStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${staffStatuses.map(status => `<option value="${status}" ${status === item.status ? 'selected' : ''}>${status}</option>`).join('')}
                        </select>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="saveEditedStaff(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                    <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;

    document.getElementById('modalContainer').innerHTML = modalHTML;
};

window.saveEditedStaff = function(id) {
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

    allStaff[index] = {
        ...allStaff[index],
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
        status: status
    };

    filterStaff();
    closeModal();
    alert('✅ تغییرات با موفقیت ذخیره شد');
};

// ==================== اجرای اولیه ====================
(function initStaff() {
    if (document.querySelector('#staffTable tbody')) {
        renderStaffTable();
    } else {
        setTimeout(() => {
            if (document.querySelector('#staffTable tbody')) {
                renderStaffTable();
            }
        }, 150);
    }
})();