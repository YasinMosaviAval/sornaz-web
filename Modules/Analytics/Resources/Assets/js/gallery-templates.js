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
        return `<div class="bg-white rounded-3xl overflow-hidden shadow border border-transparent transition-shadow duration-300 hover:shadow-xl">
            <div class="relative">
                <img src="${escapeHtml(item.url)}" alt="${escapeHtml(item.title)}" class="w-full h-52 object-cover">
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

    function galleryForm(item) {
        item = item || {};
        const isEdit = Boolean(item.id);
        const ownerOptions = typeof window.getGalleryOwnerOptions === 'function'
            ? window.getGalleryOwnerOptions(item.ownerId || 'academy')
            : '<option value="academy">آموزشگاه</option>';
        const categoryOptions = typeof window.getGalleryCategoryOptions === 'function'
            ? window.getGalleryCategoryOptions(item.category || 'gallery')
            : '';

        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <h2 class="text-2xl font-bold">${isEdit ? 'ویرایش آیتم گالری' : 'افزودن آیتم جدید'}</h2>
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
                            <input id="galleryFile" type="file" accept="image/*,video/*" class="${fieldClass}">
                            <p class="text-xs text-gray-400 mt-1">نوع تصویر یا ویدیو از فایل انتخاب‌شده تشخیص داده می‌شود.</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">بخش رسانه *</label>
                        <select id="galleryCategory" class="${fieldClass}">${categoryOptions}</select>
                    </div>
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
                        <p class="text-xs text-gray-400 mt-1">در صورت نداشتن فایل، می‌توانید لینک مستقیم تصویر یا ویدیو را وارد کنید.</p>
                    </div>
                    <div class="flex gap-4 pt-2">
                        <button type="button" onclick="${isEdit ? 'saveEditedGalleryItem(' + item.id + ')' : 'saveGalleryItem()'}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-medium">ذخیره</button>
                        <button type="button" onclick="closeModal()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    }

    window.getGalleryAddModalHTML = function () { return galleryForm({}); };
    window.getGalleryEditModalHTML = function (item) { return galleryForm(item); };
})();
