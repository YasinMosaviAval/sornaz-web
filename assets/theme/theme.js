(function () {
    const root = document.documentElement;
    const themes = ['indigo', 'emerald', 'rose', 'amber'];
    const apply = (theme, mode) => {
        const safeTheme = themes.includes(theme) ? theme : 'indigo';
        const safeMode = ['light', 'dark'].includes(mode) ? mode : 'light';
        root.dataset.theme = safeTheme;
        root.dataset.mode = safeMode;
        localStorage.setItem('sornaz.theme', safeTheme);
        localStorage.setItem('sornaz.mode', safeMode);
        document.querySelectorAll('[data-theme-select]').forEach(el => { el.value = safeTheme; });
        document.querySelectorAll('[data-theme-mode]').forEach(button => {
            button.setAttribute('aria-pressed', String(safeMode === 'dark'));
            button.querySelector('[data-light-icon]')?.classList.toggle('hidden', safeMode === 'dark');
            button.querySelector('[data-dark-icon]')?.classList.toggle('hidden', safeMode !== 'dark');
        });
    };
    window.setSiteTheme = theme => apply(theme, root.dataset.mode || 'light');
    window.toggleSiteThemeMode = () => apply(root.dataset.theme || 'indigo', root.dataset.mode === 'dark' ? 'light' : 'dark');
    window.changeSiteLanguage = locale => {
        document.body.classList.add('language-changing');
        window.setTimeout(() => { window.location.href = `/language/${locale}`; }, 90);
    };
    document.addEventListener('DOMContentLoaded', () => apply(root.dataset.theme || 'indigo', root.dataset.mode || 'light'));
})();
