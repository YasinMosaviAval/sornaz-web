let allAcademyRequests = [
    {
        id: 1, email: "new.academy@email.com", username: "avayeno",
        academy_name: "آوای نو", short_desc: "آموزش گیتار و پیانو",
        bio: "تیم جوان با تمرکز روی هنرجویان نوجوان", status: "pending"
    },
    {
        id: 2, email: "music.home@email.com", username: "musichome",
        academy_name: "خانه موسیقی کرج", short_desc: "موسیقی ایرانی",
        bio: "", status: "approved"
    }
];

const reqStatusMap = {
    pending: { text: 'در انتظار', cls: 'bg-yellow-100 text-yellow-700' },
    approved: { text: 'تأیید شده', cls: 'bg-green-100 text-green-700' },
    rejected: { text: 'رد شده', cls: 'bg-red-100 text-red-700' }
};

window.submitAcademyRequest = async function () {
    const email = document.getElementById('reqEmail')?.value.trim();
    const username = document.getElementById('reqUsername')?.value.trim();
    const pass = document.getElementById('reqPassword')?.value;
    const pass2 = document.getElementById('reqPassword2')?.value;
    const name = document.getElementById('reqAcademyName')?.value.trim();

    if (!email || !username || !pass || !name) return alert('فیلدهای ستاره‌دار الزامی است');
    if (pass !== pass2) return alert('رمز عبور و تکرار آن یکسان نیست');

    allAcademyRequests.unshift({
        id: Date.now(),
        email, username,
        academy_name: name,
        short_desc: document.getElementById('reqShortDesc')?.value.trim() || '',
        bio: document.getElementById('reqBio')?.value.trim() || '',
        status: 'pending'
    });

    ['reqEmail', 'reqUsername', 'reqPassword', 'reqPassword2', 'reqAcademyName', 'reqShortDesc', 'reqBio']
        .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });

    renderAcademyRequestsTable();
    alert('✅ ثبت آموزشگاه انجام شد و پس از بررسی نتیجه اعلام می‌شود.');
};

window.renderAcademyRequestsTable = async function () {
    const tbody = document.querySelector('#academyRequestsTable tbody');
    if (!tbody) return;
    tbody.innerHTML = allAcademyRequests.length === 0
        ? `<tr><td colspan="4" class="py-10 text-center text-gray-400">درخواستی نیست</td></tr>`
        : allAcademyRequests.map(r => {
            const st = reqStatusMap[r.status] || { text: r.status, cls: 'bg-gray-100' };
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5">
                    <div class="font-medium">${r.academy_name}</div>
                    <div class="text-xs text-gray-400">${r.short_desc || ''}</div>
                </td>
                <td class="py-4 px-5">
                    <div class="text-sm">${r.email}</div>
                    <div class="text-xs text-gray-400">@${r.username}</div>
                </td>
                <td class="py-4 px-5"><span class="px-2.5 py-1 rounded-full text-xs ${st.cls}">${st.text}</span></td>
                <td class="py-4 px-5 text-left whitespace-nowrap">
                    ${r.status === 'pending' ? `
                        <button onclick="approveAcademyRequest(${r.id})" class="text-green-600 text-sm ml-2">تأیید</button>
                        <button onclick="rejectAcademyRequest(${r.id})" class="text-red-500 text-sm">رد</button>
                    ` : `
                        <button onclick="viewAcademyRequest(${r.id})" class="text-indigo-600 text-sm">جزئیات</button>
                    `}
                </td>
            </tr>`;
        }).join('');
};

window.approveAcademyRequest = async function (id) {
    const r = allAcademyRequests.find(x => x.id === id);
    if (!r) return;
    r.status = 'approved';
    if (typeof allAcademiesList !== 'undefined') {
        allAcademiesList.push({
            id: Date.now(),
            name: r.academy_name,
            city: '—',
            rating: null,
            classes: 0,
            students: 0,
            income: null,
            isMine: false,
            role: null,
            summary: r.short_desc,
            description: r.bio
        });
        if (typeof renderAcademies === 'function') renderAcademies();
    }
    renderAcademyRequestsTable();
    alert('✅ آموزشگاه تأیید و به لیست اضافه شد');
};

window.rejectAcademyRequest = async function (id) {
    const r = allAcademyRequests.find(x => x.id === id);
    if (!r) return;
    if (!(await AppDialog.confirm(`رد درخواست "${r.name || r.title || 'درخواست #' + id}"؟`))) return;
    r.status = 'rejected';
    renderAcademyRequestsTable();
};

window.viewAcademyRequest = async function (id) {
    const r = allAcademyRequests.find(x => x.id === id);
    if (!r) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl p-8" onclick="event.stopPropagation()">
            <div class="flex justify-between mb-4">
                <h2 class="text-xl font-bold">${r.academy_name}</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="text-sm space-y-2">
                <p><span class="text-gray-500">ایمیل:</span> ${r.email}</p>
                <p><span class="text-gray-500">کاربر:</span> @${r.username}</p>
                <p><span class="text-gray-500">خلاصه:</span> ${r.short_desc || '—'}</p>
                <p class="text-gray-600 pt-2">${r.bio || ''}</p>
            </div>
        </div>
    </div>`;
};

(function () {
    setTimeout(() => {
        if (document.getElementById('academyRequestsTable')) renderAcademyRequestsTable();
    }, 200);
})();
