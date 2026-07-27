// ==================== داده نمونه اعلان‌ها ====================
let allNotifications = [
    { id: 1, title: "تعطیلی شعبه در روز جمعه", branchId: 1, branchName: "شعبه مرکزی", priority: "بالا", date: "۱۴۰۴/۰۵/۱۲", status: "منتشر شده" },
    { id: 2, title: "شروع ثبت‌نام ترم جدید", branchId: 1, branchName: "شعبه مرکزی", priority: "متوسط", date: "۱۴۰۴/۰۵/۱۰", status: "منتشر شده" },
    { id: 3, title: "تغییر ساعت کلاس‌های عصر", branchId: 2, branchName: "شعبه ونک", priority: "بالا", date: "۱۴۰۴/۰۵/۰۹", status: "منتشر شده" },
    { id: 4, title: "برگزاری مستر کلاس رایگان", branchId: 3, branchName: "شعبه سعادت‌آباد", priority: "کم", date: "۱۴۰۴/۰۵/۰۸", status: "پیش‌نویس" },
    { id: 5, title: "اطلاعیه پرداخت شهریه", branchId: 4, branchName: "شعبه کرج", priority: "بالا", date: "۱۴۰۴/۰۵/۰۷", status: "منتشر شده" },
    { id: 6, title: "جشن پایان ترم تابستان", branchId: 1, branchName: "شعبه مرکزی", priority: "متوسط", date: "۱۴۰۴/۰۵/۰۵", status: "منتشر شده" }
];

let currentNotificationBranch = 'all';

window.renderNotificationsBranchTabs = function() {
    const container = document.getElementById('notificationsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.notification-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'notification-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterNotificationsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterNotificationsByBranch = function(branchId) {
    currentNotificationBranch = branchId;

    document.querySelectorAll('.notification-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.notification-branch-tab');
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

    renderNotificationsTable();
};

window.renderNotificationsTable = function() {
    const tbody = document.querySelector('#notificationsTable tbody');
    if (!tbody) return;

    const list = currentNotificationBranch === 'all' 
        ? allNotifications 
        : allNotifications.filter(n => n.branchId == currentNotificationBranch);

    tbody.innerHTML = list.length === 0 
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">اعلانی یافت نشد</td></tr>`
        : list.map(n => {
            const priorityClass = {
                'بالا': 'bg-red-100 text-red-700',
                'متوسط': 'bg-yellow-100 text-yellow-700',
                'کم': 'bg-blue-100 text-blue-700'
            }[n.priority] || 'bg-gray-100';

            const statusClass = n.status === 'منتشر شده' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600';

            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${n.title}</td>
                <td class="py-4 px-5">${n.branchName}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${priorityClass}">${n.priority}</span></td>
                <td class="py-4 px-5">${n.date}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${n.status}</span></td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewNotification(${n.id})" class="text-indigo-600 hover:underline text-sm ml-3">مشاهده</button>
                    <button onclick="deleteNotification(${n.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddNotificationModal = function() {
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
                <h2 class="text-2xl font-bold">ثبت اعلان جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان اعلان *</label>
                    <input id="notifTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="notifBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">اولویت</label>
                    <select id="notifPriority" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="بالا">بالا</option>
                        <option value="متوسط">متوسط</option>
                        <option value="کم">کم</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">متن اعلان</label>
                    <textarea id="notifBody" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveNotification()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">انتشار</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveNotification = function() {
    const title = document.getElementById('notifTitle')?.value.trim();
    if (!title) return alert('عنوان اعلان الزامی است');

    const branchId = parseInt(document.getElementById('notifBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allNotifications.unshift({
        id: Date.now(),
        title,
        branchId,
        branchName: branch ? branch.name : 'نامشخص',
        priority: document.getElementById('notifPriority').value,
        date: new Date().toLocaleDateString('fa-IR'),
        status: "منتشر شده"
    });

    filterNotificationsByBranch(currentNotificationBranch);
    closeModal();
    alert('✅ اعلان ثبت شد');
};

window.viewNotification = function(id) {
    const n = allNotifications.find(x => x.id === id);
    if (n) alert(`عنوان: ${n.title}\nاولویت: ${n.priority}\nتاریخ: ${n.date}`);
};

window.deleteNotification = function(id) {
    if (confirm('حذف این اعلان؟')) {
        allNotifications = allNotifications.filter(n => n.id !== id);
        filterNotificationsByBranch(currentNotificationBranch);
    }
};

// Init
setTimeout(() => {
    if (document.getElementById('notificationsTable')) {
        renderNotificationsBranchTabs();
        filterNotificationsByBranch('all');
    }
}, 200);