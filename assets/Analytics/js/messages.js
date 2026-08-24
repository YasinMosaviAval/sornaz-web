(function () {
'use strict';
// ==================== مدیریت پیام‌ها ====================

window.messageStatusesList = ['خوانده‌نشده', 'خوانده‌شده'];
window.messagePrioritiesList = ['عادی', 'مهم', 'فوری'];
window.messageTypesList = ['اطلاعیه', 'یادآوری', 'هشدار', 'شخصی'];

window.getMessageBranches = function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' },
        { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' },
        { id: 4, name: 'شعبه کرج' }
    ];
};

const sampleTitles = [
    'یادآوری جلسه اولیا', 'تأخیر در پرداخت شهریه', 'تغییر ساعت کلاس', 'درخواست مرخصی استاد',
    'اطلاعیه تعطیلی موقت', 'نتیجه آزمون تئوری', 'برنامه کنسرت پایان ترم', 'ثبت‌نام ترم جدید',
    'هشدار ظرفیت کلاس', 'پیام شخصی به هنرجو', 'هماهنگی تمرین گروهی', 'به‌روزرسانی قوانین آموزشگاه'
];
const sampleSenders = ['مدیر سیستم', 'مدیر شعبه', 'استاد رضایی', 'استاد موسوی', 'استاد بهرامی', 'پذیرش', 'سیستم'];
const sampleReceivers = ['همه', 'همه والدین', 'همه هنرجویان', 'هنرجویان گیتار', 'هنرجویان سطح متوسط', 'سارا احمدی', 'مدیریت', 'اساتید'];
const sampleBodies = [
    'با سلام، لطفاً در جلسه پیش‌رو حضور به‌موقع داشته باشید.',
    'شهریه ترم جاری هنوز پرداخت نشده است. لطفاً در اسرع وقت اقدام فرمایید.',
    'ساعت کلاس به دلیل تداخل برنامه‌ها تغییر کرده است. جزئیات در متن آمده است.',
    'استاد محترم درخواست مرخصی برای تاریخ اعلام‌شده ثبت کرده است.',
    'به اطلاع می‌رساند شعبه در تاریخ مشخص‌شده تعطیل خواهد بود.',
    'نتایج آزمون تئوری در پنل قابل مشاهده است. موفق باشید.',
    'برنامه کنسرت پایان ترم نهایی شد. لطفاً برای هماهنگی با پذیرش تماس بگیرید.',
    'ثبت‌نام ترم جدید از فردا آغاز می‌شود. ظرفیت محدود است.'
];

let allMessages = [];
(function buildSample() {
    const branches = window.getMessageBranches();
    for (let i = 1; i <= 48; i++) {
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const d = new Date();
        d.setDate(d.getDate() - Math.floor(Math.random() * 40));
        allMessages.push({
            id: i,
            title: sampleTitles[Math.floor(Math.random() * sampleTitles.length)],
            body: sampleBodies[Math.floor(Math.random() * sampleBodies.length)],
            sender: sampleSenders[Math.floor(Math.random() * sampleSenders.length)],
            receiver: sampleReceivers[Math.floor(Math.random() * sampleReceivers.length)],
            branchId: branch.id,
            branchName: branch.name,
            type: window.messageTypesList[Math.floor(Math.random() * window.messageTypesList.length)],
            priority: window.messagePrioritiesList[Math.floor(Math.random() * window.messagePrioritiesList.length)],
            status: window.messageStatusesList[Math.floor(Math.random() * window.messageStatusesList.length)],
            date: d.toLocaleDateString('fa-IR'),
            dateISO: d.toISOString().split('T')[0]
        });
    }
})();

let currentMessageBranch = 'all';
let messagesCurrentPage = 1;
const messagesPerPage = 10;
let filteredMessages = allMessages.slice();
let msgSortField = '';
let msgSortDirection = 'asc';

function sortMessageItems() {
    if (!msgSortField) return;
    filteredMessages.sort(function (a, b) {
        let av = a[msgSortField], bv = b[msgSortField];
        if (msgSortField === 'date') {
            av = a.dateISO || '';
            bv = b.dateISO || '';
        } else {
            av = String(av || '').toLowerCase();
            bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return msgSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return msgSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateMessageSortIcons = async function () {
    ['title', 'sender', 'branchName', 'receiver', 'type', 'date', 'status'].forEach(function (f) {
        const icon = document.getElementById('msgSortIcon-' + f);
        if (!icon) return;
        icon.textContent = msgSortField === f ? (msgSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortMessagesBy = async function (field) {
    if (msgSortField === field) msgSortDirection = msgSortDirection === 'asc' ? 'desc' : 'asc';
    else { msgSortField = field; msgSortDirection = 'asc'; }
    sortMessageItems();
    window.renderMessagesTable(filteredMessages);
    window.updateMessageSortIcons();
};

window.renderMessagesBranchTabs = async function () {
    const container = document.getElementById('messagesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.message-branch-tab:not([data-value="all"])').forEach(function (t) { t.remove(); });
    window.getMessageBranches().forEach(function (b) {
        const active = String(currentMessageBranch) === String(b.id);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'message-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50');
        btn.dataset.value = b.id;
        btn.textContent = b.name;
        btn.onclick = function () { window.filterMessagesByBranch(b.id); };
        container.appendChild(btn);
    });
    const allTab = container.querySelector('[data-value="all"]');
    if (allTab) {
        const isAll = currentMessageBranch === 'all';
        allTab.classList.toggle('bg-indigo-600', isAll);
        allTab.classList.toggle('text-white', isAll);
        allTab.classList.toggle('border-indigo-600', isAll);
        if (!isAll) {
            allTab.classList.add('border', 'border-gray-200');
            allTab.classList.remove('bg-indigo-600', 'text-white');
        }
    }
};

window.filterMessagesByBranch = async function (branchId) {
    currentMessageBranch = branchId;
    document.querySelectorAll('.message-branch-tab').forEach(function (tab) {
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
    window.filterMessages();
};

window.filterMessages = async function () {
    const search = (document.getElementById('messageSearch') && document.getElementById('messageSearch').value || '').trim().toLowerCase();
    const status = document.getElementById('filterMessageStatus') && document.getElementById('filterMessageStatus').value || '';
    const priority = document.getElementById('filterMessagePriority') && document.getElementById('filterMessagePriority').value || '';
    const type = document.getElementById('filterMessageType') && document.getElementById('filterMessageType').value || '';

    filteredMessages = allMessages.filter(function (m) {
        const matchBranch = window.matchesOrganizationFilter(m,currentMessageBranch);
        const matchSearch = !search ||
            (m.title || '').toLowerCase().includes(search) ||
            (m.sender || '').toLowerCase().includes(search) ||
            (m.receiver || '').toLowerCase().includes(search) ||
            (m.body || '').toLowerCase().includes(search);
        const matchStatus = !status || m.status === status;
        const matchPriority = !priority || m.priority === priority;
        const matchType = !type || m.type === type;
        return matchBranch && matchSearch && matchStatus && matchPriority && matchType;
    });

    messagesCurrentPage = 1;
    sortMessageItems();
    window.renderMessagesTable(filteredMessages);
};

window.renderMessagesTable = async function (list) {
    list = list || filteredMessages;
    const tbody = document.querySelector('#messagesTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / messagesPerPage) || 1;
    if (messagesCurrentPage > totalPages) messagesCurrentPage = totalPages;

    const start = (messagesCurrentPage - 1) * messagesPerPage;
    const end = start + messagesPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getMessageEmptyRowHTML ? window.getMessageEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition' + (item.status === 'خوانده‌نشده' ? ' bg-indigo-50/40' : '');
            tr.innerHTML = window.getMessageRowHTML ? window.getMessageRowHTML(item) : '';
            tbody.appendChild(tr);
        });
    }
    updateMessagesPagination(list.length, start, end, totalPages);
    window.updateMessageSortIcons();
};

function updateMessagesPagination(total, start, end, totalPages) {
    const info = document.getElementById('messagesPaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' پیام';
    }
    const pagination = document.getElementById('messagesPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeMessagesPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (messagesCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeMessagesPage(' + (messagesCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (messagesCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, messagesCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeMessagesPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === messagesCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeMessagesPage(' + (messagesCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (messagesCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeMessagesPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (messagesCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeMessagesPage = async function (page) {
    const totalPages = Math.ceil(filteredMessages.length / messagesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    messagesCurrentPage = page;
    window.renderMessagesTable(filteredMessages);
};

window.openAddMessageModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getMessageAddModalHTML
        ? window.getMessageAddModalHTML() : '';
};

window.saveMessage = async function () {
    const title = (document.getElementById('msgTitle') && document.getElementById('msgTitle').value || '').trim();
    const body = (document.getElementById('msgBody') && document.getElementById('msgBody').value || '').trim();
    if (!title) return alert('عنوان پیام الزامی است');
    if (!body) return alert('متن پیام الزامی است');

    const branchId = parseInt(document.getElementById('msgBranch') && document.getElementById('msgBranch').value, 10);
    const branch = window.getMessageBranches().find(function (b) { return b.id === branchId; });
    const now = new Date();

    allMessages.unshift({
        id: Date.now(),
        title: title,
        body: body,
        sender: 'مدیر سیستم',
        branchId: branchId,
        branchName: branch ? branch.name : 'نامشخص',
        receiver: (document.getElementById('msgReceiver') && document.getElementById('msgReceiver').value || '').trim() || 'همه',
        type: document.getElementById('msgType') && document.getElementById('msgType').value || 'اطلاعیه',
        priority: document.getElementById('msgPriority') && document.getElementById('msgPriority').value || 'عادی',
        date: now.toLocaleDateString('fa-IR'),
        dateISO: now.toISOString().split('T')[0],
        status: 'خوانده‌نشده'
    });

    window.filterMessages();
    closeModal();
    alert('✅ پیام ارسال شد');
};

window.viewMessage = async function (id) {
    const item = allMessages.find(function (x) { return x.id === id; });
    if (!item) return;
    // علامت‌گذاری به‌عنوان خوانده‌شده
    if (item.status === 'خوانده‌نشده') {
        item.status = 'خوانده‌شده';
        window.filterMessages();
    }
    document.getElementById('modalContainer').innerHTML = window.getMessageDetailsModalHTML
        ? window.getMessageDetailsModalHTML(item) : '';
};

window.markMessageUnread = async function (id) {
    const item = allMessages.find(function (x) { return x.id === id; });
    if (!item) return;
    item.status = 'خوانده‌نشده';
    window.filterMessages();
    closeModal();
};

window.deleteMessage = async function (id) {
    if (!(await AppDialog.confirmDelete(allMessages, id, 'پیام'))) return;
    allMessages = allMessages.filter(function (m) { return m.id !== id; });
    window.filterMessages();
};

setTimeout(function () {
    if (document.getElementById('messagesTable')) {
        window.renderMessagesBranchTabs();
        window.filterMessages();
    }
}, 200);
})();
