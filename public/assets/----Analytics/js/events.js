const eventTypeLabels = {
    concert: 'کنسرت', festival: 'جشنواره', competition: 'مسابقه', workshop: 'کارگاه', other: 'سایر'
};

let allEvents = [
    { id: 1, title: "کنسرت پایان ترم پاییز", organization: "موزیک آکادمی", summary: "اجرای هنرجویان", description: "اجرای گروهی هنرجویان پیانو و گیتار.", event_type: "concert", event_date: "۱۴۰۳/۰۹/۲۵", branchId: 1, branchName: "شعبه مرکزی",
      address: { province: "تهران", city: "تهران", address: "سالن اصلی شعبه مرکزی", postal_code: "", lat: "35.7219", lng: "51.3347" } },
    { id: 2, title: "جشنواره موسیقی جوان", organization: "وزارت ارشاد", summary: "حضور در جشنواره", description: "شرکت هنرجویان در بخش گروهی.", event_type: "festival", event_date: "۱۴۰۲/۱۱/۱۰", branchId: 2, branchName: "شعبه ونک",
      address: { province: "تهران", city: "تهران", address: "تالار وحدت", postal_code: "", lat: "", lng: "" } },
    { id: 3, title: "کارگاه ویولن استاد مهمان", organization: "خانه موسیقی", summary: "مستر کلاس", description: "کارگاه یک‌روزه با حضور استاد مهمان.", event_type: "workshop", event_date: "۱۴۰۳/۰۵/۱۲", branchId: 1, branchName: "شعبه مرکزی",
      address: { province: "تهران", city: "تهران", address: "خیابان ولیعصر", postal_code: "", lat: "", lng: "" } }
];
let currentEventBranch = 'all';

function getEventProvinceOptions(selected = 'تهران') {
    const list = window.iranProvinces || ["تهران", "البرز", "اصفهان", "فارس"];
    return list.map(p => `<option value="${p}" ${p === selected ? 'selected' : ''}>${p}</option>`).join('');
}
function getEventCityOptions(selected = 'تهران') {
    const list = window.tehranCities || ["تهران", "شمیرانات", "ری", "شهریار"];
    return list.map(c => `<option value="${c}" ${c === selected ? 'selected' : ''}>${c}</option>`).join('');
}

window.renderEventsBranchTabs = function() {
    const container = document.getElementById('eventsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.event-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'event-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterEventsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterEventsByBranch = function(branchId) {
    currentEventBranch = branchId;
    document.querySelectorAll('.event-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.event-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    renderEventsTable();
};

window.renderEventsTable = function() {
    const tbody = document.querySelector('#eventsTable tbody');
    if (!tbody) return;
    const list = currentEventBranch === 'all' ? allEvents : allEvents.filter(e => e.branchId == currentEventBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">رویدادی یافت نشد</td></tr>`
        : list.map(e => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${e.title}</td>
                <td class="py-4 px-5">${e.organization}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">${eventTypeLabels[e.event_type] || e.event_type}</span></td>
                <td class="py-4 px-5">${e.event_date || '—'}</td>
                <td class="py-4 px-5">${e.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewEvent(${e.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editEvent(${e.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteEvent(${e.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`).join('');
};

window.openAddEventModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">افزودن رویداد</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5 max-h-[75vh] overflow-y-auto">
                <input id="eventTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="eventOrg" type="text" placeholder="سازمان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="eventSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="eventDesc" rows="3" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="eventType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="concert">کنسرت</option>
                        <option value="festival">جشنواره</option>
                        <option value="competition">مسابقه</option>
                        <option value="workshop">کارگاه</option>
                        <option value="other">سایر</option>
                    </select>
                    <input id="eventDate" type="text" placeholder="تاریخ رویداد" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="eventBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="border border-gray-200 rounded-2xl p-4 space-y-3 address-block">
                    <h4 class="font-medium text-indigo-700">آدرس رویداد</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <select id="eventProvince" class="addr-province border border-gray-300 rounded-2xl py-3 px-4 w-full">${getEventProvinceOptions()}</select>
                        <select id="eventCity" class="addr-city border border-gray-300 rounded-2xl py-3 px-4 w-full">${getEventCityOptions()}</select>
                    </div>
                    <input id="eventAddress" type="text" placeholder="ادامه آدرس" class="addr-address w-full border border-gray-300 rounded-2xl py-3 px-5">
                    <div class="grid grid-cols-3 gap-3">
                        <input id="eventPostal" type="text" placeholder="کد پستی" class="addr-postal border border-gray-300 rounded-2xl py-3 px-4">
                        <input id="eventLat" type="text" placeholder="Lat" class="addr-lat border border-gray-300 rounded-2xl py-3 px-4">
                        <input id="eventLng" type="text" placeholder="Lng" class="addr-lng border border-gray-300 rounded-2xl py-3 px-4">
                    </div>
                    <button type="button" onclick="(typeof openGoogleMapsPicker==='function'?openGoogleMapsPicker(this):alert('تابع نقشه در دسترس نیست'))" class="text-sm text-indigo-600">
                        <i class="fas fa-map-marker-alt"></i> انتخاب روی نقشه
                    </button>
                </div>
                <div class="flex gap-4">
                    <button onclick="saveEvent()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEvent = function() {
    const title = document.getElementById('eventTitle')?.value.trim();
    const org = document.getElementById('eventOrg')?.value.trim();
    if (!title || !org) return alert('عنوان و سازمان الزامی است');
    const branchId = parseInt(document.getElementById('eventBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allEvents.unshift({
        id: Date.now(), title, organization: org,
        summary: document.getElementById('eventSummary').value.trim(),
        description: document.getElementById('eventDesc').value.trim(),
        event_type: document.getElementById('eventType').value,
        event_date: document.getElementById('eventDate').value.trim() || null,
        branchId, branchName: branch ? branch.name : 'نامشخص',
        address: {
            province: document.getElementById('eventProvince').value,
            city: document.getElementById('eventCity').value,
            address: document.getElementById('eventAddress').value.trim(),
            postal_code: document.getElementById('eventPostal').value.trim(),
            lat: document.getElementById('eventLat').value.trim(),
            lng: document.getElementById('eventLng').value.trim()
        }
    });
    filterEventsByBranch(currentEventBranch);
    closeModal();
    alert('✅ رویداد ثبت شد');
};

window.viewEvent = function(id) {
    const e = allEvents.find(x => x.id === id);
    if (!e) return;
    const addr = e.address || {};
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${e.title}</h2>
                    <p class="text-sm text-gray-500">${e.organization} — ${eventTypeLabels[e.event_type] || e.event_type}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editEvent(${e.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${e.summary ? `<p class="text-indigo-600 font-medium">${e.summary}</p>` : ''}
                ${e.description ? `<p class="text-gray-600">${e.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ</span><span>${e.event_date || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${e.branchName}</span></div>
                </div>
                <div>
                    <h3 class="font-semibold text-indigo-700 mb-2">آدرس</h3>
                    <p class="text-sm">${addr.province || ''}، ${addr.city || ''}، ${addr.address || '—'}</p>
                </div>
            </div>
        </div>
    </div>`;
};

window.editEvent = function(id) {
    const e = allEvents.find(x => x.id === id);
    if (!e) return;
    const addr = e.address || {};
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === e.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">ویرایش رویداد</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5 max-h-[75vh] overflow-y-auto">
                <input id="editEventTitle" type="text" value="${e.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editEventOrg" type="text" value="${e.organization}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editEventSummary" type="text" value="${e.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editEventDesc" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${e.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="editEventType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${['concert','festival','competition','workshop','other'].map(t =>
                            `<option value="${t}" ${e.event_type===t?'selected':''}>${eventTypeLabels[t]}</option>`
                        ).join('')}
                    </select>
                    <input id="editEventDate" type="text" value="${e.event_date || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="editEventBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="border border-gray-200 rounded-2xl p-4 space-y-3 address-block">
                    <div class="grid grid-cols-2 gap-3">
                        <select id="editEventProvince" class="addr-province border border-gray-300 rounded-2xl py-3 px-4 w-full">${getEventProvinceOptions(addr.province)}</select>
                        <select id="editEventCity" class="addr-city border border-gray-300 rounded-2xl py-3 px-4 w-full">${getEventCityOptions(addr.city)}</select>
                    </div>
                    <input id="editEventAddress" type="text" value="${addr.address || ''}" class="addr-address w-full border border-gray-300 rounded-2xl py-3 px-5">
                    <div class="grid grid-cols-3 gap-3">
                        <input id="editEventPostal" type="text" value="${addr.postal_code || ''}" class="addr-postal border border-gray-300 rounded-2xl py-3 px-4">
                        <input id="editEventLat" type="text" value="${addr.lat || ''}" class="addr-lat border border-gray-300 rounded-2xl py-3 px-4">
                        <input id="editEventLng" type="text" value="${addr.lng || ''}" class="addr-lng border border-gray-300 rounded-2xl py-3 px-4">
                    </div>
                </div>
                <div class="flex gap-4">
                    <button onclick="saveEditedEvent(${e.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedEvent = function(id) {
    const title = document.getElementById('editEventTitle')?.value.trim();
    const org = document.getElementById('editEventOrg')?.value.trim();
    if (!title || !org) return alert('عنوان و سازمان الزامی است');
    const index = allEvents.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editEventBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allEvents[index] = {
        ...allEvents[index], title, organization: org,
        summary: document.getElementById('editEventSummary').value.trim(),
        description: document.getElementById('editEventDesc').value.trim(),
        event_type: document.getElementById('editEventType').value,
        event_date: document.getElementById('editEventDate').value.trim() || null,
        branchId, branchName: branch ? branch.name : 'نامشخص',
        address: {
            province: document.getElementById('editEventProvince').value,
            city: document.getElementById('editEventCity').value,
            address: document.getElementById('editEventAddress').value.trim(),
            postal_code: document.getElementById('editEventPostal').value.trim(),
            lat: document.getElementById('editEventLat').value.trim(),
            lng: document.getElementById('editEventLng').value.trim()
        }
    };
    filterEventsByBranch(currentEventBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteEvent = function(id) {
    if (confirm('حذف این رویداد؟')) {
        allEvents = allEvents.filter(e => e.id !== id);
        filterEventsByBranch(currentEventBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#eventsTable tbody')) {
            renderEventsBranchTabs();
            filterEventsByBranch('all');
        }
    }, 200);
})();