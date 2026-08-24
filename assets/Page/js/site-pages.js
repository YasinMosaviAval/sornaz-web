// —— داده نمونه (اگر allArticles / allAcademiesList از پنل لود شده باشد از همان استفاده می‌شود) ——

const siteArticleCategories = [];

function getSiteArticles() {
    if (Array.isArray(window.siteArticlesData)) return [...window.siteArticlesData].sort((a,b) => String(b.published_at || '').localeCompare(String(a.published_at || '')));
    if (typeof allArticles !== 'undefined' && allArticles.length) {
        return allArticles.filter(a => a.status === 'published' || !a.status).sort((a,b) => String(b.published_at || '').localeCompare(String(a.published_at || '')));
    }
    return [];
}

function getSiteAcademies() {
    if (Array.isArray(window.siteAcademiesData)) return window.siteAcademiesData;
    if (typeof allAcademiesList !== 'undefined' && allAcademiesList.length) return allAcademiesList;
    return [
        {
            id: 1,
            name: "آوای هنر",
            city: "تهران",
            type: "موسیقی",
            slogan: "همراه شما در مسیر یادگیری",
            summary: "آموزشگاه تخصصی پیانو و تئوری",
            // description: "شعب مرکزی و ونک",
            bio: "با بیش از ده سال سابقه در آموزش موسیقی کلاسیک و ایرانی...",
            rating: 4.8,
            classes: 12,
            students: 58,
            teachers_count: 6,
            status: "فعال",
            phones: ["02188776655", "09121234567"],
            links: [
                { title: "اینستاگرام", url: "https://instagram.com/" },
                { title: "وب‌سایت", url: "https://example.com" }
            ],
            addresses: [
                { province: "تهران", city: "تهران", address: "ولیعصر، پلاک ۱۰۰", postal_code: "14157" }
            ]
        },
        {
            id: 2,
            name: "نوای شرق",
            city: "شیراز",
            type: "موسیقی",
            slogan: "همراه شما در مسیر یادگیری",
            summary: "موسیقی ایرانی و ردیف",
            // description: "",
            bio: "با بیش از ده سال سابقه در آموزش موسیقی کلاسیک و ایرانی...",
            rating: 4.5,
            classes: 8,
            students: 40,
            teachers_count: 6,
            status: "فعال",
            phones: ["02188776655", "09121234567"],
            links: [
                { title: "اینستاگرام", url: "https://instagram.com/" },
                { title: "وب‌سایت", url: "https://example.com" }
            ],
            addresses: [
                { province: "تهران", city: "تهران", address: "ولیعصر، پلاک ۱۰۰", postal_code: "14157" }
            ]
        },
        {
            id: 3,
            name: "هنرستان موسیقی تهران",
            city: "تهران",
            type: "موسیقی",
            slogan: "همراه شما در مسیر یادگیری",
            summary: "آموزش رسمی و آکادمیک",
            // description: "از سال ۱۳۵۰",
            bio: "با بیش از ده سال سابقه در آموزش موسیقی کلاسیک و ایرانی...",
            rating: 4.9,
            classes: 40,
            students: 320,
            teachers_count: 6,
            status: "فعال",
            phones: ["02188776655", "09121234567"],
            links: [
                { title: "اینستاگرام", url: "https://instagram.com/" },
                { title: "وب‌سایت", url: "https://example.com" }
            ],
            addresses: [
                { province: "تهران", city: "تهران", address: "ولیعصر، پلاک ۱۰۰", postal_code: "14157" }
            ]
        },
        {
            id: 4,
            name: "خانه موسیقی اصفهان",
            city: "اصفهان",
            type: "موسیقی",
            slogan: "همراه شما در مسیر یادگیری",
            summary: "سنتور، تار و سه‌تار",
            // description: "",
            bio: "با بیش از ده سال سابقه در آموزش موسیقی کلاسیک و ایرانی...",
            rating: 4.6,
            classes: 15,
            students: 90,
            teachers_count: 6,
            status: "فعال",
            phones: ["02188776655", "09121234567"],
            links: [
                { title: "اینستاگرام", url: "https://instagram.com/" },
                { title: "وب‌سایت", url: "https://example.com" }
            ],
            addresses: [
                { province: "تهران", city: "تهران", address: "ولیعصر، پلاک ۱۰۰", postal_code: "14157" }
            ]
        },

    ];
}

const siteUsersSample = [
    {
        id: 1,
        name: "علی رضایی",
        role: "teacher",
        roleLabel: "مدرس",
        city: "تهران",
        bio: "مدرس پیانو و تئوری موسیقی با بیش از ۱۵ سال سابقه تدریس در آموزشگاه‌های تهران.",
        instruments: "پیانو، تئوری",
        instrument_list: [
            { title: "پیانو", level: "حرفه‌ای", is_primary: true },
            { title: "تئوری موسیقی", level: "پیشرفته", is_primary: false }
        ],
        lessons: [
            { title: "سلفژ", level: "متوسط" },
            { title: "هارمونی", level: "پیشرفته" }
        ],
        rating: 4.9,
        years_of_experience: 15,
        start_career_date: "۱۳۸۸",
        level: "حرفه‌ای",
        show_in_public: 1,
        experiences: [
            { title: "تدریس پیانو", organization: "آوای هنر", start_date: "۱۳۹۵", end_date: "اکنون", summary: "کلاس‌های خصوصی و گروهی" }
        ],
        awards: [
            { title: "مدرس برگزیده سال", date: "۱۴۰۲" }
        ],
        badges: [
            { title: "مدرس تأییدشده", status: "active" }
        ],
        academies: [{ id: 1, name: "آوای هنر" }],
        branchId: 1,
        branchName: "شعبه مرکزی"
    },
    {
        id: 2,
        name: "سارا موسوی",
        role: "teacher",
        roleLabel: "مدرس",
        city: "تهران",
        bio: "گیتار کلاسیک و فلامنکو",
        instruments: "گیتار",
        instrument_list: [
            { title: "پیانو", level: "حرفه‌ای", is_primary: true },
            { title: "تئوری موسیقی", level: "پیشرفته", is_primary: false }
        ],
        lessons: [
            { title: "سلفژ", level: "متوسط" },
            { title: "هارمونی", level: "پیشرفته" }
        ],
        rating: 4.8,
        years_of_experience: 15,
        start_career_date: "۱۳۸۸",
        level: "حرفه‌ای",
        show_in_public: 1,
        experiences: [
            { title: "تدریس پیانو", organization: "آوای هنر", start_date: "۱۳۹۵", end_date: "اکنون", summary: "کلاس‌های خصوصی و گروهی" }
        ],
        awards: [
            { title: "مدرس برگزیده سال", date: "۱۴۰۲" }
        ],
        badges: [
            { title: "مدرس تأییدشده", status: "active" }
        ],
        academies: [{ id: 1, name: "آوای هنر" }],
        branchId: 1,
        branchName: "شعبه مرکزی"
    },
    {
        id: 3,
        name: "مینا احمدی",
        role: "student",
        roleLabel: "هنرجو",
        city: "کرج",
        bio: "هنرجوی پیانو سطح متوسط",
        instruments: "پیانو",
        instrument_list: [
            { title: "پیانو", level: "حرفه‌ای", is_primary: true },
            { title: "تئوری موسیقی", level: "پیشرفته", is_primary: false }
        ],
        lessons: [
            { title: "سلفژ", level: "متوسط" },
            { title: "هارمونی", level: "پیشرفته" }
        ],
        rating: null,
        years_of_experience: 15,
        start_career_date: "۱۳۸۸",
        level: "حرفه‌ای",
        show_in_public: 1,
        experiences: [
            { title: "تدریس پیانو", organization: "آوای هنر", start_date: "۱۳۹۵", end_date: "اکنون", summary: "کلاس‌های خصوصی و گروهی" }
        ],
        awards: [
            { title: "مدرس برگزیده سال", date: "۱۴۰۲" }
        ],
        badges: [
            { title: "مدرس تأییدشده", status: "active" }
        ],
        academies: [{ id: 1, name: "آوای هنر" }],
        branchId: 1,
        branchName: "شعبه مرکزی"
    },
    {
        id: 4,
        name: "رضا کریمی",
        role: "student",
        roleLabel: "هنرجو",
        city: "شیراز",
        bio: "علاقه‌مند به موسیقی ایرانی",
        instruments: "سه‌تار",
        instrument_list: [
            { title: "پیانو", level: "حرفه‌ای", is_primary: true },
            { title: "تئوری موسیقی", level: "پیشرفته", is_primary: false }
        ],
        lessons: [
            { title: "سلفژ", level: "متوسط" },
            { title: "هارمونی", level: "پیشرفته" }
        ],
        rating: null,
        years_of_experience: 15,
        start_career_date: "۱۳۸۸",
        level: "حرفه‌ای",
        show_in_public: 1,
        experiences: [
            { title: "تدریس پیانو", organization: "آوای هنر", start_date: "۱۳۹۵", end_date: "اکنون", summary: "کلاس‌های خصوصی و گروهی" }
        ],
        awards: [
            { title: "مدرس برگزیده سال", date: "۱۴۰۲" }
        ],
        badges: [
            { title: "مدرس تأییدشده", status: "active" }
        ],
        academies: [{ id: 1, name: "آوای هنر" }],
        branchId: 1,
        branchName: "شعبه مرکزی"
    },
    {
        id: 5,
        name: "مریم نوری",
        role: "manager",
        roleLabel: "مدیر",
        city: "تهران",
        bio: "مدیر شعبه مرکزی",
        instruments: "",
        instrument_list: [
            { title: "پیانو", level: "حرفه‌ای", is_primary: true },
            { title: "تئوری موسیقی", level: "پیشرفته", is_primary: false }
        ],
        lessons: [
            { title: "سلفژ", level: "متوسط" },
            { title: "هارمونی", level: "پیشرفته" }
        ],
        rating: null,
        years_of_experience: 15,
        start_career_date: "۱۳۸۸",
        level: "حرفه‌ای",
        show_in_public: 1,
        experiences: [
            { title: "تدریس پیانو", organization: "آوای هنر", start_date: "۱۳۹۵", end_date: "اکنون", summary: "کلاس‌های خصوصی و گروهی" }
        ],
        awards: [
            { title: "مدرس برگزیده سال", date: "۱۴۰۲" }
        ],
        badges: [
            { title: "مدرس تأییدشده", status: "active" }
        ],
        academies: [{ id: 1, name: "آوای هنر" }],
        branchId: 1,
        branchName: "شعبه مرکزی"
    },
    {
        id: 6,
        name: "حسین مهدوی",
        role: "teacher",
        roleLabel: "مدرس",
        city: "اصفهان",
        bio: "ویولن و سلفژ",
        instruments: "ویولن",
        instrument_list: [
            { title: "پیانو", level: "حرفه‌ای", is_primary: true },
            { title: "تئوری موسیقی", level: "پیشرفته", is_primary: false }
        ],
        lessons: [
            { title: "سلفژ", level: "متوسط" },
            { title: "هارمونی", level: "پیشرفته" }
        ],
        rating: 4.7,
        years_of_experience: 15,
        start_career_date: "۱۳۸۸",
        level: "حرفه‌ای",
        show_in_public: 1,
        experiences: [
            { title: "تدریس پیانو", organization: "آوای هنر", start_date: "۱۳۹۵", end_date: "اکنون", summary: "کلاس‌های خصوصی و گروهی" }
        ],
        awards: [
            { title: "مدرس برگزیده سال", date: "۱۴۰۲" }
        ],
        badges: [
            { title: "مدرس تأییدشده", status: "active" }
        ],
        academies: [{ id: 1, name: "آوای هنر" }],
        branchId: 1,
        branchName: "شعبه مرکزی"
    },
];







function getSiteUsers() {
    if (Array.isArray(window.siteUsersData)) return window.siteUsersData;
    if (typeof allUsers !== 'undefined' && allUsers.length) return allUsers;
    return siteUsersSample;
}

let siteArtCat = 'all';
let siteUserRole = 'all';

// ========== مقالات ==========
window.renderSiteArticleCats = async function () {
    const box = document.getElementById('siteArticleCats');
    if (!box) return;
    box.querySelectorAll('.site-art-cat:not(:first-child)').forEach(t => t.remove());
    [...new Set(getSiteArticles().flatMap(article => article.categories || []))].forEach(cat => {
        const btn = document.createElement('button');
        btn.className = 'site-art-cat px-4 py-2 rounded-xl text-sm border border-gray-200 hover:bg-gray-50';
        btn.textContent = cat;
        btn.onclick = () => filterSiteArticles(cat);
        box.appendChild(btn);
    });
};

window.filterSiteArticles = async function (cat) {
    if (typeof cat === 'string') siteArtCat = cat;
    document.querySelectorAll('.site-art-cat').forEach(tab => {
        const active = (siteArtCat === 'all' && tab.textContent === 'همه') || tab.textContent === siteArtCat;
        tab.classList.toggle('bg-indigo-600', active);
        tab.classList.toggle('hover:bg-indigo-700', active);
        tab.classList.toggle('hover:bg-gray-50', !active);
        tab.classList.toggle('text-white', active);
        tab.classList.toggle('border', !active);
        tab.classList.toggle('border-gray-200', !active);
    });
    renderSiteArticlesList();
};

window.renderSiteArticlesList = async function () {
    const box = document.getElementById('siteArticlesList');
    if (!box) return;
    const q = (document.getElementById('siteArticleSearch')?.value || '').trim().toLowerCase();
    let list = getSiteArticles();
    if (siteArtCat !== 'all') list = list.filter(a => (a.categories || []).includes(siteArtCat));
    if (q) list = list.filter(a =>
        (a.title || '').toLowerCase().includes(q) ||
        (a.summary || '').toLowerCase().includes(q)
    );

    box.innerHTML = list.length === 0
        ? `<p class="text-center text-gray-400 py-16">مقاله‌ای یافت نشد</p>`
        : list.map(a => `
            <article class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition border border-gray-50 flex flex-col md:flex-row items-center gap-[30px] p-[30px]">
                ${a.thumbnail || a.cover ? `<a href="/analytics/article-details?id=${a.id}" class="block w-full md:w-80 md:shrink-0"><img src="${a.thumbnail || a.cover}" alt="${a.title}" class="block w-full aspect-[16/9] object-cover rounded-xl" loading="lazy"></a>` : ''}
                <div class="flex-1"><div class="flex flex-wrap gap-2 mb-3">
                    ${(a.categories || []).map(c =>
                        `<span class="px-2.5 py-1 rounded-lg text-xs bg-indigo-50 text-indigo-700">${c}</span>`
                    ).join('')}
                </div>
                <a href="/analytics/article-details?id=${a.id}">
                    <h2 class="text-xl font-bold mb-3 hover:text-indigo-600">${a.title}</h2>
                </a>
                <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">${a.summary || a.description || ''}</p>
                <div class="flex items-center justify-between mt-3 text-xs text-gray-400" dir="ltr">
                    <span><i class="far fa-calendar ml-1"></i>${a.published_at || '—'}</span>
                </div>
                </div>
            </article>
        `).join('');
};



// ========== آموزشگاه‌ها ==========
window.renderSiteAcademies = async function () {
    const box = document.getElementById('siteAcademiesGrid');
    if (!box) return;
    const q = (document.getElementById('siteAcademySearch')?.value || '').trim().toLowerCase();
    let list = getSiteAcademies();
    if (q) list = list.filter(a =>
        (a.name || '').toLowerCase().includes(q) ||
        (a.city || '').toLowerCase().includes(q)
    );

    box.innerHTML = list.length === 0
        ? `<p class="col-span-full text-center text-gray-400 py-16">آموزشگاهی یافت نشد</p>`
        : list.map(a => `
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-50 hover:shadow-md transition">
                <h3 class="text-xl font-bold mb-1">${a.name}</h3>
                <p class="text-sm text-gray-500 mb-3">📍 ${a.city || '—'} ${a.rating ? '· ⭐ ' + a.rating : ''}</p>
                ${a.summary ? `<p class="text-sm text-gray-600 mb-4 line-clamp-2">${a.summary}</p>` : ''}
                <div class="grid grid-cols-2 gap-2 text-center mb-5">
                    <div class="bg-gray-50 rounded-2xl py-3">
                        <div class="text-xs text-gray-400">کلاس‌ها</div>
                        <div class="font-bold mt-0.5">${a.classes ?? '—'}</div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl py-3">
                        <div class="text-xs text-gray-400">هنرجوها</div>
                        <div class="font-bold mt-0.5">${a.students ?? '—'}</div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="/academy/academy" class="flex-1 border border-indigo-200 text-indigo-600 py-2.5 rounded-xl text-sm hover:bg-indigo-50 text-center block">
                        مشاهده
                    </a>
                    <a href="/academy/academy-enroll" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl text-sm hover:bg-indigo-700 text-center block">
                        ثبت‌نام
                    </a>
                </div>
            </div>
        `).join('');
};

// window.openSiteAcademy = async function (id) {
//     const a = getSiteAcademies().find(x => x.id === id);
//     if (!a) return;
//     const el = document.getElementById('siteAcademyDetail');
//     if (!el) return;
//     el.innerHTML = `
//         <div class="bg-white rounded-3xl p-8 shadow-sm">
//             <h1 class="text-3xl font-bold mb-2">${a.name}</h1>
//             <p class="text-gray-500 mb-6">📍 ${a.city || '—'} ${a.rating ? '· ⭐ ' + a.rating : ''}</p>
//             ${a.summary ? `<p class="text-indigo-600 font-medium text-lg mb-3">${a.summary}</p>` : ''}
//             ${a.description ? `<p class="text-gray-600 leading-relaxed mb-6">${a.description}</p>` : ''}
//             <div class="grid grid-cols-3 gap-3 text-center mb-8">
//                 <div class="bg-gray-50 rounded-2xl py-4">
//                     <div class="text-xs text-gray-400">کلاس</div>
//                     <div class="text-xl font-bold mt-1">${a.classes ?? '—'}</div>
//                 </div>
//                 <div class="bg-gray-50 rounded-2xl py-4">
//                     <div class="text-xs text-gray-400">هنرجو</div>
//                     <div class="text-xl font-bold mt-1">${a.students ?? '—'}</div>
//                 </div>
//                 <div class="bg-gray-50 rounded-2xl py-4">
//                     <div class="text-xs text-gray-400">امتیاز</div>
//                     <div class="text-xl font-bold mt-1">${a.rating ?? '—'}</div>
//                 </div>
//             </div>
//             <button onclick="showSitePage('login')" class="w-full bg-indigo-600 text-white py-3.5 rounded-2xl hover:bg-indigo-700">
//                 ورود برای ثبت‌نام در کلاس
//             </button>
//         </div>
//     `;
//     showSitePage('academy-detail');
// };



window._currentProfileAcademyId = null;

window.openSiteAcademy = async function (id) {
    openSiteAcademyProfile(id);
};

window.openSiteAcademyProfile = async function (id) {
    const list = (typeof getSiteAcademies === 'function') ? getSiteAcademies() : [];
    // اگر از allBranches پنل هم داده دارید، ادغام کنید
    let a = list.find(x => x.id == id);
    if (!a && typeof allBranches !== 'undefined') {
        a = allBranches.find(x => x.id == id);
    }
    if (!a) {
        alert('آموزشگاه پیدا نشد');
        return;
    }

    window._currentProfileAcademyId = a.id;

    // نام و آواتار
    document.getElementById('apName').textContent = a.name || 'آموزشگاه';
    const initial = (a.name || 'آ').charAt(0);
    const avatar = document.getElementById('apAvatar');
    if (avatar) avatar.textContent = initial;

    // شعار
    const sloganEl = document.getElementById('apSlogan');
    if (a.slogan) {
        sloganEl.textContent = a.slogan;
        sloganEl.classList.remove('hidden');
    } else {
        sloganEl.classList.add('hidden');
    }

    // نوع
    const typeBadge = document.getElementById('apTypeBadge');
    if (a.type) {
        typeBadge.textContent = a.type;
        typeBadge.classList.remove('hidden');
    } else {
        typeBadge.classList.add('hidden');
    }

    // مکان
    const city = a.city || (a.addresses && a.addresses[0] && (a.addresses[0].city || a.addresses[0].province)) || '';
    const rating = a.rating != null ? ` · ⭐ ${a.rating}` : '';
    document.getElementById('apLocation').textContent = city ? `📍 ${city}${rating}` : (rating ? rating.replace(' · ', '') : '');

    // آمار
    document.getElementById('apRating').textContent = a.rating != null ? a.rating : '—';
    document.getElementById('apClasses').textContent = a.classes ?? a.classrooms ?? '—';
    document.getElementById('apStudents').textContent = a.students != null ? a.students : '—';
    document.getElementById('apTeachers').textContent = a.teachers_count != null ? a.teachers_count : (a.teachers ? a.teachers.length : '—');

    // درباره
    const sumEl = document.getElementById('apSummary');
    if (a.summary) {
        sumEl.textContent = a.summary;
        sumEl.classList.remove('hidden');
    } else {
        sumEl.classList.add('hidden');
    }
    document.getElementById('apBio').textContent =
        a.bio || a.description || 'توضیحی برای این آموزشگاه ثبت نشده است.';

    // وضعیت
    const st = document.getElementById('apStatus');
    const statusText = a.status || 'فعال';
    st.textContent = statusText;
    st.className = 'inline-flex px-3 py-1.5 rounded-full text-xs font-medium ' +
        (statusText === 'فعال' || statusText === 'active'
            ? 'bg-green-100 text-green-700'
            : 'bg-gray-100 text-gray-600');

    // تماس
    renderProfileContacts(a);
    // آدرس
    renderProfileAddresses(a);
    // دوره‌ها
    renderProfileCourses(a);
    // اساتید
    renderProfileTeachers(a);

    showSitePage('academy-profile');
};

function renderProfileContacts(a) {
    const box = document.getElementById('apContacts');
    if (!box) return;
    const items = [];

    // phones: آرایه رشته یا آبجکت
    const phones = a.phones || (a.phone ? [a.phone] : []);
    phones.forEach(p => {
        const num = typeof p === 'string' ? p : (p.number || p.value || '');
        if (num) {
            items.push(`
                <a href="tel:${num}" class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 hover:bg-indigo-50 transition">
                    <span class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="fas fa-phone"></i></span>
                    <span dir="ltr">${num}</span>
                </a>`);
        }
    });

    // links
    const links = a.links || [];
    links.forEach(l => {
        const title = l.title || l.platform || 'لینک';
        const url = l.url || '#';
        items.push(`
            <a href="${url}" target="_blank" rel="noopener"
               class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 hover:bg-indigo-50 transition">
                <span class="w-9 h-9 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center"><i class="fas fa-link"></i></span>
                <span class="truncate">${title}</span>
            </a>`);
    });

    if (a.manager) {
        items.push(`
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50">
                <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fas fa-user-tie"></i></span>
                <span>مدیر: ${a.manager}</span>
            </div>`);
    }

    box.innerHTML = items.length
        ? items.join('')
        : `<p class="text-gray-400 text-center py-2">اطلاعات تماسی ثبت نشده</p>`;
}

function renderProfileAddresses(a) {
    const box = document.getElementById('apAddresses');
    if (!box) return;

    let addresses = a.addresses || [];
    if (!addresses.length && a.address) {
        addresses = [typeof a.address === 'string' ? { address: a.address } : a.address];
    }
    if (!addresses.length && a.city) {
        addresses = [{ city: a.city, address: a.city }];
    }

    box.innerHTML = addresses.length === 0
        ? `<p class="text-gray-400 text-center py-2">آدرسی ثبت نشده</p>`
        : addresses.map((addr, i) => {
            const line = typeof addr === 'string'
                ? addr
                : [addr.province, addr.city, addr.address].filter(Boolean).join('، ') || addr.address || '—';
            const postal = addr.postal_code ? `<div class="text-xs text-gray-400 mt-1">کد پستی: ${addr.postal_code}</div>` : '';
            return `
                <div class="p-3 rounded-2xl bg-gray-50">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt text-indigo-500 mt-1"></i>
                        <div>
                            ${i === 0 ? '<span class="text-xs text-indigo-600 font-medium">آدرس اصلی</span>' : ''}
                            <p class="text-gray-700">${line}</p>
                            ${postal}
                        </div>
                    </div>
                </div>`;
        }).join('');
}

function renderProfileCourses(a) {
    const box = document.getElementById('apCourses');
    if (!box) return;

    // از داده آموزشگاه یا نمونه
    let courses = a.courses || a.course_list || [];
    if (!courses.length) {
        courses = [
            { title: 'پیانو مبتدی', level: 'مبتدی', students: 12 },
            { title: 'تئوری موسیقی', level: 'همه سطوح', students: 20 },
            { title: 'گیتار کلاسیک', level: 'متوسط', students: 8 }
        ];
    }

    box.innerHTML = courses.map(c => `
        <div class="flex items-center justify-between gap-3 p-4 rounded-2xl border border-gray-100 hover:border-indigo-100 transition">
            <div>
                <div class="font-medium">${c.title || c.name}</div>
                <div class="text-xs text-gray-400 mt-0.5">${c.level || ''} ${c.students != null ? '· ' + c.students + ' هنرجو' : ''}</div>
            </div>
            <button type="button" onclick="goEnrollFromProfile()"
                    class="text-indigo-600 text-sm shrink-0 hover:underline">ثبت‌نام</button>
        </div>
    `).join('');
}

function renderProfileTeachers(a) {
    const box = document.getElementById('apTeachersList');
    if (!box) return;

    let teachers = a.teachers || [];
    if (!teachers.length && typeof getSiteUsers === 'function') {
        teachers = getSiteUsers().filter(u => u.role === 'teacher').slice(0, 4);
    }
    if (!teachers.length) {
        teachers = [
            { name: 'علی رضایی', instruments: 'پیانو', roleLabel: 'مدرس' },
            { name: 'سارا موسوی', instruments: 'گیتار', roleLabel: 'مدرس' }
        ];
    }

    box.innerHTML = teachers.map(t => `
        <div class="flex items-center gap-3 p-3 rounded-2xl border border-gray-100">
            <div class="w-11 h-11 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold shrink-0">
                ${(t.name || '?').charAt(0)}
            </div>
            <div class="min-w-0">
                <div class="font-medium text-sm truncate">${t.name}</div>
                <div class="text-xs text-gray-400 truncate">${t.instruments || t.roleLabel || 'مدرس'}</div>
            </div>
        </div>
    `).join('');
}

window.goEnrollFromProfile = async function () {
    const id = window._currentProfileAcademyId;
    showSitePage('academy-enroll');
    setTimeout(() => {
        if (typeof renderSiteEnrollPage === 'function') renderSiteEnrollPage();
        const sel = document.getElementById('siteEnrollAcademy');
        if (sel && id != null) sel.value = String(id);
    }, 80);
};










// ========== کاربران ==========
window.filterSiteUsers = async function (role) {
    if (typeof role === 'string') siteUserRole = role;
    document.querySelectorAll('.site-user-role').forEach(tab => {
        const map = { all: 'همه', teacher: 'اساتید', student: 'هنرجویان', manager: 'مدیران' };
        const active = tab.textContent === map[siteUserRole];
        tab.classList.toggle('bg-indigo-600', active);
        tab.classList.toggle('text-white', active);
        tab.classList.toggle('border', !active);
        tab.classList.toggle('border-gray-200', !active);
    });
    renderSiteUsers();
};

window.renderSiteUsers = async function () {
    const box = document.getElementById('siteUsersGrid');
    if (!box) return;
    const q = (document.getElementById('siteUserSearch')?.value || '').trim().toLowerCase();
    let list = getSiteUsers();
    if (siteUserRole !== 'all') list = list.filter(u => u.role === siteUserRole);
    if (q) list = list.filter(u => (u.name || '').toLowerCase().includes(q));

    const roleColors = {
        teacher: 'bg-purple-100 text-purple-700',
        student: 'bg-blue-100 text-blue-700',
        manager: 'bg-amber-100 text-amber-700'
    };

    box.innerHTML = list.length === 0
        ? `<p class="col-span-full text-center text-gray-400 py-16">کاربری یافت نشد</p>`
        : list.map(u => `
            <div class="relative bg-white rounded-3xl p-6 shadow-sm border border-gray-50 text-center hover:shadow-md transition cursor-pointer"
                 onclick="openSiteUser(${u.id})">
                <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-indigo-100 text-indigo-600 overflow-hidden flex items-center justify-center text-2xl font-bold">
                    ${u.avatar ? `<img src="${u.avatar}" alt="${u.name || 'کاربر'}" class="w-full h-full object-cover" loading="lazy">` : (u.name || '?').charAt(0)}
                </div>
                <h3 class="font-bold text-lg mb-1">${u.name}</h3>
                <span class="inline-block px-2.5 py-0.5 rounded-lg text-xs ${roleColors[u.role] || 'bg-gray-100'} mb-2">
                    ${u.roleLabel || u.role}
                </span>
                ${u.city ? `<p class="text-xs text-gray-400 mb-2">📍 ${u.city}</p>` : ''}
                <p class="text-sm text-gray-500 line-clamp-2">${u.bio || ''}</p>
                ${u.rating ? `<p class="text-amber-500 text-sm mt-2">⭐ ${u.rating}</p>` : ''}
                <br>
                <div class="flex gap-2 absolute bottom-0 left-0 right-0 p-4 bg-white">
                    <button type="button" onclick="event.stopPropagation();openSiteUser(${u.id})" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl text-sm hover:bg-indigo-700 text-center block">
                        مشاهده
                    </button>
                </div>
            </div>
        `).join('');
};

// window.openSiteUser = async function (id) {
//     const u = getSiteUsers().find(x => x.id === id);
//     if (!u) return;
//     const el = document.getElementById('siteUserDetail');
//     if (!el) return;
//     el.innerHTML = `
//         <div class="bg-white rounded-3xl p-8 shadow-sm text-center">
//             <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl font-bold">
//                 ${(u.name || '?').charAt(0)}
//             </div>
//             <h1 class="text-2xl font-bold mb-1">${u.name}</h1>
//             <p class="text-indigo-600 text-sm mb-4">${u.roleLabel || u.role}</p>
//             ${u.city ? `<p class="text-gray-400 text-sm mb-4">📍 ${u.city}</p>` : ''}
//             <p class="text-gray-600 leading-relaxed mb-4">${u.bio || ''}</p>
//             ${u.instruments ? `<p class="text-sm text-gray-500"><span class="font-medium">ساز / تخصص:</span> ${u.instruments}</p>` : ''}
//             ${u.rating ? `<p class="text-amber-500 text-lg mt-4">⭐ ${u.rating}</p>` : ''}
//         </div>
//     `;
//     showSitePage('user-detail');
// };



window.openSiteUser = async function (id) {
    openSiteUserProfile(id);
};

window.openSiteUserProfile = async function (id) {
    const list = (typeof getSiteUsers === 'function') ? getSiteUsers() : [];
    const u = list.find(x => x.id == id);
    if (!u) {
        alert('کاربر پیدا نشد');
        return;
    }

    window._currentProfileUserId = u.id;

    // نام و آواتار
    document.getElementById('upName').textContent = u.name || 'کاربر';
    const avatar = document.getElementById('upAvatar');
    if (avatar) avatar.innerHTML = u.avatar
        ? `<img src="${u.avatar}" alt="${u.name || 'کاربر'}" class="w-full h-full rounded-full object-cover">`
        : (u.name || '?').charAt(0);
    const cover = document.getElementById('upCover');
    if (cover) {
        cover.style.backgroundImage = u.cover ? `url("${u.cover}")` : '';
        cover.classList.toggle('bg-cover', !!u.cover);
        cover.classList.toggle('bg-center', !!u.cover);
    }

    // نقش
    const roleBadge = document.getElementById('upRoleBadge');
    const roleMap = {
        teacher: { label: 'مدرس', cls: 'bg-purple-100 text-purple-700' },
        student: { label: 'هنرجو', cls: 'bg-blue-100 text-blue-700' },
        manager: { label: 'مدیر', cls: 'bg-amber-100 text-amber-700' }
    };
    const roleKey = u.role || 'student';
    const role = roleMap[roleKey] || { label: u.roleLabel || roleKey, cls: 'bg-gray-100 text-gray-600' };
    roleBadge.textContent = u.roleLabel || role.label;
    roleBadge.className = 'px-3 py-1 rounded-full text-xs font-medium ' + role.cls;

    // تیتر و مکان
    document.getElementById('upHeadline').textContent =
        u.headline || u.instruments || (u.roleLabel ? u.roleLabel + (u.city ? ' · ' + u.city : '') : '');
    document.getElementById('upLocation').textContent = u.city ? '📍 ' + u.city : '';

    // آمار
    document.getElementById('upRating').textContent = u.rating != null ? u.rating : '—';
    const instruments = normalizeList(u.instruments);
    document.getElementById('upInstrumentsCount').textContent =
        u.instruments_count != null ? u.instruments_count : (instruments.length || '—');
    document.getElementById('upYears').textContent =
        u.years_of_experience != null ? u.years_of_experience : (u.years || '—');
    document.getElementById('upBadgesCount').textContent =
        (u.badges && u.badges.length) ? u.badges.length : (u.badges_count || '—');

    // درباره
    document.getElementById('upBio').textContent =
        u.bio || u.description || 'بیوگرافی ثبت نشده است.';

    // سازها
    renderUserInstruments(u, instruments);
    // درس‌ها
    renderUserLessons(u);
    // تجربه‌ها
    renderUserExperiences(u);
    // افتخارات
    renderUserAchievements(u);
    // اطلاعات سایدبار
    renderUserInfo(u, role);
    // نشان‌ها
    renderUserBadges(u);
    // آموزشگاه‌ها
    renderUserAcademies(u);
    renderUserMedia(u);
    renderUserAddresses(u);
    renderUserContacts(u);
    renderUserAvailability(u);

    showSitePage('user-profile');
};

function normalizeList(val) {
    if (!val) return [];
    if (Array.isArray(val)) return val.map(v => (typeof v === 'string' ? v : v.title || v.name || '')).filter(Boolean);
    if (typeof val === 'string') return val.split(/[,،]/).map(s => s.trim()).filter(Boolean);
    return [];
}

function renderUserInstruments(u, instruments) {
    const box = document.getElementById('upInstruments');
    if (!box) return;
    // اگر آبجکت با level
    let items = [];
    if (Array.isArray(u.instrument_list) && u.instrument_list.length) {
        items = u.instrument_list;
    } else {
        items = instruments.map(name => ({ title: name, is_primary: false }));
    }
    if (!items.length) {
        box.innerHTML = '<p class="text-sm text-gray-400">سازی ثبت نشده</p>';
        return;
    }
    box.innerHTML = items.map(it => {
        const title = it.title || it.name || it;
        const primary = it.is_primary ? ' ring-2 ring-indigo-300' : '';
        const level = it.level ? `<span class="text-xs text-gray-400 mr-1">(${it.level})</span>` : '';
        return `<span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm bg-indigo-50 text-indigo-700${primary}">
            ${it.is_primary ? '<i class="fas fa-star text-amber-400 text-xs ml-1"></i>' : ''}
            ${title}${level}
        </span>`;
    }).join('');
}

function renderUserLessons(u) {
    const box = document.getElementById('upLessons');
    if (!box) return;
    const lessons = u.lessons || u.lesson_list || [];
    if (!lessons.length) {
        box.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">درسی ثبت نشده</p>';
        return;
    }
    box.innerHTML = lessons.map(l => {
        const title = l.title || l.name || l;
        const level = l.level ? ` · ${l.level}` : '';
        return `<div class="p-3 rounded-2xl border border-gray-100">
            <div class="flex items-center justify-between gap-3"><span class="font-medium text-sm">${title}</span>
            <span class="text-xs text-gray-400">${level.replace(' · ', '')}</span></div>
            ${l.start_date ? `<div class="text-xs text-gray-400 mt-2">شروع: ${l.start_date}</div>` : ''}
            ${l.summary ? `<p class="text-sm text-gray-500 mt-2">${l.summary}</p>` : ''}
        </div>`;
    }).join('');
}

function renderUserExperiences(u) {
    const box = document.getElementById('upExperiences');
    if (!box) return;
    const exps = u.experiences || [];
    if (!exps.length) {
        // نمونه خالی یا از allExperiences فیلتر با user_id
        let fromGlobal = [];
        if (typeof allExperiences !== 'undefined') {
            fromGlobal = allExperiences.filter(e => e.user_id == u.id).slice(0, 5);
        }
        if (!fromGlobal.length) {
            box.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">سابقه‌ای ثبت نشده</p>';
            return;
        }
        box.innerHTML = fromGlobal.map(expItemHTML).join('');
        return;
    }
    box.innerHTML = exps.map(expItemHTML).join('');
}

function expItemHTML(e) {
    const title = e.title || e.name || 'تجربه';
    const org = e.organization || '';
    const dates = [e.start_date, e.end_date].filter(Boolean).join(' تا ');
    return `<div class="border-r-4 border-indigo-200 pr-4">
        <div class="font-medium">${title}</div>
        ${org ? `<div class="text-sm text-indigo-600">${org}</div>` : ''}
        ${dates ? `<div class="text-xs text-gray-400 mt-1">${dates}</div>` : ''}
        ${e.summary || e.description ? `<p class="text-sm text-gray-500 mt-1 line-clamp-2">${e.summary || e.description}</p>` : ''}
    </div>`;
}

function renderUserAchievements(u) {
    const box = document.getElementById('upAchievements');
    if (!box) return;
    const awards = u.awards || u.publications || [];
    if (!awards.length) {
        box.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">موردی ثبت نشده</p>';
        return;
    }
    box.innerHTML = awards.map(a => `
        <div class="flex items-start gap-3 p-3 rounded-2xl bg-gray-50">
            <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fas fa-trophy"></i>
            </span>
            <div>
                <div class="font-medium text-sm">${a.title || a.name}</div>
                <div class="text-xs text-gray-400">${a.date || a.published_date || a.organization || ''}</div>
            </div>
        </div>
    `).join('');
}

function renderUserInfo(u, role) {
    const box = document.getElementById('upInfo');
    if (!box) return;
    const rows = [
        { label: 'نقش', value: u.roleLabel || role.label },
        { label: 'شهر', value: u.city },
        { label: 'سطح', value: u.level || u.student_level },
        { label: 'شروع فعالیت', value: u.start_career_date || u.start_date },
        { label: 'نام کاربری', value: u.username },
        { label: 'جنسیت', value: u.gender === 'male' ? 'مرد' : (u.gender === 'female' ? 'زن' : u.gender) },
        { label: 'وضعیت', value: u.status },
        { label: 'تلفن', value: u.phone },
        { label: 'ایمیل', value: u.email },
        { label: 'تولد', value: u.birthday },
        { label: 'تاریخ عضویت', value: u.register_time },
        { label: 'روش عضویت', value: u.register_method },
        { label: 'نمایش عمومی', value: u.show_in_public === 0 ? 'خصوصی' : 'عمومی' }
    ].filter(r => r.value != null && r.value !== '');

    box.innerHTML = rows.length
        ? rows.map(r => `
            <div class="flex justify-between gap-2 border-b border-gray-50 pb-2">
                <span class="text-gray-400">${r.label}</span>
                <span class="font-medium text-left">${r.value}</span>
            </div>`).join('')
        : '<p class="text-gray-400 text-center">—</p>';
}

let userGalleryImages = [];
let userGalleryIndex = 0;

function renderUserMedia(u) {
    const videoSection = document.getElementById('upIntroSection');
    const video = document.getElementById('upIntroVideo');
    if (videoSection && video) {
        videoSection.classList.toggle('hidden', !u.intro_video);
        video.src = u.intro_video || '';
        if (!u.intro_video) video.removeAttribute('src');
        video.load();
    }
    userGalleryImages = Array.isArray(u.gallery) ? u.gallery : [];
    const gallerySection = document.getElementById('upGallerySection');
    const gallery = document.getElementById('upGallery');
    if (gallerySection) gallerySection.classList.toggle('hidden', !userGalleryImages.length);
    if (gallery) gallery.innerHTML = userGalleryImages.map((src, index) => `
        <button type="button" onclick="openUserGalleryDialog(${index})" class="aspect-[4/3] overflow-hidden rounded-2xl bg-gray-100 focus:ring-2 focus:ring-indigo-500">
            <img src="${src}" alt="تصویر ${index + 1} گالری" class="w-full h-full object-cover transition duration-300 hover:scale-105" loading="lazy">
        </button>`).join('');
}

function renderUserAddresses(u) {
    const box = document.getElementById('upAddresses'); if (!box) return;
    const rows = Array.isArray(u.addresses) ? u.addresses : [];
    box.innerHTML = rows.length ? rows.map(row => `<div class="rounded-2xl bg-gray-50 p-3">
        <div class="font-medium leading-6">${row.address || '—'}</div>${row.note ? `<p class="text-xs text-gray-400 mt-2">${row.note}</p>` : ''}
        ${row.is_main ? '<span class="inline-block mt-2 text-xs text-indigo-600">نشانی اصلی</span>' : ''}</div>`).join('') : '<p class="text-gray-400">نشانی ثبت نشده</p>';
}

function renderUserContacts(u) {
    const box = document.getElementById('upContacts'); if (!box) return;
    const rows = Array.isArray(u.contacts) ? u.contacts : [];
    box.innerHTML = rows.length ? rows.map(row => `<div class="rounded-2xl bg-gray-50 p-3">
        <div class="flex justify-between gap-2"><span class="font-medium break-all">${row.value || '—'}</span><span class="text-xs text-gray-400">${row.platform || row.mode || ''}</span></div>
        ${row.note ? `<p class="text-xs text-gray-400 mt-2">${row.note}</p>` : ''}${row.is_main ? '<span class="inline-block mt-2 text-xs text-indigo-600">راه اصلی</span>' : ''}</div>`).join('') : '<p class="text-gray-400">راه ارتباطی ثبت نشده</p>';
}

function renderUserAvailability(u) {
    const dayLabels={saturday:'شنبه',sunday:'یکشنبه',monday:'دوشنبه',tuesday:'سه‌شنبه',wednesday:'چهارشنبه',thursday:'پنجشنبه',friday:'جمعه'};
    const typeLabels={holiday:'تعطیل',closed:'بسته',unavailable:'عدم حضور',busy:'مشغول',vacation:'مرخصی',blocked:'مسدود'};
    const rows=Array.isArray(u.availabilities)?u.availabilities:[];
    const box=document.getElementById('upAvailability');
    if(box){
        const recurring=rows.filter(row=>row.is_repeating || !row.date); const specific=rows.filter(row=>row.date);
        const grouped={}; recurring.forEach(row=>(grouped[row.day_of_week]??=[]).push(row));
        box.innerHTML=Object.keys(dayLabels).map(day=>`<div class="grid grid-cols-1 sm:grid-cols-[100px_1fr] gap-2 border-b border-gray-100 pb-3">
            <div class="font-medium">${dayLabels[day]}</div><div class="flex flex-wrap gap-2">${(grouped[day]||[]).map(row=>`<span class="rounded-xl bg-emerald-50 text-emerald-700 px-3 py-1.5 text-sm" title="${row.description||''}">${String(row.start_time).slice(0,5)} تا ${String(row.end_time).slice(0,5)}</span>`).join('')||'<span class="text-gray-400 text-sm">بدون برنامه</span>'}</div></div>`).join('')+
            (specific.length?`<div class="pt-2"><h3 class="font-medium mb-2">حضورهای تاریخ‌دار</h3>${specific.map(row=>`<div class="text-sm rounded-xl bg-indigo-50 text-indigo-700 px-3 py-2 mb-2">${row.date} · ${String(row.start_time).slice(0,5)} تا ${String(row.end_time).slice(0,5)} — ${row.summary||''}</div>`).join('')}</div>`:'');
    }
    const exceptionBox=document.getElementById('upAvailabilityExceptions'); const exceptions=Array.isArray(u.availability_exceptions)?u.availability_exceptions:[];
    if(exceptionBox) exceptionBox.innerHTML=exceptions.length?exceptions.map(row=>`<div class="rounded-2xl bg-rose-50 p-4"><div class="flex flex-wrap justify-between gap-2"><span class="font-medium text-rose-700">${typeLabels[row.type]||row.type}</span><span class="text-sm text-gray-500">${row.date}${row.start_time?' · '+String(row.start_time).slice(0,5)+' تا '+String(row.end_time).slice(0,5):' · تمام‌روز'}</span></div><p class="text-sm text-gray-600 mt-2">${row.summary||''}</p>${row.description?`<p class="text-xs text-gray-400 mt-1">${row.description}</p>`:''}</div>`).join(''):'<p class="text-gray-400">موردی ثبت نشده</p>';
}

window.openUserGalleryDialog = async function (index) {
    if (!userGalleryImages.length) return;
    userGalleryIndex = index;
    const dialog = document.getElementById('userGalleryDialog');
    dialog.classList.remove('hidden'); dialog.classList.add('flex');
    document.body.classList.add('overflow-hidden'); updateUserGalleryDialog();
};
window.closeUserGalleryDialog = async function () {
    const dialog = document.getElementById('userGalleryDialog');
    dialog.classList.add('hidden'); dialog.classList.remove('flex'); document.body.classList.remove('overflow-hidden');
};
window.moveUserGallery = async function (step) { userGalleryIndex = (userGalleryIndex + step + userGalleryImages.length) % userGalleryImages.length; updateUserGalleryDialog(); };
function updateUserGalleryDialog() {
    document.getElementById('userGalleryDialogImage').src = userGalleryImages[userGalleryIndex];
    document.getElementById('userGalleryDialogCounter').textContent = `${userGalleryIndex + 1} از ${userGalleryImages.length}`;
}
document.addEventListener('keydown', event => {
    const dialog = document.getElementById('userGalleryDialog'); if (!dialog || dialog.classList.contains('hidden')) return;
    if (event.key === 'Escape') closeUserGalleryDialog();
    if (event.key === 'ArrowRight') moveUserGallery(-1);
    if (event.key === 'ArrowLeft') moveUserGallery(1);
});

function renderUserBadges(u) {
    const box = document.getElementById('upBadges');
    if (!box) return;
    let badges = u.badges || [];
    if (!badges.length && typeof allBadges !== 'undefined') {
        badges = allBadges.filter(b => b.user_id == u.id && b.status === 'active').slice(0, 6);
    }
    if (!badges.length) {
        box.innerHTML = '<p class="text-sm text-gray-400">نشانی نیست</p>';
        return;
    }
    box.innerHTML = badges.map(b => `
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs bg-violet-50 text-violet-700"
              title="${b.summary || ''}">
            <i class="fas fa-medal"></i> ${b.title || b.name}
        </span>
    `).join('');
}

function renderUserAcademies(u) {
    const box = document.getElementById('upAcademies');
    if (!box) return;
    let academies = u.academies || [];
    if (!academies.length && u.branchName) {
        academies = [{ name: u.branchName, id: u.branchId }];
    }
    if (!academies.length) {
        box.innerHTML = '<p class="text-sm text-gray-400">—</p>';
        return;
    }
    box.innerHTML = academies.map(a => {
        const name = a.name || a;
        const id = a.id;
        if (id && typeof openSiteAcademyProfile === 'function') {
            return `<button type="button" onclick="openSiteAcademyProfile(${id})"
                        class="w-full text-right p-3 rounded-2xl bg-gray-50 hover:bg-indigo-50 text-sm font-medium transition">
                        ${name}
                    </button>`;
        }
        return `<div class="p-3 rounded-2xl bg-gray-50 text-sm font-medium">${name}</div>`;
    }).join('');
}



// ========== اتصال به showSitePage ==========
const _origShowSitePage = window.showSitePage;
window.showSitePage = async function (page) {
    if (typeof _origShowSitePage === 'function') _origShowSitePage(page);
    else {
        document.querySelectorAll('.site-page').forEach(el => el.classList.remove('active'));
        document.getElementById('page-' + page)?.classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    if (page === 'articles') {
        renderSiteArticleCats();
        filterSiteArticles('all');
    }
    if (page === 'academies') renderSiteAcademies();
    if (page === 'users') filterSiteUsers('all');
};




// ========== ثبت‌نام در کلاس (سایت) ==========
window.renderSiteEnrollPage = async function () {
    const sel = document.getElementById('siteEnrollAcademy');
    if (!sel) return;
    const list = (typeof getSiteAcademies === 'function') ? getSiteAcademies() : [];
    sel.innerHTML = '<option value="">انتخاب آموزشگاه</option>' +
        list.map(a => `<option value="${a.id}">${a.name}${a.city ? ' — ' + a.city : ''}</option>`).join('');
};

window.submitSiteEnroll = async function () {
    const name = document.getElementById('siteEnrollName')?.value.trim();
    const phone = document.getElementById('siteEnrollPhone')?.value.trim();
    const academyId = document.getElementById('siteEnrollAcademy')?.value;
    const course = document.getElementById('siteEnrollCourse')?.value;

    if (!name || !phone || !academyId || !course) {
        return alert('لطفاً فیلدهای ستاره‌دار را کامل کنید');
    }
    if (!/^09\d{9}$/.test(phone.replace(/\s/g, ''))) {
        return alert('شماره موبایل معتبر نیست (مثال: 09123456789)');
    }

    const academy = (typeof getSiteAcademies === 'function')
        ? getSiteAcademies().find(a => a.id == academyId) : null;
    const courseText = document.getElementById('siteEnrollCourse')?.selectedOptions[0]?.text || course;

    const payload = {
        id: Date.now(),
        name,
        phone: phone.replace(/\s/g, ''),
        email: document.getElementById('siteEnrollEmail')?.value.trim() || '',
        academy_id: academyId,
        academy: academy ? academy.name : '—',
        course: courseText,
        level: document.getElementById('siteEnrollLevel')?.value || 'beginner',
        note: document.getElementById('siteEnrollNote')?.value.trim() || '',
        status: 'pending',
        created_at: new Date().toLocaleDateString('fa-IR')
    };

    // اگر آرایه پنل ادمین موجود باشد، همان‌جا هم اضافه می‌شود
    if (typeof allEnrollments !== 'undefined') {
        allEnrollments.unshift(payload);
        if (typeof renderEnrollmentsTable === 'function') renderEnrollmentsTable();
    } else {
        try {
            const key = 'siteEnrollments';
            const prev = JSON.parse(localStorage.getItem(key) || '[]');
            prev.unshift(payload);
            localStorage.setItem(key, JSON.stringify(prev));
        } catch (e) {}
    }

    ['siteEnrollName', 'siteEnrollPhone', 'siteEnrollEmail', 'siteEnrollNote'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    const courseSel = document.getElementById('siteEnrollCourse');
    if (courseSel) courseSel.value = '';

    alert('✅ درخواست ثبت‌نام ارسال شد.\nبه‌زودی برای هماهنگی زمان کلاس با شما تماس گرفته می‌شود.');
};

// ========== ثبت آموزشگاه (سایت) ==========
window.submitSiteAcademyRequest = async function () {
    const email = document.getElementById('siteReqEmail')?.value.trim();
    const username = document.getElementById('siteReqUsername')?.value.trim();
    const pass = document.getElementById('siteReqPassword')?.value;
    const pass2 = document.getElementById('siteReqPassword2')?.value;
    const name = document.getElementById('siteReqAcademyName')?.value.trim();

    if (!email || !username || !pass || !name) {
        return alert('فیلدهای ستاره‌دار الزامی است');
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        return alert('ایمیل معتبر نیست');
    }
    if (pass.length < 8) {
        return alert('رمز عبور حداقل ۸ کاراکتر باشد');
    }
    if (pass !== pass2) {
        return alert('رمز عبور و تکرار آن یکسان نیست');
    }

    const payload = {
        id: Date.now(),
        email,
        username,
        academy_name: name,
        short_desc: document.getElementById('siteReqShortDesc')?.value.trim() || '',
        bio: document.getElementById('siteReqBio')?.value.trim() || '',
        status: 'pending',
        created_at: new Date().toLocaleDateString('fa-IR')
    };

    if (typeof allAcademyRequests !== 'undefined') {
        allAcademyRequests.unshift(payload);
        if (typeof renderAcademyRequestsTable === 'function') renderAcademyRequestsTable();
    } else {
        try {
            const key = 'siteAcademyRequests';
            const prev = JSON.parse(localStorage.getItem(key) || '[]');
            prev.unshift(payload);
            localStorage.setItem(key, JSON.stringify(prev));
        } catch (e) {}
    }

    ['siteReqEmail', 'siteReqUsername', 'siteReqPassword', 'siteReqPassword2',
     'siteReqAcademyName', 'siteReqShortDesc', 'siteReqBio'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    alert('✅ ثبت آموزشگاه انجام شد.\nپس از بررسی، نتیجه از طریق ایمیل اعلام می‌شود.');
};



window.openSiteArticle = async function (id) {
    const list = (typeof getSiteArticles === 'function') ? getSiteArticles() : [];
    const a = list.find(x => x.id == id);
    if (!a) {
        alert('مقاله پیدا نشد');
        return;
    }

    // عنوان و breadcrumb
    document.getElementById('adTitle').textContent = a.title || '';
    document.getElementById('adBreadcrumbTitle').textContent = a.title || 'مقاله';

    // تاریخ‌ها
    document.getElementById('adPublished').textContent = a.published_at || a.published_date || '—';
    const updated = a.updated_at || a.updated_date || a.published_at || '';
    document.getElementById('adUpdated').textContent = updated || '—';
    document.getElementById('adUpdatedWrap').style.display = updated ? '' : 'none';

    const views = a.views ?? a.views_count;
    if (views != null) {
        document.getElementById('adViews').textContent = views;
        document.getElementById('adViewsWrap').style.display = '';
    } else {
        document.getElementById('adViewsWrap').style.display = 'none';
    }

    // دسته‌ها
    const catBox = document.getElementById('adCategories');
    catBox.innerHTML = (a.categories || []).map(c =>
        `<button type="button" onclick="filterSiteArticles('${c}'); showSitePage('articles');"
                class="px-3 py-1.5 rounded-xl text-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100">${c}</button>`
    ).join('');

    // خلاصه
    const sumEl = document.getElementById('adSummary');
    const summaryText = a.summary || a.description || '';
    if (summaryText) {
        sumEl.textContent = summaryText;
        sumEl.classList.remove('hidden');
    } else {
        sumEl.classList.add('hidden');
    }

    // محتوا — اگر HTML باشد همان، وگرنه متن ساده با حفظ خط‌ها
    const contentEl = document.getElementById('adContent');
    const raw = a.content || '';
    if (/<[a-z][\s\S]*>/i.test(raw)) {
        contentEl.innerHTML = raw;
    } else {
        contentEl.innerHTML = formatPlainArticleContent(raw);
    }

    // مقالات مرتبط (هم‌دسته)
    renderRelatedArticles(a);

    // نظرات خالی / نمونه
    renderArticleComments(a.id);

    // ذخیره id فعلی برای کامنت و اشتراک
    window._currentArticleId = a.id;
    window._currentArticleTitle = a.title;

    showSitePage('article-detail');
};

function formatPlainArticleContent(text) {
    if (!text) return '<p class="text-gray-400">محتوایی ثبت نشده است.</p>';
    // تبدیل ## عنوان و پاراگراف‌ها
    return text
        .split(/\n\n+/)
        .map(block => {
            const t = block.trim();
            if (!t) return '';
            if (t.startsWith('## ')) return `<h2>${escapeHtml(t.slice(3))}</h2>`;
            if (t.startsWith('### ')) return `<h3>${escapeHtml(t.slice(4))}</h3>`;
            // بولد ساده **text**
            const withBold = escapeHtml(t).replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            return `<p>${withBold.replace(/\n/g, '<br>')}</p>`;
        })
        .join('');
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function renderRelatedArticles(current) {
    const box = document.getElementById('adRelated');
    if (!box) return;
    const all = (typeof getSiteArticles === 'function') ? getSiteArticles() : [];
    const cats = current.categories || [];
    let related = all.filter(a =>
        a.id !== current.id &&
        (a.categories || []).some(c => cats.includes(c))
    ).slice(0, 4);
    if (!related.length) {
        related = all.filter(a => a.id !== current.id).slice(0, 2);
    }
    box.innerHTML = related.length === 0
        ? `<p class="text-sm text-gray-400 col-span-full">مقاله مرتبطی نیست</p>`
        : related.map(a => `
            <button type="button" onclick="openSiteArticle(${a.id})"
                    class="text-right bg-white rounded-2xl p-5 shadow-sm border border-gray-50 hover:border-indigo-200 hover:shadow transition">
                <div class="text-xs text-indigo-500 mb-1">${(a.categories || [])[0] || ''}</div>
                <div class="font-bold text-sm line-clamp-2">${a.title}</div>
                <div class="text-xs text-gray-400 mt-2">${a.published_at || ''}</div>
            </button>
        `).join('');
}

// نظرات (سمپل سمت کلاینت)
const siteArticleComments = {};

function renderArticleComments(articleId) {
    const box = document.getElementById('adCommentsList');
    if (!box) return;
    const list = siteArticleComments[articleId] || [];
    if (!list.length) {
        box.innerHTML = `<p class="text-sm text-gray-400 text-center py-4">هنوز نظری ثبت نشده است.</p>`;
        return;
    }
    box.innerHTML = list.map(c => `
        <div class="border border-gray-100 rounded-2xl p-4">
            <div class="flex justify-between items-center mb-2">
                <span class="font-medium text-sm">${escapeHtml(c.name)}</span>
                <span class="text-xs text-gray-400">${c.date}</span>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">${escapeHtml(c.body)}</p>
            ${c.pending ? '<p class="text-xs text-amber-600 mt-2">در انتظار تأیید</p>' : ''}
        </div>
    `).join('');
}

window.submitArticleComment = async function (e) {
    e.preventDefault();
    const name = document.getElementById('adCommentName')?.value.trim();
    const body = document.getElementById('adCommentBody')?.value.trim();
    if (!name || !body) return;
    const id = window._currentArticleId;
    if (!id) return;
    if (!siteArticleComments[id]) siteArticleComments[id] = [];
    siteArticleComments[id].unshift({
        name,
        email: document.getElementById('adCommentEmail')?.value.trim(),
        body,
        date: new Date().toLocaleDateString('fa-IR'),
        pending: true
    });
    document.getElementById('adCommentForm').reset();
    renderArticleComments(id);
    alert('نظر شما ثبت شد و پس از تأیید مدیر نمایش داده می‌شود.');
};

window.shareArticle = async function (type) {
    const title = window._currentArticleTitle || document.getElementById('adTitle')?.textContent || '';
    const url = location.href;
    if (type === 'telegram') {
        window.open('https://t.me/share/url?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(title), '_blank');
    } else if (type === 'copy') {
        navigator.clipboard?.writeText(url).then(() => alert('لینک کپی شد')).catch(async () => await AppDialog.prompt('لینک:', url));
    }
};









// ========== نمایش صفحه + رندر اولیه ==========
window.renderSitePageContent = async function (page) {
    if (page === 'articles' || page === 'article-detail') {
        if (typeof renderSiteArticleCats === 'function') renderSiteArticleCats();
        // مهم: حتماً با 'all' تا لیست از اول پر شود
        if (typeof filterSiteArticles === 'function') filterSiteArticles('all');
        else if (typeof renderSiteArticlesList === 'function') renderSiteArticlesList();
    }
    if (page === 'academies' || page === 'academy-detail') {
        if (typeof renderSiteAcademies === 'function') renderSiteAcademies();
    }
    if (page === 'users' || page === 'user-detail') {
        if (typeof filterSiteUsers === 'function') filterSiteUsers('all');
        else if (typeof renderSiteUsers === 'function') renderSiteUsers();
    }
    if (page === 'academy-enroll') {
        setTimeout(() => {
            if (typeof renderSiteEnrollPage === 'function') renderSiteEnrollPage();
        }, 50);
    }
    if (page === 'academy-request') {
        // فرم استاتیک است؛ نیازی به رندر لیست نیست
    }
};

// اگر showSitePage از قبل در site.js هست، آن را گسترش بده
(function () {
    const prev = window.showSitePage;
    window.showSitePage = async function (page) {
        if (typeof prev === 'function') {
            prev(page);
        } else {
            document.querySelectorAll('.site-page').forEach(el => el.classList.remove('active'));
            const target = document.getElementById('page-' + page);
            if (target) target.classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (typeof closeMobileMenu === 'function') closeMobileMenu();
        }
        // رندر بعد از نمایش DOM
        setTimeout(() => {
            if (typeof renderSitePageContent === 'function') renderSitePageContent(page);
        }, 50);
    };
})();

// ========== Init — لود اول صفحه ==========
(function initSitePages() {
    function run() {
        // اگر صفحه articles/academies/users از اول active است
        const active = document.querySelector('.site-page.active');
        let page = 'home';
        if (active && active.id && active.id.startsWith('page-')) {
            page = active.id.replace('page-', '');
        }

        // همیشه داده‌های این سه بخش را یک‌بار آماده کن (حتی اگر صفحه دیگری باز است)
        setTimeout(() => {
            if (document.getElementById('siteArticlesList')) {
                if (typeof renderSiteArticleCats === 'function') renderSiteArticleCats();
                if (typeof filterSiteArticles === 'function') filterSiteArticles('all');
            }
            if (document.getElementById('siteAcademiesGrid')) {
                if (typeof renderSiteAcademies === 'function') renderSiteAcademies();
            }
            if (document.getElementById('siteUsersGrid')) {
                if (typeof filterSiteUsers === 'function') filterSiteUsers('all');
            }
            if (document.getElementById('siteEnrollAcademy')) renderSiteEnrollPage();
        }, 100);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();
