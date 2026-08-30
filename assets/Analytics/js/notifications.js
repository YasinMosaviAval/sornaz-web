(function () {
'use strict';
// ==================== مدیریت اعلان‌های سیستم ====================

window.notificationStatusesList = ['در انتظار', 'پیش‌نویس', 'منتشر شده', 'خصوصی', 'زباله‌دان'];
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

let allNotifications = [];

let currentNotificationBranch = 'all';
let notificationsCurrentPage = 1;
const notificationsPerPage = 10;
let filteredNotifications = allNotifications.slice();
let notifSortField = '';
let notifSortDirection = 'asc';
let lastNotificationId = 0;
let notificationPolling = false;
const notificationChannel = 'BroadcastChannel' in window ? new BroadcastChannel('sornaz-admin-notifications') : null;

function notificationEncode(data) {
    const bytes = new TextEncoder().encode(JSON.stringify(data));
    let value = '';
    bytes.forEach(function (byte) { value += String.fromCharCode(byte); });
    return btoa(value).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}

async function notificationApi(url, data = null) {
    const token = window.adminCsrfToken || '';
    const options = { method: data === null ? 'GET' : 'POST', credentials: 'same-origin', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } };
    if (data !== null) {
        options.headers['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';
        options.headers['X-CSRF-TOKEN'] = token;
        options.body = new URLSearchParams({ _token: token, payload_b64: notificationEncode(data) }).toString();
    }
    const response = await fetch(url, options);
    const payload = await response.json();
    const envelope = payload.data ?? payload;
    if (!response.ok || envelope.success === false) throw new Error(envelope.message || 'عملیات اعلان ناموفق بود.');
    return envelope.data ?? envelope;
}

window.loadAdminNotifications = async function (notify = false) {
    const data = await notificationApi('/analytics/admin-notifications');
    const incoming = data.notifications || [];
    const fresh = incoming.find(function (item) { return item.id > lastNotificationId && item.readStatus === 'خوانده‌نشده'; });
    allNotifications = incoming;
    filteredNotifications = allNotifications.slice();
    window.filterNotifications(false);
    if (notify && fresh) showIncomingNotificationToast(fresh);
    lastNotificationId = Math.max(lastNotificationId, ...incoming.map(function (item) { return Number(item.id) || 0; }), 0);
    return data;
};

function showIncomingNotificationToast(item) {
    const toast = document.createElement('button');
    toast.type = 'button';
    toast.className = 'fixed left-5 bottom-5 z-[2147483000] w-[min(380px,calc(100vw-2.5rem))] rounded-2xl border border-amber-200 bg-white p-4 text-right shadow-2xl';
    const heading = document.createElement('b');
    heading.textContent = 'اعلان جدید';
    const text = document.createElement('span');
    text.className = 'mt-1 block truncate text-sm text-gray-600';
    text.textContent = item.title || 'بدون عنوان';
    toast.append(heading, text);
    toast.onclick = function () { toast.remove(); showSection('notifications'); viewNotification(item.id); };
    document.body.appendChild(toast);
    setTimeout(function () { toast.remove(); }, 9000);
}

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
    ['title', 'branchName', 'audience', 'priority', 'date', 'status', 'readStatus'].forEach(function (f) {
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

window.filterNotifications = async function (resetPage = true) {
    const search = (document.getElementById('notificationSearch') && document.getElementById('notificationSearch').value || '').trim().toLowerCase();
    const status = document.getElementById('filterNotificationStatus') && document.getElementById('filterNotificationStatus').value || '';
    const priority = document.getElementById('filterNotificationPriority') && document.getElementById('filterNotificationPriority').value || '';
    const audience = document.getElementById('filterNotificationAudience') && document.getElementById('filterNotificationAudience').value || '';

    filteredNotifications = allNotifications.filter(function (n) {
        const matchBranch = window.matchesOrganizationFilter(n,currentNotificationBranch);
        const matchSearch = !search ||
            (n.title || '').toLowerCase().includes(search) ||
            (n.body || '').toLowerCase().includes(search);
        const matchStatus = !status || n.status === status;
        const matchPriority = !priority || n.priority === priority;
        const matchAudience = !audience || n.audience === audience;
        return matchBranch && matchSearch && matchStatus && matchPriority && matchAudience;
    });

    if (resetPage) notificationsCurrentPage = 1;
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
    const wrapper = document.getElementById('notificationsPagination');
    if (wrapper) {
        wrapper.classList.toggle('hidden', total <= 10);
        wrapper.classList.toggle('flex', total > 10);
    }
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

    try {
        const audience = document.getElementById('notifAudience')?.value || 'همه';
        await notificationApi('/analytics/admin-notifications', { title: title, body: body, audience: audience, asDraft: Boolean(asDraft) });
        await window.loadAdminNotifications();
        notificationChannel?.postMessage(Date.now());
        closeModal();
        alert(asDraft ? '✅ اعلان ذخیره شد' : '✅ اعلان ثبت شد');
    } catch (error) { alert(error.message); }
};

window.viewNotification = async function (id) {
    let item = allNotifications.find(function (x) { return x.id === id; });
    if (!item) return;
    if (item.readStatus === 'خوانده‌نشده') {
        try {
            await notificationApi('/analytics/admin-notifications/' + id + '/read', {});
            await window.loadAdminNotifications();
            item = allNotifications.find(function (x) { return x.id === id; }) || item;
        } catch (error) { return alert(error.message); }
    }
    document.getElementById('modalContainer').innerHTML = window.getNotificationDetailsModalHTML
        ? window.getNotificationDetailsModalHTML(item) : '';
};

window.publishNotification = async function (id) {
    const item = allNotifications.find(function (x) { return x.id === id; });
    if (!item) return;
    try { await notificationApi('/analytics/admin-notifications/' + id + '/publish', {}); await window.loadAdminNotifications(); closeModal(); alert('✅ اعلان منتشر شد'); } catch (error) { alert(error.message); }
};

window.expireNotification = async function (id) {
    const item = allNotifications.find(function (x) { return x.id === id; });
    if (!item) return;
    try { await notificationApi('/analytics/admin-notifications/' + id + '/expire', {}); await window.loadAdminNotifications(); closeModal(); } catch (error) { alert(error.message); }
};

window.deleteNotification = async function (id) {
    if (!(await AppDialog.confirmDelete(allNotifications, id, 'اعلان'))) return;
    try { await notificationApi('/analytics/admin-notifications/' + id + '/delete', {}); await window.loadAdminNotifications(); } catch (error) { alert(error.message); }
};

setTimeout(async function () {
    if (document.getElementById('notificationsTable')) {
        window.renderNotificationsBranchTabs();
        try { await window.loadAdminNotifications(); } catch (error) { console.error(error); alert(error.message); }
    }
}, 200);
async function pollAdminNotifications() { if (notificationPolling || document.hidden) return; notificationPolling = true; try { await window.loadAdminNotifications(true); } catch (error) {} finally { notificationPolling = false; } }
notificationChannel && (notificationChannel.onmessage = pollAdminNotifications);
setInterval(pollAdminNotifications, 4000);
})();
