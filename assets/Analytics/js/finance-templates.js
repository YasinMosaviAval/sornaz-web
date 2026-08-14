(function () {
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function renderOptions(options, selectedValue) {
        return (options || []).map(function (option) {
            const value = option.value ?? option.id ?? option.name ?? option;
            const label = option.label ?? option.name ?? option;
            const selected = String(value) === String(selectedValue) ? 'selected' : '';
            return `<option value="${escapeHtml(value)}" ${selected}>${escapeHtml(label)}</option>`;
        }).join('');
    }
    function statusClass(status) {
        return {
            'پیش‌نویس': 'bg-gray-100 text-gray-600',
            'صادرشده': 'bg-blue-100 text-blue-700',
            'پرداخت جزئی': 'bg-yellow-100 text-yellow-700',
            'پرداخت‌شده': 'bg-green-100 text-green-700',
            'لغوشده': 'bg-red-100 text-red-700',
            'تأیید شده': 'bg-green-100 text-green-700',
            'در انتظار تأیید': 'bg-yellow-100 text-yellow-700',
            'رد شده': 'bg-red-100 text-red-700',
            'حذف‌شده': 'bg-gray-100 text-gray-600'
        }[status] || 'bg-gray-100 text-gray-600';
    }

    window.getFinanceRowHTML = function (item) {
        return `
            <td class="py-4 px-5 font-medium">#${escapeHtml(item.id)} — ${escapeHtml(item.termName)}</td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5">${escapeHtml(item.course || '—')}</td>
            <td class="py-4 px-5 font-medium">${Number(item.amount || 0).toLocaleString('fa-IR')}</td>
            <td class="py-4 px-5">${escapeHtml(item.date || '—')}</td>
            <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass(item.status)}">${escapeHtml(item.status)}</span></td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button onclick="viewTransaction(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    <button onclick="toggleFinanceInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                    <button onclick="toggleFinanceInlineInstallments(${item.id})" class="text-emerald-600 hover:underline text-sm">مشاهده اقساط</button>
                </div>
            </td>`;
    };

    window.getFinanceEmptyRowHTML = function () {
        return `<tr><td colspan="7" class="py-12 text-center text-gray-400">فاکتور ترمی یافت نشد</td></tr>`;
    };

    function financeInstallmentsPanel(item, isInline) {
        const installments = item.installments || [];
        const firstUnpaid = installments.find(function (installment) { return installment.statusCode !== 'paid'; });
        const rows = installments.map(function (installment) {
            const badge = installment.statusCode === 'paid'
                ? 'bg-green-100 text-green-700'
                : (installment.statusCode === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
            const isNextPayable = firstUnpaid && firstUnpaid.id === installment.id;
            const payButton = installment.statusCode === 'paid' ? '' : (isNextPayable
                ? `<button onclick="payFinanceInstallment(${item.id}, ${installment.id}, ${isInline ? 'true' : 'false'})" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">پرداخت</button>`
                : '<button type="button" disabled title="ابتدا قسط قبلی را پرداخت کنید" class="cursor-not-allowed rounded-xl bg-gray-200 px-4 py-2 text-sm text-gray-400">پرداخت</button>');
            return `<div class="grid grid-cols-2 md:grid-cols-5 gap-3 items-center border border-gray-200 rounded-2xl p-4">
                <div><span class="block text-xs text-gray-400 mb-1">شماره قسط</span><strong>${Number(installment.number).toLocaleString('fa-IR')}</strong></div>
                <div><span class="block text-xs text-gray-400 mb-1">مبلغ</span><strong>${Number(installment.amount || 0).toLocaleString('fa-IR')}</strong></div>
                <div><span class="block text-xs text-gray-400 mb-1">سررسید</span><strong>${escapeHtml(installment.dueDate || '—')}</strong></div>
                <div><span class="inline-flex px-3 py-1 rounded-full text-xs ${badge}">${escapeHtml(installment.status)}</span></div>
                <div class="text-left">${payButton}</div>
            </div>`;
        }).join('') || '<p class="text-center text-gray-400 py-8">قسطی برای این فاکتور ثبت نشده است.</p>';
        return `<div id="financeInstallmentsPanel-${item.id}"><div class="flex items-center justify-between mb-4"><div><h3 class="font-bold text-lg">اقساط فاکتور #${escapeHtml(item.id)}</h3><p class="text-sm text-gray-500 mt-1">${escapeHtml(item.termName)} — ${escapeHtml(item.course)}</p></div>${isInline ? `<button onclick="toggleFinanceInlineInstallments(${item.id})" class="text-gray-500 hover:text-gray-700">بستن</button>` : ''}</div><div class="max-h-[55vh] overflow-y-auto space-y-3 pr-1">${rows}</div></div>`;
    }

    window.getFinanceInlineInstallmentsRowHTML = function (item) {
        return `<td colspan="7" class="p-5 border-t"><div class="rounded-2xl border border-gray-200 bg-white p-5">${financeInstallmentsPanel(item, true)}</div></td>`;
    };

    window.getFinanceInstallmentsModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <div><h2 class="text-2xl font-bold">اقساط فاکتور #${escapeHtml(item.id)}</h2><p class="text-sm text-gray-500 mt-1">${escapeHtml(item.termName)} — ${escapeHtml(item.course)}</p></div>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8">${financeInstallmentsPanel(item, false)}<button onclick="closeModal()" class="w-full mt-6 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">بستن</button></div>
            </div>
        </div>`;
    };

    window.getFinanceInlineExpandRowHTML = function (item) {
        return `<td colspan="7" class="p-5 border-t">${window.getFinanceInlineEditRowHTML ? window.getFinanceInlineEditRowHTML(item) : ''}</td>`;
    };

    function formFields(item, prefix, isInvoiceEdit) {
        const id = function (n) { return prefix ? prefix + n : 'trans' + n; };
        const branches = (typeof allBranches !== 'undefined' ? allBranches : []).map(function (b) {
            return { value: b.id, label: b.name };
        });
        const types = isInvoiceEdit ? [{ value: 'فاکتور ترم', label: 'فاکتور ترم' }] : [{ value: 'درآمد', label: 'درآمد' }, { value: 'هزینه', label: 'هزینه' }];
        const statuses = [
            { value: 'draft', label: 'پیش‌نویس' },
            { value: 'issued', label: 'صادرشده' },
            { value: 'partial', label: 'پرداخت جزئی' },
            { value: 'paid', label: 'پرداخت‌شده' },
            { value: 'canceled', label: 'لغوشده' }
        ];
        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="${id('Branch')}" ${isInvoiceEdit ? 'disabled' : ''} class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                        ${renderOptions(branches, item.branchId)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع تراکنش *</label>
                    <select id="${id('Type')}" ${isInvoiceEdit ? 'disabled' : ''} class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                        ${renderOptions(types, item.type || 'درآمد')}
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-2">شرح تراکنش *</label>
                    <input id="${id('Title')}" type="text" value="${escapeHtml(item.title || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">مبلغ (تومان) *</label>
                    <input id="${id('Amount')}" type="number" min="0" value="${escapeHtml(item.amount ?? '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تاریخ</label>
                    <input id="${id('Date')}" type="date" value="${escapeHtml(item.dateIso || item.date || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="${id('Status')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(statuses, item.statusCode || 'draft')}
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-2">خلاصه</label>
                    <input id="${id('Summary')}" type="text" value="${escapeHtml(item.summary || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="${id('Description')}" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${escapeHtml(item.description || '')}</textarea>
                </div>
            </div>`;
    }

    window.getFinanceInlineEditRowHTML = function (item) {
        return `<div class="space-y-6">
            ${formFields(item, 'inlineTrans' + item.id, true)}
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <button onclick="saveInlineTransaction(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="toggleFinanceInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div>
        </div>`;
    };

    window.getFinanceAddModalHTML = function () {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ثبت تراکنش جدید</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields({}, '', false)}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveTransaction()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getFinanceEditModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش تراکنش</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields(item, 'editTrans', true)}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveEditedTransaction(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getFinanceDetailsModalHTML = function (item) {
        const typeClass = item.type === 'درآمد' ? 'text-green-600' : 'text-red-600';
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${escapeHtml(item.title)}</h2>
                        <p class="text-sm text-gray-500 mt-1">کد تراکنش: #${item.id}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="editTransaction(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm">ویرایش</button>
                        <button onclick="openFinanceInstallments(${item.id})" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm">مشاهده اقساط</button>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    ${item.summary ? `<p class="text-indigo-600 font-medium">${escapeHtml(item.summary)}</p>` : ''}
                    ${item.description ? `<p class="text-gray-600 leading-relaxed">${escapeHtml(item.description)}</p>` : ''}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع</span><span class="font-medium ${typeClass}">${escapeHtml(item.type)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">مبلغ</span><span class="font-medium">${Number(item.amount || 0).toLocaleString('fa-IR')} تومان</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ</span><span class="font-medium">${escapeHtml(item.date || '—')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getFinancePDFModalHTML = function (cols) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <h2 class="text-2xl font-bold">تنظیمات خروجی PDF امور مالی</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-6" style="max-height:calc(100vh - 10rem);overflow-y:auto;">
                    <input id="financePdfTitle" type="text" value="گزارش امور مالی آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="financePdfSubtitle" type="text" value="لیست تراکنش‌ها، درآمد و هزینه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-5">
                        <select id="financePdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="a4">A4</option><option value="letter">Letter</option></select>
                        <select id="financePdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select>
                    </div>
                    <input id="financePdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-2">
                        ${cols.map(function (c) {
                            return `<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="financePdfCol-${c.field}" checked> ${c.label}</label>`;
                        }).join('')}
                    </div>
                    <div class="grid grid-cols-3 gap-5">
                        <input id="financePdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="financePdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="financePdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border rounded-2xl p-2">
                    </div>
                    <label class="flex items-center gap-3"><input id="financePdfIncludeDate" type="checkbox" checked> <span class="text-sm">نمایش تاریخ استخراج</span></label>
                    <div class="flex gap-4 pt-4">
                        <button onclick="generateFinancePDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button>
                        <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getFinancePDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
        const o = options;
        return `<div style="width:100%;padding:24px;background:#fff;direction:rtl;">
            ${isFirstPage ? `<h1 style="margin:0 0 6px;font-size:28px;font-weight:700;">${escapeHtml(o.title)}</h1>
            <p style="margin:0 0 16px;color:#4b5563;font-size:14px;">${escapeHtml(o.subtitle)}</p>
            ${o.includeDate ? `<p style="margin:0 0 16px;color:#6b7280;font-size:12px;">تاریخ استخراج: ${escapeHtml(o.date)}</p>` : ''}` : ''}
            <table style="width:100%;border-collapse:collapse;">
                <thead style="background:${o.headerColor};"><tr>
                    ${o.selectedColumns.map(function (c) {
                        return `<th style="padding:12px 14px;text-align:right;font-weight:600;">${escapeHtml(c.label)}</th>`;
                    }).join('')}
                </tr></thead>
                <tbody>
                    ${rows.map(function (item, index) {
                        return `<tr style="background:${index % 2 === 0 ? o.evenRowColor : o.oddRowColor};">
                            ${o.selectedColumns.map(function (c) {
                                const v = c.field === 'index' ? (pageNumber - 1) * o.rowsPerPage + index + 1 : item[c.field];
                                return `<td style="padding:12px 14px;text-align:right;">${escapeHtml(v)}</td>`;
                            }).join('')}
                        </tr>`;
                    }).join('')}
                </tbody>
            </table>
            ${isFirstPage && o.footer ? `<p style="margin-top:16px;color:#6b7280;font-size:12px;">${escapeHtml(o.footer)}</p>` : ''}
            <div style="margin-top:16px;text-align:left;color:#6b7280;font-size:12px;">صفحه ${pageNumber} / ${o.totalPages}</div>
        </div>`;
    };
})();
