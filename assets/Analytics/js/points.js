(function () {
'use strict';
// ==================== سیستم امتیازدهی ====================

window.pointTypeLabels = { general: 'عمومی', specialized: 'تخصصی', custom: 'اختصاصی' };
window.pointCategoryLabels = {
    attendance: 'حضور',
    academic: 'آموزشی',
    event: 'رویداد',
    social: 'اجتماعی',
    financial: 'مالی',
    profile: 'پروفایل',
    achievement: 'دستاورد'
};
window.pointStatusesList = ['فعال', 'غیرفعال'];

window.getPointBranches = async function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' },
        { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' },
        { id: 4, name: 'شعبه کرج' }
    ];
};

/** قوانین پیش‌فرض امتیاز — پوشش اکثر سناریوهای آموزشگاه */
const defaultPointRules = [
    // پروفایل
    { title: 'تکمیل پروفایل', summary: 'امتیاز خوش‌آمدگویی', description: 'بابت تکمیل اطلاعات پایه پروفایل کاربر.', type: 'general', category: 'profile', points: 50, action: 'complete_profile', reference_type: 'user' },
    { title: 'آپلود عکس پروفایل', summary: 'هویت بصری', description: 'بابت بارگذاری تصویر پروفایل معتبر.', type: 'general', category: 'profile', points: 20, action: 'upload_avatar', reference_type: 'user' },
    { title: 'تأیید شماره موبایل', summary: 'احراز هویت', description: 'بابت تأیید شماره تماس از طریق کد یکبارمصرف.', type: 'general', category: 'profile', points: 30, action: 'verify_phone', reference_type: 'user' },
    { title: 'تکمیل اطلاعات والد', summary: 'هنرجوی زیر ۱۸', description: 'بابت ثبت کامل اطلاعات سرپرست قانونی.', type: 'general', category: 'profile', points: 25, action: 'complete_parent_info', reference_type: 'user' },

    // حضور
    { title: 'حضور در جلسه کلاس', summary: 'حضور عادی', description: 'امتیاز پایه بابت حضور در هر جلسه کلاس.', type: 'general', category: 'attendance', points: 10, action: 'class_attendance', reference_type: 'session' },
    { title: 'حضور به‌موقع', summary: 'بدون تأخیر', description: 'حضور قبل از شروع کلاس (بدون تأخیر).', type: 'general', category: 'attendance', points: 5, action: 'on_time_attendance', reference_type: 'session' },
    { title: 'حضور کامل ماهانه', summary: 'بدون غیبت در ماه', description: 'حضور در تمام جلسات یک ماه تقویمی.', type: 'specialized', category: 'attendance', points: 80, action: 'perfect_month_attendance', reference_type: 'user' },
    { title: 'حضور کامل ترم', summary: 'بدون غیبت در ترم', description: 'حضور در تمام جلسات یک ترم آموزشی.', type: 'specialized', category: 'attendance', points: 200, action: 'perfect_term_attendance', reference_type: 'user' },

    // آموزشی
    { title: 'تحویل تکلیف', summary: 'تکلیف هفتگی', description: 'بابت تحویل به‌موقع تکلیف یا تمرین خانگی.', type: 'general', category: 'academic', points: 15, action: 'submit_homework', reference_type: 'assignment' },
    { title: 'قبولی آزمون تئوری', summary: 'آزمون کتبی', description: 'کسب نمره قبولی در آزمون تئوری موسیقی.', type: 'specialized', category: 'academic', points: 100, action: 'pass_theory_exam', reference_type: 'exam' },
    { title: 'قبولی آزمون عملی', summary: 'آزمون اجرایی', description: 'کسب نمره قبولی در آزمون عملی ساز.', type: 'specialized', category: 'academic', points: 150, action: 'pass_practical_exam', reference_type: 'exam' },
    { title: 'ارتقای سطح', summary: 'پیشرفت سطح', description: 'ارتقا از یک سطح آموزشی به سطح بالاتر.', type: 'specialized', category: 'academic', points: 250, action: 'level_up', reference_type: 'user' },
    { title: 'تکمیل پکیج جلسات', summary: 'پایان پکیج', description: 'اتمام موفق یک پکیج کامل جلسات.', type: 'general', category: 'academic', points: 60, action: 'complete_package', reference_type: 'package' },
    { title: 'ثبت‌نام زودهنگام ترم', summary: 'ثبت‌نام پیش از موعد', description: 'ثبت‌نام ترم جدید قبل از مهلت رسمی.', type: 'general', category: 'academic', points: 40, action: 'early_registration', reference_type: 'term' },

    // رویداد
    { title: 'شرکت در کنسرت', summary: 'حضور در اجرا', description: 'حضور به‌عنوان مخاطب یا اجراکننده در کنسرت آموزشگاه.', type: 'specialized', category: 'event', points: 100, action: 'attend_concert', reference_type: 'event' },
    { title: 'اجرا در کنسرت', summary: 'اجرای روی صحنه', description: 'اجرای قطعه در کنسرت پایان ترم یا رویداد رسمی.', type: 'specialized', category: 'event', points: 200, action: 'perform_concert', reference_type: 'event' },
    { title: 'شرکت در مسترکلاس', summary: 'کارگاه تخصصی', description: 'حضور در مسترکلاس یا ورکشاپ تخصصی.', type: 'specialized', category: 'event', points: 120, action: 'attend_masterclass', reference_type: 'event' },
    { title: 'شرکت در مسابقه داخلی', summary: 'رقابت داخلی', description: 'شرکت در مسابقه یا جشنواره داخلی آموزشگاه.', type: 'specialized', category: 'event', points: 80, action: 'join_internal_contest', reference_type: 'event' },
    { title: 'رتبه در مسابقه', summary: 'کسب مقام', description: 'کسب رتبه اول تا سوم در مسابقه داخلی یا خارجی.', type: 'specialized', category: 'achievement', points: 300, action: 'contest_rank', reference_type: 'event' },

    // اجتماعی
    { title: 'معرفی هنرجوی جدید', summary: 'امتیاز ارجاع', description: 'معرفی فردی که پس از ثبت‌نام حداقل یک پکیج خریداری کند.', type: 'general', category: 'social', points: 200, action: 'referral', reference_type: 'user' },
    { title: 'ثبت بازخورد کلاس', summary: 'نظرسنجی', description: 'ثبت بازخورد یا امتیازدهی به جلسه کلاس.', type: 'general', category: 'social', points: 10, action: 'class_feedback', reference_type: 'session' },
    { title: 'اشتراک در شبکه‌های اجتماعی', summary: 'بازدید محتوا', description: 'اشتراک‌گذاری پست یا رویداد رسمی آموزشگاه.', type: 'general', category: 'social', points: 15, action: 'social_share', reference_type: 'post' },
    { title: 'نظر مثبت عمومی', summary: 'نظر در سایت', description: 'ثبت نظر مثبت تأییدشده در صفحه عمومی آموزشگاه.', type: 'general', category: 'social', points: 50, action: 'public_review', reference_type: 'review' },

    // مالی
    { title: 'پرداخت به‌موقع شهریه', summary: 'بدون تأخیر', description: 'پرداخت شهریه قبل یا در مهلت تعیین‌شده.', type: 'general', category: 'financial', points: 40, action: 'on_time_payment', reference_type: 'invoice' },
    { title: 'خرید پکیج سالانه', summary: 'تعهد بلندمدت', description: 'خرید پکیج یک‌ساله یا چندترم.', type: 'general', category: 'financial', points: 150, action: 'annual_package', reference_type: 'package' },
    { title: 'تمدید خودکار', summary: 'تمدید بدون وقفه', description: 'تمدید پکیج قبل از اتمام جلسات باقی‌مانده.', type: 'general', category: 'financial', points: 35, action: 'auto_renew', reference_type: 'package' },

    // دستاورد
    { title: 'انتشار مقاله / قطعه', summary: 'تألیف', description: 'انتشار مقاله، نت یا قطعه تأییدشده توسط آموزشگاه.', type: 'specialized', category: 'achievement', points: 300, action: 'publish_work', reference_type: 'publication' },
    { title: 'همکاری داوطلبانه', summary: 'داوطلبی', description: 'کمک در رویدادها، پذیرش یا هماهنگی بدون دستمزد.', type: 'specialized', category: 'achievement', points: 90, action: 'volunteer', reference_type: 'event' },
    { title: 'سالگرد عضویت', summary: 'وفاداری', description: 'امتیاز سالگرد یک‌ساله عضویت فعال در آموزشگاه.', type: 'general', category: 'achievement', points: 100, action: 'membership_anniversary', reference_type: 'user' },
    { title: 'تولد هنرجو', summary: 'هدیه تولد', description: 'امتیاز تبریک تولد ثبت‌شده در پروفایل.', type: 'general', category: 'achievement', points: 25, action: 'birthday_bonus', reference_type: 'user' },
    { title: 'ارزیابی بالای استاد', summary: 'امتیاز استاد', description: 'دریافت نمره ارزیابی بالای ۸۰ از استاد در پایان ترم.', type: 'specialized', category: 'achievement', points: 120, action: 'high_teacher_rating', reference_type: 'evaluation' },

    // اختصاصی آموزشگاه
    { title: 'جایزه ویژه آموزشگاه', summary: 'امتیاز داخلی', description: 'امتیاز اختصاصی تعریف‌شده توسط مدیریت آموزشگاه.', type: 'custom', category: 'achievement', points: 75, action: 'academy_special_award', reference_type: 'user' },
    { title: 'پویش داخلی فصلی', summary: 'کمپین اختصاصی', description: 'شرکت در پویش یا کمپین فصلی اختصاصی همین آموزشگاه.', type: 'custom', category: 'event', points: 50, action: 'academy_campaign', reference_type: 'event' }
];

let allPoints = [];
(function buildSample() {
    const branches = window.getPointBranches();
    let id = 1;
    // یک‌بار برای «همه شعب» (شعبه مرکزی به‌عنوان پایه سراسری) + تنوع شعب
    defaultPointRules.forEach(function (rule, idx) {
        const branch = branches[idx % branches.length];
        allPoints.push(Object.assign({}, rule, {
            id: id++,
            branchId: branch.id,
            branchName: branch.name,
            reference_id: null,
            user_id: null,
            status: Math.random() > 0.12 ? 'فعال' : 'غیرفعال'
        }));
    });
    // تکرار برخی قوانین در شعب دیگر برای نمایش فیلتر شعبه
    defaultPointRules.slice(0, 12).forEach(function (rule, idx) {
        const branch = branches[(idx + 1) % branches.length];
        allPoints.push(Object.assign({}, rule, {
            id: id++,
            branchId: branch.id,
            branchName: branch.name,
            reference_id: null,
            user_id: null,
            status: 'فعال'
        }));
    });
})();

let currentPointBranch = 'all';
let pointsCurrentPage = 1;
const pointsPerPage = 10;
let filteredPoints = allPoints.slice();
let pointSortField = '';
let pointSortDirection = 'asc';
let editingPointRowId = null;

function sortPointItems() {
    if (!pointSortField) return;
    filteredPoints.sort(function (a, b) {
        let av = a[pointSortField], bv = b[pointSortField];
        if (pointSortField === 'points') {
            av = Number(av); bv = Number(bv);
        } else if (pointSortField === 'type') {
            av = window.pointTypeLabels[av] || av;
            bv = window.pointTypeLabels[bv] || bv;
            av = String(av).toLowerCase(); bv = String(bv).toLowerCase();
        } else if (pointSortField === 'category') {
            av = window.pointCategoryLabels[av] || av;
            bv = window.pointCategoryLabels[bv] || bv;
            av = String(av).toLowerCase(); bv = String(bv).toLowerCase();
        } else {
            av = String(av || '').toLowerCase();
            bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return pointSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return pointSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updatePointSortIcons = async function () {
    ['title', 'type', 'category', 'points', 'action', 'branchName', 'status'].forEach(function (f) {
        const icon = document.getElementById('pointSortIcon-' + f);
        if (!icon) return;
        icon.textContent = pointSortField === f ? (pointSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortPointsBy = async function (field) {
    if (pointSortField === field) pointSortDirection = pointSortDirection === 'asc' ? 'desc' : 'asc';
    else { pointSortField = field; pointSortDirection = 'asc'; }
    sortPointItems();
    window.renderPointsTable(filteredPoints);
    window.updatePointSortIcons();
};

window.renderPointsBranchTabs = async function () {
    const container = document.getElementById('pointsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.point-branch-tab:not([data-value="all"])').forEach(function (t) { t.remove(); });
    window.getPointBranches().forEach(function (b) {
        const active = String(currentPointBranch) === String(b.id);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'point-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50');
        btn.dataset.value = b.id;
        btn.textContent = b.name;
        btn.onclick = function () { window.filterPointsByBranch(b.id); };
        container.appendChild(btn);
    });
    const allTab = container.querySelector('[data-value="all"]');
    if (allTab) {
        const isAll = currentPointBranch === 'all';
        allTab.classList.toggle('bg-indigo-600', isAll);
        allTab.classList.toggle('text-white', isAll);
        allTab.classList.toggle('border-indigo-600', isAll);
        if (!isAll) {
            allTab.classList.add('border', 'border-gray-200');
            allTab.classList.remove('bg-indigo-600', 'text-white');
        }
    }
};

window.filterPointsByBranch = async function (branchId) {
    currentPointBranch = branchId;
    document.querySelectorAll('.point-branch-tab').forEach(function (tab) {
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
    window.filterPoints();
};

window.filterPoints = async function () {
    const search = (document.getElementById('pointSearch') && document.getElementById('pointSearch').value || '').trim().toLowerCase();
    const type = document.getElementById('filterPointType') && document.getElementById('filterPointType').value || '';
    const category = document.getElementById('filterPointCategory') && document.getElementById('filterPointCategory').value || '';
    const status = document.getElementById('filterPointStatus') && document.getElementById('filterPointStatus').value || '';

    filteredPoints = allPoints.filter(function (p) {
        const matchBranch = window.matchesOrganizationFilter(p,currentPointBranch);
        const matchSearch = !search ||
            (p.title || '').toLowerCase().includes(search) ||
            (p.action || '').toLowerCase().includes(search) ||
            (p.summary || '').toLowerCase().includes(search) ||
            (p.description || '').toLowerCase().includes(search);
        const matchType = !type || p.type === type;
        const matchCategory = !category || p.category === category;
        const matchStatus = !status || p.status === status;
        return matchBranch && matchSearch && matchType && matchCategory && matchStatus;
    });

    pointsCurrentPage = 1;
    sortPointItems();
    window.renderPointsTable(filteredPoints);
};

window.renderPointsTable = async function (list) {
    list = list || filteredPoints;
    const tbody = document.querySelector('#pointsTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / pointsPerPage) || 1;
    if (pointsCurrentPage > totalPages) pointsCurrentPage = totalPages;

    const start = (pointsCurrentPage - 1) * pointsPerPage;
    const end = start + pointsPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getPointEmptyRowHTML ? window.getPointEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getPointRowHTML ? window.getPointRowHTML(item) : '';
            tbody.appendChild(tr);
            if (editingPointRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50 point-inline-expand';
                expand.innerHTML = window.getPointInlineExpandRowHTML ? window.getPointInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updatePointsPagination(list.length, start, end, totalPages);
    window.updatePointSortIcons();
};

window.togglePointInlineEdit = async function (id) {
    editingPointRowId = editingPointRowId === id ? null : id;
    window.renderPointsTable(filteredPoints);
};

window.saveInlinePoint = async function (id) {
    const data = readPointForm('inlinePoint' + id);
    if (!data.title) return alert('عنوان الزامی است');
    if (!data.action) return alert('کد عملیات (action) الزامی است');
    const index = allPoints.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allPoints[index] = Object.assign({}, allPoints[index], data);
    editingPointRowId = null;
    window.filterPoints();
    alert('✅ تغییرات ذخیره شد');
};

function updatePointsPagination(total, start, end, totalPages) {
    const info = document.getElementById('pointsPaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' قانون';
    }
    const pagination = document.getElementById('pointsPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changePointsPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (pointsCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changePointsPage(' + (pointsCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (pointsCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, pointsCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changePointsPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === pointsCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changePointsPage(' + (pointsCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (pointsCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changePointsPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (pointsCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changePointsPage = async function (page) {
    const totalPages = Math.ceil(filteredPoints.length / pointsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    pointsCurrentPage = page;
    window.renderPointsTable(filteredPoints);
};

function readPointForm(prefix) {
    const g = function (id) { return document.getElementById(prefix + id); };
    const branchId = parseInt(g('Branch') && g('Branch').value, 10);
    const branch = window.getPointBranches().find(function (b) { return b.id === branchId; });
    return {
        title: (g('Title') && g('Title').value || '').trim(),
        summary: (g('Summary') && g('Summary').value || '').trim(),
        description: (g('Desc') && g('Desc').value || '').trim(),
        type: g('Type') && g('Type').value || 'general',
        category: g('Category') && g('Category').value || 'profile',
        points: parseInt(g('Value') && g('Value').value, 10) || 0,
        action: (g('Action') && g('Action').value || '').trim(),
        reference_type: (g('RefType') && g('RefType').value || '').trim(),
        reference_id: parseInt(g('RefId') && g('RefId').value, 10) || null,
        status: g('Status') && g('Status').value || 'فعال',
        branchId: branchId,
        branchName: branch ? branch.name : 'نامشخص'
    };
}

window.openAddPointModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getPointAddModalHTML
        ? window.getPointAddModalHTML() : '';
};

window.savePoint = async function () {
    const data = readPointForm('point');
    if (!data.title) return alert('عنوان الزامی است');
    if (!data.action) return alert('کد عملیات (action) الزامی است');
    allPoints.unshift(Object.assign({}, data, { id: Date.now(), user_id: null }));
    window.filterPoints();
    closeModal();
    alert('✅ قانون امتیاز ثبت شد');
};

window.viewPoint = async function (id) {
    const item = allPoints.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getPointDetailsModalHTML
        ? window.getPointDetailsModalHTML(item) : '';
};

window.editPoint = async function (id) {
    const item = allPoints.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getPointEditModalHTML
        ? window.getPointEditModalHTML(item) : '';
};

window.saveEditedPoint = async function (id) {
    const data = readPointForm('editPoint');
    if (!data.title) return alert('عنوان الزامی است');
    if (!data.action) return alert('کد عملیات (action) الزامی است');
    const index = allPoints.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allPoints[index] = Object.assign({}, allPoints[index], data);
    window.filterPoints();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.togglePointStatus = async function (id) {
    const item = allPoints.find(function (x) { return x.id === id; });
    if (!item) return;
    item.status = item.status === 'فعال' ? 'غیرفعال' : 'فعال';
    window.filterPoints();
    closeModal();
};

window.deletePoint = async function (id) {
    if (!(await AppDialog.confirmDelete(allPoints, id, 'قانون امتیاز'))) return;
    allPoints = allPoints.filter(function (p) { return p.id !== id; });
    if (editingPointRowId === id) editingPointRowId = null;
    window.filterPoints();
};

setTimeout(function () {
    if (document.getElementById('pointsTable')) {
        window.renderPointsBranchTabs();
        window.filterPoints();
    }
}, 200);
})();
