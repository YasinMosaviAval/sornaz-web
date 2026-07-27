let aboutUsData = {
    intro: "برنامه آموزشی این آموزشگاه با هدف کمک به هنرجویان موسیقی برای یادگیری مؤثرتر طراحی شده است. علاوه بر همراهی هنرجویان، به‌عنوان دستیار قدرتمند برای اساتید نیز عمل می‌کند.",
    sections: [
        { id: 1, title: "مأموریت ما", content: "ماموریت ما ساده‌سازی مسیر یادگیری موسیقی است. تلاش می‌کنیم محیطی فراهم کنیم که هنرجویان بتوانند بدون پیچیدگی، روند پیشرفت خود را مشاهده و مدیریت کنند." },
        { id: 2, title: "چشم‌انداز ما", content: "چشم‌انداز ما ایجاد فضای آموزشی کامل برای علاقه‌مندان به موسیقی است؛ فضایی که با ابزارها و نمودارهای تحلیلی، روند یادگیری را شفاف و قابل سنجش کند." },
        { id: 3, title: "ارزش‌های ما", content: "ارائه آموزش باکیفیت در اولویت است. باور داریم هنرجویان امروز، هنرمندان فردا هستند؛ بنابراین با ابزارها و برنامه‌ریزی دقیق آن‌ها را در مسیر رشد همراهی می‌کنیم." },
        { id: 4, title: "داستان شکل‌گیری", content: "بسیاری از هنرجویان با سردرگمی در مسیر یادگیری مواجه‌اند. این آموزشگاه از همین نیاز شکل گرفت تا مسیر آموزشی را روشن‌تر و قابل مدیریت کند." },
        { id: 5, title: "تیم ما", content: "توسعه و مدیریت توسط تیم تخصصی آموزشگاه انجام می‌شود. طراحی هویت بصری و برند نیز با همکاری گرافیست‌های حرفه‌ای صورت گرفته است." },
        { id: 6, title: "ویژگی‌های اصلی", content: "مقالات و آموزش‌های تخصصی · پخش‌کننده موسیقی · ضبط صدا · مترونوم · تیونر · مدیریت کلاس و هنرجو" },
        { id: 7, title: "تعهد ما", content: "تمام تلاش ما حمایت از هنرمندان، هنرجویان و علاقه‌مندان موسیقی است و امکانات را به‌صورت مداوم بهبود می‌دهیم." }
    ],
    email: "info@academy.com",
    website: "https://example.com",
    instagram: "https://instagram.com/academy",
    youtube: "https://youtube.com/@academy"
};

window.renderAboutUs = function() {
    const intro = document.getElementById('aboutIntro');
    if (intro) intro.value = aboutUsData.intro || '';

    document.getElementById('aboutEmail').value = aboutUsData.email || '';
    document.getElementById('aboutWebsite').value = aboutUsData.website || '';
    document.getElementById('aboutInstagram').value = aboutUsData.instagram || '';
    document.getElementById('aboutYoutube').value = aboutUsData.youtube || '';

    const container = document.getElementById('aboutSections');
    if (!container) return;
    container.innerHTML = aboutUsData.sections.map((s, i) => `
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <button type="button" onclick="toggleAboutSection(${i})"
                    class="w-full px-6 py-4 flex justify-between items-center hover:bg-gray-50 text-right">
                <span class="font-bold text-lg">${s.title}</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform" id="aboutChevron${i}"></i>
            </button>
            <div id="aboutBody${i}" class="hidden px-6 pb-5">
                <input type="text" value="${s.title}" onchange="aboutUsData.sections[${i}].title=this.value"
                       class="w-full border border-gray-200 rounded-2xl py-2.5 px-4 mb-3 font-medium">
                <textarea rows="4" onchange="aboutUsData.sections[${i}].content=this.value"
                          class="w-full border border-gray-200 rounded-2xl py-3 px-4">${s.content}</textarea>
            </div>
        </div>
    `).join('');
};

window.toggleAboutSection = function(i) {
    const body = document.getElementById('aboutBody' + i);
    const chevron = document.getElementById('aboutChevron' + i);
    if (!body) return;
    body.classList.toggle('hidden');
    if (chevron) chevron.style.transform = body.classList.contains('hidden') ? '' : 'rotate(180deg)';
};

window.saveAboutUs = function() {
    aboutUsData.intro = document.getElementById('aboutIntro')?.value || '';
    aboutUsData.email = document.getElementById('aboutEmail')?.value || '';
    aboutUsData.website = document.getElementById('aboutWebsite')?.value || '';
    aboutUsData.instagram = document.getElementById('aboutInstagram')?.value || '';
    aboutUsData.youtube = document.getElementById('aboutYoutube')?.value || '';
    alert('✅ محتوای درباره ما ذخیره شد');
};

(function() {
    setTimeout(() => {
        if (document.getElementById('aboutSections')) renderAboutUs();
    }, 200);
})();