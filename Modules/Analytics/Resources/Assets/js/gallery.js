const galleryCategories = [
    { value: 'cover', label: 'کاور', icon: 'fa-image' },
    { value: 'logo', label: 'لوگو', icon: 'fa-certificate' },
    { value: 'intro_video', label: 'ویدیو معرفی', icon: 'fa-video' },
    { value: 'gallery', label: 'مجموعه عکس‌ها و ویدیوها', icon: 'fa-images' }
];

let allGalleryItems = [
    { id: 1, ownerId: 'academy', ownerName: 'آموزشگاه', category: 'cover', type: 'image', title: 'کاور اصلی آموزشگاه', summary: 'تصویر معرفی', description: 'تصویر اصلی استفاده‌شده در صفحه آموزشگاه.', url: 'https://via.placeholder.com/600x400/4F46E5/FFFFFF?text=Academy+Cover', date: '۲ روز پیش' },
    { id: 2, ownerId: 'academy', ownerName: 'آموزشگاه', category: 'intro_video', type: 'video', title: 'ویدیوی معرفی آموزشگاه', summary: 'معرفی مجموعه', description: 'مروری کوتاه بر فضای آموزشی و خدمات آموزشگاه.', url: 'https://via.placeholder.com/600x400/10B981/FFFFFF?text=Academy+Video', date: '۵ روز پیش' },
    { id: 3, ownerId: 1, ownerName: 'شعبه مرکزی', category: 'cover', type: 'image', title: 'کاور شعبه مرکزی', summary: 'تصویر هدر شعبه', description: 'نمای بیرونی شعبه مرکزی.', url: 'https://via.placeholder.com/600x400/F59E0B/FFFFFF?text=Central+Cover', date: '۱ هفته پیش' },
    { id: 4, ownerId: 1, ownerName: 'شعبه مرکزی', category: 'gallery', type: 'image', title: 'کنسرت پایان ترم', summary: 'گالری رویداد', description: 'اجرای هنرجویان پیانو در سالن اصلی.', url: 'https://via.placeholder.com/600x400/EC4899/FFFFFF?text=Concert', date: '۱۰ روز پیش' },
    { id: 5, ownerId: 2, ownerName: 'شعبه ونک', category: 'logo', type: 'image', title: 'لوگوی شعبه ونک', summary: 'هویت بصری شعبه', description: 'نسخهٔ اصلی لوگوی شعبه ونک.', url: 'https://via.placeholder.com/600x400/8B5CF6/FFFFFF?text=Vanak+Logo', date: '۲ هفته پیش' },
    { id: 6, ownerId: 4, ownerName: 'شعبه کرج', category: 'intro_video', type: 'video', title: 'معرفی شعبه کرج', summary: 'تور ویدیویی', description: 'آشنایی با کلاس‌ها و محیط شعبه کرج.', url: 'https://via.placeholder.com/600x400/06B6D4/FFFFFF?text=Karaj+Video', date: '۳ هفته پیش' }
];
// برای هر آموزشگاه و شعبه، نمونه‌ای از هر چهار بخش رسانه نمایش داده می‌شود.
[{ id: 'academy', name: 'آموزشگاه' }, ...(typeof allBranches !== 'undefined' ? allBranches.map(branch => ({ id: branch.id, name: branch.name })) : [])].forEach(owner => {
    galleryCategories.forEach((category, categoryIndex) => {
        if (allGalleryItems.some(item => String(item.ownerId) === String(owner.id) && item.category === category.value)) return;
        const type = category.value === 'intro_video' ? 'video' : 'image';
        allGalleryItems.push({ id: 100 + allGalleryItems.length, ownerId: owner.id, ownerName: owner.name, category: category.value, type, title: `${category.label} ${owner.name}`, summary: `نمونه ${category.label}`, description: `رسانهٔ نمونه برای ${category.label} ${owner.name}.`, url: `https://via.placeholder.com/600x400/${['4F46E5', '10B981', 'F59E0B', '8B5CF6'][categoryIndex]}/FFFFFF?text=${encodeURIComponent(category.label)}`, date: '۱ ماه پیش' });
    });
});
let currentGalleryOwner = 'academy';
let currentGalleryCategory = 'cover';

window.getGalleryCategory = value => galleryCategories.find(item => item.value === value) || galleryCategories[3];
window.getGalleryCategoryOptions = selected => galleryCategories.map(item => `<option value="${item.value}" ${item.value === selected ? 'selected' : ''}>${item.label}</option>`).join('');
window.getGalleryOwnerOptions = selected => ['<option value="academy">آموزشگاه</option>', ...(typeof allBranches !== 'undefined' ? allBranches.map(branch => `<option value="${branch.id}">${branch.name}</option>`) : [])].map(option => option.replace(`value="${selected}"`, `value="${selected}" selected`)).join('');

function setActiveGalleryTab(selector, value) {
    document.querySelectorAll(selector).forEach(tab => {
        const active = tab.dataset.value === String(value);
        tab.classList.toggle('bg-indigo-600', active); tab.classList.toggle('text-white', active);
        tab.classList.toggle('border-indigo-600', active); tab.classList.toggle('border-gray-200', !active);
        tab.classList.toggle('hover:bg-indigo-700', active); tab.classList.toggle('hover:text-white', active);
        tab.classList.toggle('hover:bg-slate-50', !active); tab.classList.toggle('hover:text-indigo-700', !active);
    });
}
window.renderGalleryOwnerTabs = function () {
    const container = document.getElementById('galleryOwnerTabs');
    if (!container) return;
    const branches = typeof allBranches !== 'undefined' ? allBranches : [];
    container.innerHTML = [{ id: 'academy', name: 'آموزشگاه', icon: 'fa-school' }, ...branches.map(branch => ({ id: branch.id, name: branch.name, icon: 'fa-building' }))].map(owner => `<button data-value="${owner.id}" onclick="filterGalleryByOwner('${owner.id}')" class="gallery-owner-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition-colors"><i class="fas ${owner.icon} ml-1"></i>${owner.name}</button>`).join('');
    setActiveGalleryTab('.gallery-owner-tab', currentGalleryOwner);
};
window.renderGalleryCategoryTabs = function () {
    const container = document.getElementById('galleryCategoryTabs');
    if (!container) return;
    container.innerHTML = galleryCategories.map(category => `<button data-value="${category.value}" onclick="filterGalleryByCategory('${category.value}')" class="gallery-category-tab px-4 py-2 rounded-xl text-sm font-medium border transition-colors"><i class="fas ${category.icon} ml-1"></i>${category.label}</button>`).join('');
    setActiveGalleryTab('.gallery-category-tab', currentGalleryCategory);
};
window.filterGalleryByOwner = function (ownerId) { currentGalleryOwner = ownerId === 'academy' ? 'academy' : Number(ownerId); setActiveGalleryTab('.gallery-owner-tab', currentGalleryOwner); window.renderGallery(); };
window.filterGalleryByCategory = function (category) { currentGalleryCategory = category; setActiveGalleryTab('.gallery-category-tab', category); window.renderGallery(); };
window.renderGallery = function () {
    const grid = document.getElementById('galleryGrid'); if (!grid) return;
    const list = allGalleryItems.filter(item => String(item.ownerId) === String(currentGalleryOwner) && item.category === currentGalleryCategory);
    grid.innerHTML = list.length ? list.map(item => window.getGalleryCardHTML(item)).join('') : window.getGalleryEmptyHTML();
};

function readGalleryForm(existing = {}) {
    const ownerValue = document.getElementById('galleryOwner')?.value;
    const owner = ownerValue === 'academy' ? { id: 'academy', name: 'آموزشگاه' } : (allBranches || []).find(branch => branch.id === Number(ownerValue));
    const file = document.getElementById('galleryFile')?.files?.[0];
    const urlValue = document.getElementById('galleryUrl')?.value.trim();
    const type = file ? (file.type.startsWith('video/') ? 'video' : 'image') : (existing.type || (urlValue?.match(/\.(mp4|webm|mov)(\?.*)?$/i) ? 'video' : 'image'));
    const category = document.getElementById('galleryCategory')?.value || 'gallery';
    const title = document.getElementById('galleryTitle')?.value.trim();
    if (!title || !owner) return null;
    return { ownerId: owner.id, ownerName: owner.name, category, type, title, summary: document.getElementById('gallerySummary')?.value.trim() || '', description: document.getElementById('galleryDesc')?.value.trim() || '', url: urlValue || (file ? URL.createObjectURL(file) : existing.url || `https://via.placeholder.com/600x400/${type === 'video' ? '10B981' : '4F46E5'}/FFFFFF?text=New+${type}`) };
}
window.openAddGalleryModal = function () { document.getElementById('modalContainer').innerHTML = window.getGalleryAddModalHTML(); };
window.saveGalleryItem = function () {
    const item = readGalleryForm(); if (!item) return alert('آموزشگاه یا شعبه و عنوان الزامی هستند');
    allGalleryItems.unshift({ ...item, id: Date.now(), date: 'همین الان' });
    currentGalleryOwner = item.ownerId; currentGalleryCategory = item.category;
    window.renderGalleryOwnerTabs(); window.renderGalleryCategoryTabs(); window.renderGallery(); closeModal(); alert('✅ آیتم با موفقیت اضافه شد');
};
window.editGalleryItem = function (id) { const item = allGalleryItems.find(entry => entry.id === id); if (item) document.getElementById('modalContainer').innerHTML = window.getGalleryEditModalHTML(item); };
window.saveEditedGalleryItem = function (id) {
    const existing = allGalleryItems.find(entry => entry.id === id);
    const data = readGalleryForm(existing); if (!data) return alert('آموزشگاه یا شعبه و عنوان الزامی هستند');
    const index = allGalleryItems.findIndex(entry => entry.id === id); if (index === -1) return;
    allGalleryItems[index] = { ...allGalleryItems[index], ...data }; currentGalleryOwner = data.ownerId; currentGalleryCategory = data.category;
    window.renderGalleryOwnerTabs(); window.renderGalleryCategoryTabs(); window.renderGallery(); closeModal(); alert('✅ تغییرات ذخیره شد');
};
window.deleteGalleryItem = function (id) { if (confirm('آیا از حذف این آیتم مطمئن هستید؟')) { allGalleryItems = allGalleryItems.filter(item => item.id !== id); window.renderGallery(); } };

(function () { setTimeout(() => { if (document.getElementById('galleryGrid')) { window.renderGalleryOwnerTabs(); window.renderGalleryCategoryTabs(); window.renderGallery(); } }, 200); })();
