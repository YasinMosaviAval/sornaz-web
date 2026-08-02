(function () {
    'use strict';
    const fieldClass = 'w-full border border-gray-300 rounded-2xl py-3.5 px-5';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function renderOptions(options, selectedValue) {
        return (options || []).map(function (option) {
            const value = option.value ?? option.id ?? option;
            const label = option.label ?? option.name ?? option.title ?? option;
            const selected = String(value) === String(selectedValue) ? 'selected' : '';
            return '<option value="' + escapeHtml(value) + '" ' + selected + '>' + escapeHtml(label) + '</option>';
        }).join('');
    }

    function statusBadge(status) {
        return {
            'فعال': 'bg-green-100 text-green-700',
            'غیرفعال': 'bg-gray-100 text-gray-600',
            'معلق': 'bg-yellow-100 text-yellow-700'
        }[status] || 'bg-gray-100 text-gray-600';
    }

    function permissionsCheckboxes(item, prefix) {
        const selected = {};
        (item.permissions || []).forEach(function (p) { selected[p.name] = true; });
        const catalog = window.userPermissionsCatalog || [];
        const byGroup = {};
        catalog.forEach(function (p) {
            if (!byGroup[p.group]) byGroup[p.group] = [];
            byGroup[p.group].push(p);
        });
        return Object.keys(byGroup).map(function (group) {
            return '<div class="mb-3"><div class="text-xs font-semibold text-gray-500 mb-2">' + escapeHtml(group) + '</div>' +
                '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">' +
                byGroup[group].map(function (p) {
                    const checked = selected[p.name] ? 'checked' : '';
                    return '<label class="inline-flex items-center gap-2 text-sm border border-gray-200 rounded-xl px-3 py-2 hover:bg-gray-50">' +
                        '<input type="checkbox" id="' + prefix + 'Perm_' + p.name + '" ' + checked + ' class="text-indigo-600 rounded">' +
                        escapeHtml(p.title) + '</label>';
                }).join('') + '</div></div>';
        }).join('');
    }

    function formFields(item, prefix) {
        item = item || {};
        const id = function (n) { return prefix + n; };
        const branches = (typeof window.getUserBranches === 'function' ? window.getUserBranches() : []).map(function (b) {
            return { value: b.id, label: b.name };
        });
        const roles = (window.userRolesCatalog || []).map(function (r) {
            return { value: r.name, label: r.title };
        });
        const types = window.userTypesList || [];
        const statuses = (window.userStatusesList || []).map(function (s) { return { value: s, label: s }; });

        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="${id('Branch')}" class="${fieldClass}">${renderOptions(branches, item.branchId)}</select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                    <input id="${id('Name')}" type="text" value="${escapeHtml(item.name || '')}" class="${fieldClass}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                    <input id="${id('Phone')}" type="tel" value="${escapeHtml(item.phone || '')}" class="${fieldClass}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">ایمیل</label>
                    <input id="${id('Email')}" type="email" value="${escapeHtml(item.email || '')}" class="${fieldClass}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع کاربر</label>
                    <select id="${id('Type')}" class="${fieldClass}">${renderOptions(types, item.userType || 'student')}</select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نقش *</label>
                    <select id="${id('Role')}" class="${fieldClass}">${renderOptions(roles, item.roleName || 'user')}</select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="${id('Status')}" class="${fieldClass}">${renderOptions(statuses, item.status || 'فعال')}</select>
                </div>
            </div>
            <div class="mt-6">
                <h3 class="font-semibold text-indigo-700 mb-3">دسترسی‌ها</h3>
                <div class="border border-gray-200 rounded-2xl p-4 max-h-64 overflow-y-auto bg-gray-50/50">
                    ${permissionsCheckboxes(item, prefix)}
                </div>
            </div>`;
    }

    window.getUserRowHTML = function (item) {
        const perms = item.permissions || [];
        const shown = perms.slice(0, 2).map(function (p) {
            return '<span class="px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600">' + escapeHtml(p.title) + '</span>';
        }).join(' ');
        const more = perms.length > 2 ? '<span class="text-xs text-gray-400">+' + (perms.length - 2) + '</span>' : '';
        return `
            <td class="py-4 px-4 font-medium">${escapeHtml(item.name)}</td>
            <td class="py-4 px-4">${escapeHtml(item.phone)}</td>
            <td class="py-4 px-4">${escapeHtml(item.userTypeLabel)}</td>
            <td class="py-4 px-4">
                <span class="inline-flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full" style="background:${escapeHtml(item.roleColor || '#6B7280')}"></span>
                    ${escapeHtml(item.roleTitle)}
                </span>
            </td>
            <td class="py-4 px-4">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-4"><div class="flex flex-wrap items-center gap-1">${shown || '<span class="text-gray-400">—</span>'} ${more}</div></td>
            <td class="py-4 px-4"><span class="px-3 py-1 rounded-full text-xs ${statusBadge(item.status)}">${escapeHtml(item.status)}</span></td>
            <td class="py-4 px-4 text-left">
                <div class="inline-flex flex-nowrap items-center gap-3 whitespace-nowrap">
                    <button onclick="viewUser(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    <button onclick="toggleUserInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                    <button onclick="deleteUser(${item.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </div>
            </td>`;
    };

    window.getUserEmptyRowHTML = function () {
        return '<tr><td colspan="8" class="py-12 text-center text-gray-400">کاربری یافت نشد</td></tr>';
    };

    window.getUserInlineExpandRowHTML = function (item) {
        return '<td colspan="8" class="p-5 border-t">' + (window.getUserInlineEditRowHTML ? window.getUserInlineEditRowHTML(item) : '') + '</td>';
    };

    window.getUserInlineEditRowHTML = function (item) {
        return `<div class="space-y-6">${formFields(item, 'inlineUser' + item.id)}
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <button onclick="saveInlineUser(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="toggleUserInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div></div>`;
    };

    window.getUserAddModalHTML = function () {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-4xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">افزودن کاربر</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields({}, 'user')}
                    <div class="flex gap-4 pt-2">
                        <button onclick="saveUser()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ثبت کاربر</button>
                        <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getUserEditModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-4xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش کاربر</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields(item, 'editUser')}
                    <div class="flex gap-4 pt-2">
                        <button onclick="saveEditedUser(${item.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getUserDetailsModalHTML = function (item) {
        const permsByGroup = {};
        (item.permissions || []).forEach(function (p) {
            if (!permsByGroup[p.group]) permsByGroup[p.group] = [];
            permsByGroup[p.group].push(p);
        });
        const permHtml = Object.keys(permsByGroup).length
            ? Object.keys(permsByGroup).map(function (g) {
                return '<div class="mb-3"><div class="text-xs font-semibold text-gray-500 mb-2">' + escapeHtml(g) + '</div>' +
                    '<div class="flex flex-wrap gap-2">' +
                    permsByGroup[g].map(function (p) {
                        return '<span class="px-3 py-1 rounded-full text-xs bg-indigo-50 text-indigo-700">' + escapeHtml(p.title) + '</span>';
                    }).join('') + '</div></div>';
            }).join('')
            : '<p class="text-gray-400 text-sm">دسترسی‌ای تعریف نشده</p>';

        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${escapeHtml(item.name)}</h2>
                        <p class="text-sm text-gray-500 mt-1">کد کاربر: #${item.id}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="editUser(${item.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                        <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-8">
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-user"></i> اطلاعات کاربری</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام</span><span class="font-medium">${escapeHtml(item.name)}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">موبایل</span><span class="font-medium">${escapeHtml(item.phone)}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ایمیل</span><span class="font-medium">${escapeHtml(item.email || '—')}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع</span><span class="font-medium">${escapeHtml(item.userTypeLabel)}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span>
                                <span class="px-3 py-1 rounded-full text-xs ${statusBadge(item.status)}">${escapeHtml(item.status)}</span>
                            </div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">آخرین ورود</span><span class="font-medium">${escapeHtml(item.lastLogin || '—')}</span></div>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-user-tag"></i> نقش و شعبه</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نقش</span>
                                <span class="font-medium inline-flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background:${escapeHtml(item.roleColor || '#6B7280')}"></span>
                                    ${escapeHtml(item.roleTitle)}
                                </span>
                            </div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">کد نقش</span><span class="font-medium font-mono text-xs">${escapeHtml(item.roleName)}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-key"></i> دسترسی‌ها (${(item.permissions || []).length})</h3>
                        ${permHtml}
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getUserPDFModalHTML = function (cols) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center"><h2 class="text-2xl font-bold">تنظیمات خروجی PDF کاربران</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div>
                <div class="p-8 space-y-5" style="max-height:calc(100vh - 10rem);overflow-y:auto;">
                    <input id="userPdfTitle" type="text" value="گزارش کاربران" class="${fieldClass}">
                    <input id="userPdfSubtitle" type="text" value="لیست کاربران، نقش‌ها و شعبه‌ها" class="${fieldClass}">
                    <div class="grid grid-cols-2 gap-4">
                        <select id="userPdfFormat" class="${fieldClass}"><option value="a4">A4</option><option value="letter">Letter</option></select>
                        <select id="userPdfOrientation" class="${fieldClass}"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select>
                    </div>
                    <input id="userPdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="${fieldClass}">
                    <div class="grid grid-cols-2 gap-2">${(cols || []).map(function (c) {
                        return '<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="userPdfCol-' + c.field + '" checked> ' + c.label + '</label>';
                    }).join('')}</div>
                    <div class="grid grid-cols-3 gap-4">
                        <input id="userPdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="userPdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="userPdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border rounded-2xl p-2">
                    </div>
                    <label class="flex items-center gap-3"><input id="userPdfIncludeDate" type="checkbox" checked> <span class="text-sm">نمایش تاریخ استخراج</span></label>
                    <div class="flex gap-4">
                        <button onclick="generateUsersPDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button>
                        <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getUserPDFPageHTML = function (pageNumber, rows, isFirstPage, o) {
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
