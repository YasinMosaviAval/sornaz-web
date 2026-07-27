// ==================== داده نمونه گالری ====================
let allGalleryItems = [
    { id: 1, branchId: 1, branchName: "شعبه مرکزی", type: "image", title: "کنسرت پایان ترم پاییز", description: "اجرای زیبای هنرجویان پیانو در سالن اصلی شعبه مرکزی با حضور خانواده‌ها", url: "https://via.placeholder.com/600x400/4F46E5/FFFFFF?text=Concert", date: "۲ روز پیش" },
    { id: 2, branchId: 1, branchName: "شعبه مرکزی", type: "video", title: "کلاس گروهی گیتار", description: "تمرین گروهی هنرجویان سطح متوسط گیتار کلاسیک", url: "https://via.placeholder.com/600x400/10B981/FFFFFF?text=Guitar+Class", date: "۵ روز پیش" },
    { id: 3, branchId: 2, branchName: "شعبه ونک", type: "image", title: "مستر کلاس ویولن", description: "کارگاه یک‌روزه با حضور استاد مهمان از هنرستان موسیقی", url: "https://via.placeholder.com/600x400/F59E0B/FFFFFF?text=Violin+Master", date: "۱ هفته پیش" },
    { id: 4, branchId: 3, branchName: "شعبه سعادت‌آباد", type: "image", title: "جشنواره موسیقی کودکان", description: "اجرای هنرجویان کودک در جشنواره سالانه آموزشگاه", url: "https://via.placeholder.com/600x400/EC4899/FFFFFF?text=Kids+Festival", date: "۱۰ روز پیش" },
    { id: 5, branchId: 4, branchName: "شعبه کرج", type: "video", title: "آموزش ریتم درام", description: "ویدیوی آموزشی ریتم‌های پایه برای هنرجویان مبتدی درام", url: "https://via.placeholder.com/600x400/8B5CF6/FFFFFF?text=Drum+Lesson", date: "۲ هفته پیش" },
    { id: 6, branchId: 1, branchName: "شعبه مرکزی", type: "image", title: "نمایشگاه سازهای سنتی", description: "نمایشگاه سازهای ایرانی در لابی شعبه مرکزی", url: "https://via.placeholder.com/600x400/06B6D4/FFFFFF?text=Instruments", date: "۳ هفته پیش" }
];

let currentGalleryBranch = 'all';

// ==================== تاپ‌بار شعبه‌ها ====================
window.renderGalleryBranchTabs = function() {
    const container = document.getElementById('galleryBranchTabs');
    if (!container) return;

    container.querySelectorAll('.gallery-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'gallery-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterGalleryByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterGalleryByBranch = function(branchId) {
    currentGalleryBranch = branchId;

    document.querySelectorAll('.gallery-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.gallery-branch-tab');
    if (branchId === 'all') {
        if (tabs[0]) {
            tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            tabs[0].classList.remove('border-gray-200');
        }
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }

    renderGallery();
};

// ==================== رندر گالری ====================
window.renderGallery = function() {
    const grid = document.getElementById('galleryGrid');
    if (!grid) return;

    const list = currentGalleryBranch === 'all' 
        ? allGalleryItems 
        : allGalleryItems.filter(i => i.branchId == currentGalleryBranch);

    if (list.length === 0) {
        grid.innerHTML = `<p class="col-span-full text-center text-gray-400 py-16">آیتمی در گالری وجود ندارد</p>`;
        return;
    }

    grid.innerHTML = list.map(item => `
        <div class="bg-white rounded-3xl overflow-hidden shadow card-hover">
            <div class="relative">
                <img src="${item.url}" alt="${item.title}" class="w-full h-52 object-cover">
                ${item.type === 'video' ? `
                    <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                        <i class="fas fa-play-circle text-white text-5xl opacity-90"></i>
                    </div>` : ''}
                <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs bg-black/60 text-white">
                    ${item.type === 'video' ? 'ویدیو' : 'تصویر'}
                </span>
            </div>
            <div class="p-5">
                <div class="flex justify-between items-start mb-2 gap-2">
                    <h3 class="font-bold text-lg leading-tight">${item.title}</h3>
                    <span class="text-xs text-gray-400 whitespace-nowrap">${item.date}</span>
                </div>
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">${item.description}</p>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500">${item.branchName}</span>
                    <div class="flex gap-3">
                        <button onclick="editGalleryItem(${item.id})" class="text-indigo-600 hover:underline">ویرایش</button>
                        <button onclick="deleteGalleryItem(${item.id})" class="text-red-500 hover:underline">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
};

// ==================== افزودن آیتم گالری ====================
window.openAddGalleryModal = function() {
    if (!document.getElementById('modalContainer')) {
        alert('خطا: المان modalContainer در صفحه اصلی وجود ندارد!');
        return;
    }

    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : [])
        .map(b => `<option value="${b.id}">${b.name}</option>`).join('');

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن به گالری</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نوع محتوا</label>
                    <select id="galleryType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="image">تصویر</option>
                        <option value="video">ویدیو</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان *</label>
                    <input id="galleryTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="galleryDesc" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="galleryBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${branchOptions || '<option>شعبه‌ای تعریف نشده</option>'}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">آپلود فایل (شبیه‌سازی)</label>
                    <input type="file" accept="image/*,video/*" class="w-full border border-gray-300 rounded-2xl py-3 px-5">
                    <p class="text-xs text-gray-400 mt-1">در نسخه واقعی فایل روی سرور آپلود می‌شود</p>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveGalleryItem()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-medium">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveGalleryItem = function() {
    const title = document.getElementById('galleryTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');

    const branchId = parseInt(document.getElementById('galleryBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    const type = document.getElementById('galleryType').value;

    allGalleryItems.unshift({
        id: Date.now(),
        branchId,
        branchName: branch ? branch.name : 'نامشخص',
        type,
        title,
        description: document.getElementById('galleryDesc').value || 'بدون توضیحات',
        url: type === 'video' 
            ? "https://via.placeholder.com/600x400/10B981/FFFFFF?text=New+Video" 
            : "https://via.placeholder.com/600x400/4F46E5/FFFFFF?text=New+Image",
        date: "همین الان"
    });

    filterGalleryByBranch(currentGalleryBranch);
    closeModal();
    alert('✅ آیتم با موفقیت به گالری اضافه شد');
};

window.deleteGalleryItem = function(id) {
    if (confirm('آیا از حذف این آیتم مطمئن هستید؟')) {
        allGalleryItems = allGalleryItems.filter(i => i.id !== id);
        filterGalleryByBranch(currentGalleryBranch);
    }
};

window.editGalleryItem = function(id) {
    alert('ویرایش آیتم گالری (مشابه بقیه بخش‌ها قابل پیاده‌سازی است)');
};

// ==================== Init ====================
(function initGallery() {
    setTimeout(() => {
        if (document.getElementById('galleryGrid')) {
            renderGalleryBranchTabs();
            filterGalleryByBranch('all');
        }
    }, 200);
})();
