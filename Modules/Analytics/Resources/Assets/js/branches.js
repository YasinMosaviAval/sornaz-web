/*
    // ==================== انواع شعبه (قابل مدیریت) ====================
    let allBranchTypes = [
        { id: 1, name: "موسیقی" },
        { id: 2, name: "ادبیات" },
        { id: 3, name: "شعر" },
        { id: 4, name: "نقاشی" },
        { id: 5, name: "خیاطی" },
        { id: 6, name: "سایر" }
    ];

    // ==================== داده شعبه‌ها (گسترش‌یافته) ====================
    let allBranches = [
        {
            id: 1,
            name: "شعبه مرکزی",
            type: "موسیقی",
            slogan: "آموزش با عشق، اجرا با افتخار",
            bio: "شعبه اصلی آموزشگاه با تمرکز بر سازهای کلاسیک و ایرانی. بیش از ۱۰ سال سابقه.",
            manager: "آقای رضایی",
            classrooms: 8,
            status: "فعال",
            phones: ["۰۲۱-۸۸۷۷۶۶۵۵", "۰۹۱۲۱۲۳۴۵۶۷"],
            links: [
                { title: "کلاس آنلاین", url: "https://meet.example.com/central" },
                { title: "اینستاگرام", url: "https://instagram.com/musicacademy" }
            ],
            addresses: [
                "تهران، خیابان ولیعصر، پلاک ۱۲۳",
                "تهران، خیابان طالقانی، پلاک ۴۵ (بخش تمرین)"
            ]
        },
        {
            id: 2,
            name: "شعبه ونک",
            type: "موسیقی",
            slogan: "صدای آینده از اینجا شروع می‌شود",
            bio: "شعبه تخصصی گیتار و آواز در منطقه ونک.",
            manager: "خانم موسوی",
            classrooms: 5,
            status: "فعال",
            phones: ["۰۲۱-۸۸۶۶۵۵۴۴"],
            links: [{ title: "کلاس آنلاین", url: "https://meet.example.com/vanak" }],
            addresses: ["تهران، میدان ونک، برج آسمان، طبقه ۳"]
        },
        {
            id: 3,
            name: "شعبه سعادت‌آباد",
            type: "نقاشی",
            slogan: "رنگ‌ها را زندگی کنید",
            bio: "شعبه هنرهای تجسمی و نقاشی برای کودکان و بزرگسالان.",
            manager: "آقای بهرامی",
            classrooms: 6,
            status: "فعال",
            phones: ["۰۲۱-۲۲۱۱۰۰۳۳", "۰۹۱۹۸۷۶۵۴۳۲"],
            links: [],
            addresses: ["تهران، سعادت‌آباد، میدان کاج"]
        },
        {
            id: 4,
            name: "شعبه کرج",
            type: "خیاطی",
            slogan: "طراحی و دوخت حرفه‌ای",
            bio: "آموزش خیاطی و طراحی لباس در کرج.",
            manager: "خانم کریمی",
            classrooms: 4,
            status: "فعال",
            phones: ["۰۲۶-۳۴۵۶۷۸۹۰"],
            links: [{ title: "واتساپ پشتیبانی", url: "https://wa.me/98912xxxxxxx" }],
            addresses: ["کرج، مهرویلا، خیابان شهید بهشتی"]
        }
    ];

    let filteredBranches = [...allBranches];

    // ==================== رندر کارت‌ها ====================
    window.renderBranches = function(list = filteredBranches) {
        const container = document.getElementById('branchesCards');
        if (!container) return;

        if (list.length === 0) {
            container.innerHTML = `<p class="col-span-full text-center text-gray-400 py-16">شعبه‌ای یافت نشد</p>`;
            return;
        }

        window.renderBranchTypeFilter = function() {
            const select = document.getElementById('filterBranchType');
            if (!select) return;
            select.innerHTML = `<option value="">همه انواع شعبه</option>` + 
                allBranchTypes.map(t => `<option value="${t.name}">${t.name}</option>`).join('');
        };

        container.innerHTML = list.map(b => `
            <div class="bg-white rounded-3xl p-6 shadow card-hover">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-xl font-bold">${b.name}</h3>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">${b.type}</span>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs ${b.status === 'فعال' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${b.status}</span>
                </div>

                ${b.slogan ? `<p class="text-sm text-indigo-600 italic mb-3">«${b.slogan}»</p>` : ''}

                <div class="space-y-2 text-sm mb-5">
                    <div class="flex justify-between"><span class="text-gray-500">مدیر شعبه</span><span>${b.manager || '—'}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">تلفن اصلی</span><span>${(b.phones && b.phones[0]) || '—'}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">تعداد کلاس</span><span class="font-medium">${b.classrooms || 0} کلاس</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">آدرس‌ها</span><span>${(b.addresses && b.addresses.length) || 0} آدرس</span></div>
                </div>

                <div class="flex gap-2">
                    <button onclick="viewBranch(${b.id})" class="flex-1 border border-indigo-200 text-indigo-600 py-2 rounded-xl text-sm hover:bg-indigo-50">جزئیات</button>
                    <button onclick="editBranch(${b.id})" class="flex-1 bg-indigo-600 text-white py-2 rounded-xl text-sm hover:bg-indigo-700">ویرایش</button>
                </div>
            </div>
        `).join('');
    };

    // ==================== فیلتر ====================
    window.filterBranches = function() {
        const search = (document.getElementById('branchSearch')?.value || '').trim().toLowerCase();
        const type = document.getElementById('filterBranchType')?.value || '';

        filteredBranches = allBranches.filter(b => {
            const matchSearch = !search || 
                b.name.toLowerCase().includes(search) || 
                (b.manager && b.manager.toLowerCase().includes(search));
            const matchType = !type || b.type === type;
            return matchSearch && matchType;
        });

        renderBranches(filteredBranches);
    };

    // ==================== خروجی اکسل ====================
    window.exportBranchesToExcel = function() {
        const data = filteredBranches.length ? filteredBranches : allBranches;
        let csv = '\uFEFF';
        csv += 'ردیف,نام شعبه,نوع,مدیر,وضعیت,شعار,تلفن‌ها,آدرس‌ها,تعداد کلاس\n';

        data.forEach((b, index) => {
            const phones = (b.phones || []).join(' | ');
            const addresses = (b.addresses || []).join(' | ');
            csv += `${index + 1},"${b.name}","${b.type}","${b.manager || ''}","${b.status}","${b.slogan || ''}","${phones}","${addresses}",${b.classrooms || 0}\n`;
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `شعبه‌ها_${new Date().toLocaleDateString('fa-IR')}.csv`;
        link.click();
    };

    // ==================== Modal افزودن شعبه ====================
    window.openAddBranchModal = function() {
        if (!document.getElementById('modalContainer')) {
            alert('modalContainer پیدا نشد!');
            return;
        }

        // const typeOptions = branchTypes.map(t => `<option value="${t}">${t}</option>`).join('');
        const typeOptions = getBranchTypeOptions();

        document.getElementById('modalContainer').innerHTML = `
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <h2 class="text-2xl font-bold">افزودن شعبه جدید</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">نام شعبه *</label>
                            <input id="branchName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">نوع شعبه *</label>
                            <select id="branchType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${typeOptions}</select>
                            <button type="button" onclick="promptAddBranchType()" class="text-sm text-indigo-600 mt-1">+ نوع جدید</button>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">مدیر شعبه</label>
                            <input id="branchManager" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">وضعیت</label>
                            <select id="branchStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                <option value="فعال">فعال</option>
                                <option value="غیرفعال">غیرفعال</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">شعار شعبه</label>
                        <input id="branchSlogan" type="text" placeholder="یک جمله کوتاه و الهام‌بخش" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">خلاصه / بیوگرافی</label>
                        <textarea id="branchBio" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                    </div>

                    <!-- شماره‌های تماس (چندتایی) -->
                    <div>
                        <label class="block text-sm font-medium mb-2">شماره‌های تماس</label>
                        <div id="phonesContainer" class="space-y-2">
                            <input type="text" class="phone-input w-full border border-gray-300 rounded-2xl py-3 px-5" placeholder="شماره تماس ۱">
                        </div>
                        <button type="button" onclick="addPhoneField()" class="mt-2 text-sm text-indigo-600 hover:underline">+ افزودن شماره دیگر</button>
                    </div>

                    <!-- لینک‌ها -->
                    <div>
                        <label class="block text-sm font-medium mb-2">لینک‌های اینترنتی (کلاس آنلاین و ...)</label>
                        <div id="linksContainer" class="space-y-2">
                            <div class="flex gap-2">
                                <input type="text" class="link-title w-1/3 border border-gray-300 rounded-2xl py-3 px-4" placeholder="عنوان">
                                <input type="text" class="link-url w-2/3 border border-gray-300 rounded-2xl py-3 px-4" placeholder="آدرس لینک">
                            </div>
                        </div>
                        <button type="button" onclick="addLinkField()" class="mt-2 text-sm text-indigo-600 hover:underline">+ افزودن لینک دیگر</button>
                    </div>

                    <!-- آدرس‌ها -->
                    <div>
                        <label class="block text-sm font-medium mb-2">آدرس‌ها</label>
                        <div id="addressesContainer" class="space-y-2">
                            <input type="text" class="address-input w-full border border-gray-300 rounded-2xl py-3 px-5" placeholder="آدرس ۱">
                        </div>
                        <button type="button" onclick="addAddressField()" class="mt-2 text-sm text-indigo-600 hover:underline">+ افزودن آدرس دیگر</button>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button onclick="saveBranch()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره شعبه</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    // ==================== Modal افزودن نوع شعبه ====================
    function getBranchTypeOptions(selected = '') {
        return allBranchTypes.map(t => 
            `<option value="${t.name}" ${t.name === selected ? 'selected' : ''}>${t.name}</option>`
        ).join('');
    }


    window.promptAddBranchType = function() {
        const name = prompt('نام نوع شعبه جدید را وارد کنید:');
        if (!name || !name.trim()) return;
        if (allBranchTypes.some(t => t.name === name.trim())) {
            alert('این نوع قبلاً وجود دارد');
            return;
        }
        allBranchTypes.push({ id: Date.now(), name: name.trim() });
        // رفرش سلکت‌ها
        const selects = document.querySelectorAll('#branchType, #editBranchType, #filterBranchType');
        selects.forEach(sel => {
            if (sel) {
                const current = sel.value;
                sel.innerHTML = (sel.id === 'filterBranchType' ? `<option value="">همه انواع شعبه</option>` : '') + 
                    getBranchTypeOptions(current);
            }
        });
        alert('✅ نوع شعبه اضافه شد');
    };


    // توابع کمکی برای فیلدهای چندتایی
    window.addPhoneField = function() {
        const container = document.getElementById('phonesContainer');
        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'phone-input w-full border border-gray-300 rounded-2xl py-3 px-5';
        input.placeholder = 'شماره تماس دیگر';
        container.appendChild(input);
    };

    window.addLinkField = function() {
        const container = document.getElementById('linksContainer');
        const div = document.createElement('div');
        div.className = 'flex gap-2';
        div.innerHTML = `
            <input type="text" class="link-title w-1/3 border border-gray-300 rounded-2xl py-3 px-4" placeholder="عنوان">
            <input type="text" class="link-url w-2/3 border border-gray-300 rounded-2xl py-3 px-4" placeholder="آدرس لینک">
        `;
        container.appendChild(div);
    };

    window.addAddressField = function() {
        const container = document.getElementById('addressesContainer');
        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'address-input w-full border border-gray-300 rounded-2xl py-3 px-5';
        input.placeholder = 'آدرس دیگر';
        container.appendChild(input);
    };

    window.addEditLinkField = function() {
        const container = document.getElementById('editLinksContainer');
        if (!container) return;
        const div = document.createElement('div');
        div.className = 'flex gap-2 mb-2';
        div.innerHTML = `
            <input type="text" class="link-title w-1/3 border border-gray-300 rounded-2xl py-3 px-4" placeholder="عنوان">
            <input type="text" class="link-url w-2/3 border border-gray-300 rounded-2xl py-3 px-4" placeholder="آدرس لینک">
        `;
        container.appendChild(div);
    };

    window.saveBranch = function() {
        const name = document.getElementById('branchName')?.value.trim();
        if (!name) return alert('نام شعبه الزامی است');

        const phones = Array.from(document.querySelectorAll('.phone-input'))
            .map(i => i.value.trim()).filter(v => v);

        const links = [];
        document.querySelectorAll('#linksContainer > div').forEach(div => {
            const title = div.querySelector('.link-title')?.value.trim();
            const url = div.querySelector('.link-url')?.value.trim();
            if (title || url) links.push({ title: title || 'لینک', url: url || '#' });
        });

        const addresses = Array.from(document.querySelectorAll('.address-input'))
            .map(i => i.value.trim()).filter(v => v);

        allBranches.unshift({
            id: Date.now(),
            name,
            type: document.getElementById('branchType').value,
            slogan: document.getElementById('branchSlogan').value.trim(),
            bio: document.getElementById('branchBio').value.trim(),
            manager: document.getElementById('branchManager').value.trim(),
            status: document.getElementById('branchStatus').value,
            phones,
            links,
            addresses,
            classrooms: 0
        });

        filterBranches();
        closeModal();
        alert('✅ شعبه با موفقیت اضافه شد');
    };

    // ==================== جزئیات شعبه ====================
    window.viewBranch = function(id) {
        const b = allBranches.find(x => x.id === id);
        if (!b) return;

        const phonesHtml = (b.phones || []).map(p => `<div class="text-sm">${p}</div>`).join('') || '<span class="text-gray-400">—</span>';
        const linksHtml = (b.links || []).map(l => `<a href="${l.url}" target="_blank" class="text-indigo-600 text-sm hover:underline block">${l.title}</a>`).join('') || '<span class="text-gray-400">—</span>';
        const addressesHtml = (b.addresses || []).map(a => `<div class="text-sm">${a}</div>`).join('') || '<span class="text-gray-400">—</span>';

        document.getElementById('modalContainer').innerHTML = `
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${b.name}</h2>
                        <p class="text-sm text-gray-500 mt-1">کد شعبه: #${b.id} — <span class="text-indigo-600">${b.type}</span></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="editBranch(${b.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                </div>
                
                <div class="p-8 space-y-8">
                    ${b.slogan ? `<p class="text-lg text-indigo-600 italic text-center">«${b.slogan}»</p>` : ''}
                    
                    ${b.bio ? `
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-2">درباره شعبه</h3>
                        <p class="text-gray-600 leading-relaxed">${b.bio}</p>
                    </div>` : ''}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-3">اطلاعات پایه</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">مدیر</span><span>${b.manager || '—'}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span>
                                    <span class="px-3 py-1 rounded-full text-xs ${b.status === 'فعال' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${b.status}</span>
                                </div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تعداد کلاس</span><span>${b.classrooms || 0}</span></div>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-3">شماره‌های تماس</h3>
                            ${phonesHtml}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-3">آدرس‌ها</h3>
                            ${addressesHtml}
                        </div>
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-3">لینک‌ها</h3>
                            ${linksHtml}
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    };

    // ==================== ویرایش شعبه ====================
    window.editBranch = function(id) {
        const b = allBranches.find(x => x.id === id);
        if (!b) return;

        // const typeOptions = branchTypes.map(t => 
        //     `<option value="${t}" ${t === b.type ? 'selected' : ''}>${t}</option>`
        // ).join('');
        const typeOptions = getBranchTypeOptions(b.type);

        const phonesHtml = (b.phones && b.phones.length ? b.phones : ['']).map(p => 
            `<input type="text" class="phone-input w-full border border-gray-300 rounded-2xl py-3 px-5 mb-2" value="${p}">`
        ).join('');

        const linksHtml = (b.links && b.links.length ? b.links : [{title:'', url:''}]).map(l => `
            <div class="flex gap-2 mb-2">
                <input type="text" class="link-title w-1/3 border border-gray-300 rounded-2xl py-3 px-4" value="${l.title || ''}" placeholder="عنوان">
                <input type="text" class="link-url w-2/3 border border-gray-300 rounded-2xl py-3 px-4" value="${l.url || ''}" placeholder="لینک">
            </div>
        `).join('');

        const addressesHtml = (b.addresses && b.addresses.length ? b.addresses : ['']).map(a => 
            `<input type="text" class="address-input w-full border border-gray-300 rounded-2xl py-3 px-5 mb-2" value="${a}">`
        ).join('');

        document.getElementById('modalContainer').innerHTML = `
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <h2 class="text-2xl font-bold">ویرایش شعبه</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">نام شعبه *</label>
                            <input id="editBranchName" type="text" value="${b.name}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">نوع شعبه</label>
                            <select id="editBranchType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${typeOptions}</select>
                            <button type="button" onclick="promptAddBranchType()" class="text-sm text-indigo-600 mt-1">+ نوع جدید</button>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">مدیر شعبه</label>
                            <input id="editBranchManager" type="text" value="${b.manager || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">وضعیت</label>
                            <select id="editBranchStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                <option value="فعال" ${b.status === 'فعال' ? 'selected' : ''}>فعال</option>
                                <option value="غیرفعال" ${b.status === 'غیرفعال' ? 'selected' : ''}>غیرفعال</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">شعار</label>
                        <input id="editBranchSlogan" type="text" value="${b.slogan || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">بیوگرافی</label>
                        <textarea id="editBranchBio" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${b.bio || ''}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">شماره‌های تماس</label>
                        <div id="editPhonesContainer">${phonesHtml}</div>
                        <button type="button" onclick="document.getElementById('editPhonesContainer').insertAdjacentHTML('beforeend', '<input type=\\'text\\' class=\\'phone-input w-full border border-gray-300 rounded-2xl py-3 px-5 mb-2\\' placeholder=\\'شماره جدید\\'>')" class="mt-2 text-sm text-indigo-600">+ افزودن شماره</button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">لینک‌ها</label>
                        <div id="editLinksContainer">${linksHtml}</div>
                        <button type="button" onclick="addEditLinkField()" class="mt-2 text-sm text-indigo-600 hover:underline">+ افزودن لینک دیگر</button>
                    </div>
                    

                    <div>
                        <label class="block text-sm font-medium mb-2">آدرس‌ها</label>
                        <div id="editAddressesContainer">${addressesHtml}</div>
                        <button type="button" onclick="document.getElementById('editAddressesContainer').insertAdjacentHTML('beforeend', '<input type=\\'text\\' class=\\'address-input w-full border border-gray-300 rounded-2xl py-3 px-5 mb-2\\' placeholder=\\'آدرس جدید\\'>')" class="mt-2 text-sm text-indigo-600">+ افزودن آدرس</button>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button onclick="saveEditedBranch(${b.id})" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ذخیره تغییرات</button>
                        <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.saveEditedBranch = function(id) {
        const name = document.getElementById('editBranchName')?.value.trim();
        if (!name) return alert('نام شعبه الزامی است');

        const index = allBranches.findIndex(x => x.id === id);
        if (index === -1) return;

        const phones = Array.from(document.querySelectorAll('#editPhonesContainer .phone-input'))
            .map(i => i.value.trim()).filter(v => v);

        const links = [];
        document.querySelectorAll('#editLinksContainer > div').forEach(div => {
            const title = div.querySelector('.link-title')?.value.trim();
            const url = div.querySelector('.link-url')?.value.trim();
            if (title || url) links.push({ title: title || 'لینک', url: url || '#' });
        });

        const addresses = Array.from(document.querySelectorAll('#editAddressesContainer .address-input'))
            .map(i => i.value.trim()).filter(v => v);

        allBranches[index] = {
            ...allBranches[index],
            name,
            type: document.getElementById('editBranchType').value,
            manager: document.getElementById('editBranchManager').value.trim(),
            status: document.getElementById('editBranchStatus').value,
            slogan: document.getElementById('editBranchSlogan').value.trim(),
            bio: document.getElementById('editBranchBio').value.trim(),
            phones,
            links,
            addresses
        };

        filterBranches();
        closeModal();
        alert('✅ تغییرات ذخیره شد');
    };

    // ==================== Init ====================
    (function() {
        setTimeout(() => {
            if (document.getElementById('branchesCards')) {
                filterBranches();
            }
        }, 150);
        renderBranchTypeFilter();
    })();

*/


// ==================== انواع شعبه ====================
let allBranchTypes = [
    { id: 1, name: "موسیقی" },
    { id: 2, name: "ادبیات" },
    { id: 3, name: "شعر" },
    { id: 4, name: "نقاشی" },
    { id: 5, name: "خیاطی" },
    { id: 6, name: "سایر" }
];

// ==================== استان‌ها و شهرها ====================
const iranProvinces = [
    "تهران", "البرز", "اصفهان", "فارس", "خراسان رضوی", "آذربایجان شرقی", "آذربایجان غربی",
    "خوزستان", "مازندران", "گیلان", "کرمان", "سیستان و بلوچستان", "همدان", "کرمانشاه",
    "لرستان", "کردستان", "یزد", "مرکزی", "قم", "قزوین", "زنجان", "اردبیل", "بوشهر",
    "هرمزگان", "چهارمحال و بختیاری", "کهگیلویه و بویراحمد", "ایلام", "سمنان", "گلستان", "خراسان شمالی", "خراسان جنوبی"
];

const tehranCities = [
    "تهران", "شمیرانات", "ری", "اسلامشهر", "شهریار", "قدس", "ملارد", "پردیس", "دماوند", "فیروزکوه", "ورامین", "پاکدشت", "رباط‌کریم"
];

const platformOptions = [
    { value: "instagram", label: "اینستاگرام" },
    { value: "whats-app", label: "واتساپ" },
    { value: "youtube", label: "یوتیوب" },
    { value: "spotify", label: "اسپاتیفای" },
    { value: "soundcloud", label: "ساوندکلود" },
    { value: "telegram", label: "تلگرام" },
    { value: "linkedin", label: "لینکدین" },
    { value: "x", label: "ایکس (توییتر)" },
    { value: "website", label: "وب‌سایت" },
    { value: "zoom", label: "زوم" },
    { value: "google-meet", label: "گوگل میت" },
    { value: "skype", label: "اسکایپ" },
    { value: "custom", label: "سفارشی" },
    { value: "other", label: "سایر" }
];

const priorityOptions = [
    { value: "primary", label: "اصلی" },
    { value: "secondary", label: "فرعی" },
    { value: "emergency", label: "اضطراری" },
    { value: "ledger", label: "دفتر" },
    { value: "support", label: "پشتیبانی" },
    { value: "other", label: "سایر" }
];

// ==================== داده شعبه‌ها ====================
let allBranches = [
    {
        id: 1,
        name: "شعبه مرکزی",
        type: "موسیقی",
        slogan: "آموزش با عشق، اجرا با افتخار",
        bio: "شعبه اصلی آموزشگاه با تمرکز بر سازهای کلاسیک و ایرانی.",
        manager: "آقای رضایی",
        classrooms: 8,
        status: "فعال",
        phones: [
            { number: "۰۲۱-۸۸۷۷۶۶۵۵", priority: "primary", is_main: true },
            { number: "۰۹۱۲۱۲۳۴۵۶۷", priority: "secondary", is_main: false }
        ],
        links: [
            { title: "کلاس آنلاین", url: "https://meet.example.com/central", mode: "social", platform: "google-meet", priority: "primary", is_main: true },
            { title: "اینستاگرام", url: "https://instagram.com/musicacademy", mode: "social", platform: "instagram", priority: "secondary", is_main: false }
        ],
        addresses: [
            { province: "تهران", city: "تهران", address: "خیابان ولیعصر، پلاک ۱۲۳", postal_code: "۱۴۱۵۷۴۳۴۵۶", lat: "35.7219", lng: "51.3347", is_main: true },
            { province: "تهران", city: "تهران", address: "خیابان طالقانی، پلاک ۴۵", postal_code: "", lat: "", lng: "", is_main: false }
        ]
    },
    {
        id: 2,
        name: "شعبه ونک",
        type: "موسیقی",
        slogan: "صدای آینده از اینجا شروع می‌شود",
        bio: "شعبه تخصصی گیتار و آواز.",
        manager: "خانم موسوی",
        classrooms: 5,
        status: "فعال",
        phones: [{ number: "۰۲۱-۸۸۶۶۵۵۴۴", priority: "primary", is_main: true }],
        links: [{ title: "کلاس آنلاین", url: "https://meet.example.com/vanak", mode: "social", platform: "zoom", priority: "primary", is_main: true }],
        addresses: [{ province: "تهران", city: "تهران", address: "میدان ونک، برج آسمان، طبقه ۳", postal_code: "۱۹۹۴۷۶۳۴۵۶", lat: "35.7575", lng: "51.4100", is_main: true }]
    },
    {
        id: 3,
        name: "شعبه سعادت‌آباد",
        type: "نقاشی",
        slogan: "رنگ‌ها را زندگی کنید",
        bio: "شعبه هنرهای تجسمی.",
        manager: "آقای بهرامی",
        classrooms: 6,
        status: "فعال",
        phones: [
            { number: "۰۲۱-۲۲۱۱۰۰۳۳", priority: "primary", is_main: true },
            { number: "۰۹۱۹۸۷۶۵۴۳۲", priority: "support", is_main: false }
        ],
        links: [],
        addresses: [{ province: "تهران", city: "تهران", address: "سعادت‌آباد، میدان کاج", postal_code: "", lat: "", lng: "", is_main: true }]
    },
    {
        id: 4,
        name: "شعبه کرج",
        type: "خیاطی",
        slogan: "طراحی و دوخت حرفه‌ای",
        bio: "آموزش خیاطی در کرج.",
        manager: "خانم کریمی",
        classrooms: 4,
        status: "فعال",
        phones: [{ number: "۰۲۶-۳۴۵۶۷۸۹۰", priority: "primary", is_main: true }],
        links: [{ title: "واتساپ پشتیبانی", url: "https://wa.me/98912xxxxxxx", mode: "social", platform: "whats-app", priority: "support", is_main: true }],
        addresses: [{ province: "البرز", city: "کرج", address: "مهرویلا، خیابان شهید بهشتی", postal_code: "", lat: "35.8400", lng: "50.9391", is_main: true }]
    }
];

let filteredBranches = [...allBranches];

// ==================== توابع کمکی ====================
function getBranchTypeOptions(selected = '') {
    return allBranchTypes.map(t =>
        `<option value="${t.name}" ${t.name === selected ? 'selected' : ''}>${t.name}</option>`
    ).join('');
}

function getPriorityOptions(selected = 'primary') {
    return priorityOptions.map(p =>
        `<option value="${p.value}" ${p.value === selected ? 'selected' : ''}>${p.label}</option>`
    ).join('');
}

function getPlatformOptions(selected = 'website') {
    return platformOptions.map(p =>
        `<option value="${p.value}" ${p.value === selected ? 'selected' : ''}>${p.label}</option>`
    ).join('');
}

function getProvinceOptions(selected = 'تهران') {
    return iranProvinces.map(p =>
        `<option value="${p}" ${p === selected ? 'selected' : ''}>${p}</option>`
    ).join('');
}

function getCityOptions(selected = 'تهران') {
    return tehranCities.map(c =>
        `<option value="${c}" ${c === selected ? 'selected' : ''}>${c}</option>`
    ).join('');
}

window.renderBranchTypeFilter = function() {
    const select = document.getElementById('filterBranchType');
    if (!select) return;
    select.innerHTML = `<option value="">همه انواع شعبه</option>` + getBranchTypeOptions();
};

window.promptAddBranchType = function() {
    const name = prompt('نام نوع شعبه جدید را وارد کنید:');
    if (!name || !name.trim()) return;
    if (allBranchTypes.some(t => t.name === name.trim())) {
        alert('این نوع قبلاً وجود دارد');
        return;
    }
    allBranchTypes.push({ id: Date.now(), name: name.trim() });
    document.querySelectorAll('#branchType, #editBranchType, #filterBranchType').forEach(sel => {
        if (sel) {
            const current = sel.value;
            sel.innerHTML = (sel.id === 'filterBranchType' ? `<option value="">همه انواع شعبه</option>` : '') + getBranchTypeOptions(current);
        }
    });
    alert('✅ نوع شعبه اضافه شد');
};

// ==================== رندر کارت‌ها ====================
window.renderBranches = function(list = filteredBranches) {
    const container = document.getElementById('branchesCards');
    if (!container) return;

    if (list.length === 0) {
        container.innerHTML = `<p class="col-span-full text-center text-gray-400 py-16">شعبه‌ای یافت نشد</p>`;
        return;
    }

    container.innerHTML = list.map(b => {
        const mainAddress = (b.addresses && b.addresses.length)
            ? (b.addresses.find(a => a.is_main) || b.addresses[0])
            : null;
        const addressText = mainAddress
            ? `${mainAddress.province || ''}، ${mainAddress.city || ''}، ${mainAddress.address || ''}`
            : '—';

        const mainPhone = (b.phones && b.phones.length)
            ? (b.phones.find(p => p.is_main) || b.phones[0])
            : null;

        return `
        <div class="bg-white rounded-3xl p-6 shadow card-hover">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-xl font-bold">${b.name}</h3>
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">${b.type}</span>
                </div>
                <span class="px-3 py-1 rounded-full text-xs ${b.status === 'فعال' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${b.status}</span>
            </div>
            ${b.slogan ? `<p class="text-sm text-indigo-600 italic mb-3">«${b.slogan}»</p>` : ''}
            <div class="space-y-2 text-sm mb-5">
                <div class="flex justify-between"><span class="text-gray-500">مدیر</span><span>${b.manager || '—'}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">تلفن اصلی</span><span>${mainPhone ? mainPhone.number : '—'}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">آدرس اصلی</span><span class="text-left max-w-[60%] truncate" title="${addressText}">${addressText}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">تعداد کلاس</span><span class="font-medium">${b.classrooms || 0}</span></div>
            </div>
            <div class="flex gap-2">
                <button onclick="viewBranch(${b.id})" class="flex-1 border border-indigo-200 text-indigo-600 py-2 rounded-xl text-sm hover:bg-indigo-50">جزئیات</button>
                <button onclick="editBranch(${b.id})" class="flex-1 bg-indigo-600 text-white py-2 rounded-xl text-sm hover:bg-indigo-700">ویرایش</button>
            </div>
        </div>`;
    }).join('');
};

window.filterBranches = function() {
    const search = (document.getElementById('branchSearch')?.value || '').trim().toLowerCase();
    const type = document.getElementById('filterBranchType')?.value || '';
    filteredBranches = allBranches.filter(b => {
        const matchSearch = !search || b.name.toLowerCase().includes(search) || (b.manager && b.manager.toLowerCase().includes(search));
        const matchType = !type || b.type === type;
        return matchSearch && matchType;
    });
    renderBranches(filteredBranches);
};

window.exportBranchesToExcel = function() {
    const data = filteredBranches.length ? filteredBranches : allBranches;
    let csv = '\uFEFF';
    csv += 'ردیف,نام,نوع,مدیر,وضعیت,شعار,تلفن اصلی,آدرس اصلی,تعداد کلاس\n';
    data.forEach((b, i) => {
        const mainPhone = (b.phones && b.phones.find(p => p.is_main)) || (b.phones && b.phones[0]) || {};
        const mainAddr = (b.addresses && b.addresses.find(a => a.is_main)) || (b.addresses && b.addresses[0]) || {};
        const addr = `${mainAddr.province || ''} ${mainAddr.city || ''} ${mainAddr.address || ''}`;
        csv += `${i+1},"${b.name}","${b.type}","${b.manager||''}","${b.status}","${b.slogan||''}","${mainPhone.number||''}","${addr}",${b.classrooms||0}\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `شعبه‌ها_${new Date().toLocaleDateString('fa-IR')}.csv`;
    link.click();
};

// ==================== فیلدهای پویا ====================
window.addPhoneField = function(containerId = 'phonesContainer') {
    const container = document.getElementById(containerId);
    if (!container) return;
    const div = document.createElement('div');
    div.className = 'border border-gray-200 rounded-2xl p-4 space-y-3 mb-3';
    div.innerHTML = `
        <input type="text" class="phone-number w-full border border-gray-300 rounded-2xl py-3 px-5" placeholder="شماره تماس">
        <div class="grid grid-cols-2 gap-3">
            <select class="phone-priority border border-gray-300 rounded-2xl py-3 px-4">${getPriorityOptions()}</select>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" class="phone-is-main"> اصلی</label>
        </div>
    `;
    container.appendChild(div);
};

window.addLinkField = function(containerId = 'linksContainer') {
    const container = document.getElementById(containerId);
    if (!container) return;
    const div = document.createElement('div');
    div.className = 'border border-gray-200 rounded-2xl p-4 space-y-3 mb-3';
    div.innerHTML = `
        <input type="text" class="link-title w-full border border-gray-300 rounded-2xl py-3 px-5" placeholder="عنوان لینک">
        <input type="text" class="link-url w-full border border-gray-300 rounded-2xl py-3 px-5" placeholder="آدرس URL">
        <div class="grid grid-cols-2 gap-3">
            <select class="link-mode border border-gray-300 rounded-2xl py-3 px-4">
                <option value="social">شبکه اجتماعی / کلاس</option>
                <option value="email">ایمیل</option>
            </select>
            <select class="link-platform border border-gray-300 rounded-2xl py-3 px-4">${getPlatformOptions()}</select>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <select class="link-priority border border-gray-300 rounded-2xl py-3 px-4">${getPriorityOptions()}</select>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" class="link-is-main"> اصلی</label>
        </div>
    `;
    container.appendChild(div);
};

window.addAddressField = function(containerId = 'addressesContainer') {
    const container = document.getElementById(containerId);
    if (!container) return;
    const div = document.createElement('div');
    div.className = 'border border-gray-200 rounded-2xl p-4 space-y-3 mb-3 address-block';
    div.innerHTML = `
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-500 mb-1 block">استان</label>
                <select class="addr-province border border-gray-300 rounded-2xl py-3 px-4 w-full">${getProvinceOptions()}</select>
            </div>
            <div>
                <label class="text-xs text-gray-500 mb-1 block">شهر</label>
                <select class="addr-city border border-gray-300 rounded-2xl py-3 px-4 w-full">${getCityOptions()}</select>
            </div>
        </div>
        <div>
            <label class="text-xs text-gray-500 mb-1 block">ادامه آدرس</label>
            <input type="text" class="addr-address w-full border border-gray-300 rounded-2xl py-3 px-5" placeholder="خیابان، پلاک، واحد...">
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="text-xs text-gray-500 mb-1 block">کد پستی</label>
                <input type="text" class="addr-postal w-full border border-gray-300 rounded-2xl py-3 px-4" placeholder="کد پستی">
            </div>
            <div>
                <label class="text-xs text-gray-500 mb-1 block">عرض جغرافیایی</label>
                <input type="text" class="addr-lat w-full border border-gray-300 rounded-2xl py-3 px-4" placeholder="Lat">
            </div>
            <div>
                <label class="text-xs text-gray-500 mb-1 block">طول جغرافیایی</label>
                <input type="text" class="addr-lng w-full border border-gray-300 rounded-2xl py-3 px-4" placeholder="Lng">
            </div>
        </div>
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" class="addr-is-main"> آدرس اصلی</label>
            <button type="button" onclick="openGoogleMapsPicker(this)" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                <i class="fas fa-map-marker-alt"></i> انتخاب روی نقشه
            </button>
        </div>
    `;
    container.appendChild(div);
};

window.openGoogleMapsPicker = function(btn) {
    const block = btn.closest('.address-block');
    if (!block) return;
    const latInput = block.querySelector('.addr-lat');
    const lngInput = block.querySelector('.addr-lng');
    // باز کردن گوگل مپ در تب جدید (کاربر می‌تواند مختصات را کپی کند)
    const lat = latInput.value || '35.6892';
    const lng = lngInput.value || '51.3890';
    window.open(`https://www.google.com/maps/@${lat},${lng},15z`, '_blank');
    alert('پس از انتخاب موقعیت در گوگل مپ، مختصات (Lat, Lng) را از نوار آدرس یا کلیک راست کپی کرده و در فیلدها وارد کنید.');
};

// ==================== افزودن شعبه ====================
window.openAddBranchModal = function() {
    if (!document.getElementById('modalContainer')) {
        alert('modalContainer پیدا نشد!');
        return;
    }

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl z-10">
                <h2 class="text-2xl font-bold">افزودن شعبه جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
            </div>
            <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">نام شعبه *</label>
                        <input id="branchName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع شعبه *</label>
                        <select id="branchType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${getBranchTypeOptions()}</select>
                        <button type="button" onclick="promptAddBranchType()" class="text-sm text-indigo-600 mt-1">+ نوع جدید</button>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">مدیر شعبه</label>
                        <input id="branchManager" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">وضعیت</label>
                        <select id="branchStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            <option value="فعال">فعال</option>
                            <option value="غیرفعال">غیرفعال</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعار</label>
                    <input id="branchSlogan" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">بیوگرافی</label>
                    <textarea id="branchBio" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                </div>

                <!-- تلفن‌ها -->
                <div>
                    <label class="block text-sm font-medium mb-2">شماره‌های تماس</label>
                    <div id="phonesContainer"></div>
                    <button type="button" onclick="addPhoneField()" class="mt-2 text-sm text-indigo-600 hover:underline">+ افزودن شماره</button>
                </div>

                <!-- لینک‌ها -->
                <div>
                    <label class="block text-sm font-medium mb-2">لینک‌ها</label>
                    <div id="linksContainer"></div>
                    <button type="button" onclick="addLinkField()" class="mt-2 text-sm text-indigo-600 hover:underline">+ افزودن لینک</button>
                </div>

                <!-- آدرس‌ها -->
                <div>
                    <label class="block text-sm font-medium mb-2">آدرس‌ها</label>
                    <div id="addressesContainer"></div>
                    <button type="button" onclick="addAddressField()" class="mt-2 text-sm text-indigo-600 hover:underline">+ افزودن آدرس</button>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="saveBranch()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-medium">ذخیره شعبه</button>
                    <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;

    // یک فیلد پیش‌فرض برای هر کدام
    addPhoneField();
    addLinkField();
    addAddressField();
};

window.saveBranch = function() {
    const name = document.getElementById('branchName')?.value.trim();
    if (!name) return alert('نام شعبه الزامی است');

    // جمع‌آوری تلفن‌ها
    const phones = [];
    document.querySelectorAll('#phonesContainer > div').forEach(div => {
        const number = div.querySelector('.phone-number')?.value.trim();
        if (number) {
            phones.push({
                number,
                priority: div.querySelector('.phone-priority')?.value || 'primary',
                is_main: div.querySelector('.phone-is-main')?.checked || false
            });
        }
    });

    // جمع‌آوری لینک‌ها
    const links = [];
    document.querySelectorAll('#linksContainer > div').forEach(div => {
        const title = div.querySelector('.link-title')?.value.trim();
        const url = div.querySelector('.link-url')?.value.trim();
        if (title || url) {
            links.push({
                title: title || 'لینک',
                url: url || '#',
                mode: div.querySelector('.link-mode')?.value || 'social',
                platform: div.querySelector('.link-platform')?.value || 'other',
                priority: div.querySelector('.link-priority')?.value || 'secondary',
                is_main: div.querySelector('.link-is-main')?.checked || false
            });
        }
    });

    // جمع‌آوری آدرس‌ها
    const addresses = [];
    document.querySelectorAll('#addressesContainer > div').forEach(div => {
        const address = div.querySelector('.addr-address')?.value.trim();
        if (address || div.querySelector('.addr-province')?.value) {
            addresses.push({
                province: div.querySelector('.addr-province')?.value || '',
                city: div.querySelector('.addr-city')?.value || '',
                address: address || '',
                postal_code: div.querySelector('.addr-postal')?.value.trim() || '',
                lat: div.querySelector('.addr-lat')?.value.trim() || '',
                lng: div.querySelector('.addr-lng')?.value.trim() || '',
                is_main: div.querySelector('.addr-is-main')?.checked || false
            });
        }
    });

    allBranches.unshift({
        id: Date.now(),
        name,
        type: document.getElementById('branchType').value,
        slogan: document.getElementById('branchSlogan').value.trim(),
        bio: document.getElementById('branchBio').value.trim(),
        manager: document.getElementById('branchManager').value.trim(),
        status: document.getElementById('branchStatus').value,
        phones,
        links,
        addresses,
        classrooms: 0
    });

    filterBranches();
    closeModal();
    alert('✅ شعبه اضافه شد');
};

// ==================== جزئیات ====================
window.viewBranch = function(id) {
    const b = allBranches.find(x => x.id === id);
    if (!b) return;

    const phonesHtml = (b.phones || []).map(p => `
        <div class="flex justify-between text-sm border-b pb-2">
            <span>${p.number}</span>
            <span class="text-xs text-gray-500">${p.priority}${p.is_main ? ' (اصلی)' : ''}</span>
        </div>
    `).join('') || '<span class="text-gray-400">—</span>';

    const linksHtml = (b.links || []).map(l => `
        <div class="text-sm border-b pb-2">
            <a href="${l.url}" target="_blank" class="text-indigo-600 hover:underline">${l.title}</a>
            <span class="text-xs text-gray-400 mr-2">${l.platform} | ${l.priority}${l.is_main ? ' (اصلی)' : ''}</span>
        </div>
    `).join('') || '<span class="text-gray-400">—</span>';

    const addressesHtml = (b.addresses || []).map(a => `
        <div class="text-sm border-b pb-3 mb-2">
            <div>${a.province}، ${a.city}، ${a.address}</div>
            <div class="text-xs text-gray-400 mt-1">
                کدپستی: ${a.postal_code || '—'} | 
                Lat: ${a.lat || '—'} | Lng: ${a.lng || '—'}
                ${a.is_main ? ' | <span class="text-indigo-600">اصلی</span>' : ''}
            </div>
        </div>
    `).join('') || '<span class="text-gray-400">—</span>';

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <div>
                    <h2 class="text-2xl font-bold">${b.name}</h2>
                    <p class="text-sm text-gray-500 mt-1">#${b.id} — <span class="text-indigo-600">${b.type}</span></p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editBranch(${b.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-8">
                ${b.slogan ? `<p class="text-lg text-indigo-600 italic text-center">«${b.slogan}»</p>` : ''}
                ${b.bio ? `<div><h3 class="font-semibold text-indigo-700 mb-2">درباره شعبه</h3><p class="text-gray-600">${b.bio}</p></div>` : ''}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-3">اطلاعات پایه</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">مدیر</span><span>${b.manager || '—'}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="px-3 py-1 rounded-full text-xs ${b.status==='فعال'?'bg-green-100 text-green-700':'bg-red-100 text-red-700'}">${b.status}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">کلاس‌ها</span><span>${b.classrooms||0}</span></div>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-3">تلفن‌ها</h3>
                        ${phonesHtml}
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-3">آدرس‌ها</h3>
                        ${addressesHtml}
                    </div>
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-3">لینک‌ها</h3>
                        ${linksHtml}
                    </div>
                </div>
            </div>
        </div>
    </div>`;
};

// ==================== ویرایش ====================
window.editBranch = function(id) {
    const b = allBranches.find(x => x.id === id);
    if (!b) return;

    // ساخت فیلدهای تلفن موجود
    let phonesHtml = '';
    (b.phones && b.phones.length ? b.phones : [{number:'', priority:'primary', is_main:false}]).forEach(p => {
        phonesHtml += `
        <div class="border border-gray-200 rounded-2xl p-4 space-y-3 mb-3">
            <input type="text" class="phone-number w-full border border-gray-300 rounded-2xl py-3 px-5" value="${p.number || ''}">
            <div class="grid grid-cols-2 gap-3">
                <select class="phone-priority border border-gray-300 rounded-2xl py-3 px-4">${getPriorityOptions(p.priority)}</select>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" class="phone-is-main" ${p.is_main ? 'checked' : ''}> اصلی</label>
            </div>
        </div>`;
    });

    // لینک‌ها
    let linksHtml = '';
    (b.links && b.links.length ? b.links : [{title:'', url:'', mode:'social', platform:'website', priority:'secondary', is_main:false}]).forEach(l => {
        linksHtml += `
        <div class="border border-gray-200 rounded-2xl p-4 space-y-3 mb-3">
            <input type="text" class="link-title w-full border border-gray-300 rounded-2xl py-3 px-5" value="${l.title || ''}" placeholder="عنوان">
            <input type="text" class="link-url w-full border border-gray-300 rounded-2xl py-3 px-5" value="${l.url || ''}" placeholder="URL">
            <div class="grid grid-cols-2 gap-3">
                <select class="link-mode border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="social" ${l.mode==='social'?'selected':''}>شبکه اجتماعی / کلاس</option>
                    <option value="email" ${l.mode==='email'?'selected':''}>ایمیل</option>
                </select>
                <select class="link-platform border border-gray-300 rounded-2xl py-3 px-4">${getPlatformOptions(l.platform)}</select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <select class="link-priority border border-gray-300 rounded-2xl py-3 px-4">${getPriorityOptions(l.priority)}</select>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" class="link-is-main" ${l.is_main?'checked':''}> اصلی</label>
            </div>
        </div>`;
    });

    // آدرس‌ها
    let addressesHtml = '';
    (b.addresses && b.addresses.length ? b.addresses : [{province:'تهران', city:'تهران', address:'', postal_code:'', lat:'', lng:'', is_main:false}]).forEach(a => {
        addressesHtml += `
        <div class="border border-gray-200 rounded-2xl p-4 space-y-3 mb-3 address-block">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">استان</label>
                    <select class="addr-province border border-gray-300 rounded-2xl py-3 px-4 w-full">${getProvinceOptions(a.province)}</select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">شهر</label>
                    <select class="addr-city border border-gray-300 rounded-2xl py-3 px-4 w-full">${getCityOptions(a.city)}</select>
                </div>
            </div>
            <div>
                <label class="text-xs text-gray-500 mb-1 block">ادامه آدرس</label>
                <input type="text" class="addr-address w-full border border-gray-300 rounded-2xl py-3 px-5" value="${a.address || ''}">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">کد پستی</label>
                    <input type="text" class="addr-postal w-full border border-gray-300 rounded-2xl py-3 px-4" value="${a.postal_code || ''}">
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">عرض جغرافیایی</label>
                    <input type="text" class="addr-lat w-full border border-gray-300 rounded-2xl py-3 px-4" value="${a.lat || ''}">
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">طول جغرافیایی</label>
                    <input type="text" class="addr-lng w-full border border-gray-300 rounded-2xl py-3 px-4" value="${a.lng || ''}">
                </div>
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" class="addr-is-main" ${a.is_main?'checked':''}> آدرس اصلی</label>
                <button type="button" onclick="openGoogleMapsPicker(this)" class="text-sm text-indigo-600 hover:underline"><i class="fas fa-map-marker-alt"></i> انتخاب روی نقشه</button>
            </div>
        </div>`;
    });

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl z-10">
                <h2 class="text-2xl font-bold">ویرایش شعبه</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">نام شعبه *</label>
                        <input id="editBranchName" type="text" value="${b.name}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع شعبه</label>
                        <select id="editBranchType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${getBranchTypeOptions(b.type)}</select>
                        <button type="button" onclick="promptAddBranchType()" class="text-sm text-indigo-600 mt-1">+ نوع جدید</button>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">مدیر</label>
                        <input id="editBranchManager" type="text" value="${b.manager || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">وضعیت</label>
                        <select id="editBranchStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            <option value="فعال" ${b.status==='فعال'?'selected':''}>فعال</option>
                            <option value="غیرفعال" ${b.status==='غیرفعال'?'selected':''}>غیرفعال</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعار</label>
                    <input id="editBranchSlogan" type="text" value="${b.slogan || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">بیوگرافی</label>
                    <textarea id="editBranchBio" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${b.bio || ''}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">شماره‌های تماس</label>
                    <div id="editPhonesContainer">${phonesHtml}</div>
                    <button type="button" onclick="addPhoneField('editPhonesContainer')" class="mt-2 text-sm text-indigo-600">+ افزودن شماره</button>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">لینک‌ها</label>
                    <div id="editLinksContainer">${linksHtml}</div>
                    <button type="button" onclick="addLinkField('editLinksContainer')" class="mt-2 text-sm text-indigo-600">+ افزودن لینک</button>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">آدرس‌ها</label>
                    <div id="editAddressesContainer">${addressesHtml}</div>
                    <button type="button" onclick="addAddressField('editAddressesContainer')" class="mt-2 text-sm text-indigo-600">+ افزودن آدرس</button>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="saveEditedBranch(${b.id})" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ذخیره تغییرات</button>
                    <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedBranch = function(id) {
    const name = document.getElementById('editBranchName')?.value.trim();
    if (!name) return alert('نام شعبه الزامی است');

    const index = allBranches.findIndex(x => x.id === id);
    if (index === -1) return;

    const phones = [];
    document.querySelectorAll('#editPhonesContainer > div').forEach(div => {
        const number = div.querySelector('.phone-number')?.value.trim();
        if (number) phones.push({
            number,
            priority: div.querySelector('.phone-priority')?.value || 'primary',
            is_main: div.querySelector('.phone-is-main')?.checked || false
        });
    });

    const links = [];
    document.querySelectorAll('#editLinksContainer > div').forEach(div => {
        const title = div.querySelector('.link-title')?.value.trim();
        const url = div.querySelector('.link-url')?.value.trim();
        if (title || url) links.push({
            title: title || 'لینک',
            url: url || '#',
            mode: div.querySelector('.link-mode')?.value || 'social',
            platform: div.querySelector('.link-platform')?.value || 'other',
            priority: div.querySelector('.link-priority')?.value || 'secondary',
            is_main: div.querySelector('.link-is-main')?.checked || false
        });
    });

    const addresses = [];
    document.querySelectorAll('#editAddressesContainer > div').forEach(div => {
        const address = div.querySelector('.addr-address')?.value.trim();
        addresses.push({
            province: div.querySelector('.addr-province')?.value || '',
            city: div.querySelector('.addr-city')?.value || '',
            address: address || '',
            postal_code: div.querySelector('.addr-postal')?.value.trim() || '',
            lat: div.querySelector('.addr-lat')?.value.trim() || '',
            lng: div.querySelector('.addr-lng')?.value.trim() || '',
            is_main: div.querySelector('.addr-is-main')?.checked || false
        });
    });

    allBranches[index] = {
        ...allBranches[index],
        name,
        type: document.getElementById('editBranchType').value,
        manager: document.getElementById('editBranchManager').value.trim(),
        status: document.getElementById('editBranchStatus').value,
        slogan: document.getElementById('editBranchSlogan').value.trim(),
        bio: document.getElementById('editBranchBio').value.trim(),
        phones,
        links,
        addresses
    };

    filterBranches();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

// ==================== Init ====================
(function() {
    setTimeout(() => {
        renderBranchTypeFilter();
        if (document.getElementById('branchesCards')) filterBranches();
    }, 150);
})();