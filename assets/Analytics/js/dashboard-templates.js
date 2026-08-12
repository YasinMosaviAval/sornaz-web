(function () {
    'use strict';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function emptyState(text) {
        return '<p class="text-center text-gray-400 text-sm py-6">' + escapeHtml(text || 'موردی نیست') + '</p>';
    }

    window.getDashboardStatsHTML = function (s) {
        s = s || {};
        const cards = [
            { label: 'هنرجویان فعال', value: s.activeStudents, color: 'indigo', icon: 'fa-users' },
            { label: 'کلاس‌های امروز', value: s.todayClasses, color: 'amber', icon: 'fa-calendar' },
            { label: 'درآمد ماهانه', value: s.monthlyIncome, color: 'emerald', icon: 'fa-money-bill-wave' },
            { label: 'نرخ حضور', value: s.attendanceRate, color: 'rose', icon: 'fa-chart-pie' },
            { label: 'پرداخت‌های معوق', value: s.pendingPayments, color: 'orange', icon: 'fa-clock' },
            { label: 'پیام‌های جدید', value: s.newMessages, color: 'violet', icon: 'fa-envelope' },
            { label: 'ثبت‌نام هفته', value: s.newStudentsWeek, color: 'teal', icon: 'fa-user-plus' },
            { label: 'امتیازهای اهداشده', value: s.pointsAwarded, color: 'sky', icon: 'fa-star' }
        ];
        return cards.map(function (c) {
            return `<div class="bg-white rounded-3xl p-5 shadow hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm">${escapeHtml(c.label)}</p>
                        <p class="text-3xl font-bold text-${c.color}-600 mt-2">${escapeHtml(c.value != null ? c.value : '—')}</p>
                    </div>
                    <i class="fas ${c.icon} text-4xl text-${c.color}-200"></i>
                </div>
            </div>`;
        }).join('');
    };

    window.getDashboardTodayClassesHTML = function (list) {
        if (!list || !list.length) return emptyState('کلاسی برای امروز نیست');
        return list.map(function (c) {
            return `<div class="flex justify-between items-center p-3 bg-gray-50 rounded-2xl gap-3">
                <div class="min-w-0">
                    <p class="font-medium truncate">${escapeHtml(c.student)}</p>
                    <p class="text-sm text-gray-500 truncate">${escapeHtml(c.instrument)} — ${escapeHtml(c.teacher)}</p>
                </div>
                <div class="text-left shrink-0">
                    <p class="font-mono text-lg">${escapeHtml(c.time)}</p>
                    <p class="text-xs text-gray-400">${escapeHtml(c.branch)}</p>
                </div>
            </div>`;
        }).join('');
    };

    window.getDashboardUrgentHTML = function (list) {
        if (!list || !list.length) return emptyState('مورد فوری نیست');
        const colorMap = {
            rose: 'bg-rose-50 border-rose-200 text-rose-700',
            amber: 'bg-amber-50 border-amber-200 text-amber-700',
            indigo: 'bg-indigo-50 border-indigo-200 text-indigo-700',
            red: 'bg-red-50 border-red-200 text-red-700'
        };
        return list.map(function (u) {
            const section = u.section || 'notifications';
            return `<div class="p-3 rounded-2xl border ${colorMap[u.color] || 'bg-gray-50'} flex justify-between items-start gap-3">
                <div>
                    <span class="text-xs font-bold">${escapeHtml(u.type)}</span>
                    <p class="text-sm mt-1">${escapeHtml(u.text)}</p>
                </div>
                <button type="button" onclick="goToSectionFromDashboard('${escapeHtml(section)}')" class="text-xs shrink-0 underline opacity-80 hover:opacity-100">مشاهده</button>
            </div>`;
        }).join('');
    };

    window.getDashboardPaymentsHTML = function (list) {
        if (!list || !list.length) return emptyState('پرداختی ثبت نشده');
        return list.map(function (p) {
            return `<div class="flex justify-between items-center p-3 bg-gray-50 rounded-2xl gap-3">
                <div class="min-w-0">
                    <p class="font-medium truncate">${escapeHtml(p.name)}</p>
                    <p class="text-xs text-gray-400">${escapeHtml(p.branch)} — ${escapeHtml(p.time)}</p>
                </div>
                <p class="font-bold text-emerald-600 shrink-0">${escapeHtml(p.amount)} تومان</p>
            </div>`;
        }).join('');
    };

    window.getDashboardDepositsHTML = function (list) {
        if (!list || !list.length) return emptyState('واریزی ثبت نشده');
        return list.map(function (d) {
            return `<div class="flex justify-between items-center p-3 bg-teal-50/60 rounded-2xl border border-teal-100 gap-3">
                <div class="min-w-0">
                    <p class="font-medium truncate">${escapeHtml(d.title)}</p>
                    <p class="text-xs text-gray-400">${escapeHtml(d.bank)} · ${escapeHtml(d.branch)} — ${escapeHtml(d.time)}</p>
                </div>
                <p class="font-bold text-teal-700 shrink-0">${escapeHtml(d.amount)} تومان</p>
            </div>`;
        }).join('');
    };

    window.getDashboardAbsencesHTML = function (list) {
        if (!list || !list.length) return emptyState('غیبتی ثبت نشده');
        return list.map(function (a) {
            return `<div class="flex justify-between items-center p-3 bg-rose-50 rounded-2xl border border-rose-100 gap-3">
                <div class="min-w-0">
                    <p class="font-medium truncate">${escapeHtml(a.student)}</p>
                    <p class="text-sm text-gray-500 truncate">${escapeHtml(a.instrument)} — ${escapeHtml(a.teacher)}</p>
                </div>
                <div class="text-left shrink-0">
                    <p class="font-mono">${escapeHtml(a.time)}</p>
                    <p class="text-xs text-gray-400">${escapeHtml(a.branch)}</p>
                </div>
            </div>`;
        }).join('');
    };

    window.getDashboardMessagesHTML = function (list) {
        if (!list || !list.length) return emptyState('پیام خوانده‌نشده‌ای نیست');
        return list.map(function (m) {
            return `<div class="dashboard-unread-message flex justify-between items-start p-3 bg-indigo-50/50 rounded-2xl border border-indigo-100 gap-3">
                <div class="min-w-0">
                    <p class="dashboard-unread-sender font-medium truncate">${escapeHtml(m.from)}</p>
                    <p class="dashboard-unread-preview text-sm text-gray-600 truncate">${escapeHtml(m.preview)}</p>
                    <p class="text-xs text-gray-400 mt-1">${escapeHtml(m.branch)} · ${escapeHtml(m.time)}</p>
                </div>
                <button type="button" onclick="goToSectionFromDashboard('messages')" class="dashboard-unread-action text-xs text-indigo-600 shrink-0 hover:underline">مشاهده</button>
            </div>`;
        }).join('');
    };

    window.getDashboardRegistrationsHTML = function (list) {
        if (!list || !list.length) return emptyState('ثبت‌نام جدیدی نیست');
        return list.map(function (r) {
            return `<div class="flex justify-between items-center p-3 bg-violet-50/50 rounded-2xl border border-violet-100 gap-3">
                <div class="min-w-0">
                    <p class="font-medium truncate">${escapeHtml(r.name)}</p>
                    <p class="text-sm text-gray-500">${escapeHtml(r.instrument)} · ${escapeHtml(r.branch)}</p>
                </div>
                <span class="text-xs text-gray-400 shrink-0">${escapeHtml(r.date)}</span>
            </div>`;
        }).join('');
    };

    window.getDashboardHolidaysHTML = function (list) {
        if (!list || !list.length) return emptyState('موردی نزدیک نیست');
        return list.map(function (h) {
            return `<div class="flex justify-between items-center p-3 bg-orange-50/50 rounded-2xl border border-orange-100 gap-3">
                <div class="min-w-0">
                    <p class="font-medium truncate">${escapeHtml(h.title)}</p>
                    <p class="text-sm text-gray-500">${escapeHtml(h.branch)}</p>
                </div>
                <span class="text-xs text-gray-500 shrink-0 font-mono">${escapeHtml(h.date)}</span>
            </div>`;
        }).join('');
    };

    window.getDashboardQuickLinksHTML = function () {
        const links = [
            { section: 'students', label: 'هنرجویان', icon: 'fa-user-graduate', color: 'indigo' },
            { section: 'teachers', label: 'پرسنل', icon: 'fa-chalkboard-teacher', color: 'teal' },
            { section: 'branches', label: 'شعبه‌ها', icon: 'fa-building', color: 'slate' },
            { section: 'messages', label: 'پیام‌ها', icon: 'fa-comments', color: 'violet' },
            { section: 'reports', label: 'گزارش‌ها', icon: 'fa-chart-bar', color: 'emerald' },
            { section: 'points', label: 'امتیازات', icon: 'fa-star', color: 'amber' }
        ];
        return links.map(function (l) {
            return `<button type="button" onclick="goToSectionFromDashboard('${l.section}')"
                class="flex flex-col items-center gap-2 p-4 rounded-2xl border border-gray-100 hover:bg-gray-50 transition text-center">
                <span class="w-10 h-10 rounded-xl bg-${l.color}-100 text-${l.color}-600 flex items-center justify-center">
                    <i class="fas ${l.icon}"></i>
                </span>
                <span class="text-xs font-medium text-gray-700">${escapeHtml(l.label)}</span>
            </button>`;
        }).join('');
    };
})();
