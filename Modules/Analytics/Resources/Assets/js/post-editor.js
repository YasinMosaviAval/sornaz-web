let currentEditingPostId = null; // null = نوشته جدید

// باز کردن ادیتور برای نوشته جدید یا موجود
window.openPostEditor = function(postId = null) {
    currentEditingPostId = postId;

    // پر کردن سلکت شعبه
    const branchSelect = document.getElementById('peBranch');
    if (branchSelect && typeof allBranches !== 'undefined') {
        branchSelect.innerHTML = allBranches.map(b =>
            `<option value="${b.id}">${b.name}</option>`
        ).join('');
    }

    if (postId) {
        const p = (typeof allPosts !== 'undefined') ? allPosts.find(x => x.id === postId) : null;
        if (!p) {
            alert('نوشته پیدا نشد');
            return;
        }
        fillPostEditor(p);
        document.getElementById('postEditorPageTitle').textContent = 'ویرایش نوشته';
        document.getElementById('postEditorSubtitle').textContent = (typeof postStatusLabels !== 'undefined' ? postStatusLabels[p.status] : p.status) || '';
        document.getElementById('peTrashBtn').classList.remove('hidden');
    } else {
        clearPostEditor();
        document.getElementById('postEditorPageTitle').textContent = 'افزودن نوشته';
        document.getElementById('postEditorSubtitle').textContent = 'پیش‌نویس';
        document.getElementById('peTrashBtn').classList.add('hidden');
    }

    // نمایش بخش ادیتور
    if (typeof showSection === 'function') {
        showSection('post-editor');
    } else {
        document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'));
        document.getElementById('post-editor')?.classList.remove('hidden');
    }
};

function fillPostEditor(p) {
    document.getElementById('peTitle').value = p.title || '';
    document.getElementById('peSlug').value = p.slug || '';
    document.getElementById('peContent').value = p.content || '';
    document.getElementById('peSummary').value = p.summary || '';
    document.getElementById('peDescription').value = p.description || '';
    document.getElementById('peStatus').value = p.status || 'draft';
    document.getElementById('peVisibility').value = p.visibility || 'public';
    document.getElementById('pePublishedAt').value = p.published_at || '';
    document.getElementById('pePassword').value = p.password || '';
    document.getElementById('peType').value = p.type || 'post';
    document.getElementById('peCategories').value = p.categories || '';
    document.getElementById('peCover').value = p.cover || '';
    document.getElementById('peCoverMediaId').value = p.cover_media_id || '';
    document.getElementById('peRelated').value = p.related_posts_id || '';
    document.getElementById('peGuid').value = p.guid || '';
    document.getElementById('peViewsCount').textContent = p.views_count || 0;
    document.getElementById('peCommentCount').textContent = p.comment_count || 0;
    document.getElementById('pePostId').textContent = p.id;
    if (p.branchId) document.getElementById('peBranch').value = p.branchId;
    updateCoverPreview();
}

function clearPostEditor() {
    ['peTitle','peSlug','peContent','peSummary','peDescription','pePublishedAt','pePassword','peCategories','peCover','peCoverMediaId','peRelated','peGuid'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.getElementById('peStatus').value = 'draft';
    document.getElementById('peVisibility').value = 'public';
    document.getElementById('peType').value = 'post';
    document.getElementById('peViewsCount').textContent = '0';
    document.getElementById('peCommentCount').textContent = '0';
    document.getElementById('pePostId').textContent = '—';
    updateCoverPreview();
}

window.updateCoverPreview = function() {
    const url = document.getElementById('peCover')?.value.trim();
    const box = document.getElementById('peCoverPreview');
    const img = document.getElementById('peCoverImg');
    if (url && box && img) {
        img.src = url;
        box.classList.remove('hidden');
    } else if (box) {
        box.classList.add('hidden');
    }
};

window.closePostEditor = function() {
    if (typeof showSection === 'function') {
        showSection('posts');
    }
};

window.savePostEditor = function(mode) {
    const title = document.getElementById('peTitle')?.value.trim();
    if (!title) return alert('عنوان نوشته الزامی است');

    if (typeof allPosts === 'undefined') {
        window.allPosts = [];
    }

    const branchId = parseInt(document.getElementById('peBranch').value);
    const branch = (typeof allBranches !== 'undefined') ? allBranches.find(b => b.id === branchId) : null;

    let status = document.getElementById('peStatus').value;
    if (mode === 'publish') status = 'published';
    if (mode === 'draft') status = 'draft';

    let publishedAt = document.getElementById('pePublishedAt').value.trim();
    if (mode === 'publish' && !publishedAt) {
        publishedAt = new Date().toLocaleString('fa-IR');
    }

    const payload = {
        title,
        slug: document.getElementById('peSlug').value.trim() || title.replace(/\s+/g, '-').toLowerCase(),
        content: document.getElementById('peContent').value,
        summary: document.getElementById('peSummary').value.trim(),
        description: document.getElementById('peDescription').value.trim(),
        status,
        visibility: document.getElementById('peVisibility').value,
        published_at: publishedAt || null,
        password: document.getElementById('pePassword').value.trim(),
        type: document.getElementById('peType').value,
        categories: document.getElementById('peCategories').value.trim(),
        cover: document.getElementById('peCover').value.trim(),
        cover_media_id: parseInt(document.getElementById('peCoverMediaId').value) || null,
        related_posts_id: document.getElementById('peRelated').value.trim(),
        guid: document.getElementById('peGuid').value.trim(),
        branchId: branchId || 1,
        branchName: branch ? branch.name : 'نامشخص'
    };

    if (currentEditingPostId) {
        const index = allPosts.findIndex(x => x.id === currentEditingPostId);
        if (index !== -1) {
            allPosts[index] = { ...allPosts[index], ...payload };
        }
    } else {
        const newId = Date.now();
        allPosts.unshift({
            id: newId,
            ...payload,
            author_id: 1,
            author_name: 'ادمین',
            views_count: 0,
            comment_count: 0,
            visibility_user_id: null,
            name: '',
            pinged: ''
        });
        currentEditingPostId = newId;
        document.getElementById('pePostId').textContent = newId;
        document.getElementById('peTrashBtn').classList.remove('hidden');
    }

    document.getElementById('postEditorSubtitle').textContent =
        (typeof postStatusLabels !== 'undefined' ? postStatusLabels[status] : status);

    if (typeof filterPosts === 'function') filterPosts();
    alert(mode === 'publish' ? '✅ نوشته منتشر شد' : '✅ پیش‌نویس ذخیره شد');
};

window.movePostToTrash = function() {
    if (!currentEditingPostId) return;
    if (!confirm('این نوشته به زباله‌دان منتقل شود؟')) return;
    const p = allPosts.find(x => x.id === currentEditingPostId);
    if (p) {
        p.status = 'trash';
        if (typeof filterPosts === 'function') filterPosts();
        closePostEditor();
        alert('به زباله‌دان منتقل شد');
    }
};

window.previewPostEditor = function() {
    const title = document.getElementById('peTitle')?.value || 'بدون عنوان';
    const content = document.getElementById('peContent')?.value || '';
    const summary = document.getElementById('peSummary')?.value || '';
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold">پیش‌نمایش</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-4">${title}</h1>
                ${summary ? `<p class="text-indigo-600 mb-4">${summary}</p>` : ''}
                <div class="prose max-w-none text-gray-700 whitespace-pre-wrap leading-relaxed">${content || '<span class="text-gray-400">بدون محتوا</span>'}</div>
            </div>
        </div>
    </div>`;
};

// نوار ابزار ساده
window.peFormat = function(cmd) {
    const ta = document.getElementById('peContent');
    if (!ta) return;
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const text = ta.value.substring(start, end);
    let wrap = text;
    if (cmd === 'bold') wrap = `**${text || 'متن'}**`;
    if (cmd === 'italic') wrap = `*${text || 'متن'}*`;
    if (cmd === 'underline') wrap = `<u>${text || 'متن'}</u>`;
    ta.value = ta.value.substring(0, start) + wrap + ta.value.substring(end);
    ta.focus();
};

window.peInsertHeading = function() {
    const ta = document.getElementById('peContent');
    const pos = ta.selectionStart;
    ta.value = ta.value.substring(0, pos) + '\n## عنوان بخش\n' + ta.value.substring(pos);
};

window.peInsertList = function() {
    const ta = document.getElementById('peContent');
    const pos = ta.selectionStart;
    ta.value = ta.value.substring(0, pos) + '\n- مورد یک\n- مورد دو\n' + ta.value.substring(pos);
};

window.peInsertLink = function() {
    const url = prompt('آدرس لینک:', 'https://');
    if (!url) return;
    const ta = document.getElementById('peContent');
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const text = ta.value.substring(start, end) || 'متن لینک';
    ta.value = ta.value.substring(0, start) + `[${text}](${url})` + ta.value.substring(end);
};

// اتصال دکمه ویرایش لیست نوشته‌ها به ادیتور کامل
window.editPost = function(id) {
    openPostEditor(id);
};

// دکمه «افزودن نوشته» هم می‌تواند ادیتور را باز کند
window.openAddPostModal = function() {
    openPostEditor(null);
};