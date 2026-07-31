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

// باز نگه داشتن زیرمنو وقتی یکی از بخش‌های برنامه زمانی فعال است
(function () {
    const originalShowSection = window.showSection;
    if (typeof originalShowSection !== 'function') return;
    window.showSection = function (sectionId) {
        originalShowSection(sectionId);
        const scheduleSections = ['schedules', 'memberSchedules', 'schedulingRules', 'availabilities'];
        const submenu = document.getElementById('scheduleSubmenu');
        const btn = submenu && submenu.previousElementSibling;
        if (submenu && scheduleSections.indexOf(sectionId) !== -1) {
            submenu.classList.remove('hidden');
            if (btn) {
                const chevron = btn.querySelector('.submenu-chevron');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }
        }
    };
})();