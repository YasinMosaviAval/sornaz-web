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

window.getGalleryAcceptForCategory = async function (category) {
    if (category === 'cover' || category === 'logo') return 'image/*';
    if (category === 'intro_video') return 'video/*';
    return 'image/*,video/*';
};

window.getGalleryAllowedTypeLabel = async function (category) {
    if (category === 'cover' || category === 'logo') return 'فقط تصویر';
    if (category === 'intro_video') return 'فقط ویدیو';
    return 'تصویر یا ویدیو';
};

window.getGalleryCropAspect = async function (category) {
    if (category === 'cover') return 16 / 9;
    if (category === 'logo') return 1;
    if (category === 'gallery') return null;
    return null;
};

window.getGalleryCropOutputSize = async function (category) {
    if (category === 'cover') return { w: 1920, h: 1080 };
    if (category === 'logo') return { w: 512, h: 512 };
    return { w: 1600, h: 900 };
};

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

let currentGalleryOwner = 'all';
let currentGalleryCategory = 'cover';
let currentGallerySectionId = 'gallery-cover';
let galleryPendingCropUrl = null;
let galleryCropState = null;
let galleryFormModalBackup = null;

window.getGalleryCategory = async function (value) {
    return galleryCategories.find(function (item) { return item.value === value; }) || galleryCategories[3];
};

window.getGalleryCategoryOptions = async function (selected) {
    return galleryCategories.map(function (item) {
        return '<option value="' + item.value + '"' + (item.value === selected ? ' selected' : '') + '>' + item.label + '</option>';
    }).join('');
};

window.getGalleryOwnerOptions = async function (selected) {
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

window.renderGalleryOwnerTabs = async function () {
    const containers = document.querySelectorAll('.gallery-owner-tabs');
    const owners = [{ id: 'all', name: 'همه', icon: 'fa-layer-group' }].concat(
        getGalleryOwnersList().map(function (owner) {
            return {
                id: owner.id,
                name: owner.name,
                icon: owner.id === 'academy' ? 'fa-school' : 'fa-building'
            };
        })
    );
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

window.setGalleryCategory = async function (category, sectionId) {
    currentGalleryCategory = category;
    if (sectionId) currentGallerySectionId = sectionId;
    window.renderGalleryOwnerTabs();
    window.renderGallery();
};

window.filterGalleryByOwner = async function (ownerId) {
    if (ownerId === 'all' || ownerId === 'academy') {
        currentGalleryOwner = ownerId;
    } else {
        currentGalleryOwner = isNaN(Number(ownerId)) ? ownerId : Number(ownerId);
    }
    document.querySelectorAll('.gallery-owner-tabs').forEach(function (container) {
        setActiveOwnerTabsInContainer(container, currentGalleryOwner);
    });
    window.renderGallery();
};

window.renderGallery = async function () {
    const grids = document.querySelectorAll('.gallery-grid');
    grids.forEach(function (grid) {
        const cat = grid.getAttribute('data-gallery-category') || currentGalleryCategory;
        const list = allGalleryItems.filter(function (item) {
            const matchOwner = currentGalleryOwner === 'all' || String(item.ownerId) === String(currentGalleryOwner);
            return matchOwner && item.category === cat;
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
    const category = currentGalleryCategory || existing.category || 'gallery';

    let type = existing.type || 'image';
    if (galleryPendingCropUrl) {
        type = 'image';
    } else if (file) {
        type = file.type && file.type.indexOf('video/') === 0 ? 'video' : 'image';
    } else if (urlValue) {
        type = /\.(mp4|webm|mov|avi|mkv)(\?.*)?$/i.test(urlValue) ? 'video' : 'image';
    }

    if ((category === 'cover' || category === 'logo') && type === 'video') {
        alert('در بخش کاور و لوگو فقط تصویر مجاز است.');
        return null;
    }
    if (category === 'intro_video' && type === 'image') {
        alert('در بخش ویدیو معرفی فقط ویدیو مجاز است.');
        return null;
    }

    const title = (document.getElementById('galleryTitle') && document.getElementById('galleryTitle').value || '').trim();
    if (!title || !owner) return null;

    const finalUrl = galleryPendingCropUrl || urlValue ||
        (file ? URL.createObjectURL(file) : existing.url || galleryPlaceholderUrl('New ' + type, 0));

    return {
        ownerId: owner.id,
        ownerName: owner.name,
        category: category,
        type: type,
        title: title,
        summary: (document.getElementById('gallerySummary') && document.getElementById('gallerySummary').value || '').trim(),
        description: (document.getElementById('galleryDesc') && document.getElementById('galleryDesc').value || '').trim(),
        url: finalUrl
    };
}

window.openAddGalleryModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    galleryPendingCropUrl = null;
    document.getElementById('modalContainer').innerHTML = window.getGalleryAddModalHTML
        ? window.getGalleryAddModalHTML(currentGalleryCategory) : '';
    bindGalleryFileCropListener();
};

window.saveGalleryItem = async function () {
    const item = readGalleryForm();
    if (!item) return alert('آموزشگاه/شعبه و عنوان الزامی هستند');
    allGalleryItems.unshift(Object.assign({}, item, { id: Date.now(), date: 'همین الان' }));
    galleryPendingCropUrl = null;
    if (currentGalleryOwner !== 'all') currentGalleryOwner = item.ownerId;
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
    alert('آیتم با موفقیت اضافه شد');
};

window.editGalleryItem = async function (id) {
    const item = allGalleryItems.find(function (entry) { return entry.id === id; });
    if (!item) return;
    galleryPendingCropUrl = null;
    currentGalleryCategory = item.category || currentGalleryCategory;
    document.getElementById('modalContainer').innerHTML = window.getGalleryEditModalHTML
        ? window.getGalleryEditModalHTML(item) : '';
    bindGalleryFileCropListener();
};

window.saveEditedGalleryItem = async function (id) {
    const existing = allGalleryItems.find(function (entry) { return entry.id === id; });
    if (!existing) return;
    const prevCategory = currentGalleryCategory;
    currentGalleryCategory = existing.category || currentGalleryCategory;
    const data = readGalleryForm(existing);
    currentGalleryCategory = prevCategory;
    if (!data) return;
    const index = allGalleryItems.findIndex(function (entry) { return entry.id === id; });
    if (index === -1) return;
    allGalleryItems[index] = Object.assign({}, allGalleryItems[index], data);
    galleryPendingCropUrl = null;
    if (currentGalleryOwner !== 'all') currentGalleryOwner = data.ownerId;
    window.renderGalleryOwnerTabs();
    window.renderGallery();
    closeModal();
    alert('تغییرات ذخیره شد');
};

window.deleteGalleryItem = async function (id) {
    if (!(await AppDialog.confirm('آیا از حذف این آیتم مطمئن هستید؟'))) return;
    allGalleryItems = allGalleryItems.filter(function (item) { return item.id !== id; });
    window.renderGallery();
};

function bindGalleryFileCropListener() {
    const input = document.getElementById('galleryFile');
    if (!input) return;
    input.onchange = function (event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;
        if (file.type && file.type.indexOf('video/') === 0) {
            galleryPendingCropUrl = null;
            updateGalleryCropPreview(null);
            return;
        }
        if (!file.type || file.type.indexOf('image/') !== 0) {
            alert('فقط تصویر یا ویدیو مجاز است.');
            event.target.value = '';
            return;
        }
        const category = currentGalleryCategory || 'gallery';
        if (category === 'intro_video') {
            alert('در بخش ویدیو معرفی فقط ویدیو مجاز است.');
            event.target.value = '';
            return;
        }
        openGalleryCropModal(file, category);
    };
}

function updateGalleryCropPreview(url) {
    const box = document.getElementById('galleryCropPreviewBox');
    if (!box) return;
    if (url) {
        box.innerHTML = '<img src="' + url + '" class="max-h-28 rounded-xl object-cover border border-gray-200" alt="preview">' +
            '<p class="text-xs text-green-600 mt-1">تصویر کراپ‌شده آماده ذخیره است</p>';
        box.classList.remove('hidden');
    } else {
        box.innerHTML = '';
        box.classList.add('hidden');
    }
}

function openGalleryCropModal(file, category) {
    const container = document.getElementById('modalContainer');
    if (!container) return;
    galleryFormModalBackup = container.innerHTML;
    const reader = new FileReader();
    reader.onload = function (e) {
        const img = new Image();
        img.onload = function () {
            const aspect = window.getGalleryCropAspect(category);
            const circular = category === 'logo';
            const title = category === 'cover' ? 'کراپ کاور (۱۶×۹)'
                : category === 'logo' ? 'کراپ لوگو (۱×۱)'
                : 'کراپ تصویر گالری';
            container.innerHTML = window.getGalleryCropModalHTML
                ? window.getGalleryCropModalHTML(category, title, circular) : '';
            galleryCropState = {
                category: category,
                circular: circular,
                img: img,
                aspect: aspect,
                cx: 0, cy: 0, cw: 0, ch: 0,
                viewScale: 1,
                dragging: false,
                lastX: 0, lastY: 0
            };
            initGalleryCropFrame();
            drawGalleryCropCanvas();
            bindGalleryCropEvents();
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function initGalleryCropFrame() {
    const s = galleryCropState;
    const img = s.img;
    if (s.aspect) {
        if (img.width / img.height > s.aspect) {
            s.ch = img.height;
            s.cw = img.height * s.aspect;
            s.cx = (img.width - s.cw) / 2;
            s.cy = 0;
        } else {
            s.cw = img.width;
            s.ch = img.width / s.aspect;
            s.cx = 0;
            s.cy = (img.height - s.ch) / 2;
        }
    } else {
        s.cx = 0; s.cy = 0;
        s.cw = img.width; s.ch = img.height;
    }
    s.minCrop = Math.min(img.width, img.height) * 0.12;
}

function clamp(v, min, max) {
    return Math.max(min, Math.min(max, v));
}

function drawGalleryCropCanvas() {
    const s = galleryCropState;
    if (!s) return;
    const canvas = document.getElementById('galleryCropCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const maxW = Math.min(720, window.innerWidth - 64);
    const maxH = Math.min(420, window.innerHeight * 0.5);
    const scale = Math.min(maxW / s.img.width, maxH / s.img.height, 1);
    s.viewScale = scale;
    canvas.width = Math.round(s.img.width * scale);
    canvas.height = Math.round(s.img.height * scale);

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(s.img, 0, 0, canvas.width, canvas.height);
    ctx.fillStyle = 'rgba(0,0,0,0.55)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    const rx = s.cx * scale, ry = s.cy * scale, rw = s.cw * scale, rh = s.ch * scale;
    ctx.save();
    ctx.beginPath();
    if (s.circular) {
        ctx.arc(rx + rw / 2, ry + rh / 2, rw / 2, 0, Math.PI * 2);
    } else {
        ctx.rect(rx, ry, rw, rh);
    }
    ctx.clip();
    ctx.drawImage(s.img, 0, 0, canvas.width, canvas.height);
    ctx.restore();

    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 2;
    if (s.circular) {
        ctx.beginPath();
        ctx.arc(rx + rw / 2, ry + rh / 2, rw / 2, 0, Math.PI * 2);
        ctx.stroke();
    } else {
        ctx.strokeRect(rx, ry, rw, rh);
    }
    ctx.fillStyle = '#6366f1';
    const hs = 6;
    [[rx, ry], [rx + rw, ry], [rx, ry + rh], [rx + rw, ry + rh]].forEach(function (p) {
        ctx.fillRect(p[0] - hs / 2, p[1] - hs / 2, hs, hs);
    });

    const info = document.getElementById('galleryCropInfo');
    if (info) {
        const ratioLabel = s.aspect == null ? 'آزاد' : (s.aspect === 1 ? '۱:۱' : '۱۶:۹');
        info.textContent = Math.round(s.cw) + ' × ' + Math.round(s.ch) + ' px · نسبت ' + ratioLabel;
    }
}

function bindGalleryCropEvents() {
    const canvas = document.getElementById('galleryCropCanvas');
    if (!canvas) return;
    canvas.onmousedown = function (e) {
        if (!galleryCropState) return;
        galleryCropState.dragging = true;
        galleryCropState.lastX = e.clientX;
        galleryCropState.lastY = e.clientY;
    };
    window.onmousemove = async function (e) {
        if (!galleryCropState || !galleryCropState.dragging) return;
        const dx = (e.clientX - galleryCropState.lastX) / galleryCropState.viewScale;
        const dy = (e.clientY - galleryCropState.lastY) / galleryCropState.viewScale;
        galleryCropState.lastX = e.clientX;
        galleryCropState.lastY = e.clientY;
        galleryCropState.cx = clamp(galleryCropState.cx + dx, 0, galleryCropState.img.width - galleryCropState.cw);
        galleryCropState.cy = clamp(galleryCropState.cy + dy, 0, galleryCropState.img.height - galleryCropState.ch);
        drawGalleryCropCanvas();
    };
    window.onmouseup = async function () {
        if (galleryCropState) galleryCropState.dragging = false;
    };
    canvas.ontouchstart = function (e) {
        if (!galleryCropState || !e.touches[0]) return;
        galleryCropState.dragging = true;
        galleryCropState.lastX = e.touches[0].clientX;
        galleryCropState.lastY = e.touches[0].clientY;
        e.preventDefault();
    };
    canvas.ontouchmove = function (e) {
        if (!galleryCropState || !galleryCropState.dragging || !e.touches[0]) return;
        const dx = (e.touches[0].clientX - galleryCropState.lastX) / galleryCropState.viewScale;
        const dy = (e.touches[0].clientY - galleryCropState.lastY) / galleryCropState.viewScale;
        galleryCropState.lastX = e.touches[0].clientX;
        galleryCropState.lastY = e.touches[0].clientY;
        galleryCropState.cx = clamp(galleryCropState.cx + dx, 0, galleryCropState.img.width - galleryCropState.cw);
        galleryCropState.cy = clamp(galleryCropState.cy + dy, 0, galleryCropState.img.height - galleryCropState.ch);
        drawGalleryCropCanvas();
        e.preventDefault();
    };
    canvas.ontouchend = function () {
        if (galleryCropState) galleryCropState.dragging = false;
    };
}

window.zoomGalleryCrop = async function (delta) {
    const s = galleryCropState;
    if (!s) return;
    const factor = delta > 0 ? 0.92 : 1.08;
    let newW = s.cw * factor;
    let newH = s.aspect ? newW / s.aspect : s.ch * factor;

    if (newW > s.img.width) {
        newW = s.img.width;
        newH = s.aspect ? newW / s.aspect : Math.min(s.ch * (newW / s.cw), s.img.height);
    }
    if (newH > s.img.height) {
        newH = s.img.height;
        newW = s.aspect ? newH * s.aspect : Math.min(newW, s.img.width);
    }
    if (newW < s.minCrop) {
        newW = s.minCrop;
        newH = s.aspect ? newW / s.aspect : s.minCrop;
    }
    if (newH < s.minCrop) {
        newH = s.minCrop;
        newW = s.aspect ? newH * s.aspect : s.minCrop;
    }

    const centerX = s.cx + s.cw / 2;
    const centerY = s.cy + s.ch / 2;
    s.cw = newW;
    s.ch = newH;
    s.cx = clamp(centerX - s.cw / 2, 0, s.img.width - s.cw);
    s.cy = clamp(centerY - s.ch / 2, 0, s.img.height - s.ch);
    drawGalleryCropCanvas();
};

window.applyGalleryCrop = async function () {
    const s = galleryCropState;
    if (!s) return;
    const size = window.getGalleryCropOutputSize(s.category);
    let outW = size.w, outH = size.h;
    if (!s.aspect) {
        const ratio = s.cw / s.ch;
        if (ratio >= 1) {
            outW = 1600;
            outH = Math.round(1600 / ratio);
        } else {
            outH = 1600;
            outW = Math.round(1600 * ratio);
        }
    }
    const out = document.createElement('canvas');
    out.width = outW;
    out.height = outH;
    const ctx = out.getContext('2d');
    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = 'high';
    ctx.drawImage(s.img, s.cx, s.cy, s.cw, s.ch, 0, 0, outW, outH);
    galleryPendingCropUrl = out.toDataURL('image/jpeg', 0.92);
    galleryCropState = null;

    const container = document.getElementById('modalContainer');
    if (container && galleryFormModalBackup) {
        container.innerHTML = galleryFormModalBackup;
        galleryFormModalBackup = null;
        bindGalleryFileCropListener();
        const fileInput = document.getElementById('galleryFile');
        if (fileInput) fileInput.value = '';
        updateGalleryCropPreview(galleryPendingCropUrl);
    } else {
        closeModal();
    }
};

window.cancelGalleryCrop = async function () {
    galleryCropState = null;
    const container = document.getElementById('modalContainer');
    if (container && galleryFormModalBackup) {
        container.innerHTML = galleryFormModalBackup;
        galleryFormModalBackup = null;
        bindGalleryFileCropListener();
        const fileInput = document.getElementById('galleryFile');
        if (fileInput) fileInput.value = '';
    } else {
        closeModal();
    }
};

setTimeout(function () {
    if (document.querySelector('.gallery-grid')) {
        window.renderGalleryOwnerTabs();
        window.renderGallery();
    }
}, 200);
})();
