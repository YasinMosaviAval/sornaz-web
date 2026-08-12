(function () {
'use strict';

window.permissionGroupsList = ['زبان', 'درس', 'ابزار', 'هنرجو', 'استاد', 'مالی', 'گزارش', 'عمومی'];
window.getPermissionBranches = async function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [{ id: 1, name: 'شعبه مرکزی' }, { id: 2, name: 'شعبه ونک' }, { id: 3, name: 'شعبه سعادت‌آباد' }, { id: 4, name: 'شعبه کرج' }];
};

const basePermissions = [
    { name: 'request_seen_language', title: 'درخواست مشاهده زبان', title_en: 'Request Seen Language', group: 'زبان' },
    { name: 'request_remove_language', title: 'درخواست حذف زبان', title_en: 'Request Remove Language', group: 'زبان' },
    { name: 'request_edit_language', title: 'درخواست ویرایش زبان', title_en: 'Request Edit Language', group: 'زبان' },
    { name: 'request_add_language', title: 'درخواست اضافه کردن زبان', title_en: 'Request Add Language', group: 'زبان' },
    { name: 'request_seen_lesson', title: 'درخواست مشاهده درس', title_en: 'Request Seen Lesson', group: 'درس' },
    { name: 'request_remove_lesson', title: 'درخواست حذف درس', title_en: 'Request Remove Lesson', group: 'درس' },
    { name: 'request_edit_lesson', title: 'درخواست ویرایش درس', title_en: 'Request Edit Lesson', group: 'درس' },
    { name: 'request_add_lesson', title: 'درخواست اضافه کردن درس', title_en: 'Request Add Lesson', group: 'درس' },
    { name: 'request_seen_instrument', title: 'درخواست مشاهده ابزار', title_en: 'Request Seen Instrument', group: 'ابزار' },
    { name: 'request_remove_instrument', title: 'درخواست حذف ابزار', title_en: 'Request Remove Instrument', group: 'ابزار' },
    { name: 'request_edit_instrument', title: 'درخواست ویرایش ابزار', title_en: 'Request Edit Instrument', group: 'ابزار' },
    { name: 'request_add_instrument', title: 'درخواست اضافه کردن ابزار', title_en: 'Request Add Instrument', group: 'ابزار' },
    { name: 'manage_students', title: 'مدیریت هنرجویان', title_en: 'Manage Students', group: 'هنرجو' },
    { name: 'view_students', title: 'مشاهده هنرجویان', title_en: 'View Students', group: 'هنرجو' },
    { name: 'manage_teachers', title: 'مدیریت اساتید', title_en: 'Manage Teachers', group: 'استاد' },
    { name: 'view_teachers', title: 'مشاهده اساتید', title_en: 'View Teachers', group: 'استاد' },
    { name: 'manage_finance', title: 'مدیریت امور مالی', title_en: 'Manage Finance', group: 'مالی' },
    { name: 'view_finance', title: 'مشاهده امور مالی', title_en: 'View Finance', group: 'مالی' },
    { name: 'view_reports', title: 'مشاهده گزارش‌ها', title_en: 'View Reports', group: 'گزارش' },
    { name: 'export_reports', title: 'خروجی گزارش‌ها', title_en: 'Export Reports', group: 'گزارش' },
    { name: 'manage_roles', title: 'مدیریت نقش‌ها', title_en: 'Manage Roles', group: 'عمومی' },
    { name: 'manage_permissions', title: 'مدیریت دسترسی‌ها', title_en: 'Manage Permissions', group: 'عمومی' }
];

let allPermissions = [];
(function build() {
    const branches = window.getPermissionBranches();
    let id = 1;
    while (allPermissions.length < 100) {
        basePermissions.forEach(function (p) {
            if (allPermissions.length >= 100) return;
            const branch = branches[allPermissions.length % branches.length];
            allPermissions.push(Object.assign({}, p, {
                id: id++,
                name: p.name + (id > basePermissions.length ? '_' + Math.floor(id / basePermissions.length) : ''),
                branchId: branch.id,
                branchName: branch.name
            }));
        });
    }
})();

let currentPermissionBranch = 'all';
let permissionsCurrentPage = 1;
const permissionsPerPage = 10;
let filteredPermissions = allPermissions.slice();
let editingPermissionRowId = null;
let permSortField = '';
let permSortDirection = 'asc';

const permissionPdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'name', label: 'نام' },
    { field: 'title', label: 'عنوان' },
    { field: 'title_en', label: 'عنوان انگلیسی' },
    { field: 'group', label: 'گروه' },
    { field: 'branchName', label: 'شعبه' }
];

function sortPermissionItems() {
    if (!permSortField) return;
    filteredPermissions.sort(function (a, b) {
        let av = String(a[permSortField] || '').toLowerCase();
        let bv = String(b[permSortField] || '').toLowerCase();
        if (av < bv) return permSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return permSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updatePermissionSortIcons = async function () {
    ['name', 'title', 'title_en', 'group', 'branchName'].forEach(function (f) {
        const icon = document.getElementById('permSortIcon-' + f);
        if (icon) icon.textContent = permSortField === f ? (permSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortPermissionsBy = async function (field) {
    if (permSortField === field) permSortDirection = permSortDirection === 'asc' ? 'desc' : 'asc';
    else { permSortField = field; permSortDirection = 'asc'; }
    sortPermissionItems();
    window.renderPermissionsTable(filteredPermissions);
    window.updatePermissionSortIcons();
};

window.renderPermissionsBranchTabs = async function () {
    const container = document.getElementById('permissionsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.permission-branch-tab:not([data-value="all"])').forEach(function (t) { t.remove(); });
    window.getPermissionBranches().forEach(function (b) {
        const active = String(currentPermissionBranch) === String(b.id);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'permission-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50');
        btn.dataset.value = b.id;
        btn.textContent = b.name;
        btn.onclick = function () { window.filterPermissionsByBranch(b.id); };
        container.appendChild(btn);
    });
    const allTab = container.querySelector('[data-value="all"]');
    if (allTab) {
        const isAll = currentPermissionBranch === 'all';
        allTab.classList.toggle('bg-indigo-600', isAll);
        allTab.classList.toggle('text-white', isAll);
        allTab.classList.toggle('border-indigo-600', isAll);
        if (!isAll) { allTab.classList.add('border', 'border-gray-200'); allTab.classList.remove('bg-indigo-600', 'text-white'); }
    }
};

window.filterPermissionsByBranch = async function (branchId) {
    currentPermissionBranch = branchId;
    document.querySelectorAll('.permission-branch-tab').forEach(function (tab) {
        const active = String(tab.dataset.value) === String(branchId);
        tab.classList.toggle('bg-indigo-600', active);
        tab.classList.toggle('text-white', active);
        tab.classList.toggle('border-indigo-600', active);
        if (!active) { tab.classList.add('border', 'border-gray-200'); tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600'); }
        else tab.classList.remove('border-gray-200');
    });
    window.filterPermissions();
};

window.filterPermissions = async function () {
    const search = (document.getElementById('permissionSearch') && document.getElementById('permissionSearch').value || '').trim().toLowerCase();
    const group = document.getElementById('filterPermissionGroup') && document.getElementById('filterPermissionGroup').value || '';
    filteredPermissions = allPermissions.filter(function (p) {
        const matchBranch = currentPermissionBranch === 'all' || String(p.branchId) === String(currentPermissionBranch) || p.branchId === 'all';
        const matchSearch = !search || (p.name || '').toLowerCase().includes(search) || (p.title || '').toLowerCase().includes(search) || (p.title_en || '').toLowerCase().includes(search);
        const matchGroup = !group || p.group === group;
        return matchBranch && matchSearch && matchGroup;
    });
    permissionsCurrentPage = 1;
    sortPermissionItems();
    window.renderPermissionsTable(filteredPermissions);
};

window.renderPermissionsTable = async function (list) {
    list = list || filteredPermissions;
    const tbody = document.querySelector('#permissionsTable tbody');
    if (!tbody) return;
    const totalPages = Math.ceil(list.length / permissionsPerPage) || 1;
    if (permissionsCurrentPage > totalPages) permissionsCurrentPage = totalPages;
    const start = (permissionsCurrentPage - 1) * permissionsPerPage;
    const end = start + permissionsPerPage;
    const pageItems = list.slice(start, end);
    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getPermissionEmptyRowHTML ? window.getPermissionEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getPermissionRowHTML ? window.getPermissionRowHTML(item) : '';
            tbody.appendChild(tr);
            if (editingPermissionRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50 permission-inline-expand';
                expand.innerHTML = window.getPermissionInlineExpandRowHTML ? window.getPermissionInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updatePermissionsPagination(list.length, start, end, totalPages);
    window.updatePermissionSortIcons();
};

function updatePermissionsPagination(total, start, end, totalPages) {
    const info = document.getElementById('permissionsPaginationInfo');
    if (info) info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' دسترسی';
    const pagination = document.getElementById('permissionsPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changePermissionsPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (permissionsCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changePermissionsPage(' + (permissionsCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (permissionsCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, permissionsCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) html += '<button onclick="changePermissionsPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === permissionsCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    html += '<button onclick="changePermissionsPage(' + (permissionsCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (permissionsCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changePermissionsPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (permissionsCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changePermissionsPage = async function (page) {
    const totalPages = Math.ceil(filteredPermissions.length / permissionsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    permissionsCurrentPage = page;
    window.renderPermissionsTable(filteredPermissions);
};

function readPermissionForm(prefix) {
    const g = function (id) { return document.getElementById(prefix + id); };
    const branchVal = g('Branch') && g('Branch').value;
    let branchId = branchVal === 'all' ? 'all' : parseInt(branchVal, 10);
    let branchName = 'همه شعبه‌ها';
    if (branchId !== 'all') {
        const branch = window.getPermissionBranches().find(function (b) { return b.id === branchId; });
        branchName = branch ? branch.name : 'نامشخص';
    }
    return {
        name: (g('Name') && g('Name').value || '').trim(),
        title: (g('Title') && g('Title').value || '').trim(),
        title_en: (g('TitleEn') && g('TitleEn').value || '').trim(),
        group: (g('Group') && g('Group').value || '').trim() || 'عمومی',
        branchId: branchId,
        branchName: branchName
    };
}

window.openAddPermissionModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getPermissionAddModalHTML ? window.getPermissionAddModalHTML() : '';
};

window.savePermission = async function () {
    const data = readPermissionForm('perm');
    if (!data.name || !data.title) return alert('نام و عنوان الزامی است');
    if (!data.title_en) data.title_en = data.name;
    allPermissions.unshift(Object.assign({}, data, { id: Date.now() }));
    window.filterPermissions();
    closeModal();
    alert('✅ دسترسی ثبت شد');
};

window.viewPermission = async function (id) {
    const item = allPermissions.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getPermissionDetailsModalHTML ? window.getPermissionDetailsModalHTML(item) : '';
};

window.editPermission = async function (id) {
    const item = allPermissions.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getPermissionEditModalHTML ? window.getPermissionEditModalHTML(item) : '';
};

window.saveEditedPermission = async function (id) {
    const data = readPermissionForm('editPerm');
    if (!data.name || !data.title) return alert('نام و عنوان الزامی است');
    const index = allPermissions.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allPermissions[index] = Object.assign({}, allPermissions[index], data);
    window.filterPermissions();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.togglePermissionInlineEdit = async function (id) {
    editingPermissionRowId = editingPermissionRowId === id ? null : id;
    window.renderPermissionsTable(filteredPermissions);
};

window.saveInlinePermission = async function (id) {
    const data = readPermissionForm('inlinePerm' + id);
    if (!data.name || !data.title) return alert('نام و عنوان الزامی است');
    const index = allPermissions.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allPermissions[index] = Object.assign({}, allPermissions[index], data);
    editingPermissionRowId = null;
    window.filterPermissions();
    alert('✅ تغییرات ذخیره شد');
};

window.deletePermission = async function (id) {
    if (!(await AppDialog.confirm('حذف این دسترسی؟'))) return;
    allPermissions = allPermissions.filter(function (p) { return p.id !== id; });
    if (editingPermissionRowId === id) editingPermissionRowId = null;
    window.filterPermissions();
};

window.exportPermissionsToExcel = async function () {
    const data = filteredPermissions.length ? filteredPermissions : allPermissions;
    let csv = '\uFEFFردیف,نام,عنوان,عنوان انگلیسی,گروه,شعبه\n';
    data.forEach(function (p, i) {
        csv += (i + 1) + ',"' + p.name + '","' + p.title + '","' + p.title_en + '","' + p.group + '","' + p.branchName + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'دسترسی‌ها_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportPermissionsToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getPermissionPDFModalHTML ? window.getPermissionPDFModalHTML(permissionPdfColumns) : '';
};

window.generatePermissionsPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('permPdfTitle') && document.getElementById('permPdfTitle').value || 'گزارش دسترسی‌ها';
    const subtitle = document.getElementById('permPdfSubtitle') && document.getElementById('permPdfSubtitle').value || '';
    const footer = document.getElementById('permPdfFooter') && document.getElementById('permPdfFooter').value || '';
    const format = document.getElementById('permPdfFormat') && document.getElementById('permPdfFormat').value || 'a4';
    const orientation = document.getElementById('permPdfOrientation') && document.getElementById('permPdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('permPdfIncludeDate') && document.getElementById('permPdfIncludeDate').checked;
    const headerColor = document.getElementById('permPdfHeaderColor') && document.getElementById('permPdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('permPdfEvenRowColor') && document.getElementById('permPdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('permPdfOddRowColor') && document.getElementById('permPdfOddRowColor').value || '#f8fafc';
    const selectedColumns = permissionPdfColumns.filter(function (c) {
        return document.getElementById('permPdfCol-' + c.field) && document.getElementById('permPdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredPermissions.length ? filteredPermissions : allPermissions;
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getPermissionPDFPageHTML(p + 1, pageRows, p === 0, {
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
    doc.save('دسترسی‌ها_' + date + '.pdf');
    closeModal();
};

setTimeout(function () {
    if (document.getElementById('permissionsTable')) {
        window.renderPermissionsBranchTabs();
        window.filterPermissions();
    }
}, 200);
})();
