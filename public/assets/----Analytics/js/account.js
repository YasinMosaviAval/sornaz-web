// ==================== اطلاعات حساب آموزشگاه ====================
let academyProfile = {
    name: "موزیک آکادمی",
    type: "آموزشگاه موسیقی",
    manager: "علی رضایی",
    email: "admin@musicacademy.ir",
    phone: "۰۲۱-۸۸۷۷۶۶۵۵",
    address: "تهران، خیابان ولیعصر، پلاک ۱۲۳",
    founded: "۱۳۹۵",
    branches: 4,
    students: 248,
    teachers: 28
};

window.renderAccountInfo = function() {
    const container = document.getElementById('accountInfo');
    if (!container) return;

    container.innerHTML = `
        <div class="flex justify-between border-b pb-3">
            <span class="text-gray-500">نام آموزشگاه</span>
            <span class="font-medium">${academyProfile.name}</span>
        </div>
        <div class="flex justify-between border-b pb-3">
            <span class="text-gray-500">مدیر مسئول</span>
            <span class="font-medium">${academyProfile.manager}</span>
        </div>
        <div class="flex justify-between border-b pb-3">
            <span class="text-gray-500">ایمیل</span>
            <span class="font-medium">${academyProfile.email}</span>
        </div>
        <div class="flex justify-between border-b pb-3">
            <span class="text-gray-500">تلفن</span>
            <span class="font-medium">${academyProfile.phone}</span>
        </div>
        <div class="flex justify-between border-b pb-3">
            <span class="text-gray-500">آدرس</span>
            <span class="font-medium">${academyProfile.address}</span>
        </div>
        <div class="flex justify-between border-b pb-3">
            <span class="text-gray-500">سال تأسیس</span>
            <span class="font-medium">${academyProfile.founded}</span>
        </div>
        <div class="flex justify-between border-b pb-3">
            <span class="text-gray-500">تعداد شعبه‌ها</span>
            <span class="font-medium">${academyProfile.branches} شعبه</span>
        </div>
        <div class="flex justify-between border-b pb-3">
            <span class="text-gray-500">تعداد هنرجویان</span>
            <span class="font-medium">${academyProfile.students} نفر</span>
        </div>
    `;

    // به‌روزرسانی نام در کارت
    const nameEl = document.getElementById('academyName');
    if (nameEl) nameEl.textContent = academyProfile.name;
};

window.openEditProfileModal = function() {
    if (!document.getElementById('modalContainer')) {
        alert('modalContainer پیدا نشد!');
        return;
    }

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش پروفایل</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام آموزشگاه</label>
                    <input id="editAcademyName" type="text" value="${academyProfile.name}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">مدیر مسئول</label>
                    <input id="editManager" type="text" value="${academyProfile.manager}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">آدرس</label>
                    <input id="editAddress" type="text" value="${academyProfile.address}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveProfile()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveProfile = function() {
    academyProfile.name = document.getElementById('editAcademyName').value.trim() || academyProfile.name;
    academyProfile.manager = document.getElementById('editManager').value.trim() || academyProfile.manager;
    academyProfile.address = document.getElementById('editAddress').value.trim() || academyProfile.address;

    renderAccountInfo();
    closeModal();
    alert('✅ پروفایل به‌روزرسانی شد');
};

window.saveAccountSettings = function() {
    academyProfile.email = document.getElementById('accountEmail').value;
    academyProfile.phone = document.getElementById('accountPhone').value;
    alert('✅ تنظیمات حساب ذخیره شد');
    renderAccountInfo();
};

// Init
setTimeout(() => {
    if (document.getElementById('accountInfo')) {
        renderAccountInfo();
    }
}, 200);