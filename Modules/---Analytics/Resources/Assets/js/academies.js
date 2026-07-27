let allAcademiesList = [
    {
        id: 1, name: "آوای هنر", role: "مدرس", city: "تهران", rating: 4.8,
        classes: 12, students: 58, income: "18M", isMine: true,
        summary: "آموزشگاه تخصصی پیانو و تئوری", description: "شعبه‌های مرکزی و ونک"
    },
    {
        id: 2, name: "نوای شرق", role: "هنرجو", city: "شیراز", rating: 4.5,
        classes: 3, students: null, income: "2M", cost: true, isMine: true,
        summary: "موسیقی ایرانی و ردیف", description: ""
    },
    {
        id: 3, name: "هنرستان موسیقی تهران", role: null, city: "تهران", rating: 4.9,
        classes: 40, students: 320, income: null, isMine: false,
        summary: "آموزش رسمی و آکادمیک", description: "از سال ۱۳۵۰"
    },
    {
        id: 4, name: "خانه موسیقی اصفهان", role: null, city: "اصفهان", rating: 4.6,
        classes: 15, students: 90, income: null, isMine: false,
        summary: "سنتور، تار و سه‌تار", description: ""
    }
];

function academyCardHTML(a, mine) {
    const stars = a.rating ? `⭐ ${a.rating}` : '';
    const roleBadge = a.role
        ? `<span class="px-2.5 py-1 rounded-lg text-xs ${a.role === 'مدرس' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'}">${a.role}</span>`
        : '';
    const moneyLabel = a.cost ? 'هزینه' : 'درآمد';
    const moneyVal = a.income || a.cost || '—';
    return `
    <div class="bg-white rounded-3xl p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex justify-between items-start mb-3">
            <div>
                <h3 class="text-xl font-bold">${a.name}</h3>
                <p class="text-sm text-gray-500 mt-1">📍 ${a.city || '—'} ${stars ? '| ' + stars : ''}</p>
            </div>
            ${roleBadge}
        </div>
        ${a.summary ? `<p class="text-sm text-gray-600 mb-4">${a.summary}</p>` : ''}
        <div class="grid grid-cols-3 gap-3 text-center mb-5">
            <div class="bg-gray-50 rounded-2xl py-3">
                <div class="text-xs text-gray-400">کلاس‌ها</div>
                <div class="text-lg font-bold mt-1">${a.classes ?? '—'}</div>
            </div>
            <div class="bg-gray-50 rounded-2xl py-3">
                <div class="text-xs text-gray-400">هنرجوها</div>
                <div class="text-lg font-bold mt-1">${a.students ?? '—'}</div>
            </div>
            <div class="bg-gray-50 rounded-2xl py-3">
                <div class="text-xs text-gray-400">${moneyLabel}</div>
                <div class="text-lg font-bold mt-1">${moneyVal}</div>
            </div>
        </div>
        <div class="flex gap-2">
            ${mine
                ? `<button onclick="enterAcademyPanel(${a.id})" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl text-sm hover:bg-indigo-700">ورود به پنل</button>
                   <button onclick="viewAcademy(${a.id})" class="border px-4 py-2.5 rounded-xl text-sm hover:bg-gray-50">مشاهده</button>`
                : `<button onclick="viewAcademy(${a.id})" class="flex-1 border border-indigo-300 text-indigo-600 py-2.5 rounded-xl text-sm hover:bg-indigo-50">مشاهده</button>
                   <button onclick="showSection('academy-enroll')" class="bg-indigo-600 text-white px-4 py-2.5 rounded-xl text-sm">ثبت‌نام</button>`
            }
        </div>
    </div>`;
}

window.renderAcademies = function() {
    const mine = allAcademiesList.filter(a => a.isMine);
    const all = allAcademiesList;
    const myBox = document.getElementById('myAcademiesCards');
    const allBox = document.getElementById('allAcademiesCards');
    if (myBox) {
        myBox.innerHTML = mine.length
            ? mine.map(a => academyCardHTML(a, true)).join('')
            : `<p class="text-gray-400 col-span-full text-center py-8">هنوز عضو هیچ آموزشگاهی نیستید</p>`;
    }
    if (allBox) filterAcademiesList();
};

window.filterAcademiesList = function() {
    const q = (document.getElementById('academySearch')?.value || '').trim().toLowerCase();
    const box = document.getElementById('allAcademiesCards');
    if (!box) return;
    let list = allAcademiesList;
    if (q) list = list.filter(a =>
        (a.name || '').toLowerCase().includes(q) ||
        (a.city || '').toLowerCase().includes(q)
    );
    box.innerHTML = list.length
        ? list.map(a => academyCardHTML(a, !!a.isMine)).join('')
        : `<p class="text-gray-400 col-span-full text-center py-8">آموزشگاهی یافت نشد</p>`;
};

window.enterAcademyPanel = function(id) {
    alert('ورود به پنل آموزشگاه #' + id + ' (در نسخه واقعی به داشبورد همان آموزشگاه می‌رود)');
};

window.viewAcademy = function(id) {
    const a = allAcademiesList.find(x => x.id === id);
    if (!a) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${a.name}</h2>
                    <p class="text-sm text-gray-500">📍 ${a.city} ${a.rating ? '· ⭐ ' + a.rating : ''}</p>
                </div>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-3">
                ${a.summary ? `<p class="text-indigo-600 font-medium">${a.summary}</p>` : ''}
                ${a.description ? `<p class="text-gray-600">${a.description}</p>` : ''}
                <div class="grid grid-cols-3 gap-3 text-center pt-2">
                    <div class="bg-gray-50 rounded-2xl py-3"><div class="text-xs text-gray-400">کلاس</div><div class="font-bold">${a.classes ?? '—'}</div></div>
                    <div class="bg-gray-50 rounded-2xl py-3"><div class="text-xs text-gray-400">هنرجو</div><div class="font-bold">${a.students ?? '—'}</div></div>
                    <div class="bg-gray-50 rounded-2xl py-3"><div class="text-xs text-gray-400">امتیاز</div><div class="font-bold">${a.rating ?? '—'}</div></div>
                </div>
                <button onclick="showSection('academy-enroll'); closeModal();" class="w-full mt-4 bg-indigo-600 text-white py-3 rounded-2xl">ثبت‌نام در این آموزشگاه</button>
            </div>
        </div>
    </div>`;
};

window.openAddAcademyModal = function() {
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold">افزودن آموزشگاه (ادمین)</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-4">
                <input id="newAcName" type="text" placeholder="نام آموزشگاه *" class="w-full border rounded-2xl py-3 px-4">
                <input id="newAcCity" type="text" placeholder="شهر" class="w-full border rounded-2xl py-3 px-4">
                <input id="newAcSummary" type="text" placeholder="خلاصه" class="w-full border rounded-2xl py-3 px-4">
                <button onclick="saveNewAcademy()" class="w-full bg-indigo-600 text-white py-3 rounded-2xl">ذخیره</button>
            </div>
        </div>
    </div>`;
};

window.saveNewAcademy = function() {
    const name = document.getElementById('newAcName')?.value.trim();
    if (!name) return alert('نام الزامی است');
    allAcademiesList.push({
        id: Date.now(), name,
        city: document.getElementById('newAcCity').value.trim() || '—',
        summary: document.getElementById('newAcSummary').value.trim(),
        rating: null, classes: 0, students: 0, income: null, isMine: false, role: null, description: ''
    });
    renderAcademies();
    closeModal();
    alert('✅ اضافه شد');
};

(function () {
    setTimeout(() => {
        if (document.getElementById('myAcademiesCards')) renderAcademies();
    }, 200);
})();