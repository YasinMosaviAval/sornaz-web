(function () {
'use strict';
// ==================== مدیریت رسانه (گالری) ====================

const galleryCategories = [
    { value: 'cover', label: 'کاور', icon: 'fa-image' },
    { value: 'logo', label: 'لوگو', icon: 'fa-certificate' },
    { value: 'intro_video', label: 'ویدیو معرفی', icon: 'fa-video' },
    { value: 'gallery', label: 'مجموعه عکس‌ها و ویدیوها', icon: 'fa-images' }
];

const galleryPlaceholders = [
    '4F46E5', '10B981', 'F59E0B', 'EC4899', '8B5CF6', '06B6D4', 'EF4444', '14B8A6', 'F97316', '6366F1'
];

function galleryPlaceholderUrl(label, colorIndex) {
    const color = galleryPlaceholders[colorIndex % galleryPlaceholders.length];
    return 'https://via.placeholder.com/600x400/' + color + '/FFFFFF?text=' + encodeURIComponent(label);
}

function getGalleryOwnersList() {
    const branches = (typeof allBranches !== 'undefined' && allBranches.length)
        ? allBranches
        : [
            { id: 1, name: 'شعبه مرکزی' },
            { id: 2, name: 'شعبه ونک' },
            { id: 3, name: 'شعبه سعادت‌آباد' },
            { id: 4, name: 'شعبه کرج' }
        ];
    return [{ id: 'academy', name: 'آموزشگاه' }].concat(branches.map(function (b) {
        return { id: b.id, name: b.name };
    }));
}

let allGalleryItems = [];
(function buildSampleGallery() {
    const owners = getGalleryOwnersList();
    let id = 1;
    const relativeDates = ['همین الان', '۱ روز پیش', '۲ روز پیش', '۵ روز پیش', '۱ هفته پیش', '۱۰ روز پیش', '۲ هفته پیش', '۳ هفته پیش', '۱ ماه پیش'];

    owners.forEach(function (owner, ownerIdx) {
        allGalleryItems.push({
            id: id++, ownerId: owner.id, ownerName: owner.name, category: 'cover', type: 'image',
            title: 'کاور اصلی ' + owner.name, summary: 'تصویر هدر صفحه',
            description: 'تصویر اصلی استفاده‌شده در بالای صفحه ' + owner.name + '.',
            url: galleryPlaceholderUrl('Cover ' + owner.name, ownerIdx),
            date: relativeDates[ownerIdx % relativeDates.length]
        });
        allGalleryItems.push({
            id: id++, ownerId: owner.id, ownerName: owner.name, category: 'cover', type: 'image',
            title: 'کاور فصلی ' + owner.name, summary: 'کاور مناسبت‌ها',
            description: 'کاور جایگزین برای کمپین‌ها و مناسبت‌های خاص.',
            url: galleryPlaceholderUrl('Seasonal Cover', ownerIdx + 1),
            date: relativeDates[(ownerIdx + 2) % relativeDates.length]
        });

        allGalleryItems.push({
            id: id++, ownerId: owner.id, ownerName: owner.name, category: 'logo', type: 'image',
            title: 'لوگوی اصلی ' + owner.name, summary: 'هویت بصری',
            description: 'نسخه اصلی لوگوی ' + owner.name + ' برای استفاده در وب و چاپ.',
            url: galleryPlaceholderUrl('Logo ' + owner.name, ownerIdx + 2),
            date: relativeDates[(ownerIdx + 1) % relativeDates.length]
        });
        allGalleryItems.push({
            id: id++, ownerId: owner.id, ownerName: owner.name, category: 'logo', type: 'image',
            title: 'لوگوی افقی ' + owner.name, summary: 'نسخه عریض',
            description: 'لوگوی افقی مناسب هدر سایت و بنر.',
            url: galleryPlaceholderUrl('Logo Wide', ownerIdx + 3),
            date: relativeDates[(ownerIdx + 3) % relativeDates.length]
        });

        allGalleryItems.push({
            id: id++, ownerId: owner.id, ownerName: owner.name, category: 'intro_video', type: 'video',
            title: 'ویدیوی معرفی ' + owner.name, summary: 'تور کوتاه',
            description: 'مروری کوتاه بر فضای آموزشی و خدمات ' + owner.name + '.',
            url: galleryPlaceholderUrl('Intro Video', ownerIdx + 4),
            date: relativeDates[(ownerIdx + 2) % relativeDates.length]
        });
        allGalleryItems.push({
            id: id++, ownerId: owner.id, ownerName: owner.name, category: 'intro_video', type: 'video',
            title: 'مصاحبه با مدیر ' + owner.name, summary: 'گفت‌وگو',
            description: 'مصاحبه کوتاه درباره اهداف و برنامه‌های آینده.',
            url: galleryPlaceholderUrl('Manager Talk', ownerIdx + 5),
            date: relativeDates[(ownerIdx + 4) % relativeDates.length]
        });

        const gallerySamples = [
            { title: 'کنسرت پایان ترم', summary: 'گالری رویداد', desc: 'اجرای هنرجویان در سالن اصلی.', type: 'image' },
            { title: 'کلاس گروهی پیانو', summary: 'فضای کلاس', desc: 'نمایی از کلاس‌های گروهی پیانو.', type: 'image' },
            { title: 'ورکشاپ نقاشی', summary: 'کارگاه', desc: 'کارگاه یک‌روزه هنرهای تجسمی.', type: 'image' },
            { title: 'اجرای زنده هنرجویان', summary: 'ویدیو اجرا', desc: 'ضبط اجرای زنده هنرجویان پیشرفته.', type: 'video' },
            { title: 'فضای داخلی شعبه', summary: 'محیط', desc: 'تصاویر محیط آموزشی و پذیرش.', type: 'image' },
            { title: 'جشن پایان سال', summary: 'رویداد', desc: 'مراسم تقدیر از هنرجویان برتر.', type: 'image' },
            { title: 'تمرین ارکستر', summary: 'پشت صحنه', desc: 'تمرین گروهی ارکستر آموزشگاه.', type: 'video' },
            { title: 'نمایشگاه آثار', summary: 'گالری هنری', desc: 'نمایشگاه آثار هنرجویان نقاشی.', type: 'image' }
        ];
        gallerySamples.forEach(function (sample, si) {
            allGalleryItems.push({
                id: id++, ownerId: owner.id, ownerName: owner.name, category: 'gallery', type: sample.type,
                title: sample.title + ' — ' + owner.name, summary: sample.summary,
                description: sample.desc,
                url: galleryPlaceholderUrl(sample.title, ownerIdx + si),
                date: relativeDates[(ownerIdx + si) % relativeDates.length]
            });
        });
    });
})();

let currentGalleryOwner = 'academy';
let currentGalleryCategory = 'cover';
let currentGallerySectionId = 'gallery-cover';

window.getGalleryCategory = function (value) {
    return galleryCategories.find(function (item) { return item.value === value; }) || galleryCategories[3];
};

window.getGalleryCategoryOptions = function (selected) {
    return galleryCategories.map(function (item) {
        return '<option value="' + item.value + '"' + (item.value === selected ? ' selected' : '') + '>' + item.label + '</option>';
    }).join('');
};

window.getGalleryOwnerOptions = function (selected) {
    return getGalleryOwnersList().map(function (owner) {
        const sel = String(owner.id) === String(selected) ? ' selected' : '';
        return '<option value="' + owner.id + '"' + sel + '>' + owner.name + '</option>';
    }).join('');
};

function setActiveOwnerTabsInContainer(container, value) {
    if (!container) return;
    container.querySelectorAll('.gallery-owner-tab').forEach(function (tab) {
        const active = String(tab.dataset.value) === String(value);
        tab.classList.toggle('bg-indigo-600', active);
        tab.classList.toggle('text-white', active);
        tab.classList.toggle('border-indigo-600', active);
        tab.classList.toggle('border-gray-200', !active);
        tab.classList.toggle('hover:bg-indigo-700', active);
        tab.classList.toggle('hover:text-white', active);
        tab.classList.toggle('hover:bg-slate-50', !active);
        tab.classList.toggle('hover:text-indigo-700', !active);
    });
}

window.renderGalleryOwnerTabs = function () {
    const containers = document.querySelectorAll('.gallery-owner-tabs');
    const owners = getGalleryOwnersList().map(function (owner) {
        return {
            id: owner.id,
            name: owner.name,
            icon: owner.id === 'academy' ? 'fa-school' : 'fa-building'
        };
    });
    containers.forEach(function (container) {
        container.innerHTML = owners.map(function (owner) {
            return '<button type="button" data-value="' + owner.id + '" ' +
                'onclick="filterGalleryByOwner(\'' + owner.id + '\')" ' +
                'class="gallery-owner-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition-colors">' +
                '<i class="fas ' + owner.icon + ' ml-1"></i>' + owner.name + '</button>';
        }).join('');
        setActiveOwnerTabsInContainer(container, currentGalleryOwner);
    });
};

/** فراخوانی از sidebar هنگام باز شدن زیربخش گالری */
window.setGalleryCategory = function (category, sectionId) {
    currentGalleryCategory = category;
    if (sectionId) currentGallerySectionId = sectionId;
    window.renderGalleryOwnerTabs();
    window.renderGallery();
};

window.filterGalleryByOwner = function (ownerId) {
    currentGalleryOwner = ownerId === 'academy' ? 'academy' : (isNaN(Number(ownerId)) ? ownerId : Number(ownerId));
    document.querySelectorAll('.gallery-owner-tabs').forEach(function (container) {
        setActiveOwnerTabsInContainer(container, currentGalleryOwner);
    });
    window.renderGallery();
};

window.renderGallery = function () {
    const grids = document.querySelectorAll('.gallery-grid');
    grids.forEach(function (grid) {
        const cat = grid.getAttribute('data-gallery-category') || currentGalleryCategory;
        const list = allGalleryItems.filter(function (item) {
            return String(item.ownerId) === String(currentGalleryOwner) && item.category === cat;
        });
        grid.innerHTML = list.length
            ? list.map(function (item) { return window.getGalleryCardHTML(item); }).join('')
            : (window.getGalleryEmptyHTML ? window.getGalleryEmptyHTML() : '');
    });
};

function readGalleryForm(existing) {
    existing = existing || {};
    const ownerValue = document.getElementById('galleryOwner') && document.getElementById('galleryOwner').value;
    const owners = getGalleryOwnersList();
    const owner = owners.find(function (o) { return String(o.id) === String(ownerValue); });
    const fileInput = document.getElementById('galleryFile');
    const file = fileInput && fileInput.files && fileInput.files[0];
    const urlValue = (document.getElementById('galleryUrl') && document.getElementById('galleryUrl').value || '').trim();
    let type = existing.type || 'image';
    if (file) {
        type = file.type && file.type.indexOf('video/') === 0 ? 'video' : 'image';
    } else if (urlValue) {
        type = /\.(mp4|webm|mov|avi|mkv)(\?.*)?$/i.test(urlValue) ? 'video' : 'image';
    }
    const categorySelect = document.getElementById('galleryCategory');
    const category = (categorySelect && categorySelect.value) || currentGalleryCategory || 'gallery';
    const title = (document.getElementById('galleryTitle') && document.getElementById('galleryTitle').value || '').trim();
    if (!title || !owner) return null;
    return {
        ownerId: owner.id,
        ownerName: owner.name,
        category: category,
        type: type,
        title: title,
        summary: (document.getElementById('gallerySummary') && document.getElementById('gallerySummary').value || '').trim(),
        description: (document.getElementById('galleryDesc') && document.getElementById('galleryDesc').value || '').trim(),
        url: urlValue || (file ? URL.createObjectURL(file) : existing.url || galleryPlaceholderUrl('New ' + type, 0))
    };
}

window.openAddGalleryModal = function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getGalleryAddModalHTML
        ? window.getGalleryAddModalHTML(currentGalleryCategory) : '';
};

window.saveGalleryItem = function () {
    const item = readGalleryForm();
    if (!item) return alert('آموزشگاه/شعبه و عنوان الزامی هستند');
    allGalleryItems.unshift(Object.assign({}, item, { id: Date.now(), date: 'همین الان' }));
    currentGalleryOwner = item.ownerId;
    currentGalleryCategory = item.category;
    const sectionMap = {
        cover: 'gallery-cover',
        logo: 'gallery-logo',
        intro_video: 'gallery-intro-video',
        gallery: 'gallery-collection'
    };
    if (sectionMap[item.category]) currentGallerySectionId = sectionMap[item.category];
    window.renderGalleryOwnerTabs();
    window.renderGallery();
    closeModal();
    alert('✅ آیتم با موفقیت اضافه شد');
};

window.editGalleryItem = function (id) {
    const item = allGalleryItems.find(function (entry) { return entry.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getGalleryEditModalHTML
        ? window.getGalleryEditModalHTML(item) : '';
};

window.saveEditedGalleryItem = function (id) {
    const existing = allGalleryItems.find(function (entry) { return entry.id === id; });
    if (!existing) return;
    const data = readGalleryForm(existing);
    if (!data) return alert('آموزشگاه/شعبه و عنوان الزامی هستند');
    const index = allGalleryItems.findIndex(function (entry) { return entry.id === id; });
    if (index === -1) return;
    allGalleryItems[index] = Object.assign({}, allGalleryItems[index], data);
    currentGalleryOwner = data.ownerId;
    currentGalleryCategory = data.category;
    window.renderGalleryOwnerTabs();
    window.renderGallery();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.deleteGalleryItem = function (id) {
    if (!confirm('آیا از حذف این آیتم مطمئن هستید؟')) return;
    allGalleryItems = allGalleryItems.filter(function (item) { return item.id !== id; });
    window.renderGallery();
};

setTimeout(function () {
    if (document.querySelector('.gallery-grid')) {
        window.renderGalleryOwnerTabs();
        window.renderGallery();
    }
}, 200);
})();
