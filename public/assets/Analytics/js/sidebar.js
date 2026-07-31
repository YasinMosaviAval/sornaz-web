window.toggleSidebarSubmenu = function (id, btn) {
    const menu = document.getElementById(id);
    if (!menu) return;
    const isHidden = menu.classList.contains('hidden');
    menu.classList.toggle('hidden', !isHidden);
    const chevron = btn && btn.querySelector('.submenu-chevron');
    if (chevron) {
        chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
    }
};

// باز نگه داشتن زیرمنو وقتی یکی از بخش‌های مربوطه فعال است
(function () {
    const originalShowSection = window.showSection;
    if (typeof originalShowSection !== 'function') return;

    const submenuMap = {
        profilesSubmenu: ['profiles', 'roles', 'permissions'],
        gallerySubmenu: [
            'gallery-cover',
            'gallery-logo',
            'gallery-intro-video',
            'gallery-collection'
        ],
        scheduleSubmenu: [
            'schedules',
            'member-schedules',
            'scheduling-rules',
            'availabilities',
            'availability-exceptions'
        ]
    };

    // نگاشت بخش‌های گالری به دسته رسانه
    const gallerySectionCategoryMap = {
        'gallery-cover': 'cover',
        'gallery-logo': 'logo',
        'gallery-intro-video': 'intro_video',
        'gallery-collection': 'gallery'
    };

    window.showSection = function (sectionId) {
        // اگر یکی از زیربخش‌های گالری باشد، دسته را تنظیم کن
        if (gallerySectionCategoryMap[sectionId] && typeof window.setGalleryCategory === 'function') {
            window.setGalleryCategory(gallerySectionCategoryMap[sectionId], sectionId);
        }

        originalShowSection(sectionId);

        Object.keys(submenuMap).forEach(function (submenuId) {
            const sections = submenuMap[submenuId];
            if (sections.indexOf(sectionId) === -1) return;

            const submenu = document.getElementById(submenuId);
            if (!submenu) return;

            submenu.classList.remove('hidden');
            const btn = submenu.previousElementSibling;
            if (btn) {
                const chevron = btn.querySelector('.submenu-chevron');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }
        });
    };
})();
