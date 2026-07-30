(function () {
    const fieldClass = 'w-full border border-gray-300 rounded-2xl py-3.5 px-5';

    window.getBranchCardHTML = function (branch) {
        const mainAddress = (branch.addresses || []).find(item => item.is_main) || (branch.addresses || [])[0];
        const mainPhone = (branch.phones || []).find(item => item.is_main) || (branch.phones || [])[0];
        const address = mainAddress ? `${mainAddress.province || ''}، ${mainAddress.city || ''}، ${mainAddress.address || ''}` : '—';
        const isMain = Boolean(branch.is_main);
        const physicalLabel = window.getBranchPhysicalTypeLabel(branch.physical_type);

        return `
            <div class="rounded-3xl p-6 shadow border transition-shadow duration-300 hover:shadow-xl ${isMain ? 'bg-amber-50 border-amber-300 ring-1 ring-amber-200' : 'bg-white border-transparent'}">
                <div class="flex justify-between items-start gap-3 mb-3">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-xl font-bold">${branch.name}</h3>
                            ${isMain ? '<span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-200 text-amber-900"><i class="fas fa-star ml-1"></i>شعبه اصلی</span>' : ''}
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">${branch.type}</span>
                            <span class="px-3 py-1 rounded-full text-xs bg-sky-100 text-sky-700">${physicalLabel}</span>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs ${branch.status === 'فعال' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${branch.status}</span>
                </div>
                ${branch.slogan ? `<p class="text-sm text-indigo-600 italic mb-3">«${branch.slogan}»</p>` : ''}
                <div class="space-y-2 text-sm mb-5">
                    <div class="flex justify-between"><span class="text-gray-500">مدیر</span><span>${branch.manager || '—'}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">تلفن اصلی</span><span>${mainPhone ? mainPhone.number : '—'}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">آدرس اصلی</span><span class="text-left max-w-[60%] truncate" title="${address}">${address}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">تعداد کلاس</span><span class="font-medium">${branch.classrooms || 0}</span></div>
                </div>
                <div class="flex gap-2">
                    <button onclick="viewBranch(${branch.id})" class="flex-1 border border-indigo-200 text-indigo-600 py-2 rounded-xl text-sm hover:bg-indigo-50">جزئیات</button>
                    <button onclick="editBranch(${branch.id})" class="flex-1 bg-indigo-600 text-white py-2 rounded-xl text-sm hover:bg-indigo-700">ویرایش</button>
                </div>
            </div>`;
    };

    window.getBranchEmptyHTML = function () {
        return '<p class="col-span-full text-center text-gray-400 py-16">شعبه‌ای یافت نشد</p>';
    };

    window.getBranchPhoneFieldHTML = function (phone = {}) {
        return `<div class="border border-gray-200 rounded-2xl p-4 space-y-3 mb-3">
            <input type="text" class="phone-number ${fieldClass}" value="${phone.number || ''}" placeholder="شماره تماس">
            <div class="grid grid-cols-2 gap-3"><select class="phone-priority border border-gray-300 rounded-2xl py-3 px-4">${getPriorityOptions(phone.priority || 'primary')}</select><label class="flex items-center gap-2 text-sm"><input type="checkbox" class="phone-is-main" onchange="enforceSingleBranchPrimary(this, 'phone-is-main')" ${phone.is_main ? 'checked' : ''}> اصلی</label></div>
        </div>`;
    };

    window.getBranchLinkFieldHTML = function (link = {}) {
        return `<div class="border border-gray-200 rounded-2xl p-4 space-y-3 mb-3">
            <input type="text" class="link-title ${fieldClass}" value="${link.title || ''}" placeholder="عنوان لینک">
            <input type="text" class="link-url ${fieldClass}" value="${link.url || ''}" placeholder="آدرس URL">
            <div class="grid grid-cols-2 gap-3"><select class="link-mode border border-gray-300 rounded-2xl py-3 px-4"><option value="social" ${link.mode !== 'email' ? 'selected' : ''}>شبکه اجتماعی / کلاس</option><option value="email" ${link.mode === 'email' ? 'selected' : ''}>ایمیل</option></select><select class="link-platform border border-gray-300 rounded-2xl py-3 px-4">${getPlatformOptions(link.platform || 'website')}</select></div>
            <div class="grid grid-cols-2 gap-3"><select class="link-priority border border-gray-300 rounded-2xl py-3 px-4">${getPriorityOptions(link.priority || 'secondary')}</select><label class="flex items-center gap-2 text-sm"><input type="checkbox" class="link-is-main" onchange="enforceSingleBranchPrimary(this, 'link-is-main')" ${link.is_main ? 'checked' : ''}> اصلی</label></div>
        </div>`;
    };

    window.getBranchAddressFieldHTML = function (address = {}) {
        return `<div class="border border-gray-200 rounded-2xl p-4 space-y-3 mb-3 address-block">
            <div class="grid grid-cols-2 gap-3"><div><label class="text-xs text-gray-500 mb-1 block">استان</label><select class="addr-province border border-gray-300 rounded-2xl py-3 px-4 w-full">${getProvinceOptions(address.province || 'تهران')}</select></div><div><label class="text-xs text-gray-500 mb-1 block">شهر</label><select class="addr-city border border-gray-300 rounded-2xl py-3 px-4 w-full">${getCityOptions(address.city || 'تهران')}</select></div></div>
            <div><label class="text-xs text-gray-500 mb-1 block">ادامه آدرس</label><input type="text" class="addr-address ${fieldClass}" value="${address.address || ''}" placeholder="خیابان، پلاک، واحد..."></div>
            <div class="grid grid-cols-3 gap-3"><div><label class="text-xs text-gray-500 mb-1 block">کد پستی</label><input type="text" class="addr-postal w-full border border-gray-300 rounded-2xl py-3 px-4" value="${address.postal_code || ''}"></div><div><label class="text-xs text-gray-500 mb-1 block">عرض جغرافیایی</label><input type="text" class="addr-lat w-full border border-gray-300 rounded-2xl py-3 px-4" value="${address.lat || ''}"></div><div><label class="text-xs text-gray-500 mb-1 block">طول جغرافیایی</label><input type="text" class="addr-lng w-full border border-gray-300 rounded-2xl py-3 px-4" value="${address.lng || ''}"></div></div>
            <div class="flex items-center justify-between"><label class="flex items-center gap-2 text-sm"><input type="checkbox" class="addr-is-main" onchange="enforceSingleBranchPrimary(this, 'addr-is-main')" ${address.is_main ? 'checked' : ''}> آدرس اصلی</label><button type="button" onclick="openGoogleMapsPicker(this)" class="text-sm text-indigo-600 hover:underline"><i class="fas fa-map-marker-alt"></i> انتخاب روی نقشه</button></div>
        </div>`;
    };

    function branchForm(branch, isEdit) {
        const prefix = isEdit ? 'edit' : '';
        const id = key => isEdit ? `edit${key.charAt(0).toUpperCase()}${key.slice(1)}` : key;
        const title = isEdit ? 'ویرایش شعبه' : 'افزودن شعبه جدید';
        const save = isEdit ? `saveEditedBranch(${branch.id})` : 'saveBranch()';
        const phones = (branch.phones || [{}]).map(window.getBranchPhoneFieldHTML).join('');
        const links = (branch.links || [{}]).map(window.getBranchLinkFieldHTML).join('');
        const addresses = (branch.addresses || [{}]).map(window.getBranchAddressFieldHTML).join('');
        const container = name => isEdit ? `edit${name.charAt(0).toUpperCase()}${name.slice(1)}Container` : `${name}Container`;
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()"><div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()"><div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl z-10"><h2 class="text-2xl font-bold">${title}</h2><button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div><div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5"><div><label class="block text-sm font-medium mb-2">نام شعبه *</label><input id="${id('branchName')}" type="text" value="${branch.name || ''}" class="${fieldClass}"></div><div><label class="block text-sm font-medium mb-2">نوع آموزشی *</label><select id="${id('branchType')}" class="${fieldClass}">${getBranchTypeOptions(branch.type)}</select><button type="button" onclick="promptAddBranchType()" class="text-sm text-indigo-600 mt-1">+ نوع جدید</button></div><div><label class="block text-sm font-medium mb-2">نوع ارائه *</label><select id="${id('branchPhysicalType')}" class="${fieldClass}">${getBranchPhysicalTypeOptions(branch.physical_type)}</select></div><div><label class="block text-sm font-medium mb-2">وضعیت</label><select id="${id('branchStatus')}" class="${fieldClass}"><option value="فعال" ${branch.status !== 'غیرفعال' ? 'selected' : ''}>فعال</option><option value="غیرفعال" ${branch.status === 'غیرفعال' ? 'selected' : ''}>غیرفعال</option></select></div><div><label class="block text-sm font-medium mb-2">مدیر شعبه</label><input id="${id('branchManager')}" type="text" value="${branch.manager || ''}" class="${fieldClass}"></div><label class="flex items-center gap-2 text-sm mt-8"><input id="${id('branchIsMain')}" type="checkbox" ${branch.is_main ? 'checked' : ''}> شعبه اصلی آموزشگاه</label></div>
            <div><label class="block text-sm font-medium mb-2">شعار</label><input id="${id('branchSlogan')}" type="text" value="${branch.slogan || ''}" class="${fieldClass}"></div><div><label class="block text-sm font-medium mb-2">بیوگرافی</label><textarea id="${id('branchBio')}" rows="3" class="${fieldClass}">${branch.bio || ''}</textarea></div>
            <div><label class="block text-sm font-medium mb-2">شماره‌های تماس</label><div id="${container('Phones')}">${phones}</div><button type="button" onclick="addPhoneField('${container('Phones')}')" class="mt-2 text-sm text-indigo-600">+ افزودن شماره</button></div><div><label class="block text-sm font-medium mb-2">لینک‌ها</label><div id="${container('Links')}">${links}</div><button type="button" onclick="addLinkField('${container('Links')}')" class="mt-2 text-sm text-indigo-600">+ افزودن لینک</button></div><div><label class="block text-sm font-medium mb-2">آدرس‌ها</label><div id="${container('Addresses')}">${addresses}</div><button type="button" onclick="addAddressField('${container('Addresses')}')" class="mt-2 text-sm text-indigo-600">+ افزودن آدرس</button></div>
            <div class="flex gap-4 pt-4"><button onclick="${save}" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ذخیره تغییرات</button><button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button></div></div></div></div>`;
    }

    window.getBranchAddModalHTML = function () { return branchForm({}, false); };
    window.getBranchEditModalHTML = function (branch) { return branchForm(branch, true); };

    window.getBranchViewModalHTML = function (branch) {
        const phones = (branch.phones || []).map(item => `<div class="flex justify-between text-sm border-b pb-2"><span>${item.number}</span><span class="text-xs text-gray-500">${item.priority}${item.is_main ? ' (اصلی)' : ''}</span></div>`).join('') || '—';
        const addresses = (branch.addresses || []).map(item => `<div class="text-sm border-b pb-2">${item.province}، ${item.city}، ${item.address}${item.is_main ? ' <span class="text-indigo-600">(اصلی)</span>' : ''}</div>`).join('') || '—';
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target === this) closeModal()"><div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl" onclick="event.stopPropagation()"><div class="px-8 py-5 border-b flex justify-between items-center"><div><h2 class="text-2xl font-bold">${branch.name}</h2><p class="text-sm text-gray-500 mt-1">${branch.type} · ${getBranchPhysicalTypeLabel(branch.physical_type)}${branch.is_main ? ' · شعبه اصلی' : ''}</p></div><button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div><div class="p-8 space-y-6"><div class="grid grid-cols-2 gap-5 text-sm"><div><h3 class="font-semibold text-indigo-700 mb-2">اطلاعات پایه</h3><p>مدیر: ${branch.manager || '—'}</p><p class="mt-2">کلاس‌ها: ${branch.classrooms || 0}</p></div><div><h3 class="font-semibold text-indigo-700 mb-2">تلفن‌ها</h3>${phones}</div></div><div><h3 class="font-semibold text-indigo-700 mb-2">آدرس‌ها</h3>${addresses}</div><button onclick="editBranch(${branch.id})" class="w-full bg-indigo-600 text-white py-3 rounded-2xl">ویرایش</button></div></div></div>`;
    };

    window.getBranchPDFModalHTML = function (columns) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()"><div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()"><div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl"><h2 class="text-2xl font-bold">تنظیمات خروجی PDF شعبه‌ها</h2><button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div><div class="p-8 space-y-6 max-h-[calc(100vh-10rem)] overflow-y-auto">
            <div><label class="block text-sm font-medium mb-2">عنوان گزارش</label><input id="branchPdfTitle" value="گزارش شعبه‌های آموزشگاه" class="${fieldClass}"></div><div><label class="block text-sm font-medium mb-2">زیرعنوان</label><input id="branchPdfSubtitle" value="فهرست شعبه‌ها و اطلاعات پایه" class="${fieldClass}"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5"><div><label class="block text-sm font-medium mb-2">فرمت صفحه</label><select id="branchPdfFormat" class="${fieldClass}"><option value="a4">A4</option><option value="letter">Letter</option><option value="legal">Legal</option></select></div><div><label class="block text-sm font-medium mb-2">جهت صفحه</label><select id="branchPdfOrientation" class="${fieldClass}"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select></div><div><label class="block text-sm font-medium mb-2">یادداشت پایین صفحه</label><input id="branchPdfFooter" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="${fieldClass}"></div></div>
            <div><label class="block text-sm font-medium mb-2">ستون‌های خروجی PDF</label><div class="grid grid-cols-2 gap-2">${columns.map(column => `<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="branchPdfCol-${column.field}" checked class="text-indigo-600 border-gray-300 rounded">${column.label}</label>`).join('')}</div></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5"><div><label class="block text-sm font-medium mb-2">رنگ سطر عنوان</label><input id="branchPdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border border-gray-300 rounded-2xl p-2"></div><div><label class="block text-sm font-medium mb-2">رنگ سطرهای زوج</label><input id="branchPdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border border-gray-300 rounded-2xl p-2"></div><div><label class="block text-sm font-medium mb-2">رنگ سطرهای فرد</label><input id="branchPdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border border-gray-300 rounded-2xl p-2"></div></div>
            <label class="flex items-center gap-3"><input id="branchPdfIncludeDate" type="checkbox" checked class="h-4 w-4 text-indigo-600 border-gray-300 rounded">نمایش تاریخ استخراج در بالای گزارش</label><div class="flex gap-4 pt-4"><button onclick="generateBranchesPDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button><button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button></div></div></div></div>`;
    };

    window.getBranchPDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
        const escapeHtml = value => String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        return `<div style="width:100%;padding:24px;background:#fff;direction:rtl"><div style="text-align:right">${isFirstPage ? `<h1 style="margin:0 0 6px;font-size:28px">${escapeHtml(options.title)}</h1><p style="margin:0 0 16px;color:#4b5563">${escapeHtml(options.subtitle)}</p>${options.includeDate ? `<p style="margin:0 0 16px;color:#6b7280;font-size:12px">تاریخ استخراج: ${escapeHtml(options.date)}</p>` : ''}` : ''}</div><table style="width:100%;border-collapse:collapse"><thead style="background:${options.headerColor}"><tr>${options.columns.map(column => `<th style="padding:12px 14px;text-align:right">${escapeHtml(column.label)}</th>`).join('')}</tr></thead><tbody>${rows.map((row, index) => `<tr style="background:${index % 2 === 0 ? options.evenRowColor : options.oddRowColor}">${options.columns.map(column => `<td style="padding:12px 14px;text-align:right">${escapeHtml(column.field === 'index' ? (pageNumber - 1) * options.rowsPerPage + index + 1 : row[column.field])}</td>`).join('')}</tr>`).join('')}</tbody></table>${isFirstPage && options.footer ? `<p style="margin-top:16px;color:#6b7280;font-size:12px">${escapeHtml(options.footer)}</p>` : ''}<div style="margin-top:16px;text-align:left;color:#6b7280;font-size:12px">صفحه ${pageNumber} / ${options.totalPages}</div></div>`;
    };
})();
