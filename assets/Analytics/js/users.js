(function () {
'use strict';
// ==================== مدیریت کاربران (نقش + دسترسی + شعبه) ====================

window.userStatusesList = ['فعال', 'غیرفعال', 'معلق'];
window.userTypesList = [
    { value: 'staff', label: 'پرسنل' },
    { value: 'student', label: 'هنرجو' },
    { value: 'parent', label: 'والد' },
    { value: 'admin', label: 'مدیر' }
];

window.getUserBranches = async function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' },
        { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' },
        { id: 4, name: 'شعبه کرج' }
    ];
};

/** نقش‌های قابل انتخاب (هم‌راستا با roles) */
window.userRolesCatalog = [
    { name: 'user', title: 'کاربر عادی', color: '#6B7280' },
    { name: 'academy_student', title: 'هنرجو', color: '#10B981' },
    { name: 'academy_teacher', title: 'استاد', color: '#06B6D4' },
    { name: 'academy_receptionist', title: 'منشی', color: '#3B82F6' },
    { name: 'academy_manager', title: 'مدیر آموزشگاه', color: '#4B5563' },
    { name: 'academy_owner', title: 'مالک', color: '#A855F7' },
    { name: 'financial_manager', title: 'مدیر مالی', color: '#14B8A6' },
    { name: 'content_manager', title: 'مدیر محتوا', color: '#EC4899' },
    { name: 'support', title: 'پشتیبانی', color: '#F97316' },
    { name: 'admin', title: 'مدیر سایت', color: '#EF4444' },
    { name: 'superadmin', title: 'مدیر کل', color: '#DC2626' },
    { name: 'vip_member', title: 'عضو ویژه', color: '#F59E0B' }
];

/** دسترسی‌های نمونه برای نمایش در کاربر */
window.userPermissionsCatalog = [
    { name: 'view_students', title: 'مشاهده هنرجویان', group: 'هنرجو' },
    { name: 'manage_students', title: 'مدیریت هنرجویان', group: 'هنرجو' },
    { name: 'view_teachers', title: 'مشاهده اساتید', group: 'استاد' },
    { name: 'manage_teachers', title: 'مدیریت اساتید', group: 'استاد' },
    { name: 'view_finance', title: 'مشاهده مالی', group: 'مالی' },
    { name: 'manage_finance', title: 'مدیریت مالی', group: 'مالی' },
    { name: 'view_reports', title: 'مشاهده گزارش', group: 'گزارش' },
    { name: 'export_reports', title: 'خروجی گزارش', group: 'گزارش' },
    { name: 'manage_roles', title: 'مدیریت نقش‌ها', group: 'عمومی' },
    { name: 'manage_permissions', title: 'مدیریت دسترسی‌ها', group: 'عمومی' },
    { name: 'request_add_lesson', title: 'افزودن درس', group: 'درس' },
    { name: 'request_edit_lesson', title: 'ویرایش درس', group: 'درس' },
    { name: 'request_seen_language', title: 'مشاهده زبان', group: 'زبان' },
    { name: 'request_add_instrument', title: 'افزودن ابزار', group: 'ابزار' }
];

const userFirstNames = ['سارا', 'امیر', 'زهرا', 'علی', 'نگار', 'پارسا', 'مهسا', 'رضا', 'نیلوفر', 'محمد', 'فاطمه', 'حسین', 'مریم', 'آرین', 'هستی', 'کیان'];
const userLastNames = ['احمدی', 'حسینی', 'کریمی', 'محمدی', 'رضایی', 'نوری', 'موسوی', 'جعفری', 'کاظمی', 'حیدری'];

function pickPermissions(count) {
    const shuffled = window.userPermissionsCatalog.slice().sort(function () { return Math.random() - 0.5; });
    return shuffled.slice(0, count).map(function (p) { return { name: p.name, title: p.title, group: p.group }; });
}

let allUsers = [];
(function buildSample() {
    const branches = window.getUserBranches();
    const roles = window.userRolesCatalog;
    const types = window.userTypesList;
    for (let i = 1; i <= 120; i++) {
        const first = userFirstNames[Math.floor(Math.random() * userFirstNames.length)];
        const last = userLastNames[Math.floor(Math.random() * userLastNames.length)];
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const role = roles[Math.floor(Math.random() * roles.length)];
        const type = types[Math.floor(Math.random() * types.length)];
        const permCount = 2 + Math.floor(Math.random() * 5);
        allUsers.push({
            id: i,
            name: first + ' ' + last,
            phone: '۰۹۱' + Math.floor(10000000 + Math.random() * 89999999),
            email: first.toLowerCase() + i + '@example.com',
            userType: type.value,
            userTypeLabel: type.label,
            roleName: role.name,
            roleTitle: role.title,
            roleColor: role.color,
            branchId: branch.id,
            branchName: branch.name,
            permissions: pickPermissions(permCount),
            status: window.userStatusesList[Math.floor(Math.random() * window.userStatusesList.length)],
            lastLogin: (function () {
                const d = new Date();
                d.setDate(d.getDate() - Math.floor(Math.random() * 30));
                return d.toLocaleDateString('fa-IR');
            })()
        });
    }
})();

let currentUserBranch = 'all';
let usersCurrentPage = 1;
const usersPerPage = 10;
let filteredUsers = allUsers.slice();
let editingUserRowId = null;
let userSortField = '';
let userSortDirection = 'asc';

const userPdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'name', label: 'نام' },
    { field: 'phone', label: 'موبایل' },
    { field: 'email', label: 'ایمیل' },
    { field: 'userTypeLabel', label: 'نوع' },
    { field: 'roleTitle', label: 'نقش' },
    { field: 'branchName', label: 'شعبه' },
    { field: 'status', label: 'وضعیت' },
    { field: 'lastLogin', label: 'آخرین ورود' }
];

window.populateUserRoleFilter = async function () {
    const sel = document.getElementById('filterUserRole');
    if (!sel) return;
    const current = sel.value;
    sel.innerHTML = '<option value="">همه نقش‌ها</option>' +
        window.userRolesCatalog.map(function (r) {
            return '<option value="' + r.name + '">' + r.title + '</option>';
        }).join('');
    if (current) sel.value = current;
};

function sortUserItems() {
    if (!userSortField) return;
    filteredUsers.sort(function (a, b) {
        let av = String(a[userSortField] || '').toLowerCase();
        let bv = String(b[userSortField] || '').toLowerCase();
        if (av < bv) return userSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return userSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateUserSortIcons = async function () {
    ['name', 'phone', 'userTypeLabel', 'roleTitle', 'branchName', 'status'].forEach(function (f) {
        const icon = document.getElementById('userSortIcon-' + f);
        if (icon) icon.textContent = userSortField === f ? (userSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortUsersBy = async function (field) {
    if (userSortField === field) userSortDirection = userSortDirection === 'asc' ? 'desc' : 'asc';
    else { userSortField = field; userSortDirection = 'asc'; }
    sortUserItems();
    window.renderUsersTable(filteredUsers);
    window.updateUserSortIcons();
};

window.renderUsersBranchTabs = async function () {
    const container = document.getElementById('usersBranchTabs');
    if (!container) return;
    container.querySelectorAll('.user-branch-tab:not([data-value="all"])').forEach(function (t) { t.remove(); });
    window.getUserBranches().forEach(function (b) {
        const active = String(currentUserBranch) === String(b.id);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'user-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50');
        btn.dataset.value = b.id;
        btn.textContent = b.name;
        btn.onclick = function () { window.filterUsersByBranch(b.id); };
        container.appendChild(btn);
    });
    const allTab = container.querySelector('[data-value="all"]');
    if (allTab) {
        const isAll = currentUserBranch === 'all';
        allTab.classList.toggle('bg-indigo-600', isAll);
        allTab.classList.toggle('text-white', isAll);
        allTab.classList.toggle('border-indigo-600', isAll);
        if (!isAll) {
            allTab.classList.add('border', 'border-gray-200');
            allTab.classList.remove('bg-indigo-600', 'text-white');
        }
    }
};

window.filterUsersByBranch = async function (branchId) {
    currentUserBranch = branchId;
    document.querySelectorAll('.user-branch-tab').forEach(function (tab) {
        const active = String(tab.dataset.value) === String(branchId);
        tab.classList.toggle('bg-indigo-600', active);
        tab.classList.toggle('text-white', active);
        tab.classList.toggle('border-indigo-600', active);
        if (!active) {
            tab.classList.add('border', 'border-gray-200');
            tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        } else tab.classList.remove('border-gray-200');
    });
    window.filterUsers();
};

window.filterUsers = async function () {
    const search = (document.getElementById('userSearch') && document.getElementById('userSearch').value || '').trim().toLowerCase();
    const role = document.getElementById('filterUserRole') && document.getElementById('filterUserRole').value || '';
    const status = document.getElementById('filterUserStatus') && document.getElementById('filterUserStatus').value || '';
    const userType = document.getElementById('filterUserType') && document.getElementById('filterUserType').value || '';

    filteredUsers = allUsers.filter(function (u) {
        const matchBranch = currentUserBranch === 'all' || String(u.branchId) === String(currentUserBranch);
        const matchSearch = !search ||
            (u.name || '').toLowerCase().includes(search) ||
            (u.phone && String(u.phone).includes(search)) ||
            (u.email || '').toLowerCase().includes(search);
        const matchRole = !role || u.roleName === role;
        const matchStatus = !status || u.status === status;
        const matchType = !userType || u.userType === userType;
        return matchBranch && matchSearch && matchRole && matchStatus && matchType;
    });

    usersCurrentPage = 1;
    sortUserItems();
    window.renderUsersTable(filteredUsers);
};

window.renderUsersTable = async function (list) {
    list = list || filteredUsers;
    const tbody = document.querySelector('#usersTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / usersPerPage) || 1;
    if (usersCurrentPage > totalPages) usersCurrentPage = totalPages;

    const start = (usersCurrentPage - 1) * usersPerPage;
    const end = start + usersPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getUserEmptyRowHTML ? window.getUserEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getUserRowHTML ? window.getUserRowHTML(item) : '';
            tbody.appendChild(tr);
            if (editingUserRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50 user-inline-expand';
                expand.innerHTML = window.getUserInlineExpandRowHTML ? window.getUserInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updateUsersPagination(list.length, start, end, totalPages);
    window.updateUserSortIcons();
};

function updateUsersPagination(total, start, end, totalPages) {
    const info = document.getElementById('usersPaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' کاربر';
    }
    const pagination = document.getElementById('usersPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeUsersPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (usersCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeUsersPage(' + (usersCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (usersCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, usersCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeUsersPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === usersCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeUsersPage(' + (usersCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (usersCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeUsersPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (usersCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeUsersPage = async function (page) {
    const totalPages = Math.ceil(filteredUsers.length / usersPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    usersCurrentPage = page;
    window.renderUsersTable(filteredUsers);
};

function readSelectedPermissions(prefix) {
    const list = [];
    window.userPermissionsCatalog.forEach(function (p) {
        const el = document.getElementById(prefix + 'Perm_' + p.name);
        if (el && el.checked) list.push({ name: p.name, title: p.title, group: p.group });
    });
    return list;
}

function readUserForm(prefix) {
    const g = function (id) { return document.getElementById(prefix + id); };
    const branchId = parseInt(g('Branch') && g('Branch').value, 10);
    const branch = window.getUserBranches().find(function (b) { return b.id === branchId; });
    const roleName = g('Role') && g('Role').value || 'user';
    const role = window.userRolesCatalog.find(function (r) { return r.name === roleName; }) || window.userRolesCatalog[0];
    const typeVal = g('Type') && g('Type').value || 'student';
    const type = window.userTypesList.find(function (t) { return t.value === typeVal; }) || window.userTypesList[0];
    return {
        name: (g('Name') && g('Name').value || '').trim(),
        phone: (g('Phone') && g('Phone').value || '').trim(),
        email: (g('Email') && g('Email').value || '').trim(),
        userType: type.value,
        userTypeLabel: type.label,
        roleName: role.name,
        roleTitle: role.title,
        roleColor: role.color,
        branchId: branchId,
        branchName: branch ? branch.name : 'نامشخص',
        permissions: readSelectedPermissions(prefix),
        status: g('Status') && g('Status').value || 'فعال'
    };
}

window.openAddUserModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getUserAddModalHTML ? window.getUserAddModalHTML() : '';
};

window.saveUser = async function () {
    const data = readUserForm('user');
    if (!data.name || !data.phone) return alert('نام و شماره تماس الزامی است');
    allUsers.unshift(Object.assign({}, data, {
        id: Date.now(),
        lastLogin: '—'
    }));
    window.filterUsers();
    closeModal();
    alert('✅ کاربر ثبت شد');
};

window.viewUser = async function (id) {
    const item = allUsers.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getUserDetailsModalHTML ? window.getUserDetailsModalHTML(item) : '';
};

window.editUser = async function (id) {
    const item = allUsers.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getUserEditModalHTML ? window.getUserEditModalHTML(item) : '';
};

window.saveEditedUser = async function (id) {
    const data = readUserForm('editUser');
    if (!data.name || !data.phone) return alert('نام و شماره تماس الزامی است');
    const index = allUsers.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allUsers[index] = Object.assign({}, allUsers[index], data);
    window.filterUsers();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.toggleUserInlineEdit = async function (id) {
    editingUserRowId = editingUserRowId === id ? null : id;
    window.renderUsersTable(filteredUsers);
};

window.saveInlineUser = async function (id) {
    const data = readUserForm('inlineUser' + id);
    if (!data.name || !data.phone) return alert('نام و شماره تماس الزامی است');
    const index = allUsers.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allUsers[index] = Object.assign({}, allUsers[index], data);
    editingUserRowId = null;
    window.filterUsers();
    alert('✅ تغییرات ذخیره شد');
};

window.deleteUser = async function (id) {
    if (!(await AppDialog.confirmDelete(allUsers, id, 'کاربر'))) return;
    allUsers = allUsers.filter(function (u) { return u.id !== id; });
    if (editingUserRowId === id) editingUserRowId = null;
    window.filterUsers();
};

window.exportUsersToExcel = async function () {
    const data = filteredUsers.length ? filteredUsers : allUsers;
    let csv = '\uFEFFردیف,نام,موبایل,ایمیل,نوع,نقش,شعبه,دسترسی‌ها,وضعیت,آخرین ورود\n';
    data.forEach(function (u, i) {
        const perms = (u.permissions || []).map(function (p) { return p.title; }).join(' | ');
        csv += (i + 1) + ',"' + u.name + '","' + u.phone + '","' + (u.email || '') + '","' + u.userTypeLabel + '","' +
            u.roleTitle + '","' + u.branchName + '","' + perms + '","' + u.status + '","' + (u.lastLogin || '') + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'کاربران_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportUsersToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getUserPDFModalHTML
        ? window.getUserPDFModalHTML(userPdfColumns) : '';
};

window.generateUsersPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('userPdfTitle') && document.getElementById('userPdfTitle').value || 'گزارش کاربران';
    const subtitle = document.getElementById('userPdfSubtitle') && document.getElementById('userPdfSubtitle').value || '';
    const footer = document.getElementById('userPdfFooter') && document.getElementById('userPdfFooter').value || '';
    const format = document.getElementById('userPdfFormat') && document.getElementById('userPdfFormat').value || 'a4';
    const orientation = document.getElementById('userPdfOrientation') && document.getElementById('userPdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('userPdfIncludeDate') && document.getElementById('userPdfIncludeDate').checked;
    const headerColor = document.getElementById('userPdfHeaderColor') && document.getElementById('userPdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('userPdfEvenRowColor') && document.getElementById('userPdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('userPdfOddRowColor') && document.getElementById('userPdfOddRowColor').value || '#f8fafc';
    const selectedColumns = userPdfColumns.filter(function (c) {
        return document.getElementById('userPdfCol-' + c.field) && document.getElementById('userPdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredUsers.length ? filteredUsers : allUsers;
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getUserPDFPageHTML(p + 1, pageRows, p === 0, {
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
    doc.save('کاربران_' + date + '.pdf');
    closeModal();
};

setTimeout(function () {
    if (document.getElementById('usersTable')) {
        window.populateUserRoleFilter();
        window.renderUsersBranchTabs();
        window.filterUsers();
    }
}, 200);
})();
