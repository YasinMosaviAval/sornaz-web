const articleCategories = [
    "موسیقی ایران", "تاریخ موسیقی", "زندگینامه موسیقی‌دانان",
    "تئوری موسیقی", "ردیف و دستگاه", "فرم‌های موسیقی"
];

let allArticles = [
    {
        id: 1,
        title: "ساختار موسیقی برنامه‌ای ایرانی چگونه است؟",
        summary: "بررسی شکل‌گیری و تحول موسیقی برنامه‌ای از اواخر قاجار تا دوران رادیو و دیدگاه ارشد تهماسبی.",
        description: "محدودیت‌های صفحات گرامافون و نقش پیش‌درآمد در برنامه‌های رادیویی.",
        content: "این مقاله به بررسی شکل‌گیری و تحول «موسیقی برنامه‌ای ایرانی» از اواخر دوره قاجار تا دوران رادیو می‌پردازد...",
        categories: ["موسیقی ایران", "تاریخ موسیقی"],
        published_at: "۱۴۰۳/۰۱/۲۳",
        status: "published",
        views: 420
    },
    {
        id: 2,
        title: "مفهوم قطعه در موسیقی ایرانی",
        summary: "«قطعه» عنوانی کلی برای آثار متریک خارج از قالب‌های سنتی مانند پیش‌درآمد و رنگ.",
        description: "نقش وزیری، خالقی و صبا در گسترش این مفهوم.",
        content: "در این مقاله مفهوم «قطعه» در موسیقی دستگاهی ایران از دیدگاه ارشد تهماسبی بررسی می‌شود...",
        categories: ["تئوری موسیقی", "موسیقی ایران"],
        published_at: "۱۴۰۲/۰۶/۰۸",
        status: "published",
        views: 310
    },
    {
        id: 3,
        title: "مدرسه عالی موسیقی",
        summary: "تأسیس مدرسه عالی موسیقی در سال ۱۳۰۲ به همت علینقی وزیری.",
        description: "نقش این نهاد در آموزش علمی موسیقی نوین ایران.",
        content: "مدرسه عالی موسیقی یکی از مهم‌ترین نهادهای آموزشی موسیقی نوین در ایران به شمار می‌آید...",
        categories: ["تاریخ موسیقی"],
        published_at: "۱۴۰۲/۰۶/۰۸",
        status: "published",
        views: 280
    },
    {
        id: 4,
        title: "عبدالله دوامی",
        summary: "از مهم‌ترین راویان تصنیف و ردیف موسیقی ایرانی.",
        description: "شاگردان و نقش او در حفظ تصنیف‌های قدیمی.",
        content: "عبدالله دوامی (۱۲۷۰–۱۳۵۹) نزد آقا حسینقلی، درویش‌خان و میرزا عبدالله آموزش دید...",
        categories: ["زندگینامه موسیقی‌دانان"],
        published_at: "۱۴۰۲/۰۶/۰۸",
        status: "published",
        views: 195
    },
    {
        id: 5,
        title: "فرم ضربی سازیک در موسیقی ایران",
        summary: "جایگاه قطعات متریک و بداهه در موسیقی سازی دستگاهی.",
        description: "ریشه در بداهه‌نوازی و وزن‌های ترکیبی مانند شش‌هشتم.",
        content: "مقاله «ضربی سازیک» به بررسی جایگاه قطعات متریک و بداهه می‌پردازد...",
        categories: ["فرم‌های موسیقی", "ردیف و دستگاه"],
        published_at: "۱۴۰۲/۰۶/۰۸",
        status: "draft",
        views: 0
    }
];

let currentArticleCategory = 'all';

window.renderArticleCategoryTabs = async function () {
    const container = document.getElementById('articleCategoryTabs');
    if (!container) return;
    container.querySelectorAll('.article-cat-tab:not(:first-child)').forEach(t => t.remove());
    articleCategories.forEach(cat => {
        const btn = document.createElement('button');
        btn.className = 'article-cat-tab px-4 py-2 rounded-xl text-sm border border-gray-200 hover:bg-gray-50';
        btn.textContent = cat;
        btn.onclick = () => filterArticlesByCategory(cat);
        container.appendChild(btn);
    });
};

window.filterArticlesByCategory = async function (cat) {
    currentArticleCategory = cat;
    document.querySelectorAll('.article-cat-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white');
        tab.classList.add('border', 'border-gray-200');
    });
    document.querySelectorAll('.article-cat-tab').forEach(tab => {
        if ((cat === 'all' && tab.textContent === 'همه') || tab.textContent === cat) {
            tab.classList.add('bg-indigo-600', 'text-white');
            tab.classList.remove('border-gray-200');
        }
    });
    filterArticles();
};

window.filterArticles = async function () {
    renderArticlesList();
};

window.renderArticlesList = async function () {
    const container = document.getElementById('articlesList');
    if (!container) return;

    const search = (document.getElementById('articleSearch')?.value || '').trim().toLowerCase();
    let list = [...allArticles];
    if (currentArticleCategory !== 'all') {
        list = list.filter(a => (a.categories || []).includes(currentArticleCategory));
    }
    if (search) {
        list = list.filter(a =>
            (a.title || '').toLowerCase().includes(search) ||
            (a.summary || '').toLowerCase().includes(search) ||
            (a.content || '').toLowerCase().includes(search)
        );
    }

    container.innerHTML = list.length === 0
        ? `<p class="text-center text-gray-400 py-16">مقاله‌ای یافت نشد</p>`
        : list.map(a => `
            <article class="bg-white rounded-3xl p-6 shadow-sm card-hover">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-2 mb-2">
                            ${(a.categories || []).map(c =>
                                `<span class="px-2.5 py-1 rounded-lg text-xs bg-indigo-50 text-indigo-700">${c}</span>`
                            ).join('')}
                            ${a.status === 'draft' ? '<span class="px-2.5 py-1 rounded-lg text-xs bg-gray-100 text-gray-500">پیش‌نویس</span>' : ''}
                        </div>
                        <h3 class="text-xl font-bold mb-2 hover:text-indigo-600 cursor-pointer" onclick="viewArticle(${a.id})">${a.title}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed line-clamp-2">${a.summary || a.description || ''}</p>
                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                            <span><i class="far fa-calendar ml-1"></i> ${a.published_at || '—'}</span>
                            <span><i class="far fa-eye ml-1"></i> ${a.views || 0} بازدید</span>
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <button onclick="viewArticle(${a.id})" class="border border-indigo-200 text-indigo-600 px-4 py-2 rounded-xl text-sm hover:bg-indigo-50">جزئیات</button>
                        <button onclick="editArticle(${a.id})" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-indigo-700">ویرایش</button>
                        <button onclick="deleteArticle(${a.id})" class="text-red-500 px-3 py-2 text-sm">حذف</button>
                    </div>
                </div>
            </article>
        `).join('');
};

window.openAddArticleModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const catChecks = articleCategories.map(c =>
        `<label class="flex items-center gap-2 text-sm"><input type="checkbox" class="article-cat-check" value="${c}"> ${c}</label>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">افزودن مقاله آموزشی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-4 max-h-[75vh] overflow-y-auto">
                <input id="artTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="artSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="artDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <textarea id="artContent" rows="6" placeholder="محتوای کامل مقاله" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div>
                    <p class="text-sm font-medium mb-2">دسته‌بندی‌ها</p>
                    <div class="flex flex-wrap gap-3">${catChecks}</div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input id="artDate" type="text" placeholder="تاریخ انتشار" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <select id="artStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="published">منتشرشده</option>
                        <option value="draft">پیش‌نویس</option>
                    </select>
                </div>
                <div class="flex gap-4">
                    <button onclick="saveArticle()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveArticle = async function () {
    const title = document.getElementById('artTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const cats = Array.from(document.querySelectorAll('.article-cat-check:checked')).map(c => c.value);
    allArticles.unshift({
        id: Date.now(), title,
        summary: document.getElementById('artSummary').value.trim(),
        description: document.getElementById('artDesc').value.trim(),
        content: document.getElementById('artContent').value.trim(),
        categories: cats,
        published_at: document.getElementById('artDate').value.trim() || 'همین الان',
        status: document.getElementById('artStatus').value,
        views: 0
    });
    filterArticles();
    closeModal();
    alert('✅ مقاله ثبت شد');
};

window.viewArticle = async function (id) {
    const a = allArticles.find(x => x.id === id);
    if (!a) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-start gap-4">
                <div>
                    <div class="flex flex-wrap gap-2 mb-2">
                        ${(a.categories || []).map(c => `<span class="px-2 py-0.5 rounded text-xs bg-indigo-50 text-indigo-700">${c}</span>`).join('')}
                    </div>
                    <h2 class="text-2xl font-bold">${a.title}</h2>
                    <p class="text-sm text-gray-400 mt-1">${a.published_at} · ${a.views || 0} بازدید</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button onclick="editArticle(${a.id})" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4 max-h-[70vh] overflow-y-auto">
                ${a.summary ? `<p class="text-indigo-600 font-medium text-lg">${a.summary}</p>` : ''}
                ${a.description ? `<p class="text-gray-600">${a.description}</p>` : ''}
                <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap border-t pt-4">${a.content || ''}</div>
            </div>
        </div>
    </div>`;
};

window.editArticle = async function (id) {
    const a = allArticles.find(x => x.id === id);
    if (!a) return;
    const catChecks = articleCategories.map(c =>
        `<label class="flex items-center gap-2 text-sm"><input type="checkbox" class="article-cat-check" value="${c}" ${(a.categories||[]).includes(c)?'checked':''}> ${c}</label>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                <h2 class="text-2xl font-bold">ویرایش مقاله</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-4 max-h-[75vh] overflow-y-auto">
                <input id="editArtTitle" type="text" value="${a.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editArtSummary" type="text" value="${a.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editArtDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${a.description || ''}</textarea>
                <textarea id="editArtContent" rows="6" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${a.content || ''}</textarea>
                <div class="flex flex-wrap gap-3">${catChecks}</div>
                <div class="grid grid-cols-2 gap-4">
                    <input id="editArtDate" type="text" value="${a.published_at || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <select id="editArtStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="published" ${a.status==='published'?'selected':''}>منتشرشده</option>
                        <option value="draft" ${a.status==='draft'?'selected':''}>پیش‌نویس</option>
                    </select>
                </div>
                <div class="flex gap-4">
                    <button onclick="saveEditedArticle(${a.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedArticle = async function (id) {
    const title = document.getElementById('editArtTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allArticles.findIndex(x => x.id === id);
    if (index === -1) return;
    const cats = Array.from(document.querySelectorAll('.article-cat-check:checked')).map(c => c.value);
    allArticles[index] = {
        ...allArticles[index], title,
        summary: document.getElementById('editArtSummary').value.trim(),
        description: document.getElementById('editArtDesc').value.trim(),
        content: document.getElementById('editArtContent').value.trim(),
        categories: cats,
        published_at: document.getElementById('editArtDate').value.trim(),
        status: document.getElementById('editArtStatus').value
    };
    filterArticles();
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteArticle = async function (id) {
    if (await AppDialog.confirm('حذف این مقاله؟')) {
        allArticles = allArticles.filter(a => a.id !== id);
        filterArticles();
    }
};

(function() {
    setTimeout(() => {
        if (document.getElementById('articlesList')) {
            renderArticleCategoryTabs();
            filterArticlesByCategory('all');
        }
    }, 200);
})();
