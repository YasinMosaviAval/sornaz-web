(function () {
'use strict';
// ==================== مدیریت اعلان‌های سیستم ====================

window.notificationStatusesList = ['منتشر شده', 'پیش‌نویس', 'منقضی'];
window.notificationPrioritiesList = ['بالا', 'متوسط', 'کم'];
window.notificationAudiencesList = ['همه', 'هنرجویان', 'اساتید', 'والدین', 'پرسنل'];

window.getNotificationBranches = function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' },
        { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' },
        { id: 4, name: 'شعبه کرج' }
    ];
};

const notifSampleTitles = [
    'تعطیلی شعبه در روز جمعه', 'شروع ثبت‌نام ترم جدید', 'تغییر ساعت کلاس‌های عصر',
    'برگزاری مستر کلاس رایگان', 'اطلاعیه پرداخت شهریه', 'جشن پایان ترم تابستان',
    'به‌روزرسانی قوانین حضور و غیاب', 'ظرفیت کلاس‌های سطح پیشرفته تکمیل شد',
    'برنامه کنسرت هنرجویان', 'هشدار تأخیر پرداخت شهریه', 'زمان‌بندی آزمون تئوری',
    'افتتاح کلاس جدید گیتار کلاسیک'
];
const notifSampleBodies = [
    'به اطلاع می‌رساند در تاریخ اعلام‌شده شعبه تعطیل خواهد بود. کلاس‌ها به هفته بعد منتقل می‌شوند.',
    'ثبت‌نام ترم جدید از امروز آغاز شده است. برای رزرو جا با پذیرش تماس بگیرید.',
    'ساعت کلاس‌های عصر از این هفته تغییر کرده است. جزئیات در پنل هنرجو قابل مشاهده است.',
    'مستر کلاس رایگان ویژه هنرجویان سطح متوسط برگزار می‌شود. ظرفیت محدود.',
    'مهلت پرداخت شهریه ترم جاری رو به اتمام است. لطفاً در اسرع وقت اقدام فرمایید.',
    'جشن پایان ترم با حضور هنرجویان و خانواده‌ها برگزار خواهد شد. دعوت‌نامه ارسال می‌شود.',
    'قوانین حضور و غیاب به‌روزرسانی شد. مطالعه آیین‌نامه جدید الزامی است.',
    'ظرفیت کلاس‌های پیشرفته تکمیل شده است. لیست انتظار در پذیرش فعال است.',
    'برنامه کنسرت هنرجویان نهایی شد. زمان تمرین‌های گروهی اعلام خواهد شد.',
    'برای جلوگیری از قطع دسترسی، شهریه معوق را تا پایان هفته پرداخت نمایید.'
];

let allNotifications = [];
(function buildSample() {
    const branches = window.getNotificationBranches();
    for (let i = 1; i <= 42; i++) {
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const d = new Date();
        d.setDate(d.getDate() - Math.floor(Math.random() * 45));
        allNotifications.push({
            id: i,
            title: notifSampleTitles[Math.floor(Math.random() * notifSampleTitles.length)],
            body: notifSampleBodies[Math.floor(Math.random() * notifSampleBodies.length)],
            branchId: branch.id,
            branchName: branch.name,
            audience: window.notificationAudiencesList[Math.floor(Math.random() * window.notificationAudiencesList.length)],
            priority: window.notificationPrioritiesList[Math.floor(Math.random() * window.notificationPrioritiesList.length)],
            status: window.notificationStatusesList[Math.floor(Math.random() * window.notificationStatusesList.length)],
            date: d.toLocaleDateString('fa-IR'),
            dateISO: d.toISOString().split('T')[0],
            source: Math.random() > 0.4 ? 'سیستم' : 'مدیریت'
        });
    }
})();

let currentNotificationBranch = 'all';
let notificationsCurrentPage = 1;
const notificationsPerPage = 10;
let filteredNotifications = allNotifications.slice();
let notifSortField = '';
let notifSortDirection = 'asc';

function sortNotificationItems() {
    if (!notifSortField) return;
    filteredNotifications.sort(function (a, b) {
        let av = a[notifSortField], bv = b[notifSortField];
        if (notifSortField === 'date') {
            av = a.dateISO || '';
            bv = b.dateISO || '';
        } else {
            av = String(av || '').toLowerCase();
            bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return notifSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return notifSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateNotificationSortIcons = async function () {
    ['title', 'branchName', 'audience', 'priority', 'date', 'status'].forEach(function (f) {
        const icon = document.getElementById('notifSortIcon-' + f);
        if (!icon) return;
        icon.textContent = notifSortField === f ? (notifSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortNotificationsBy = async function (field) {
    if (notifSortField === field) notifSortDirection = notifSortDirection === 'asc' ? 'desc' : 'asc';
    else { notifSortField = field; notifSortDirection = 'asc'; }
    sortNotificationItems();
    window.renderNotificationsTable(filteredNotifications);
    window.updateNotificationSortIcons();
};

window.renderNotificationsBranchTabs = async function () {
    const container = document.getElementById('notificationsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.notification-branch-tab:not([data-value="all"])').forEach(function (t) { t.remove(); });
    window.getNotificationBranches().forEach(function (b) {
        const active = String(currentNotificationBranch) === String(b.id);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'notification-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50');
        btn.dataset.value = b.id;
        btn.textContent = b.name;
        btn.onclick = function () { window.filterNotificationsByBranch(b.id); };
        container.appendChild(btn);
    });
    const allTab = container.querySelector('[data-value="all"]');
    if (allTab) {
        const isAll = currentNotificationBranch === 'all';
        allTab.classList.toggle('bg-indigo-600', isAll);
        allTab.classList.toggle('text-white', isAll);
        allTab.classList.toggle('border-indigo-600', isAll);
        if (!isAll) {
            allTab.classList.add('border', 'border-gray-200');
            allTab.classList.remove('bg-indigo-600', 'text-white');
        }
    }
};

window.filterNotificationsByBranch = async function (branchId) {
    currentNotificationBranch = branchId;
    document.querySelectorAll('.notification-branch-tab').forEach(function (tab) {
        const active = String(tab.dataset.value) === String(branchId);
        tab.classList.toggle('bg-indigo-600', active);
        tab.classList.toggle('text-white', active);
        tab.classList.toggle('border-indigo-600', active);
        if (!active) {
            tab.classList.add('border', 'border-gray-200');
            tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        } else {
            tab.classList.remove('border-gray-200');
        }
    });
    window.filterNotifications();
};

window.filterNotifications = async function () {
    const search = (document.getElementById('notificationSearch') && document.getElementById('notificationSearch').value || '').trim().toLowerCase();
    const status = document.getElementById('filterNotificationStatus') && document.getElementById('filterNotificationStatus').value || '';
    const priority = document.getElementById('filterNotificationPriority') && document.getElementById('filterNotificationPriority').value || '';
    const audience = document.getElementById('filterNotificationAudience') && document.getElementById('filterNotificationAudience').value || '';

    filteredNotifications = allNotifications.filter(function (n) {
        const matchBranch = currentNotificationBranch === 'all' || String(n.branchId) === String(currentNotificationBranch);
        const matchSearch = !search ||
            (n.title || '').toLowerCase().includes(search) ||
            (n.body || '').toLowerCase().includes(search);
        const matchStatus = !status || n.status === status;
        const matchPriority = !priority || n.priority === priority;
        const matchAudience = !audience || n.audience === audience;
        return matchBranch && matchSearch && matchStatus && matchPriority && matchAudience;
    });

    notificationsCurrentPage = 1;
    sortNotificationItems();
    window.renderNotificationsTable(filteredNotifications);
};

window.renderNotificationsTable = async function (list) {
    list = list || filteredNotifications;
    const tbody = document.querySelector('#notificationsTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / notificationsPerPage) || 1;
    if (notificationsCurrentPage > totalPages) notificationsCurrentPage = totalPages;

    const start = (notificationsCurrentPage - 1) * notificationsPerPage;
    const end = start + notificationsPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getNotificationEmptyRowHTML ? window.getNotificationEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getNotificationRowHTML ? window.getNotificationRowHTML(item) : '';
            tbody.appendChild(tr);
        });
    }
    updateNotificationsPagination(list.length, start, end, totalPages);
    window.updateNotificationSortIcons();
};

function updateNotificationsPagination(total, start, end, totalPages) {
    const info = document.getElementById('notificationsPaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' اعلان';
    }
    const pagination = document.getElementById('notificationsPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeNotificationsPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (notificationsCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeNotificationsPage(' + (notificationsCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (notificationsCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, notificationsCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeNotificationsPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === notificationsCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeNotificationsPage(' + (notificationsCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (notificationsCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeNotificationsPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (notificationsCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeNotificationsPage = async function (page) {
    const totalPages = Math.ceil(filteredNotifications.length / notificationsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    notificationsCurrentPage = page;
    window.renderNotificationsTable(filteredNotifications);
};

window.openAddNotificationModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getNotificationAddModalHTML
        ? window.getNotificationAddModalHTML() : '';
};

window.saveNotification = async function (asDraft) {
    const title = (document.getElementById('notifTitle') && document.getElementById('notifTitle').value || '').trim();
    const body = (document.getElementById('notifBody') && document.getElementById('notifBody').value || '').trim();
    if (!title) return alert('عنوان اعلان الزامی است');
    if (!body) return alert('متن اعلان الزامی است');

    const branchId = parseInt(document.getElementById('notifBranch') && document.getElementById('notifBranch').value, 10);
    const branch = window.getNotificationBranches().find(function (b) { return b.id === branchId; });
    const now = new Date();

    allNotifications.unshift({
        id: Date.now(),
        title: title,
        body: body,
        branchId: branchId,
        branchName: branch ? branch.name : 'نامشخص',
        audience: document.getElementById('notifAudience') && document.getElementById('notifAudience').value || 'همه',
        priority: document.getElementById('notifPriority') && document.getElementById('notifPriority').value || 'متوسط',
        date: now.toLocaleDateString('fa-IR'),
        dateISO: now.toISOString().split('T')[0],
        status: asDraft ? 'پیش‌نویس' : 'منتشر شده',
        source: 'مدیریت'
    });

    window.filterNotifications();
    closeModal();
    alert(asDraft ? '✅ پیش‌نویس ذخیره شد' : '✅ اعلان منتشر شد');
};

window.viewNotification = async function (id) {
    const item = allNotifications.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getNotificationDetailsModalHTML
        ? window.getNotificationDetailsModalHTML(item) : '';
};

window.publishNotification = async function (id) {
    const item = allNotifications.find(function (x) { return x.id === id; });
    if (!item) return;
    item.status = 'منتشر شده';
    window.filterNotifications();
    closeModal();
    alert('✅ اعلان منتشر شد');
};

window.expireNotification = async function (id) {
    const item = allNotifications.find(function (x) { return x.id === id; });
    if (!item) return;
    item.status = 'منقضی';
    window.filterNotifications();
    closeModal();
};

window.deleteNotification = async function (id) {
    if (!(await AppDialog.confirmDelete(allNotifications, id, 'اعلان'))) return;
    allNotifications = allNotifications.filter(function (n) { return n.id !== id; });
    window.filterNotifications();
};

setTimeout(function () {
    if (document.getElementById('notificationsTable')) {
        window.renderNotificationsBranchTabs();
        window.filterNotifications();
    }
}, 200);
})();
