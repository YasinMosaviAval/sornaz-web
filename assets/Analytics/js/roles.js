(function () {
'use strict';

window.roleTypesList = ['سیستم', 'سفارشی'];
window.getRoleBranches = async function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [{ id: 1, name: 'شعبه مرکزی' }, { id: 2, name: 'شعبه ونک' }, { id: 3, name: 'شعبه سعادت‌آباد' }, { id: 4, name: 'شعبه کرج' }];
};

const baseRoles = [
    { name: 'user', title: 'کاربر عادی', title_en: 'user', type: 'سیستم', color: '#6B7280', order: 1 },
    { name: 'verified_teacher', title: 'استاد تأییدشده', title_en: 'verified_teacher', type: 'سیستم', color: '#8B5CF6', order: 2 },
    { name: 'vip_member', title: 'عضو ویژه', title_en: 'vip_member', type: 'سیستم', color: '#F59E0B', order: 3 },
    { name: 'academy_student', title: 'دانش‌آموز', title_en: 'academy_student', type: 'سیستم', color: '#10B981', order: 4 },
    { name: 'academy_teacher', title: 'استاد', title_en: 'academy_teacher', type: 'سیستم', color: '#06B6D4', order: 5 },
    { name: 'academy_receptionist', title: 'منشی آموزشگاه', title_en: 'academy_receptionist', type: 'سیستم', color: '#3B82F6', order: 6 },
    { name: 'academy_manager', title: 'مدیر آموزشگاه', title_en: 'academy_manager', type: 'سیستم', color: '#4B5563', order: 7 },
    { name: 'academy_owner', title: 'مالک آموزشگاه', title_en: 'academy_owner', type: 'سیستم', color: '#A855F7', order: 8 },
    { name: 'financial_manager', title: 'مدیر مالی', title_en: 'financial_manager', type: 'سیستم', color: '#14B8A6', order: 9 },
    { name: 'content_manager', title: 'مدیر محتوا', title_en: 'content_manager', type: 'سیستم', color: '#EC4899', order: 10 },
    { name: 'support', title: 'پشتیبانی', title_en: 'support', type: 'سیستم', color: '#F97316', order: 11 },
    { name: 'admin', title: 'مدیر سایت', title_en: 'admin', type: 'سیستم', color: '#EF4444', order: 12 },
    { name: 'superadmin', title: 'مدیر کل پلتفرم', title_en: 'superadmin', type: 'سیستم', color: '#DC2626', order: 13 },
    { name: 'branch_assistant', title: 'دستیار شعبه', title_en: 'branch_assistant', type: 'سفارشی', color: '#6366F1', order: 14 },
    { name: 'event_coordinator', title: 'هماهنگ‌کننده رویداد', title_en: 'event_coordinator', type: 'سفارشی', color: '#0EA5E9', order: 15 }
];

let allRoles = [];
(function build() {
    const branches = window.getRoleBranches();
    let id = 1;
    while (allRoles.length < 100) {
        baseRoles.forEach(function (r) {
            if (allRoles.length >= 100) return;
            const branch = branches[allRoles.length % branches.length];
            allRoles.push(Object.assign({}, r, {
                id: id++,
                name: r.name + (id > baseRoles.length ? '_' + Math.floor(id / baseRoles.length) : ''),
                branchId: branch.id,
                branchName: branch.name,
                order: r.order + Math.floor((id - 1) / baseRoles.length) * 20
            }));
        });
    }
})();

let currentRoleBranch = 'all';
let rolesCurrentPage = 1;
const rolesPerPage = 10;
let filteredRoles = allRoles.slice();
let editingRoleRowId = null;
let roleSortField = '';
let roleSortDirection = 'asc';

const rolePdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'name', label: 'نام' },
    { field: 'title', label: 'عنوان' },
    { field: 'title_en', label: 'عنوان انگلیسی' },
    { field: 'type', label: 'نوع' },
    { field: 'color', label: 'رنگ' },
    { field: 'order', label: 'ترتیب' },
    { field: 'branchName', label: 'شعبه' }
];

function sortRoleItems() {
    if (!roleSortField) return;
    filteredRoles.sort(function (a, b) {
        let av = a[roleSortField], bv = b[roleSortField];
        if (roleSortField === 'order') { av = Number(av); bv = Number(bv); }
        else { av = String(av || '').toLowerCase(); bv = String(bv || '').toLowerCase(); }
        if (av < bv) return roleSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return roleSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateRoleSortIcons = async function () {
    ['name', 'title', 'title_en', 'type', 'color', 'order', 'branchName'].forEach(function (f) {
        const icon = document.getElementById('roleSortIcon-' + f);
        if (icon) icon.textContent = roleSortField === f ? (roleSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortRolesBy = async function (field) {
    if (roleSortField === field) roleSortDirection = roleSortDirection === 'asc' ? 'desc' : 'asc';
    else { roleSortField = field; roleSortDirection = 'asc'; }
    sortRoleItems();
    window.renderRolesTable(filteredRoles);
    window.updateRoleSortIcons();
};

window.renderRolesBranchTabs = async function () {
    const container = document.getElementById('rolesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.role-branch-tab:not([data-value="all"])').forEach(function (t) { t.remove(); });
    window.getRoleBranches().forEach(function (b) {
        const active = String(currentRoleBranch) === String(b.id);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'role-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50');
        btn.dataset.value = b.id;
        btn.textContent = b.name;
        btn.onclick = function () { window.filterRolesByBranch(b.id); };
        container.appendChild(btn);
    });
    const allTab = container.querySelector('[data-value="all"]');
    if (allTab) {
        const isAll = currentRoleBranch === 'all';
        allTab.classList.toggle('bg-indigo-600', isAll);
        allTab.classList.toggle('text-white', isAll);
        allTab.classList.toggle('border-indigo-600', isAll);
        if (!isAll) { allTab.classList.add('border', 'border-gray-200'); allTab.classList.remove('bg-indigo-600', 'text-white'); }
    }
};

window.filterRolesByBranch = async function (branchId) {
    currentRoleBranch = branchId;
    document.querySelectorAll('.role-branch-tab').forEach(function (tab) {
        const active = String(tab.dataset.value) === String(branchId);
        tab.classList.toggle('bg-indigo-600', active);
        tab.classList.toggle('text-white', active);
        tab.classList.toggle('border-indigo-600', active);
        if (!active) { tab.classList.add('border', 'border-gray-200'); tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600'); }
        else tab.classList.remove('border-gray-200');
    });
    window.filterRoles();
};

window.filterRoles = async function () {
    const search = (document.getElementById('roleSearch') && document.getElementById('roleSearch').value || '').trim().toLowerCase();
    const type = document.getElementById('filterRoleType') && document.getElementById('filterRoleType').value || '';
    filteredRoles = allRoles.filter(function (r) {
        const matchBranch = currentRoleBranch === 'all' || String(r.branchId) === String(currentRoleBranch) || r.branchId === 'all';
        const matchSearch = !search || (r.name || '').toLowerCase().includes(search) || (r.title || '').toLowerCase().includes(search) || (r.title_en || '').toLowerCase().includes(search);
        const matchType = !type || r.type === type;
        return matchBranch && matchSearch && matchType;
    });
    rolesCurrentPage = 1;
    sortRoleItems();
    window.renderRolesTable(filteredRoles);
};

window.renderRolesTable = async function (list) {
    list = list || filteredRoles;
    const tbody = document.querySelector('#rolesTable tbody');
    if (!tbody) return;
    const totalPages = Math.ceil(list.length / rolesPerPage) || 1;
    if (rolesCurrentPage > totalPages) rolesCurrentPage = totalPages;
    const start = (rolesCurrentPage - 1) * rolesPerPage;
    const end = start + rolesPerPage;
    const pageItems = list.slice(start, end);
    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getRoleEmptyRowHTML ? window.getRoleEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getRoleRowHTML ? window.getRoleRowHTML(item) : '';
            tbody.appendChild(tr);
            if (editingRoleRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50 role-inline-expand';
                expand.innerHTML = window.getRoleInlineExpandRowHTML ? window.getRoleInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updateRolesPagination(list.length, start, end, totalPages);
    window.updateRoleSortIcons();
};

function updateRolesPagination(total, start, end, totalPages) {
    const info = document.getElementById('rolesPaginationInfo');
    if (info) info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' نقش';
    const pagination = document.getElementById('rolesPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeRolesPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (rolesCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeRolesPage(' + (rolesCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (rolesCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, rolesCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) html += '<button onclick="changeRolesPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === rolesCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    html += '<button onclick="changeRolesPage(' + (rolesCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (rolesCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeRolesPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (rolesCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeRolesPage = async function (page) {
    const totalPages = Math.ceil(filteredRoles.length / rolesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    rolesCurrentPage = page;
    window.renderRolesTable(filteredRoles);
};

function readRoleForm(prefix) {
    const g = function (id) { return document.getElementById(prefix + id); };
    const branchVal = g('Branch') && g('Branch').value;
    let branchId = branchVal === 'all' ? 'all' : parseInt(branchVal, 10);
    let branchName = 'همه شعبه‌ها';
    if (branchId !== 'all') {
        const branch = window.getRoleBranches().find(function (b) { return b.id === branchId; });
        branchName = branch ? branch.name : 'نامشخص';
    }
    return {
        name: (g('Name') && g('Name').value || '').trim(),
        title: (g('Title') && g('Title').value || '').trim(),
        title_en: (g('TitleEn') && g('TitleEn').value || '').trim(),
        type: g('Type') && g('Type').value || 'سیستم',
        color: g('Color') && g('Color').value || '#4F46E5',
        order: parseInt(g('Order') && g('Order').value, 10) || 1,
        branchId: branchId,
        branchName: branchName
    };
}

window.openAddRoleModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getRoleAddModalHTML ? window.getRoleAddModalHTML() : '';
};

window.saveRole = async function () {
    const data = readRoleForm('role');
    if (!data.name || !data.title) return alert('نام و عنوان الزامی است');
    if (!data.title_en) data.title_en = data.name;
    allRoles.unshift(Object.assign({}, data, { id: Date.now() }));
    window.filterRoles();
    closeModal();
    alert('✅ نقش ثبت شد');
};

window.viewRole = async function (id) {
    const item = allRoles.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getRoleDetailsModalHTML ? window.getRoleDetailsModalHTML(item) : '';
};

window.editRole = async function (id) {
    const item = allRoles.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getRoleEditModalHTML ? window.getRoleEditModalHTML(item) : '';
};

window.saveEditedRole = async function (id) {
    const data = readRoleForm('editRole');
    if (!data.name || !data.title) return alert('نام و عنوان الزامی است');
    const index = allRoles.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allRoles[index] = Object.assign({}, allRoles[index], data);
    window.filterRoles();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.toggleRoleInlineEdit = async function (id) {
    editingRoleRowId = editingRoleRowId === id ? null : id;
    window.renderRolesTable(filteredRoles);
};

window.saveInlineRole = async function (id) {
    const data = readRoleForm('inlineRole' + id);
    if (!data.name || !data.title) return alert('نام و عنوان الزامی است');
    const index = allRoles.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allRoles[index] = Object.assign({}, allRoles[index], data);
    editingRoleRowId = null;
    window.filterRoles();
    alert('✅ تغییرات ذخیره شد');
};

window.deleteRole = async function (id) {
    if (!(await AppDialog.confirmDelete(allRoles, id, 'نقش'))) return;
    allRoles = allRoles.filter(function (r) { return r.id !== id; });
    if (editingRoleRowId === id) editingRoleRowId = null;
    window.filterRoles();
};

window.exportRolesToExcel = async function () {
    const data = filteredRoles.length ? filteredRoles : allRoles;
    let csv = '\uFEFFردیف,نام,عنوان,عنوان انگلیسی,نوع,رنگ,ترتیب,شعبه\n';
    data.forEach(function (r, i) {
        csv += (i + 1) + ',"' + r.name + '","' + r.title + '","' + r.title_en + '","' + r.type + '","' + r.color + '",' + r.order + ',"' + r.branchName + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'نقش‌ها_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportRolesToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getRolePDFModalHTML ? window.getRolePDFModalHTML(rolePdfColumns) : '';
};

window.generateRolesPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('rolePdfTitle') && document.getElementById('rolePdfTitle').value || 'گزارش نقش‌ها';
    const subtitle = document.getElementById('rolePdfSubtitle') && document.getElementById('rolePdfSubtitle').value || '';
    const footer = document.getElementById('rolePdfFooter') && document.getElementById('rolePdfFooter').value || '';
    const format = document.getElementById('rolePdfFormat') && document.getElementById('rolePdfFormat').value || 'a4';
    const orientation = document.getElementById('rolePdfOrientation') && document.getElementById('rolePdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('rolePdfIncludeDate') && document.getElementById('rolePdfIncludeDate').checked;
    const headerColor = document.getElementById('rolePdfHeaderColor') && document.getElementById('rolePdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('rolePdfEvenRowColor') && document.getElementById('rolePdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('rolePdfOddRowColor') && document.getElementById('rolePdfOddRowColor').value || '#f8fafc';
    const selectedColumns = rolePdfColumns.filter(function (c) {
        return document.getElementById('rolePdfCol-' + c.field) && document.getElementById('rolePdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredRoles.length ? filteredRoles : allRoles;
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getRolePDFPageHTML(p + 1, pageRows, p === 0, {
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
    doc.save('نقش‌ها_' + date + '.pdf');
    closeModal();
};

setTimeout(function () {
    if (document.getElementById('rolesTable')) {
        window.renderRolesBranchTabs();
        window.filterRoles();
    }
}, 200);
})();
