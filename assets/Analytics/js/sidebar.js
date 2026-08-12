// ---------- باز و بسته کردن زیرمنو ----------
window.toggleSidebarSubmenu = function (id, btn) {
    const menu = document.getElementById(id);
    if (!menu) return;
    const chevron = btn?.querySelector('.submenu-chevron');
    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
        if (chevron) {
            chevron.style.transform = 'rotate(180deg)';
        }
    } else {
        menu.classList.add('hidden');
        if (chevron) {
            chevron.style.transform = 'rotate(0deg)';
        }
    }
};

// ---------- باز و بسته کردن سایدبار موبایل ----------
window.toggleMobileSidebar = function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;
    const isClosed = sidebar.classList.contains('-translate-x-full');
    if (isClosed) {
        sidebar.classList.remove('-translate-x-full');
        overlay?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
};

// ---------- بستن سایدبار ----------
window.closeMobileSidebar = function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;
    sidebar.classList.add('-translate-x-full');
    overlay?.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
};

// ---------- آماده شدن صفحه ----------
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;

    // کلیک روی Overlay
    overlay?.addEventListener('click', () => {
        window.closeMobileSidebar();
    });

    // ESC
    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape') return;
        if (window.innerWidth >= 1024) return;
        window.closeMobileSidebar();
    });

    // هنگام بزرگ شدن صفحه
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('-translate-x-full');
            overlay?.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    });
});

// ---------- باز نگه داشتن زیرمنوی فعال ----------
(function () {
    const originalShowSection = window.showSection;
    if (typeof originalShowSection !== 'function') return;
    const submenuMap = {
        branchesSubmenu: ['branches','branch-types'],
        classroomsSubmenu: ['classrooms','classroom-types'],
        profilesSubmenu: [
            'users',
            'roles',
            'permissions'
        ],
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

    const gallerySectionCategoryMap = {
        'gallery-cover': 'cover',
        'gallery-logo': 'logo',
        'gallery-intro-video': 'intro_video',
        'gallery-collection': 'gallery'
    };

    window.showSection = function (sectionId) {
        if (
            gallerySectionCategoryMap[sectionId] &&
            typeof window.setGalleryCategory === 'function'
        ) {
            window.setGalleryCategory(
                gallerySectionCategoryMap[sectionId],
                sectionId
            );
        }
        originalShowSection(sectionId);

        Object.keys(submenuMap).forEach(submenuId => {
            const sections = submenuMap[submenuId];
            if (!sections.includes(sectionId)) return;
            const submenu = document.getElementById(submenuId);
            if (!submenu) return;
            submenu.classList.remove('hidden');
            const btn = submenu.previousElementSibling;
            const chevron = btn?.querySelector('.submenu-chevron');
            if (chevron) {
                chevron.style.transform = 'rotate(180deg)';
            }
        });

        // در موبایل بعد از انتخاب منو، سایدبار بسته شود
        if (window.innerWidth < 1024) {
            window.closeMobileSidebar();
        }
    };
})();
