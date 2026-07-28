let allCertificates = [
    { id: 1, title: "مجوز تأسیس آموزشگاه موسیقی", organization: "وزارت فرهنگ و ارشاد اسلامی", summary: "مجوز رسمی", description: "مجوز فعالیت آموزشگاه موسیقی در سطح کشور.", issue_date: "۱۳۹۵/۰۳/۲۰", expire_date: "۱۴۰۵/۰۳/۲۰", certificate_url: "https://example.com/cert1.pdf", file_path: "/uploads/cert1.pdf", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "گواهی استاندارد آموزش", organization: "سازمان ملی استاندارد", summary: "گواهی کیفیت", description: "تأیید کیفیت فرآیندهای آموزشی.", issue_date: "۱۴۰۰/۰۸/۱۰", expire_date: "۱۴۰۴/۰۸/۱۰", certificate_url: "", file_path: "", branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, title: "مجوز فعالیت شعبه ونک", organization: "اداره فرهنگ و ارشاد تهران", summary: "مجوز شعبه", description: "مجوز راه‌اندازی و فعالیت شعبه ونک.", issue_date: "۱۳۹۸/۱۱/۰۵", expire_date: null, certificate_url: "https://example.com/vanak-cert.pdf", file_path: "/uploads/vanak.pdf", branchId: 2, branchName: "شعبه ونک" }
];

let currentCertBranch = 'all';

window.renderCertificatesBranchTabs = function() {
    const container = document.getElementById('certificatesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.cert-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'cert-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterCertificatesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterCertificatesByBranch = function(branchId) {
    currentCertBranch = branchId;
    document.querySelectorAll('.cert-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.cert-branch-tab');
    if (branchId === 'all') {
        if (tabs[0]) { tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600'); tabs[0].classList.remove('border-gray-200'); }
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    renderCertificatesTable();
};

window.renderCertificatesTable = function() {
    const tbody = document.querySelector('#certificatesTable tbody');
    if (!tbody) return;
    const list = currentCertBranch === 'all' ? allCertificates : allCertificates.filter(c => c.branchId == currentCertBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">تأییدیه‌ای یافت نشد</td></tr>`
        : list.map(c => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${c.title}</td>
                <td class="py-4 px-5">${c.organization}</td>
                <td class="py-4 px-5">${c.issue_date || '—'}</td>
                <td class="py-4 px-5">${c.expire_date || 'نامحدود'}</td>
                <td class="py-4 px-5">${c.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewCertificate(${c.id})" class="text-indigo-600 hover:underline text-sm ml-3">جزئیات</button>
                    <button onclick="editCertificate(${c.id})" class="text-indigo-600 hover:underline text-sm ml-3">ویرایش</button>
                    <button onclick="deleteCertificate(${c.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            </tr>`).join('');
};

window.openAddCertificateModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن تأییدیه</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان *</label>
                    <input id="certTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">سازمان *</label>
                    <input id="certOrg" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">خلاصه</label>
                    <input id="certSummary" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="certDesc" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ صدور</label>
                        <input id="certIssue" type="text" placeholder="۱۳۹۵/۰۳/۲۰" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ انقضا</label>
                        <input id="certExpire" type="text" placeholder="خالی = نامحدود" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">لینک گواهی (URL)</label>
                    <input id="certUrl" type="text" placeholder="https://..." class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">مسیر فایل</label>
                    <input id="certFile" type="text" placeholder="/uploads/cert.pdf" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="certBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <div class="flex gap-4">
                    <button onclick="saveCertificate()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveCertificate = function() {
    const title = document.getElementById('certTitle')?.value.trim();
    const org = document.getElementById('certOrg')?.value.trim();
    if (!title || !org) return alert('عنوان و سازمان الزامی است');
    const branchId = parseInt(document.getElementById('certBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allCertificates.unshift({
        id: Date.now(), title, organization: org,
        summary: document.getElementById('certSummary').value.trim(),
        description: document.getElementById('certDesc').value.trim(),
        issue_date: document.getElementById('certIssue').value.trim() || null,
        expire_date: document.getElementById('certExpire').value.trim() || null,
        certificate_url: document.getElementById('certUrl').value.trim(),
        file_path: document.getElementById('certFile').value.trim(),
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterCertificatesByBranch(currentCertBranch);
    closeModal();
    alert('✅ تأییدیه ثبت شد');
};

window.viewCertificate = function(id) {
    const c = allCertificates.find(x => x.id === id);
    if (!c) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${c.title}</h2>
                    <p class="text-sm text-gray-500">${c.organization}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editCertificate(${c.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${c.summary ? `<p class="text-indigo-600 font-medium">${c.summary}</p>` : ''}
                ${c.description ? `<p class="text-gray-600">${c.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ صدور</span><span>${c.issue_date || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ انقضا</span><span>${c.expire_date || 'نامحدود'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${c.branchName}</span></div>
                    ${c.certificate_url ? `<div class="pt-2"><a href="${c.certificate_url}" target="_blank" class="text-indigo-600 hover:underline">مشاهده گواهی (لینک)</a></div>` : ''}
                    ${c.file_path ? `<div class="text-xs text-gray-400">مسیر فایل: ${c.file_path}</div>` : ''}
                </div>
            </div>
        </div>
    </div>`;
};

window.editCertificate = function(id) {
    const c = allCertificates.find(x => x.id === id);
    if (!c) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === c.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش تأییدیه</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editCertTitle" type="text" value="${c.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="عنوان">
                <input id="editCertOrg" type="text" value="${c.organization}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="سازمان">
                <input id="editCertSummary" type="text" value="${c.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="خلاصه">
                <textarea id="editCertDesc" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${c.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <input id="editCertIssue" type="text" value="${c.issue_date || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="تاریخ صدور">
                    <input id="editCertExpire" type="text" value="${c.expire_date || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="تاریخ انقضا">
                </div>
                <input id="editCertUrl" type="text" value="${c.certificate_url || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="لینک گواهی">
                <input id="editCertFile" type="text" value="${c.file_path || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="مسیر فایل">
                <select id="editCertBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedCertificate(${c.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedCertificate = function(id) {
    const title = document.getElementById('editCertTitle')?.value.trim();
    const org = document.getElementById('editCertOrg')?.value.trim();
    if (!title || !org) return alert('عنوان و سازمان الزامی است');
    const index = allCertificates.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editCertBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allCertificates[index] = {
        ...allCertificates[index], title, organization: org,
        summary: document.getElementById('editCertSummary').value.trim(),
        description: document.getElementById('editCertDesc').value.trim(),
        issue_date: document.getElementById('editCertIssue').value.trim() || null,
        expire_date: document.getElementById('editCertExpire').value.trim() || null,
        certificate_url: document.getElementById('editCertUrl').value.trim(),
        file_path: document.getElementById('editCertFile').value.trim(),
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterCertificatesByBranch(currentCertBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteCertificate = function(id) {
    if (confirm('حذف این تأییدیه؟')) {
        allCertificates = allCertificates.filter(c => c.id !== id);
        filterCertificatesByBranch(currentCertBranch);
    }
};

setTimeout(() => {
    if (document.getElementById('certificatesTable')) {
        renderCertificatesBranchTabs();
        filterCertificatesByBranch('all');
    }
}, 200);