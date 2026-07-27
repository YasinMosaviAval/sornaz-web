if (typeof iranProvinces === 'undefined') {
    var iranProvinces = ["تهران","البرز","اصفهان","فارس","خراسان رضوی","آذربایجان شرقی","خوزستان","مازندران","گیلان","کرمان","همدان","یزد","قم","قزوین","زنجان","اردبیل","بوشهر","هرمزگان","سمنان","گلستان"];
}
if (typeof tehranCities === 'undefined') {
    var tehranCities = ["تهران","شمیرانات","ری","اسلامشهر","شهریار","قدس","ملارد","پردیس","دماوند","ورامین","پاکدشت"];
}

// برای سازگاری با بقیه کد
var iranProvinces = window.iranProvinces;
var tehranCities = window.tehranCities;

let allExperiences = [
    { id: 1, title: "تدریس پیانو پیشرفته", organization: "هنرستان موسیقی تهران", summary: "تدریس خصوصی و گروهی", description: "برگزاری کلاس‌های پیانو برای سطوح متوسط و پیشرفته به مدت ۵ سال.", start_date: "۱۳۹۸/۰۷/۰۱", end_date: "۱۴۰۳/۰۶/۳۱", branchId: 1, branchName: "شعبه مرکزی",
      address: { province: "تهران", city: "تهران", address: "خیابان ولیعصر، پلاک ۱۰۰", postal_code: "۱۴۱۵۷۴۳۴۵۶", lat: "35.7219", lng: "51.3347" } },
    { id: 2, title: "مدیریت گروه کر", organization: "مرکز فرهنگی ونک", summary: "رهبری گروه کر", description: "تشکیل و رهبری گروه کر کودکان و نوجوانان.", start_date: "۱۴۰۰/۰۱/۱۵", end_date: null, branchId: 2, branchName: "شعبه ونک",
      address: { province: "تهران", city: "تهران", address: "میدان ونک", postal_code: "", lat: "35.7575", lng: "51.4100" } },
    { id: 3, title: "کارگاه نقاشی کودکان", organization: "خانه فرهنگ سعادت‌آباد", summary: "آموزش نقاشی", description: "برگزاری کارگاه‌های فصلی نقاشی برای کودکان.", start_date: "۱۴۰۱/۰۴/۰۱", end_date: "۱۴۰۲/۱۲/۲۹", branchId: 3, branchName: "شعبه سعادت‌آباد",
      address: { province: "تهران", city: "تهران", address: "سعادت‌آباد، میدان کاج", postal_code: "", lat: "", lng: "" } }
];

let currentExpBranch = 'all';

window.renderExperiencesBranchTabs = function() {
    const container = document.getElementById('experiencesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.exp-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'exp-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterExperiencesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterExperiencesByBranch = function(branchId) {
    currentExpBranch = branchId;
    document.querySelectorAll('.exp-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.exp-branch-tab');
    if (branchId === 'all') {
        if (tabs[0]) { tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600'); tabs[0].classList.remove('border-gray-200'); }
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    renderExperiencesTable();
};

window.renderExperiencesTable = function() {
    const tbody = document.querySelector('#experiencesTable tbody');
    if (!tbody) return;
    const list = currentExpBranch === 'all' ? allExperiences : allExperiences.filter(e => e.branchId == currentExpBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">تجربه‌ای یافت نشد</td></tr>`
        : list.map(e => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${e.title}</td>
                <td class="py-4 px-5">${e.organization}</td>
                <td class="py-4 px-5">${e.start_date || '—'}</td>
                <td class="py-4 px-5">${e.end_date || 'تاکنون'}</td>
                <td class="py-4 px-5">${e.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewExperience(${e.id})" class="text-indigo-600 hover:underline text-sm ml-3">جزئیات</button>
                    <button onclick="editExperience(${e.id})" class="text-indigo-600 hover:underline text-sm ml-3">ویرایش</button>
                    <button onclick="deleteExperience(${e.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            </tr>`).join('');
};

function getProvinceOptions(selected = 'تهران') {
    const list = window.iranProvinces || iranProvinces || [];
    return list.map(p => `<option value="${p}" ${p === selected ? 'selected' : ''}>${p}</option>`).join('');
}
function getCityOptions(selected = 'تهران') {
    const list = window.tehranCities || tehranCities || [];
    return list.map(c => `<option value="${c}" ${c === selected ? 'selected' : ''}>${c}</option>`).join('');
}

window.openAddExperienceModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">افزودن تجربه</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">عنوان *</label>
                        <input id="expTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">سازمان *</label>
                        <input id="expOrg" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">خلاصه</label>
                    <input id="expSummary" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="expDesc" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ شروع</label>
                        <input id="expStart" type="text" placeholder="۱۴۰۰/۰۱/۰۱" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ پایان</label>
                        <input id="expEnd" type="text" placeholder="خالی = تاکنون" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="expBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <!-- آدرس -->
                <div class="border border-gray-200 rounded-2xl p-4 space-y-3 address-block">
                    <h4 class="font-medium text-indigo-700">آدرس مرتبط</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">استان</label>
                            <select id="expProvince" class="addr-province border border-gray-300 rounded-2xl py-3 px-4 w-full">${getProvinceOptions()}</select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">شهر</label>
                            <select id="expCity" class="addr-city border border-gray-300 rounded-2xl py-3 px-4 w-full">${getCityOptions()}</select>
                        </div>
                    </div>
                    <input id="expAddress" type="text" placeholder="ادامه آدرس" class="addr-address w-full border border-gray-300 rounded-2xl py-3 px-5">
                    <div class="grid grid-cols-3 gap-3">
                        <input id="expPostal" type="text" placeholder="کد پستی" class="addr-postal border border-gray-300 rounded-2xl py-3 px-4">
                        <input id="expLat" type="text" placeholder="عرض جغرافیایی" class="addr-lat border border-gray-300 rounded-2xl py-3 px-4">
                        <input id="expLng" type="text" placeholder="طول جغرافیایی" class="addr-lng border border-gray-300 rounded-2xl py-3 px-4">
                    </div>
                    <button type="button" onclick="openGoogleMapsPicker(this)" class="text-sm text-indigo-600 hover:underline">
                        <i class="fas fa-map-marker-alt"></i> انتخاب روی نقشه
                    </button>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveExperience()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveExperience = function() {
    const title = document.getElementById('expTitle')?.value.trim();
    const org = document.getElementById('expOrg')?.value.trim();
    if (!title || !org) return alert('عنوان و سازمان الزامی است');

    const branchId = parseInt(document.getElementById('expBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allExperiences.unshift({
        id: Date.now(),
        title,
        organization: org,
        summary: document.getElementById('expSummary').value.trim(),
        description: document.getElementById('expDesc').value.trim(),
        start_date: document.getElementById('expStart').value.trim() || null,
        end_date: document.getElementById('expEnd').value.trim() || null,
        branchId,
        branchName: branch ? branch.name : 'نامشخص',
        address: {
            province: document.getElementById('expProvince').value,
            city: document.getElementById('expCity').value,
            address: document.getElementById('expAddress').value.trim(),
            postal_code: document.getElementById('expPostal').value.trim(),
            lat: document.getElementById('expLat').value.trim(),
            lng: document.getElementById('expLng').value.trim()
        }
    });
    filterExperiencesByBranch(currentExpBranch);
    closeModal();
    alert('✅ تجربه ثبت شد');
};

window.viewExperience = function(id) {
    const e = allExperiences.find(x => x.id === id);
    if (!e) return;
    const addr = e.address || {};
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${e.title}</h2>
                    <p class="text-sm text-gray-500 mt-1">${e.organization}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editExperience(${e.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-5">
                ${e.summary ? `<p class="text-indigo-600 font-medium">${e.summary}</p>` : ''}
                ${e.description ? `<p class="text-gray-600 leading-relaxed">${e.description}</p>` : ''}
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شروع</span><span>${e.start_date || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">پایان</span><span>${e.end_date || 'تاکنون'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${e.branchName}</span></div>
                </div>
                <div>
                    <h3 class="font-semibold text-indigo-700 mb-2">آدرس</h3>
                    <p class="text-sm">${addr.province || ''}، ${addr.city || ''}، ${addr.address || '—'}</p>
                    <p class="text-xs text-gray-400 mt-1">کدپستی: ${addr.postal_code || '—'} | Lat: ${addr.lat || '—'} | Lng: ${addr.lng || '—'}</p>
                </div>
            </div>
        </div>
    </div>`;
};

window.editExperience = function(id) {
    const e = allExperiences.find(x => x.id === id);
    if (!e) return;
    const addr = e.address || {};
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === e.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">ویرایش تجربه</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">عنوان *</label>
                        <input id="editExpTitle" type="text" value="${e.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">سازمان *</label>
                        <input id="editExpOrg" type="text" value="${e.organization}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">خلاصه</label>
                    <input id="editExpSummary" type="text" value="${e.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="editExpDesc" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${e.description || ''}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ شروع</label>
                        <input id="editExpStart" type="text" value="${e.start_date || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ پایان</label>
                        <input id="editExpEnd" type="text" value="${e.end_date || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="editExpBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <div class="border border-gray-200 rounded-2xl p-4 space-y-3 address-block">
                    <h4 class="font-medium text-indigo-700">آدرس</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <select id="editExpProvince" class="addr-province border border-gray-300 rounded-2xl py-3 px-4 w-full">${getProvinceOptions(addr.province)}</select>
                        <select id="editExpCity" class="addr-city border border-gray-300 rounded-2xl py-3 px-4 w-full">${getCityOptions(addr.city)}</select>
                    </div>
                    <input id="editExpAddress" type="text" value="${addr.address || ''}" class="addr-address w-full border border-gray-300 rounded-2xl py-3 px-5">
                    <div class="grid grid-cols-3 gap-3">
                        <input id="editExpPostal" type="text" value="${addr.postal_code || ''}" class="addr-postal border border-gray-300 rounded-2xl py-3 px-4" placeholder="کد پستی">
                        <input id="editExpLat" type="text" value="${addr.lat || ''}" class="addr-lat border border-gray-300 rounded-2xl py-3 px-4" placeholder="Lat">
                        <input id="editExpLng" type="text" value="${addr.lng || ''}" class="addr-lng border border-gray-300 rounded-2xl py-3 px-4" placeholder="Lng">
                    </div>
                    <button type="button" onclick="openGoogleMapsPicker(this)" class="text-sm text-indigo-600"><i class="fas fa-map-marker-alt"></i> انتخاب روی نقشه</button>
                </div>
                <div class="flex gap-4">
                    <button onclick="saveEditedExperience(${e.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedExperience = function(id) {
    const title = document.getElementById('editExpTitle')?.value.trim();
    const org = document.getElementById('editExpOrg')?.value.trim();
    if (!title || !org) return alert('عنوان و سازمان الزامی است');
    const index = allExperiences.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editExpBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allExperiences[index] = {
        ...allExperiences[index],
        title, organization: org,
        summary: document.getElementById('editExpSummary').value.trim(),
        description: document.getElementById('editExpDesc').value.trim(),
        start_date: document.getElementById('editExpStart').value.trim() || null,
        end_date: document.getElementById('editExpEnd').value.trim() || null,
        branchId, branchName: branch ? branch.name : 'نامشخص',
        address: {
            province: document.getElementById('editExpProvince').value,
            city: document.getElementById('editExpCity').value,
            address: document.getElementById('editExpAddress').value.trim(),
            postal_code: document.getElementById('editExpPostal').value.trim(),
            lat: document.getElementById('editExpLat').value.trim(),
            lng: document.getElementById('editExpLng').value.trim()
        }
    };
    filterExperiencesByBranch(currentExpBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteExperience = function(id) {
    if (confirm('حذف این تجربه؟')) {
        allExperiences = allExperiences.filter(e => e.id !== id);
        filterExperiencesByBranch(currentExpBranch);
    }
};

setTimeout(() => {
    if (document.getElementById('experiencesTable')) {
        renderExperiencesBranchTabs();
        filterExperiencesByBranch('all');
    }
}, 200);


// ==================== Init قوی ====================
(function initExperiences() {
    let attempts = 0;
    const maxAttempts = 25;

    const tryRender = () => {
        attempts++;
        const tbody = document.querySelector('#experiencesTable tbody');
        const section = document.getElementById('experiences');

        if (tbody) {
            // مطمئن شو آرایه وجود دارد
            if (typeof allExperiences === 'undefined' || !Array.isArray(allExperiences)) {
                console.error('allExperiences تعریف نشده');
                return;
            }

            if (typeof renderExperiencesBranchTabs === 'function') {
                renderExperiencesBranchTabs();
            }
            if (typeof filterExperiencesByBranch === 'function') {
                filterExperiencesByBranch('all');
            } else if (typeof renderExperiencesTable === 'function') {
                renderExperiencesTable();
            }

            console.log('تجربه‌ها رندر شد. تعداد:', allExperiences.length);
        } else if (attempts < maxAttempts) {
            setTimeout(tryRender, 150);
        } else {
            console.warn('جدول experiencesTable پیدا نشد. HTML را چک کن.');
        }
    };

    // هم در لود اولیه و هم وقتی DOM آماده شد
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', tryRender);
    } else {
        tryRender();
    }
})();


window.openGoogleMapsPicker = function(btn) {
    const block = btn.closest('.address-block');
    if (!block) return;
    const lat = block.querySelector('.addr-lat')?.value || '35.6892';
    const lng = block.querySelector('.addr-lng')?.value || '51.3890';
    window.open(`https://www.google.com/maps/@${lat},${lng},15z`, '_blank');
    alert('مختصات را از گوگل مپ کپی کرده و در فیلدها وارد کنید.');
};