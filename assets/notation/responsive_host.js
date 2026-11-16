(function () {
    'use strict';
    const boot = window.NOTATION_BOOT;
    if (!boot) return;
    const panel = !!boot.websitePanel;
    const mobile = matchMedia('(max-width: 700px)');
    const standalone = matchMedia('(display-mode: standalone)');
    const touch = matchMedia('(pointer: coarse)');
    function update() {
        const compact = mobile.matches || touch.matches || standalone.matches || navigator.standalone === true;
        const settings = {websitePanel: panel && !compact, websiteHost: panel, compactWebsite: compact};
        try {
            const host = parent !== window ? parent.document.documentElement : document.documentElement;
            const mode = host.dataset.mode || localStorage.getItem('sornaz.personalMode') || localStorage.getItem('sornaz.mode');
            if (mode === 'dark' || mode === 'light') settings.dark = mode === 'dark';
            const accent = getComputedStyle(host).getPropertyValue('--theme-600').trim();
            if (accent) settings.accent = accent;
        } catch (_) {}
        Object.assign(boot, settings);
        document.body.classList.toggle('website-panel', settings.websitePanel);
        document.body.classList.toggle('notation-mobile', compact);
        const style = document.getElementById('notation-panel-style');
        if (style) style.disabled = !settings.websitePanel;
        if (window.Notation?.ready) window.Notation.configure(settings);
    }
    mobile.addEventListener('change', update);
    standalone.addEventListener('change', update);
    touch.addEventListener('change', update);
    try {
        new MutationObserver(update).observe(parent.document.documentElement, {attributes:true,attributeFilter:['data-mode','data-theme']});
    } catch (_) {}
    update();
})();
