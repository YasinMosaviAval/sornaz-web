let allEnrollments = [
    { id: 1, name: "مینا حسینی", phone: "0912...", academy: "آوای هنر", course: "پیانو مبتدی", level: "beginner", status: "pending", note: "" },
    { id: 2, name: "امیر رضایی", phone: "0935...", academy: "نوای شرق", course: "تئوری موسیقی", level: "intermediate", status: "contacted", note: "عصرها آزادم" }
];

const enrollStatusLabels = {
    pending: { text: 'در انتظار', cls: 'bg-yellow-100 text-yellow-700' },
    contacted: { text: 'تماس گرفته شد', cls: 'bg-blue-100 text-blue-700' },
    confirmed: { text: 'تأیید شده', cls: 'bg-green-100 text-green-700' },
    rejected: { text: 'رد شده', cls: 'bg-red-100 text-red-700' }
};

window.renderEnrollPage = function() {
    const sel = document.getElementById('enrollAcademy');
    if (sel && typeof allAcademiesList !== 'undefined') {
        sel.innerHTML = '<option value="">انتخاب آموزشگاه</option>' +
            allAcademiesList.map(a => `<option value="${a.id}">${a.name} (${a.city})</option>`).join('');
    }
    renderEnrollmentsTable();
};

window.submitAcademyEnroll = function() {
    const name = document.getElementById('enrollName')?.value.trim();
    const phone = document.getElementById('enrollPhone')?.value.trim();
    const academyId = document.getElementById('enrollAcademy')?.value;
    const course = document.getElementById('enrollCourse')?.value;
    if (!name || !phone || !academyId || !course) return alert('فیلدهای ستاره‌دار الزامی است');

    const academy = (typeof allAcademiesList !== 'undefined')
        ? allAcademiesList.find(a => a.id == academyId) : null;
    const courseText = document.getElementById('enrollCourse').selectedOptions[0]?.text || course;

    allEnrollments.unshift({
        id: Date.now(), name, phone,
        email: document.getElementById('enrollEmail')?.value.trim(),
        academy: academy ? academy.name : '—',
        course: courseText,
        level: document.getElementById('enrollLevel')?.value,
        note: document.getElementById('enrollNote')?.value.trim(),
        status: 'pending'
    });

    ['enrollName', 'enrollPhone', 'enrollEmail', 'enrollNote'].forEach(id => {
        const el = document.getElementById(id); if (el) el.value = '';
    });
    renderEnrollmentsTable();
    alert('✅ درخواست ثبت‌نام ارسال شد. به‌زودی تماس گرفته می‌شود.');
};

window.renderEnrollmentsTable = function() {
    const tbody = document.querySelector('#enrollmentsTable tbody');
    if (!tbody) return;
    tbody.innerHTML = allEnrollments.length === 0
        ? `<tr><td colspan="4" class="py-10 text-center text-gray-400">درخواستی نیست</td></tr>`
        : allEnrollments.map(e => {
            const st = enrollStatusLabels[e.status] || { text: e.status, cls: 'bg-gray-100' };
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5">
                    <div class="font-medium">${e.name}</div>
                    <div class="text-xs text-gray-400">${e.phone || ''}</div>
                </td>
                <td class="py-4 px-5">
                    <div>${e.academy}</div>
                    <div class="text-xs text-gray-500">${e.course}</div>
                </td>
                <td class="py-4 px-5"><span class="px-2.5 py-1 rounded-full text-xs ${st.cls}">${st.text}</span></td>
                <td class="py-4 px-5 text-left">
                    <button onclick="updateEnrollStatus(${e.id})" class="text-indigo-600 text-sm">وضعیت</button>
                </td>
            </tr>`;
        }).join('');
};

window.updateEnrollStatus = function(id) {
    const e = allEnrollments.find(x => x.id === id);
    if (!e) return;
    const next = { pending: 'contacted', contacted: 'confirmed', confirmed: 'rejected', rejected: 'pending' };
    e.status = next[e.status] || 'pending';
    renderEnrollmentsTable();
};

(function () {
    setTimeout(() => {
        if (document.getElementById('enrollAcademy')) renderEnrollPage();
    }, 200);
})();