let contactSettings = {
    pageTitle: "ارتباط با ما - ارسال پیام جدید",
    hint: "بعد از مشاهده پیام، در اولین فرصت پاسخ شما را می‌دهیم.",
    receiveEmail: "info@academy.com"
};

let allContactMessages = [
    { id: 1, name: "مریم احمدی", email: "maryam@email.com", subject: "ثبت‌نام کلاس پیانو", message: "سلام، برای ثبت‌نام کلاس پیانو بزرگسالان راهنمایی می‌خواهم.", date: "۱۴۰۳/۰۹/۲۰", status: "unread" },
    { id: 2, name: "رضا کریمی", email: "reza@email.com", subject: "همکاری به‌عنوان مدرس", message: "رزومه و نمونه تدریس پیوست است.", date: "۱۴۰۳/۰۹/۱۸", status: "read" },
    { id: 3, name: "سارا نوری", email: "sara@email.com", subject: "سوال درباره شهریه", message: "شهریه ترم پاییز گیتار چقدر است؟", date: "۱۴۰۳/۰۹/۱۵", status: "replied" }
];

window.renderContactUs = function() {
    document.getElementById('contactPageTitle').value = contactSettings.pageTitle;
    document.getElementById('contactPageHint').value = contactSettings.hint;
    document.getElementById('contactReceiveEmail').value = contactSettings.receiveEmail;
    renderContactMessages();
};

window.saveContactSettings = function() {
    contactSettings.pageTitle = document.getElementById('contactPageTitle').value;
    contactSettings.hint = document.getElementById('contactPageHint').value;
    contactSettings.receiveEmail = document.getElementById('contactReceiveEmail').value;
    alert('✅ تنظیمات ذخیره شد');
};

window.renderContactMessages = function() {
    const tbody = document.querySelector('#contactMessagesTable tbody');
    const countEl = document.getElementById('contactMsgCount');
    if (countEl) countEl.textContent = allContactMessages.length + ' پیام';
    if (!tbody) return;

    const statusMap = {
        unread: '<span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">خوانده‌نشده</span>',
        read: '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">خوانده‌شده</span>',
        replied: '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">پاسخ‌داده‌شده</span>'
    };

    tbody.innerHTML = allContactMessages.length === 0
        ? `<tr><td colspan="5" class="py-10 text-center text-gray-400">پیامی نیست</td></tr>`
        : allContactMessages.map(m => `
            <tr class="hover:bg-gray-50 ${m.status === 'unread' ? 'bg-indigo-50/40' : ''}">
                <td class="py-4 px-5">
                    <div class="font-medium">${m.name}</div>
                    <div class="text-xs text-gray-400">${m.email}</div>
                </td>
                <td class="py-4 px-5">${m.subject}</td>
                <td class="py-4 px-5 text-sm text-gray-500">${m.date}</td>
                <td class="py-4 px-5">${statusMap[m.status] || m.status}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewContactMessage(${m.id})" class="text-indigo-600 text-sm">مشاهده</button>
                </td>
            </tr>`).join('');
};

window.viewContactMessage = function(id) {
    const m = allContactMessages.find(x => x.id === id);
    if (!m) return;
    if (m.status === 'unread') m.status = 'read';
    renderContactMessages();

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold">${m.subject}</h2>
                    <p class="text-sm text-gray-500 mt-1">${m.name} · ${m.email}</p>
                </div>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-4">
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">${m.message}</p>
                <p class="text-xs text-gray-400">${m.date}</p>
                <div class="flex gap-3 pt-2">
                    <button onclick="markContactReplied(${m.id})" class="flex-1 bg-indigo-600 text-white py-3 rounded-2xl text-sm">علامت به‌عنوان پاسخ‌داده‌شده</button>
                    <button onclick="deleteContactMessage(${m.id})" class="border border-red-200 text-red-500 px-5 py-3 rounded-2xl text-sm">حذف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.markContactReplied = function(id) {
    const m = allContactMessages.find(x => x.id === id);
    if (m) m.status = 'replied';
    renderContactMessages();
    closeModal();
};

window.deleteContactMessage = function(id) {
    if (!confirm('حذف این پیام؟')) return;
    allContactMessages = allContactMessages.filter(m => m.id !== id);
    renderContactMessages();
    closeModal();
};

(function() {
    setTimeout(() => {
        if (document.getElementById('contactMessagesTable')) renderContactUs();
    }, 200);
})();