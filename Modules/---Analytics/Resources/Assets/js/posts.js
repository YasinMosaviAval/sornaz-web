const postTypeLabels = { post: 'نوشته', product: 'محصول', music_theory: 'تئوری موسیقی' };
const postStatusLabels = {
    draft: 'پیش‌نویس', published: 'منتشرشده', private: 'خصوصی', inherit: 'ارثی',
    pending: 'در انتظار بررسی', trash: 'زباله‌دان', 'auto-draft': 'پیش‌نویس خودکار',
    future: 'زمان‌بندی‌شده', 'request-pending': 'درخواست در انتظار', 'request-confirmed': 'درخواست تأییدشده'
};
const postVisibilityLabels = { public: 'عمومی', private: 'خصوصی', followers: 'دنبال‌کنندگان', premium: 'ویژه' };

const postStatusColors = {
    published: 'bg-green-100 text-green-700',
    draft: 'bg-gray-100 text-gray-600',
    pending: 'bg-yellow-100 text-yellow-700',
    private: 'bg-purple-100 text-purple-700',
    trash: 'bg-red-100 text-red-700',
    future: 'bg-blue-100 text-blue-700'
};

let allPosts = [
    {
        id: 1, title: "نکات تمرین روزانه پیانو", summary: "راهنمای تمرین", description: "چگونه هر روز ۳۰ دقیقه مفید تمرین کنیم.", content: "محتوای کامل مقاله درباره برنامه‌ریزی تمرین، گرم‌کردن انگشتان و ثبت پیشرفت...",
        author_id: 1, author_name: "علی رضایی", categories: "آموزش,پیانو", cover: "", cover_media_id: null,
        slug: "daily-piano-practice", views_count: 342, published_at: "۱۴۰۳/۰۸/۱۵ ۱۰:۳۰",
        type: "post", status: "published", visibility: "public", visibility_user_id: null, password: "",
        comment_count: 12, name: "", pinged: "", guid: "https://example.com/posts/daily-piano-practice", related_posts_id: "2,3",
        branchId: 1, branchName: "شعبه مرکزی"
    },
    {
        id: 2, title: "دوره آنلاین تئوری موسیقی", summary: "محصول آموزشی", description: "پکیج ویدیویی تئوری پایه تا متوسط.", content: "جزئیات سرفصل‌ها، مدت دوره و نحوه دسترسی...",
        author_id: 2, author_name: "سارا موسوی", categories: "تئوری,محصول", cover: "", cover_media_id: 50,
        slug: "online-music-theory-course", views_count: 890, published_at: "۱۴۰۳/۰۷/۰۱ ۰۹:۰۰",
        type: "product", status: "published", visibility: "premium", visibility_user_id: null, password: "",
        comment_count: 45, name: "", pinged: "", guid: "https://example.com/products/theory-course", related_posts_id: "",
        branchId: 1, branchName: "شعبه مرکزی"
    },
    {
        id: 3, title: "گام‌های مینور طبیعی", summary: "تئوری", description: "آموزش ساخت و کاربرد گام مینور.", content: "فرمول ساخت گام مینور طبیعی، هارمونیک و ملودیک...",
        author_id: 1, author_name: "علی رضایی", categories: "تئوری", cover: "", cover_media_id: null,
        slug: "natural-minor-scales", views_count: 156, published_at: null,
        type: "music_theory", status: "draft", visibility: "public", visibility_user_id: null, password: "",
        comment_count: 0, name: "", pinged: "", guid: "", related_posts_id: "1",
        branchId: 2, branchName: "شعبه ونک"
    },
    {
        id: 4, title: "گزارش کنسرت پایان ترم", summary: "خبر", description: "خلاصه اجرای هنرجویان در سالن اصلی.", content: "لیست قطعات و عکس‌های رویداد...",
        author_id: 3, author_name: "مدیر شعبه", categories: "رویداد,خبر", cover: "", cover_media_id: 88,
        slug: "term-end-concert-report", views_count: 210, published_at: "۱۴۰۳/۰۹/۲۰ ۱۸:۰۰",
        type: "post", status: "pending", visibility: "followers", visibility_user_id: null, password: "",
        comment_count: 3, name: "", pinged: "", guid: "", related_posts_id: "",
        branchId: 1, branchName: "شعبه مرکزی"
    },
    {
        id: 5, title: "نوشته حذف‌شده نمونه", summary: "تست", description: "این نوشته در زباله‌دان است.", content: "",
        author_id: 1, author_name: "علی رضایی", categories: "", cover: "", cover_media_id: null,
        slug: "trashed-sample", views_count: 0, published_at: null,
        type: "post", status: "trash", visibility: "private", visibility_user_id: 1, password: "",
        comment_count: 0, name: "", pinged: "", guid: "", related_posts_id: "",
        branchId: 1, branchName: "شعبه مرکزی"
    }
];

let currentPostBranch = 'all';
let currentPostStatus = 'all';

window.renderPostsBranchTabs = function() {
    const container = document.getElementById('postsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.post-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'post-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterPostsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterPostsByBranch = function(branchId) {
    currentPostBranch = branchId;
    document.querySelectorAll('.post-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.post-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    filterPosts();
};

window.filterPostsByStatus = function(status) {
    currentPostStatus = status;
    document.querySelectorAll('.post-status-tab').forEach(tab => {
        tab.classList.remove('bg-gray-900', 'text-white');
        tab.classList.add('border', 'border-gray-200');
    });
    // هایلایت تب فعلی
    document.querySelectorAll('.post-status-tab').forEach(tab => {
        const onclick = tab.getAttribute('onclick') || '';
        if (onclick.includes(`'${status}'`)) {
            tab.classList.add('bg-gray-900', 'text-white');
            tab.classList.remove('border-gray-200');
        }
    });
    filterPosts();
};

window.filterPosts = function() {
    renderPostsTable();
};

window.renderPostsTable = function() {
    const tbody = document.querySelector('#postsTable tbody');
    if (!tbody) return;

    const search = (document.getElementById('postSearch')?.value || '').trim().toLowerCase();
    const typeF = document.getElementById('postTypeFilter')?.value || '';
    const visF = document.getElementById('postVisibilityFilter')?.value || '';

    let list = [...allPosts];
    if (currentPostBranch !== 'all') list = list.filter(p => p.branchId == currentPostBranch);
    if (currentPostStatus !== 'all') list = list.filter(p => p.status === currentPostStatus);
    if (typeF) list = list.filter(p => p.type === typeF);
    if (visF) list = list.filter(p => p.visibility === visF);
    if (search) {
        list = list.filter(p =>
            (p.title || '').toLowerCase().includes(search) ||
            (p.slug || '').toLowerCase().includes(search) ||
            (p.categories || '').toLowerCase().includes(search)
        );
    }

    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="8" class="py-12 text-center text-gray-400">نوشته‌ای یافت نشد</td></tr>`
        : list.map(p => {
            const stClass = postStatusColors[p.status] || 'bg-gray-100 text-gray-600';
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5">
                    <div class="font-medium">${p.title}</div>
                    <div class="text-xs text-gray-400 mt-0.5">${p.slug || ''}</div>
                </td>
                <td class="py-4 px-5">${p.author_name || '#' + p.author_id}</td>
                <td class="py-4 px-5 text-xs text-gray-500">${p.categories || '—'}</td>
                <td class="py-4 px-5"><span class="px-2 py-1 rounded-lg text-xs bg-indigo-50 text-indigo-700">${postTypeLabels[p.type] || p.type}</span></td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${stClass}">${postStatusLabels[p.status] || p.status}</span></td>
                <td class="py-4 px-5">${p.views_count || 0}</td>
                <td class="py-4 px-5 text-xs">${p.published_at || '—'}</td>
                <td class="py-4 px-5 text-left whitespace-nowrap">
                    <button onclick="viewPost(${p.id})" class="text-indigo-600 text-sm ml-2">جزئیات</button>
                    <button onclick="editPost(${p.id})" class="text-indigo-600 text-sm ml-2">ویرایش</button>
                    <button onclick="deletePost(${p.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

function getPostFormFields(prefix, data = {}) {
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === data.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    const typeOpts = Object.entries(postTypeLabels).map(([k, v]) =>
        `<option value="${k}" ${data.type === k ? 'selected' : ''}>${v}</option>`
    ).join('');
    const statusOpts = Object.entries(postStatusLabels).map(([k, v]) =>
        `<option value="${k}" ${data.status === k ? 'selected' : ''}>${v}</option>`
    ).join('');
    const visOpts = Object.entries(postVisibilityLabels).map(([k, v]) =>
        `<option value="${k}" ${data.visibility === k ? 'selected' : ''}>${v}</option>`
    ).join('');

    return `
        <input id="${prefix}Title" type="text" value="${data.title || ''}" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
        <input id="${prefix}Summary" type="text" value="${data.summary || ''}" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
        <textarea id="${prefix}Desc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${data.description || ''}</textarea>
        <textarea id="${prefix}Content" rows="5" placeholder="محتوا (متن اصلی نوشته)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 font-mono text-sm">${data.content || ''}</textarea>
        <div class="grid grid-cols-2 gap-4">
            <input id="${prefix}Slug" type="text" value="${data.slug || ''}" placeholder="اسلاگ (slug)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
            <input id="${prefix}Categories" type="text" value="${data.categories || ''}" placeholder="دسته‌ها (با کاما)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
        </div>
        <div class="grid grid-cols-3 gap-4">
            <select id="${prefix}Type" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${typeOpts}</select>
            <select id="${prefix}Status" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${statusOpts}</select>
            <select id="${prefix}Visibility" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${visOpts}</select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <input id="${prefix}PublishedAt" type="text" value="${data.published_at || ''}" placeholder="تاریخ انتشار" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
            <input id="${prefix}Cover" type="text" value="${data.cover || ''}" placeholder="آدرس کاور / cover" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <input id="${prefix}CoverMediaId" type="number" value="${data.cover_media_id || ''}" placeholder="cover_media_id" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
            <input id="${prefix}Password" type="text" value="${data.password || ''}" placeholder="رمز عبور (اختیاری)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <input id="${prefix}Related" type="text" value="${data.related_posts_id || ''}" placeholder="شناسه نوشته‌های مرتبط" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
            <input id="${prefix}Guid" type="text" value="${data.guid || ''}" placeholder="GUID / لینک دائمی" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
        </div>
        <select id="${prefix}Branch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
    `;
}

function collectPostForm(prefix) {
    const title = document.getElementById(prefix + 'Title')?.value.trim();
    if (!title) { alert('عنوان الزامی است'); return null; }
    const branchId = parseInt(document.getElementById(prefix + 'Branch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    return {
        title,
        summary: document.getElementById(prefix + 'Summary').value.trim(),
        description: document.getElementById(prefix + 'Desc').value.trim(),
        content: document.getElementById(prefix + 'Content').value.trim(),
        slug: document.getElementById(prefix + 'Slug').value.trim(),
        categories: document.getElementById(prefix + 'Categories').value.trim(),
        type: document.getElementById(prefix + 'Type').value,
        status: document.getElementById(prefix + 'Status').value,
        visibility: document.getElementById(prefix + 'Visibility').value,
        published_at: document.getElementById(prefix + 'PublishedAt').value.trim() || null,
        cover: document.getElementById(prefix + 'Cover').value.trim(),
        cover_media_id: parseInt(document.getElementById(prefix + 'CoverMediaId').value) || null,
        password: document.getElementById(prefix + 'Password').value.trim(),
        related_posts_id: document.getElementById(prefix + 'Related').value.trim(),
        guid: document.getElementById(prefix + 'Guid').value.trim(),
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
}

window.openAddPostModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl z-10">
                <h2 class="text-2xl font-bold">افزودن نوشته جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-4 max-h-[75vh] overflow-y-auto">
                ${getPostFormFields('post', { type: 'post', status: 'draft', visibility: 'public' })}
                <div class="flex gap-4 pt-2">
                    <button onclick="savePost()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.savePost = function() {
    const data = collectPostForm('post');
    if (!data) return;
    allPosts.unshift({
        id: Date.now(),
        ...data,
        author_id: 1, author_name: 'ادمین',
        views_count: 0, comment_count: 0,
        visibility_user_id: null, name: '', pinged: ''
    });
    filterPosts();
    closeModal();
    alert('✅ نوشته ثبت شد');
};

window.viewPost = function(id) {
    const p = allPosts.find(x => x.id === id);
    if (!p) return;
    const stClass = postStatusColors[p.status] || 'bg-gray-100 text-gray-600';
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${p.title}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="px-2 py-0.5 rounded text-xs ${stClass}">${postStatusLabels[p.status]}</span>
                        · ${postTypeLabels[p.type]} · ${postVisibilityLabels[p.visibility]}
                    </p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editPost(${p.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-5 max-h-[70vh] overflow-y-auto">
                ${p.summary ? `<p class="text-indigo-600 font-medium text-lg">${p.summary}</p>` : ''}
                ${p.description ? `<p class="text-gray-600">${p.description}</p>` : ''}
                ${p.content ? `<div class="bg-gray-50 rounded-2xl p-5 text-sm leading-relaxed whitespace-pre-wrap border">${p.content}</div>` : ''}
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نویسنده</span><span>${p.author_name || '#'+p.author_id}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">اسلاگ</span><span class="font-mono text-xs">${p.slug || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">دسته‌ها</span><span>${p.categories || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">بازدید</span><span>${p.views_count || 0}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نظرات</span><span>${p.comment_count || 0}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">انتشار</span><span>${p.published_at || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">مرتبط</span><span>${p.related_posts_id || '—'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${p.branchName}</span></div>
                </div>
                ${p.guid ? `<a href="${p.guid}" target="_blank" class="text-indigo-600 text-sm hover:underline">مشاهده لینک دائمی</a>` : ''}
            </div>
        </div>
    </div>`;
};

window.editPost = function(id) {
    const p = allPosts.find(x => x.id === id);
    if (!p) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl z-10">
                <h2 class="text-2xl font-bold">ویرایش نوشته</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-4 max-h-[75vh] overflow-y-auto">
                ${getPostFormFields('editPost', p)}
                <div class="flex gap-4 pt-2">
                    <button onclick="saveEditedPost(${p.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره تغییرات</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedPost = function(id) {
    const data = collectPostForm('editPost');
    if (!data) return;
    const index = allPosts.findIndex(x => x.id === id);
    if (index === -1) return;
    allPosts[index] = { ...allPosts[index], ...data };
    filterPosts();
    closeModal();
    alert('✅ ذخیره شد');
};

window.deletePost = function(id) {
    const p = allPosts.find(x => x.id === id);
    if (!p) return;
    if (p.status === 'trash') {
        if (confirm('حذف دائمی این نوشته؟')) {
            allPosts = allPosts.filter(x => x.id !== id);
            filterPosts();
        }
    } else {
        if (confirm('انتقال به زباله‌دان؟')) {
            p.status = 'trash';
            filterPosts();
        }
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#postsTable tbody')) {
            renderPostsBranchTabs();
            filterPostsByBranch('all');
            filterPostsByStatus('all');
        }
    }, 200);
})();