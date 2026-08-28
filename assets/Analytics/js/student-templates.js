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
            const label = option.label ?? option.name ?? option;
            const selected = String(value) === String(selectedValue) ? 'selected' : '';
            return '<option value="' + escapeHtml(value) + '" ' + selected + '>' + escapeHtml(label) + '</option>';
        }).join('');
    }

    window.getStudentAddressHTML=function(address,prefix){address=address||{};const province=address.province||'',cities=(window.studentCounties||[]).filter(x=>{const p=(window.studentProvinces||[]).find(y=>y.province_name===province);return p&&String(x.province_id)===String(p.province_id);});return `<div class="student-address-block address-block rounded-2xl border border-gray-200 p-4 space-y-3"><div class="grid grid-cols-1 sm:grid-cols-2 gap-3"><div><label class="text-xs text-gray-500 block mb-1">استان</label><select class="student-address-province ${fieldClass}" onchange="updateStudentCountySelect(this)"><option value="">انتخاب استان</option>${renderOptions((window.studentProvinces||[]).map(x=>({value:x.province_name,label:x.province_name})),province)}</select></div><div><label class="text-xs text-gray-500 block mb-1">شهر</label><select class="student-address-city ${fieldClass}" ${province?'':'disabled'}><option value="">${province?'انتخاب شهر':'ابتدا استان را انتخاب کنید'}</option>${renderOptions(cities.map(x=>({value:x.county_name,label:x.county_name})),address.city||'')}</select></div></div><div><label class="text-xs text-gray-500 block mb-1">ادامه آدرس</label><input class="student-address-text ${fieldClass}" value="${escapeHtml(address.address||'')}" placeholder="خیابان، پلاک، واحد..."></div><div class="grid grid-cols-1 sm:grid-cols-3 gap-3"><div><label class="text-xs text-gray-500 block mb-1">کد پستی</label><input class="student-address-postal ${fieldClass}" value="${escapeHtml(address.postal_code||'')}"></div><div><label class="text-xs text-gray-500 block mb-1">عرض جغرافیایی</label><input class="student-address-lat addr-lat ${fieldClass}" value="${escapeHtml(address.lat||'')}"></div><div><label class="text-xs text-gray-500 block mb-1">طول جغرافیایی</label><input class="student-address-lng addr-lng ${fieldClass}" value="${escapeHtml(address.lng||'')}"></div></div><div class="text-left"><button type="button" onclick="openGoogleMapsPicker(this)" class="text-sm text-indigo-600"><i class="fas fa-map-marker-alt"></i> انتخاب روی نقشه</button></div></div>`;};
    window.getStudentAvailabilityHTML=function(row,prefix){row=row||{};const days=[['saturday','شنبه'],['sunday','یکشنبه'],['monday','دوشنبه'],['tuesday','سه‌شنبه'],['wednesday','چهارشنبه'],['thursday','پنجشنبه'],['friday','جمعه']];return `<div class="student-availability-row rounded-2xl border border-indigo-100 bg-white p-4"><div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3"><div><label class="text-xs text-gray-500 block mb-1">روز *</label><select class="student-availability-day ${fieldClass}">${renderOptions(days.map(x=>({value:x[0],label:x[1]})),row.day||'saturday')}</select></div><div><label class="text-xs text-gray-500 block mb-1">منطقه زمانی *</label><select class="student-availability-timezone ${fieldClass}">${renderOptions((window.studentTimezones||[]).map(x=>({value:x.timezone_id,label:x.timezone})),row.timezoneId||'')}</select></div><div><label class="text-xs text-gray-500 block mb-1">ساعت شروع *</label><input type="time" class="student-availability-start ${fieldClass}" value="${escapeHtml(row.startTime||'')}"></div><div><label class="text-xs text-gray-500 block mb-1">ساعت پایان *</label><input type="time" class="student-availability-end ${fieldClass}" value="${escapeHtml(row.endTime||'')}"></div><div><label class="text-xs text-gray-500 block mb-1">وضعیت</label><select class="student-availability-status ${fieldClass}"><option value="available" ${(row.status||'available')==='available'?'selected':''}>فعال</option><option value="unavailable" ${row.status==='unavailable'?'selected':''}>غیرفعال</option></select></div></div><button type="button" onclick="this.closest('.student-availability-row').remove()" class="mt-3 text-sm text-red-500">حذف بازه</button></div>`;};

    function studentTimeOptions(selected){let html='<option value="">انتخاب ساعت</option>';for(let minutes=0;minutes<1440;minutes+=30){const value=String(Math.floor(minutes/60)).padStart(2,'0')+':'+String(minutes%60).padStart(2,'0');html+=`<option value="${value}" ${value===selected?'selected':''}>${value}</option>`;}return html;}
    window.getStudentAvailabilityHTML=function(row,prefix){row=row||{};const days=[['saturday','شنبه'],['sunday','یکشنبه'],['monday','دوشنبه'],['tuesday','سه‌شنبه'],['wednesday','چهارشنبه'],['thursday','پنجشنبه'],['friday','جمعه']];return `<div class="student-availability-row rounded-2xl border border-indigo-100 bg-white p-4"><div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3"><div><label class="text-xs text-gray-500 block mb-1">روز *</label><select class="student-availability-day ${fieldClass}">${renderOptions(days.map(x=>({value:x[0],label:x[1]})),row.day||'saturday')}</select></div><div><label class="text-xs text-gray-500 block mb-1">منطقه زمانی *</label><select class="student-availability-timezone ${fieldClass}">${renderOptions((window.studentTimezones||[]).map(x=>({value:x.timezone_id,label:x.timezone})),row.timezoneId||'')}</select></div><div><label class="text-xs text-gray-500 block mb-1">ساعت شروع *</label><select class="student-availability-start ${fieldClass}" dir="ltr">${studentTimeOptions(row.startTime||'')}</select></div><div><label class="text-xs text-gray-500 block mb-1">ساعت پایان *</label><select class="student-availability-end ${fieldClass}" dir="ltr">${studentTimeOptions(row.endTime||'')}</select></div></div><button type="button" onclick="this.closest('.student-availability-row').remove()" class="mt-3 text-sm text-red-500">حذف بازه</button></div>`;};

    function todayISO() {
        return new Date().toISOString().split('T')[0];
    }

    function formFields(item, prefix) {
        item = item || {};
        const id = function (n) { return prefix + n; };
        const organizations = (window.getStudentOrganizations?.() || []);
        const inferredOrganization=organizations.find(o=>Number(o.user_id)===Number(item.organizationUserId))||organizations.find(o=>o.kind==='branch'&&Number(o.id)===Number(item.branchId))||(window.studentIsBranchContext?organizations.find(o=>o.kind==='branch'):organizations[0]);
        const organizationUserId=Number(item.organizationUserId||inferredOrganization?.user_id||0);
        const groups=(window.studentTermGroups||[]).filter(x=>Number(x.organizationUserId)===organizationUserId);
        const selectedGroup=groups.find(x=>x.key===item.termKey)||groups.find(x=>x.name===item.instrument);
        const selectedTermKey=selectedGroup?.key||'';
        const under18 = typeof window.isStudentUnder18 === 'function' && window.isStudentUnder18(item.birthDate);
        const birthId = id('BirthDate');
        const parentWrapId = id('ParentWrap');

        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                ${window.studentIsBranchContext?`<input id="${id('Organization')}" type="hidden" value="${escapeHtml(organizationUserId)}">`:`<div>
                    <label class="block text-sm font-medium mb-2">سازمان *</label>
                    <select id="${id('Organization')}" onchange="refreshStudentTerms('${prefix}')" class="${fieldClass}">
                        <option value="">انتخاب سازمان</option>
                        ${renderOptions(organizations.map(o=>({value:o.user_id,label:o.name})),organizationUserId)}
                    </select>
                </div>`}
                <div>
                    <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                    <input id="${id('Name')}" type="text" value="${escapeHtml(item.name || '')}" class="${fieldClass}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شماره ملی *</label>
                    <input id="${id('NationalId')}" type="text" value="${escapeHtml(item.nationalId || '')}" class="${fieldClass}" placeholder="کد ملی ۱۰ رقمی">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نام پدر *</label>
                    <input id="${id('FatherName')}" type="text" value="${escapeHtml(item.fatherName || '')}" class="${fieldClass}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تاریخ تولد *</label>
                    <input id="${birthId}" type="date" value="${escapeHtml(item.birthDate || '')}" class="${fieldClass}"
                           onchange="window.toggleStudentParentFields('${birthId}', '${parentWrapId}')">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                    <input id="${id('Phone')}" type="tel" value="${escapeHtml(item.phone || '')}" class="${fieldClass}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تاریخ ثبت‌نام</label>
                    <input id="${id('RegistrationDate')}" type="date" value="${escapeHtml(item.registrationDate || todayISO())}" class="${fieldClass}">
                </div>
            </div>

            <div class="space-y-3"><h3 class="font-semibold text-indigo-700">آدرس</h3><div id="${id('Addresses')}" class="space-y-3">${window.getStudentAddressHTML(item.addresses?.[0]||{address:item.address||''},prefix)}</div></div>

            <div id="${parentWrapId}" class="student-parent-fields border border-amber-200 bg-amber-50/50 rounded-2xl p-5 space-y-4 ${under18 ? '' : 'hidden'}">
                <h3 class="font-semibold text-amber-800 flex items-center gap-2"><i class="fas fa-user-shield"></i> اطلاعات والد (الزامی برای زیر ۱۸ سال)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">نام و نام خانوادگی والد *</label>
                        <input id="${id('ParentName')}" type="text" value="${escapeHtml(item.parentName || '')}" class="${fieldClass}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شماره ملی والد *</label>
                        <input id="${id('ParentNationalId')}" type="text" value="${escapeHtml(item.parentNationalId || '')}" class="${fieldClass}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نام پدر والد *</label>
                        <input id="${id('ParentFatherName')}" type="text" value="${escapeHtml(item.parentFatherName || '')}" class="${fieldClass}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ تولد والد *</label>
                        <input id="${id('ParentBirthDate')}" type="date" value="${escapeHtml(item.parentBirthDate || '')}" class="${fieldClass}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شماره تماس والد *</label>
                        <input id="${id('ParentPhone')}" type="tel" value="${escapeHtml(item.parentPhone || '')}" class="${fieldClass}">
                    </div>
                </div>
            </div>

            <div class="border-t pt-6">
                <h3 class="font-semibold mb-4 text-indigo-700">اطلاعات آموزشی</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">ترم آموزشی *</label>
                        <select id="${id('Instrument')}" onchange="updateStudentTermFields('${prefix}')" class="${fieldClass}" ${organizationUserId?'':'disabled'}>
                            <option value="">ترم آموزشی را انتخاب کنید</option>
                            ${renderOptions(groups.map(x=>({value:x.key,label:x.name})),selectedTermKey)}
                        </select>
                    </div>
                </div>
            </div>
            <div id="${id('AvailabilityWrap')}" class="${selectedGroup?.status==='ongoing'?'':'hidden'} border border-indigo-200 bg-indigo-50/40 rounded-2xl p-5 space-y-4"><h3 class="font-semibold text-indigo-700">افزودن برنامه زمانی هنرجو</h3><div id="${id('AvailabilityRows')}" class="space-y-3">${(item.availabilities?.length?item.availabilities:[{}]).map(x=>window.getStudentAvailabilityHTML(x,prefix)).join('')}</div><button type="button" onclick="addStudentAvailability('${prefix}')" class="text-sm text-indigo-600">+ افزودن بازه زمانی جدید</button><div><label class="block text-sm font-medium mb-2">خلاصه</label><input id="${id('AvailabilitySummary')}" class="${fieldClass}" value="${escapeHtml(item.availabilitySummary||'')}"></div><div><label class="block text-sm font-medium mb-2">توضیحات</label><textarea id="${id('AvailabilityDescription')}" class="${fieldClass}" rows="3">${escapeHtml(item.availabilityDescription||'')}</textarea></div></div>`;
    }

    window.getStudentRowHTML = function (item) {
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.name)}</td>
            <td class="py-4 px-5">${escapeHtml(item.instrument)}</td>
            <td class="py-4 px-5">${escapeHtml(item.level)}</td>
            <td class="py-4 px-5">${escapeHtml(item.teacher)}</td>
            <td class="py-4 px-5">${escapeHtml(item.branch)}</td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-3 whitespace-nowrap">
                    <button onclick="viewStudent(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    <button onclick="toggleStudentInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                    <button onclick="deleteStudent(${item.id})" class="text-red-500 hover:text-red-700 text-sm">حذف</button>
                </div>
            </td>`;
    };

    window.getStudentEmptyRowHTML = function () {
        return '<tr><td colspan="6" class="py-12 text-center text-gray-400">هیچ هنرجویی یافت نشد</td></tr>';
    };

    window.getStudentInlineExpandRowHTML = function (item) {
        return '<td colspan="6" class="p-5 border-t">' + (window.getStudentInlineEditRowHTML ? window.getStudentInlineEditRowHTML(item) : '') + '</td>';
    };

    window.getStudentInlineEditRowHTML = function (item) {
        return `<div class="space-y-6">
            ${formFields(item, 'inlineStu' + item.id)}
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <button onclick="saveInlineStudent(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="toggleStudentInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div>
        </div>`;
    };

    window.getStudentAddModalHTML = function () {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">افزودن هنرجو جدید</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields({ registrationDate: todayISO() }, 'stu')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveStudent()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره هنرجو</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getStudentEditModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش هنرجو</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields(item, 'editStu')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveEditedStudent(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getStudentDetailsModalHTML = function (item) {
        const age = item.birthDate ? (function () {
            const d = new Date(item.birthDate);
            const now = new Date();
            let a = now.getFullYear() - d.getFullYear();
            const m = now.getMonth() - d.getMonth();
            if (m < 0 || (m === 0 && now.getDate() < d.getDate())) a--;
            return a;
        })() : '—';
        const under18 = typeof age === 'number' && age < 18;
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${escapeHtml(item.name)}</h2>
                        <p class="text-sm text-gray-500 mt-1">کد هنرجو: #${item.id}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="editStudent(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-8">
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-user"></i> اطلاعات شخصی</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام و نام خانوادگی</span><span class="font-medium">${escapeHtml(item.name)}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شماره تماس</span><span class="font-medium">${escapeHtml(item.phone)}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شماره ملی</span><span class="font-medium">${escapeHtml(item.nationalId || '—')}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام پدر</span><span class="font-medium">${escapeHtml(item.fatherName || '—')}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ تولد</span><span class="font-medium">${escapeHtml(item.birthDate || '—')}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سن</span><span class="font-medium">${escapeHtml(age)}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ ثبت‌نام</span><span class="font-medium">${escapeHtml(item.registrationDate || '—')}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سازمان</span><span class="font-medium">${escapeHtml(item.branch)}</span></div>
                            <div class="flex justify-between border-b pb-2 md:col-span-2"><span class="text-gray-500">آدرس</span><span class="font-medium text-left max-w-[70%]">${escapeHtml(item.address || '—')}</span></div>
                        </div>
                    </div>
                    ${under18 ? `
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-user-shield"></i> اطلاعات والد</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام والد</span><span class="font-medium">${escapeHtml(item.parentName || '—')}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">کد ملی والد</span><span class="font-medium">${escapeHtml(item.parentNationalId || '—')}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام پدر والد</span><span class="font-medium">${escapeHtml(item.parentFatherName || '—')}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ تولد والد</span><span class="font-medium">${escapeHtml(item.parentBirthDate || '—')}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تلفن والد</span><span class="font-medium">${escapeHtml(item.parentPhone || '—')}</span></div>
                        </div>
                    </div>` : ''}
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-music"></i> اطلاعات آموزشی</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ترم آموزشی</span><span class="font-medium">${escapeHtml(item.instrument)}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سطح</span><span class="font-medium">${escapeHtml(item.level)}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">استاد</span><span class="font-medium">استاد ${escapeHtml(item.teacher)}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع کلاس</span><span class="font-medium">خصوصی</span></div>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-3 flex items-center gap-2"><i class="fas fa-sticky-note"></i> یادداشت‌های استاد</h3>
                        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 text-sm text-gray-700 leading-relaxed">
                            هنرجو پیشرفت خوبی در تکنیک داشته. پیشنهاد می‌شود قطعات کلاسیک بیشتری تمرین کند.
                            <div class="text-xs text-gray-400 mt-2">آخرین به‌روزرسانی: ۲ روز پیش</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getStudentPDFModalHTML = function (cols) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <h2 class="text-2xl font-bold">تنظیمات خروجی PDF هنرجویان</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-6" style="max-height:calc(100vh - 10rem);overflow-y:auto;">
                    <input id="stuPdfTitle" type="text" value="گزارش هنرجویان" class="${fieldClass}">
                    <input id="stuPdfSubtitle" type="text" value="لیست هنرجویان و وضعیت آموزشی" class="${fieldClass}">
                    <div class="grid grid-cols-2 gap-5">
                        <select id="stuPdfFormat" class="${fieldClass}"><option value="a4">A4</option><option value="letter">Letter</option></select>
                        <select id="stuPdfOrientation" class="${fieldClass}"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select>
                    </div>
                    <input id="stuPdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="${fieldClass}">
                    <div class="grid grid-cols-2 gap-2">
                        ${(cols || []).map(function (c) {
                            return '<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="stuPdfCol-' + c.field + '" checked> ' + c.label + '</label>';
                        }).join('')}
                    </div>
                    <div class="grid grid-cols-3 gap-5">
                        <input id="stuPdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="stuPdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="stuPdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border rounded-2xl p-2">
                    </div>
                    <label class="flex items-center gap-3"><input id="stuPdfIncludeDate" type="checkbox" checked> <span class="text-sm">نمایش تاریخ استخراج</span></label>
                    <div class="flex gap-4 pt-4">
                        <button onclick="generateStudentsPDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button>
                        <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getStudentPDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
        const o = options;
        return `<div style="width:100%;padding:24px;background:#fff;direction:rtl;">
            ${isFirstPage ? `<h1 style="margin:0 0 6px;font-size:28px;font-weight:700;">${escapeHtml(o.title)}</h1>
            <p style="margin:0 0 16px;color:#4b5563;font-size:14px;">${escapeHtml(o.subtitle)}</p>
            ${o.includeDate ? `<p style="margin:0 0 16px;color:#6b7280;font-size:12px;">تاریخ استخراج: ${escapeHtml(o.date)}</p>` : ''}` : ''}
            <table style="width:100%;border-collapse:collapse;">
                <thead style="background:${o.headerColor};"><tr>
                    ${o.selectedColumns.map(function (c) {
                        return '<th style="padding:12px 14px;text-align:right;font-weight:600;">' + escapeHtml(c.label) + '</th>';
                    }).join('')}
                </tr></thead>
                <tbody>
                    ${rows.map(function (item, index) {
                        return '<tr style="background:' + (index % 2 === 0 ? o.evenRowColor : o.oddRowColor) + ';">' +
                            o.selectedColumns.map(function (c) {
                                const v = c.field === 'index' ? (pageNumber - 1) * o.rowsPerPage + index + 1 : item[c.field];
                                return '<td style="padding:12px 14px;text-align:right;">' + escapeHtml(v) + '</td>';
                            }).join('') + '</tr>';
                    }).join('')}
                </tbody>
            </table>
            ${isFirstPage && o.footer ? `<p style="margin-top:16px;color:#6b7280;font-size:12px;">${escapeHtml(o.footer)}</p>` : ''}
            <div style="margin-top:16px;text-align:left;color:#6b7280;font-size:12px;">صفحه ${pageNumber} / ${o.totalPages}</div>
        </div>`;
    };
})();
