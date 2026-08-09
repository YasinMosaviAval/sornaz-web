window.renderHome = function() {
    // آمار از داده‌های موجود (در صورت تعریف)
    const set = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    };
    if (typeof allArticles !== 'undefined') {
        set('homeStatArticles', allArticles.length + '+');
    }
    if (typeof allTeachers !== 'undefined') {
        set('homeStatTeachers', allTeachers.length + '+');
    } else if (typeof allUserInstruments !== 'undefined') {
        // fallback
    }
    if (typeof allStudents !== 'undefined') {
        set('homeStatStudents', allStudents.length + '+');
    }
    if (typeof allCourses !== 'undefined') {
        set('homeStatCourses', allCourses.length + '+');
    }

    // آخرین مقالات
    const box = document.getElementById('homeLatestArticles');
    if (!box) return;

    let articles = [];
    if (typeof allArticles !== 'undefined' && allArticles.length) {
        articles = allArticles.filter(a => a.status !== 'draft').slice(0, 3);
    } else {
        articles = [
            { id: 1, title: "ساختار موسیقی برنامه‌ای ایرانی", summary: "از قاجار تا رادیو و گل‌های رنگارنگ", categories: ["موسیقی ایران"], published_at: "۱۴۰۳/۰۱/۲۳" },
            { id: 2, title: "مفهوم قطعه در موسیقی ایرانی", summary: "آثار متریک خارج از قالب‌های سنتی", categories: ["تئوری موسیقی"], published_at: "۱۴۰۲/۰۶/۰۸" },
            { id: 3, title: "مدرسه عالی موسیقی", summary: "نقش علینقی وزیری در آموزش آکادمیک", categories: ["تاریخ موسیقی"], published_at: "۱۴۰۲/۰۶/۰۸" }
        ];
    }

    box.innerHTML = articles.map(a => `
        <article class="bg-white rounded-3xl p-6 shadow-sm card-hover border border-gray-50 cursor-pointer"
                 onclick="typeof viewArticle==='function' ? viewArticle(${a.id}) : showSection('articles')">
            <div class="flex flex-wrap gap-2 mb-3">
                ${(a.categories || ['آموزش']).slice(0, 2).map(c =>
                    `<span class="px-2.5 py-1 rounded-lg text-xs bg-indigo-50 text-indigo-700">${c}</span>`
                ).join('')}
            </div>
            <h3 class="font-bold text-lg mb-2 line-clamp-2">${a.title}</h3>
            <p class="text-sm text-gray-500 line-clamp-2 mb-4">${a.summary || a.description || ''}</p>
            <div class="text-xs text-gray-400">${a.published_at || ''}</div>
        </article>
    `).join('');
};

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
        if (document.getElementById('home')) renderHome();
        initHomeHeroSlider();
    }, 200);
})();
