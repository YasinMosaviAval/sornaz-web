(function () {
    'use strict';
    const fieldClass = 'w-full border border-gray-300 rounded-2xl py-3.5 px-5';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    window.getGalleryCardHTML = function (item) {
        const category = window.getGalleryCategory ? window.getGalleryCategory(item.category) : { label: item.category };
        const isVideo = item.type === 'video';
        const isLogo = item.category === 'logo';
        return `<div class="bg-white rounded-3xl overflow-hidden shadow border border-transparent transition-shadow duration-300 hover:shadow-xl">
            <div class="relative ${isLogo ? 'bg-gray-50 flex items-center justify-center p-6' : ''}">
                <img src="${escapeHtml(item.url)}" alt="${escapeHtml(item.title)}"
                     class="${isLogo ? 'w-32 h-32 object-cover rounded-full shadow' : 'w-full h-52 object-cover'}">
                ${isVideo ? '<div class="absolute inset-0 flex items-center justify-center bg-black/40"><i class="fas fa-play-circle text-white text-5xl"></i></div>' : ''}
                <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs bg-black/60 text-white">${isVideo ? 'ویدیو' : 'تصویر'}</span>
            </div>
            <div class="p-5">
                <div class="flex justify-between items-start gap-2 mb-2">
                    <h3 class="font-bold text-lg leading-tight">${escapeHtml(item.title)}</h3>
                    <span class="text-xs text-gray-400 whitespace-nowrap">${escapeHtml(item.date || '')}</span>
                </div>
                <p class="text-sm text-indigo-600 mb-2">${escapeHtml(item.summary || '—')}</p>
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">${escapeHtml(item.description || 'بدون توضیحات')}</p>
                <div class="flex justify-between items-center text-xs">
                    <div>
                        <span class="text-gray-500">${escapeHtml(item.ownerName)}</span>
                        <span class="mr-2 px-2 py-1 rounded-full bg-gray-100 text-gray-600">${escapeHtml(category.label)}</span>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="editGalleryItem(${item.id})" class="text-indigo-600 hover:underline">ویرایش</button>
                        <button type="button" onclick="deleteGalleryItem(${item.id})" class="text-red-500 hover:underline">حذف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getGalleryEmptyHTML = function () {
        return '<p class="col-span-full text-center text-gray-400 py-16">آیتمی در این بخش وجود ندارد</p>';
    };

    function galleryForm(item, defaultCategory) {
        item = item || {};
        const isEdit = Boolean(item.id);
        const category = item.category || defaultCategory || 'gallery';
        const ownerOptions = typeof window.getGalleryOwnerOptions === 'function'
            ? window.getGalleryOwnerOptions(item.ownerId || 'academy')
            : '<option value="academy">آموزشگاه</option>';
        const accept = typeof window.getGalleryAcceptForCategory === 'function'
            ? window.getGalleryAcceptForCategory(category)
            : 'image/*,video/*';
        const typeHint = typeof window.getGalleryAllowedTypeLabel === 'function'
            ? window.getGalleryAllowedTypeLabel(category)
            : 'تصویر یا ویدیو';
        const categoryLabel = (window.getGalleryCategory && window.getGalleryCategory(category).label) || category;
        const cropHint = category === 'cover' ? 'پس از انتخاب تصویر، کراپ ۱۶×۹ باز می‌شود.'
            : category === 'logo' ? 'پس از انتخاب تصویر، کراپ ۱×۱ (دایره‌ای) باز می‌شود.'
            : category === 'intro_video' ? 'فقط ویدیو — بدون کراپ.'
            : 'برای تصاویر، مودال کراپ باز می‌شود.';

        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${isEdit ? 'ویرایش آیتم گالری' : 'افزودن آیتم جدید'}</h2>
                        <p class="text-sm text-gray-500 mt-1">بخش: ${escapeHtml(categoryLabel)}</p>
                    </div>
                    <button type="button" onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">آموزشگاه / شعبه *</label>
                            <select id="galleryOwner" class="${fieldClass}">${ownerOptions}</select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">فایل رسانه ${isEdit ? '(اختیاری)' : '*'}</label>
                            <input id="galleryFile" type="file" accept="${accept}" class="${fieldClass}">
                            <p class="text-xs text-gray-400 mt-1">${escapeHtml(typeHint)} — ${escapeHtml(cropHint)}</p>
                        </div>
                    </div>
                    <div id="galleryCropPreviewBox" class="hidden"></div>
                    <div>
                        <label class="block text-sm font-medium mb-2">عنوان *</label>
                        <input id="galleryTitle" type="text" value="${escapeHtml(item.title || '')}" class="${fieldClass}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">خلاصه</label>
                        <input id="gallerySummary" type="text" value="${escapeHtml(item.summary || '')}" class="${fieldClass}" placeholder="یک خط کوتاه درباره این رسانه">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">توضیحات</label>
                        <textarea id="galleryDesc" rows="3" class="${fieldClass}">${escapeHtml(item.description || '')}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">لینک رسانه</label>
                        <input id="galleryUrl" type="text" value="${escapeHtml(item.url || '')}" class="${fieldClass}" placeholder="https://...">
                        <p class="text-xs text-gray-400 mt-1">در صورت نداشتن فایل، می‌توانید لینک مستقیم را وارد کنید.</p>
                    </div>
                    <div class="flex gap-4 pt-2">
                        <button type="button" onclick="${isEdit ? 'saveEditedGalleryItem(' + item.id + ')' : 'saveGalleryItem()'}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-medium">ذخیره</button>
                        <button type="button" onclick="closeModal()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    }

    window.getGalleryAddModalHTML = function (defaultCategory) { return galleryForm({}, defaultCategory); };
    window.getGalleryEditModalHTML = function (item) { return galleryForm(item); };

    window.getGalleryCropModalHTML = function (category, title, circular) {
        const hint = category === 'cover'
            ? 'کادر ۱۶×۹ را جابه‌جا کنید و با زوم کوچک/بزرگ کنید. نسبت ثابت می‌ماند.'
            : category === 'logo'
            ? 'کادر مربعی ۱×۱ (نمایش دایره‌ای) را تنظیم کنید. نسبت ثابت می‌ماند.'
            : 'ناحیه مورد نظر را جابه‌جا و زوم کنید، سپس تأیید کنید.';
        return `<div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) cancelGalleryCrop()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-6 shadow-2xl" onclick="event.stopPropagation()">
                <div class="px-6 py-4 border-b flex justify-between items-center gap-3">
                    <div>
                        <h2 class="text-xl font-bold">${escapeHtml(title || 'کراپ تصویر')}</h2>
                        <p class="text-xs text-gray-500 mt-1">${escapeHtml(hint)}</p>
                    </div>
                    <button type="button" onclick="cancelGalleryCrop()" class="text-3xl text-gray-300 leading-none">×</button>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="bg-gray-900 rounded-2xl overflow-hidden flex items-center justify-center min-h-[200px]">
                        <canvas id="galleryCropCanvas" class="max-w-full cursor-move touch-none"></canvas>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-4">
                        <p id="galleryCropInfo" class="text-sm text-gray-500"></p>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="zoomGalleryCrop(1)" class="px-4 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 text-sm">
                                <i class="fas fa-search-plus"></i> زوم +
                            </button>
                            <button type="button" onclick="zoomGalleryCrop(-1)" class="px-4 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 text-sm">
                                <i class="fas fa-search-minus"></i> زوم −
                            </button>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-5">
                        <button type="button" onclick="applyGalleryCrop()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-medium">تأیید کراپ</button>
                        <button type="button" onclick="cancelGalleryCrop()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
})();
