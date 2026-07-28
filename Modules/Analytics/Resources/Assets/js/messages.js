// ==================== داده نمونه پیام‌ها ====================
let allMessages = [
    { id: 1, title: "یادآوری جلسه اولیا", sender: "مدیر شعبه", branchId: 1, branchName: "شعبه مرکزی", receiver: "همه والدین", date: "۱۴۰۴/۰۵/۱۲", status: "خوانده‌شده" },
    { id: 2, title: "تأخیر در پرداخت شهریه", sender: "سیستم", branchId: 1, branchName: "شعبه مرکزی", receiver: "سارا احمدی", date: "۱۴۰۴/۰۵/۱۰", status: "خوانده‌نشده" },
    { id: 3, title: "تغییر ساعت کلاس گیتار", sender: "استاد رضایی", branchId: 2, branchName: "شعبه ونک", receiver: "هنرجویان گیتار", date: "۱۴۰۴/۰۵/۰۹", status: "خوانده‌شده" },
    { id: 4, title: "درخواست مرخصی استاد", sender: "استاد موسوی", branchId: 3, branchName: "شعبه سعادت‌آباد", receiver: "مدیریت", date: "۱۴۰۴/۰۵/۰۸", status: "خوانده‌نشده" },
    { id: 5, title: "اطلاعیه تعطیلی موقت", sender: "مدیریت", branchId: 4, branchName: "شعبه کرج", receiver: "همه", date: "۱۴۰۴/۰۵/۰۷", status: "خوانده‌شده" },
    { id: 6, title: "نتیجه آزمون تئوری", sender: "استاد بهرامی", branchId: 1, branchName: "شعبه مرکزی", receiver: "هنرجویان سطح متوسط", date: "۱۴۰۴/۰۵/۰۵", status: "خوانده‌شده" }
];

let currentMessageBranch = 'all';

window.renderMessagesBranchTabs = function() {
    const container = document.getElementById('messagesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.message-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'message-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterMessagesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterMessagesByBranch = function(branchId) {
    currentMessageBranch = branchId;

    document.querySelectorAll('.message-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.message-branch-tab');
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

    renderMessagesTable();
};

window.renderMessagesTable = function() {
    const tbody = document.querySelector('#messagesTable tbody');
    if (!tbody) return;

    const list = currentMessageBranch === 'all' 
        ? allMessages 
        : allMessages.filter(m => m.branchId == currentMessageBranch);

    tbody.innerHTML = list.length === 0 
        ? `<tr><td colspan="7" class="py-12 text-center text-gray-400">پیامی یافت نشد</td></tr>`
        : list.map(m => {
            const statusClass = m.status === 'خوانده‌شده' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${m.title}</td>
                <td class="py-4 px-5">${m.sender}</td>
                <td class="py-4 px-5">${m.branchName}</td>
                <td class="py-4 px-5">${m.receiver}</td>
                <td class="py-4 px-5">${m.date}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${m.status}</span></td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewMessage(${m.id})" class="text-indigo-600 hover:underline text-sm ml-3">مشاهده</button>
                    <button onclick="deleteMessage(${m.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddMessageModal = function() {
    if (!document.getElementById('modalContainer')) {
        alert('modalContainer پیدا نشد!');
        return;
    }

    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : [])
        .map(b => `<option value="${b.id}">${b.name}</option>`).join('');

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ارسال پیام جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان پیام *</label>
                    <input id="msgTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="msgBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">گیرنده</label>
                    <input id="msgReceiver" type="text" placeholder="نام هنرجو / همه / والدین" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">متن پیام</label>
                    <textarea id="msgBody" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveMessage()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ارسال</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveMessage = function() {
    const title = document.getElementById('msgTitle')?.value.trim();
    if (!title) return alert('عنوان پیام الزامی است');

    const branchId = parseInt(document.getElementById('msgBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allMessages.unshift({
        id: Date.now(),
        title,
        sender: "مدیر سیستم",
        branchId,
        branchName: branch ? branch.name : 'نامشخص',
        receiver: document.getElementById('msgReceiver').value || 'همه',
        date: new Date().toLocaleDateString('fa-IR'),
        status: "خوانده‌نشده"
    });

    filterMessagesByBranch(currentMessageBranch);
    closeModal();
    alert('✅ پیام ارسال شد');
};

window.viewMessage = function(id) {
    const m = allMessages.find(x => x.id === id);
    if (m) alert(`عنوان: ${m.title}\nفرستنده: ${m.sender}\nگیرنده: ${m.receiver}\nتاریخ: ${m.date}`);
};

window.deleteMessage = function(id) {
    if (confirm('حذف این پیام؟')) {
        allMessages = allMessages.filter(m => m.id !== id);
        filterMessagesByBranch(currentMessageBranch);
    }
};

// Init
setTimeout(() => {
    if (document.getElementById('messagesTable')) {
        renderMessagesBranchTabs();
        filterMessagesByBranch('all');
    }
}, 200);