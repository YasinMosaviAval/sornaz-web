(() => {
    const header = document.querySelector('body > header');
    if (!header) return;
    const update = () => document.documentElement.style.setProperty('--site-header-height', `${header.getBoundingClientRect().height}px`);
    new ResizeObserver(update).observe(header);
    update();
    const bar = document.getElementById('siteScrollProgressBar');
    const progress = () => {
        if (!bar) return;
        const max = document.documentElement.scrollHeight - innerHeight;
        bar.parentElement.style.display = max > 0 ? 'block' : 'none';
        bar.style.width = `${max > 0 ? Math.min(100, Math.max(0, scrollY / max * 100)) : 0}%`;
    };
    addEventListener('scroll', progress, {passive: true});
    addEventListener('resize', progress);
    progress();
})();
