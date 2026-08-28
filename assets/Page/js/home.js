window.initHomeHeroSlider = function() {
    const slider = document.getElementById('homeHeroSlider');
    if (!slider || slider.dataset.initialized) return;
    slider.dataset.initialized = 'true';
    const slides = Array.from(slider.querySelectorAll('.home-hero-slide'));
    const dotsBox = slider.querySelector('.hero-slider-dots');
    if (slides.length < 2 || !dotsBox) return;
    let current = 0;
    let timer;
    const dots = slides.map((_, index) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'hero-slider-dot' + (index === 0 ? ' is-active' : '');
        dot.setAttribute('aria-label', `نمایش اسلاید ${index + 1}`);
        dot.addEventListener('click', () => show(index, true));
        dotsBox.appendChild(dot);
        return dot;
    });
    const show = (index, restart = false) => {
        current = (index + slides.length) % slides.length;
        slides.forEach((slide, i) => slide.classList.toggle('is-active', i === current));
        dots.forEach((dot, i) => dot.classList.toggle('is-active', i === current));
        if (restart) start();
    };
    const start = () => {
        clearInterval(timer);
        timer = setInterval(() => show(current + 1), 5500);
    };
    slider.querySelector('.hero-slider-prev')?.addEventListener('click', () => show(current - 1, true));
    slider.querySelector('.hero-slider-next')?.addEventListener('click', () => show(current + 1, true));
    slider.addEventListener('mouseenter', () => clearInterval(timer));
    slider.addEventListener('mouseleave', start);
    document.addEventListener('visibilitychange', () => document.hidden ? clearInterval(timer) : start());
    start();
};

(function () {
    setTimeout(() => {
        initHomeHeroSlider();
    }, 200);
})();
