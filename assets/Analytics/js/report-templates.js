(function () {
    'use strict';
    const fieldClass = 'w-full border border-gray-300 rounded-2xl py-3.5 px-5';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function statusBadge(status) {
        return status === 'آماده'
            ? 'bg-green-100 text-green-700'
            : 'bg-yellow-100 text-yellow-700';
    }

    function typeBadge(type) {
        const map = {
            'مالی': 'bg-blue-100 text-blue-700',
            'آموزشی': 'bg-purple-100 text-purple-700',
            'حضور و غیاب': 'bg-teal-100 text-teal-700',
            'ثبت‌نام': 'bg-indigo-100 text-indigo-700',
            'نظرسنجی': 'bg-pink-100 text-pink-700',
            'عملکرد': 'bg-orange-100 text-orange-700'
        };
        return map[type] || 'bg-gray-100 text-gray-600';
    }

    window.getReportRowHTML = function (item) {
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.title)}</td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5"><span class="px-2.5 py-1 rounded-full text-xs ${typeBadge(item.type)}">${escapeHtml(item.type)}</span></td>
            <td class="py-4 px-5">${escapeHtml(item.periodLabel || '—')}</td>
            <td class="py-4 px-5">${escapeHtml(item.date)}</td>
            <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusBadge(item.status)}">${escapeHtml(item.status)}</span></td>
            <td class="py-4 px-5 text-left">
                <button onclick="viewReport(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
            </td>`;
    };

    window.getReportEmptyRowHTML = function () {
        return '<tr><td colspan="7" class="py-12 text-center text-gray-400">گزارشی یافت نشد</td></tr>';
    };

    window.getReportDetailsModalHTML = function (item) {
        const m = item.metrics || {};
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-start rounded-t-3xl gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="px-2.5 py-1 rounded-full text-xs ${statusBadge(item.status)}">${escapeHtml(item.status)}</span>
                            <span class="px-2.5 py-1 rounded-full text-xs ${typeBadge(item.type)}">${escapeHtml(item.type)}</span>
                            <span class="px-2.5 py-1 rounded-full text-xs bg-slate-100 text-slate-600">${escapeHtml(item.periodLabel || '—')}</span>
                        </div>
                        <h2 class="text-2xl font-bold leading-tight">${escapeHtml(item.title)}</h2>
                        <p class="text-sm text-gray-500 mt-1">${escapeHtml(item.date)} · ${escapeHtml(item.branchName)}</p>
                    </div>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 shrink-0">×</button>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع</span><span class="font-medium">${escapeHtml(item.type)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">بازه زمانی</span><span class="font-medium">${escapeHtml(item.periodLabel || '—')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ تولید</span><span class="font-medium">${escapeHtml(item.date)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">از تاریخ</span><span class="font-medium">${escapeHtml(item.periodFrom || '—')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تا تاریخ</span><span class="font-medium">${escapeHtml(item.periodTo || '—')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">منبع</span><span class="font-medium">سیستم (خودکار)</span></div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-3">خلاصه گزارش</h3>
                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 text-sm text-gray-700 leading-relaxed">${escapeHtml(item.summary || 'بدون خلاصه')}</div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-3">شاخص‌های نمونه</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-indigo-50 rounded-2xl p-4 text-center">
                                <p class="text-xs text-gray-500">کل موارد</p>
                                <p class="text-2xl font-bold text-indigo-700 mt-1">${m.total != null ? m.total : '—'}</p>
                            </div>
                            <div class="bg-green-50 rounded-2xl p-4 text-center">
                                <p class="text-xs text-gray-500">موفق / تکمیل</p>
                                <p class="text-2xl font-bold text-green-700 mt-1">${m.success != null ? m.success : '—'}</p>
                            </div>
                            <div class="bg-amber-50 rounded-2xl p-4 text-center">
                                <p class="text-xs text-gray-500">در انتظار</p>
                                <p class="text-2xl font-bold text-amber-700 mt-1">${m.pending != null ? m.pending : '—'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button onclick="closeModal()" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl text-sm hover:bg-indigo-700">بستن</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getReportPDFModalHTML = function (cols) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">تنظیمات خروجی PDF گزارش‌ها</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-5" style="max-height:calc(100vh - 10rem);overflow-y:auto;">
                    <input id="reportPdfTitle" type="text" value="گزارش‌های آموزشگاه" class="${fieldClass}">
                    <input id="reportPdfSubtitle" type="text" value="لیست گزارش‌های سیستمی" class="${fieldClass}">
                    <div class="grid grid-cols-2 gap-4">
                        <select id="reportPdfFormat" class="${fieldClass}"><option value="a4">A4</option><option value="letter">Letter</option></select>
                        <select id="reportPdfOrientation" class="${fieldClass}"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select>
                    </div>
                    <input id="reportPdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="${fieldClass}">
                    <div class="grid grid-cols-2 gap-2">${(cols || []).map(function (c) {
                        return '<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="reportPdfCol-' + c.field + '" checked> ' + c.label + '</label>';
                    }).join('')}</div>
                    <div class="grid grid-cols-3 gap-4">
                        <input id="reportPdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="reportPdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="reportPdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border rounded-2xl p-2">
                    </div>
                    <label class="flex items-center gap-3"><input id="reportPdfIncludeDate" type="checkbox" checked> <span class="text-sm">نمایش تاریخ استخراج</span></label>
                    <div class="flex gap-4">
                        <button onclick="generateReportsPDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button>
                        <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getReportPDFPageHTML = function (pageNumber, rows, isFirstPage, o) {
        return `<div style="width:100%;padding:24px;background:#fff;direction:rtl;">
            ${isFirstPage ? `<h1 style="margin:0 0 6px;font-size:28px;font-weight:700;">${escapeHtml(o.title)}</h1>
            <p style="margin:0 0 16px;color:#4b5563;font-size:14px;">${escapeHtml(o.subtitle)}</p>
            ${o.includeDate ? `<p style="margin:0 0 16px;color:#6b7280;font-size:12px;">تاریخ استخراج: ${escapeHtml(o.date)}</p>` : ''}` : ''}
            <table style="width:100%;border-collapse:collapse;"><thead style="background:${o.headerColor};"><tr>
                ${o.selectedColumns.map(function (c) {
                    return '<th style="padding:12px 14px;text-align:right;font-weight:600;">' + escapeHtml(c.label) + '</th>';
                }).join('')}
            </tr></thead><tbody>
                ${rows.map(function (item, index) {
                    return '<tr style="background:' + (index % 2 === 0 ? o.evenRowColor : o.oddRowColor) + ';">' +
                        o.selectedColumns.map(function (c) {
                            const v = c.field === 'index' ? (pageNumber - 1) * o.rowsPerPage + index + 1 : item[c.field];
                            return '<td style="padding:12px 14px;text-align:right;">' + escapeHtml(v) + '</td>';
                        }).join('') + '</tr>';
                }).join('')}
            </tbody></table>
            ${isFirstPage && o.footer ? `<p style="margin-top:16px;color:#6b7280;font-size:12px;">${escapeHtml(o.footer)}</p>` : ''}
            <div style="margin-top:16px;text-align:left;color:#6b7280;font-size:12px;">صفحه ${pageNumber} / ${o.totalPages}</div>
        </div>`;
    };
})();
