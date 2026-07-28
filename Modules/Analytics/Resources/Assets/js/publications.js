let allPublications = [
    { id: 1, title: "روش تدریس پیانو برای کودکان", summary: "کتاب آموزشی", description: "راهنمای عملی تدریس پیانو به کودکان ۷ تا ۱۲ سال.", publisher: "نشر موسیقی ایران", url: "https://example.com/piano-kids", published_date: "۱۴۰۱/۰۶/۱۵", content: "محتوای کامل کتاب در نسخه چاپی...", is_peer_reviewed: 1, user_id: 1, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "تحلیل هارمونی در موسیقی ایرانی", summary: "مقاله علمی", description: "بررسی ساختار هارمونیک در ردیف موسیقی ایرانی.", publisher: "فصلنامه هنر", url: "", published_date: "۱۴۰۰/۱۱/۲۰", content: "", is_peer_reviewed: 1, user_id: 2, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, title: "آلبوم آموزشی گیتار", summary: "اثر صوتی", description: "مجموعه قطعات تمرینی برای سطح متوسط.", publisher: "استودیو ونک", url: "https://soundcloud.com/example", published_date: "۱۴۰۲/۰۳/۱۰", content: "", is_peer_reviewed: 0, user_id: 3, branchId: 2, branchName: "شعبه ونک" }
];
let currentPubBranch = 'all';

window.renderPublicationsBranchTabs = function() {
    const container = document.getElementById('publicationsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.pub-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'pub-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterPublicationsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterPublicationsByBranch = function(branchId) {
    currentPubBranch = branchId;
    document.querySelectorAll('.pub-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.pub-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    renderPublicationsTable();
};

window.renderPublicationsTable = function() {
    const tbody = document.querySelector('#publicationsTable tbody');
    if (!tbody) return;
    const list = currentPubBranch === 'all' ? allPublications : allPublications.filter(p => p.branchId == currentPubBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">تألیفی یافت نشد</td></tr>`
        : list.map(p => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${p.title}</td>
                <td class="py-4 px-5">${p.publisher || '—'}</td>
                <td class="py-4 px-5">${p.published_date || '—'}</td>
                <td class="py-4 px-5">${p.is_peer_reviewed ? '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">بله</span>' : 'خیر'}</td>
                <td class="py-4 px-5">${p.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewPublication(${p.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editPublication(${p.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deletePublication(${p.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`).join('');
};

window.openAddPublicationModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن تألیف</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="pubTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="pubSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="pubDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <input id="pubPublisher" type="text" placeholder="ناشر" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="pubUrl" type="text" placeholder="لینک (URL)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="pubDate" type="text" placeholder="تاریخ انتشار" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="pubContent" rows="3" placeholder="محتوا / چکیده" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="pubPeer"> داوری‌شده (Peer Reviewed)</label>
                <select id="pubBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="savePublication()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.savePublication = function() {
    const title = document.getElementById('pubTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const branchId = parseInt(document.getElementById('pubBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allPublications.unshift({
        id: Date.now(), title,
        summary: document.getElementById('pubSummary').value.trim(),
        description: document.getElementById('pubDesc').value.trim(),
        publisher: document.getElementById('pubPublisher').value.trim(),
        url: document.getElementById('pubUrl').value.trim(),
        published_date: document.getElementById('pubDate').value.trim() || null,
        content: document.getElementById('pubContent').value.trim(),
        is_peer_reviewed: document.getElementById('pubPeer').checked ? 1 : 0,
        user_id: 1, branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterPublicationsByBranch(currentPubBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewPublication = function(id) {
    const p = allPublications.find(x => x.id === id);
    if (!p) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${p.title}</h2>
                    <p class="text-sm text-gray-500">${p.publisher || ''}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editPublication(${p.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${p.summary ? `<p class="text-indigo-600 font-medium">${p.summary}</p>` : ''}
                ${p.description ? `<p class="text-gray-600">${p.description}</p>` : ''}
                ${p.content ? `<div class="text-sm text-gray-500 border-t pt-3">${p.content}</div>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ انتشار</span><span>${p.published_date || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">داوری‌شده</span><span>${p.is_peer_reviewed ? 'بله' : 'خیر'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${p.branchName}</span></div>
                    ${p.url ? `<div class="pt-2"><a href="${p.url}" target="_blank" class="text-indigo-600 hover:underline">مشاهده لینک</a></div>` : ''}
                </div>
            </div>
        </div>
    </div>`;
};

window.editPublication = function(id) {
    const p = allPublications.find(x => x.id === id);
    if (!p) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === p.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش تألیف</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editPubTitle" type="text" value="${p.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editPubSummary" type="text" value="${p.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editPubDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${p.description || ''}</textarea>
                <input id="editPubPublisher" type="text" value="${p.publisher || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editPubUrl" type="text" value="${p.url || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editPubDate" type="text" value="${p.published_date || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editPubContent" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${p.content || ''}</textarea>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="editPubPeer" ${p.is_peer_reviewed ? 'checked' : ''}> داوری‌شده</label>
                <select id="editPubBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedPublication(${p.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedPublication = function(id) {
    const title = document.getElementById('editPubTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allPublications.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editPubBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allPublications[index] = {
        ...allPublications[index], title,
        summary: document.getElementById('editPubSummary').value.trim(),
        description: document.getElementById('editPubDesc').value.trim(),
        publisher: document.getElementById('editPubPublisher').value.trim(),
        url: document.getElementById('editPubUrl').value.trim(),
        published_date: document.getElementById('editPubDate').value.trim() || null,
        content: document.getElementById('editPubContent').value.trim(),
        is_peer_reviewed: document.getElementById('editPubPeer').checked ? 1 : 0,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterPublicationsByBranch(currentPubBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deletePublication = function(id) {
    if (confirm('حذف این تألیف؟')) {
        allPublications = allPublications.filter(p => p.id !== id);
        filterPublicationsByBranch(currentPubBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#publicationsTable tbody')) {
            renderPublicationsBranchTabs();
            filterPublicationsByBranch('all');
        }
    }, 200);
})();