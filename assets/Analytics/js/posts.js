(function () {
  'use strict';
  const typeLabels = { post: 'نوشته', product: 'محصول', music_theory: 'تئوری موسیقی' },
    statusLabels = {
      draft: 'پیش‌نویس',
      published: 'منتشرشده',
      private: 'خصوصی',
      inherit: 'ارثی',
      pending: 'در انتظار بررسی',
      trash: 'زباله‌دان',
      'auto-draft': 'پیش‌نویس خودکار',
      future: 'زمان‌بندی‌شده',
      'request-pending': 'درخواست در انتظار',
      'request-confirmed': 'درخواست تأییدشده',
    },
    visibilityLabels = {
      public: 'عمومی',
      private: 'خصوصی',
      followers: 'دنبال‌کنندگان',
      premium: 'ویژه',
    },
    statusColors = {
      published: 'bg-green-100 text-green-700',
      draft: 'bg-gray-100 text-gray-600',
      pending: 'bg-yellow-100 text-yellow-700',
      private: 'bg-purple-100 text-purple-700',
      trash: 'bg-red-100 text-red-700',
      future: 'bg-blue-100 text-blue-700',
    };
  let posts = [],
    total = 0,
    page = 1,
    perPage = 10,
    status = 'all',
    editingId = null,
    searchTimer = null,
    editorMode = 'visual';
  const enc = (data) =>
    btoa(unescape(encodeURIComponent(JSON.stringify(data))))
      .replace(/\+/g, '-')
      .replace(/\//g, '_')
      .replace(/=+$/, '');
  const esc = (value) =>
    String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  const cache = new Map(), pending = new Map();
  window.cyclePostStatus = async (id, button) => {
    if (button?.disabled) return;
    const post = posts.find(item => Number(item.id) === Number(id));
    if (!post) return;
    const statuses = ['draft', 'published', 'pending', 'private'];
    const next = statuses[(statuses.indexOf(post.status) + 1) % statuses.length];
    if (button) button.disabled = true;
    try {
      await api(`/analytics/admin-posts/${id}/status`, {status: next});
      await load(page);
    } catch (error) {
      alert(error.message);
    } finally {
      if (button?.isConnected) button.disabled = false;
    }
  };
  let generation = 0, requestVersion = 0, rendered = '';
  let inlineEditingId = null, inlineState = '', inlineError = '', inlineText = null, editorSession = 0;
  let inlineAnimation = null, inlineResizeObserver = null, inlineAnchorStyle = null, inlineTargetHeight = 0;
  const metadataHomes = [];
  function stopPostInlineAnimation(restoreAnchor = true) {
    inlineAnimation?.cancel(); inlineAnimation = null;
    inlineResizeObserver?.disconnect(); inlineResizeObserver = null;
    if (restoreAnchor && inlineAnchorStyle !== null) {
      document.documentElement.style.overflowAnchor = inlineAnchorStyle;
      inlineAnchorStyle = null;
    }
  }
  function animatePostInline(reveal, from) {
    const height = reveal.firstElementChild.getBoundingClientRect().height;
    if (inlineAnimation && inlineTargetHeight === height) return;
    const previous = from ?? reveal.getBoundingClientRect().height;
    inlineAnimation?.cancel(); inlineAnimation = null; inlineTargetHeight = height;
    if (inlineAnchorStyle === null) inlineAnchorStyle = document.documentElement.style.overflowAnchor;
    document.documentElement.style.overflowAnchor = 'none';
    const finish = () => {
      reveal.style.overflow = '';
      if (inlineAnchorStyle !== null) document.documentElement.style.overflowAnchor = inlineAnchorStyle;
      inlineAnchorStyle = null; inlineAnimation = null;
    };
    if (!reveal.animate || matchMedia('(prefers-reduced-motion: reduce)').matches || Math.abs(previous - height) < 1) { finish(); return; }
    reveal.style.overflow = 'clip';
    inlineAnimation = reveal.animate([{height:previous+'px',opacity:previous ? 1 : 0},{height:height+'px',opacity:1}], {duration:340,easing:'cubic-bezier(.22,1,.36,1)'});
    const animation = inlineAnimation;
    animation.onfinish = () => { if (inlineAnimation === animation) finish(); };
  }
  function restorePostMetadata() {
    metadataHomes.forEach(({node, home}) => home.parentNode?.insertBefore(node, home.nextSibling));
  }
  function closePostInline() {
    editorSession++; editorOptionsVersion++;
    stopPostInlineAnimation();
    restorePostMetadata();
    document.querySelectorAll('#postsTable .post-inline-expand').forEach(row => row.remove());
    document.querySelectorAll('[data-post-inline-edit]').forEach(button => button.setAttribute('aria-expanded', 'false'));
    if (inlineEditingId !== null) editingId = null;
    inlineEditingId = null; inlineState = ''; inlineError = ''; inlineText = null;
  }
  function mountPostInline() {
    if (inlineEditingId === null) return;
    const table = document.getElementById('postsTable'), row = table?.querySelector(`tr[data-post-row-id="${inlineEditingId}"]`);
    if (!row) { closePostInline(); return; }
    const existing = table.querySelector(`#postInlineRow-${inlineEditingId}`);
    const previousHeight = existing?.querySelector('.post-inline-reveal')?.getBoundingClientRect().height || 0;
    stopPostInlineAnimation(false);
    if (inlineAnchorStyle === null) inlineAnchorStyle = document.documentElement.style.overflowAnchor;
    document.documentElement.style.overflowAnchor = 'none';
    restorePostMetadata();
    table.querySelectorAll('[data-post-inline-edit]').forEach(button => button.setAttribute('aria-expanded', String(Number(button.dataset.postInlineEdit) === inlineEditingId)));
    const id = inlineEditingId, cancel = `<button type="button" data-post-inline-cancel onclick="togglePostInlineEdit(${id})" class="rounded-xl border px-5 py-2 text-sm">${editorText('انصراف', 'Cancel')}</button>`;
    const content = inlineState === 'ready'
      ? `<fieldset data-post-inline-fields ${editorSaving?'disabled':''}></fieldset><div class="mt-4 flex justify-end">${cancel}</div>`
      : `<div role="${inlineState==='error'?'alert':'status'}" class="mb-4 text-sm">${esc(inlineState==='error'?inlineError:editorText('در حال بارگیری تنظیمات نوشته…', 'Loading post settings…'))}</div><div class="flex gap-3">${inlineState==='error'?`<button type="button" onclick="togglePostInlineEdit(${id},true)" class="rounded-xl bg-indigo-600 px-5 py-2 text-sm text-white">${editorText('تلاش دوباره', 'Retry')}</button>`:''}${cancel}</div>`;
    if (!existing) row.insertAdjacentHTML('afterend', window.AdminPanelTable.rowHTML([{html:`<div class="post-inline-reveal"><div class="post-inline-editor" data-post-inline-editor="${id}"></div></div>`,attributes:` colspan="${table.tHead.rows[0].cells.length}" data-admin-identity-colspan="1" class="bg-indigo-50/40" style="padding:0"`}], ` id="postInlineRow-${id}" class="post-inline-expand admin-inline-expand" data-admin-inline-expand`));
    const host = table.querySelector('.post-inline-editor'), reveal = host.parentElement;
    host.innerHTML = content;
    host.setAttribute('aria-busy', String(inlineState==='loading'||editorSaving));
    host.style.maxWidth = Math.max(260, table.parentElement.clientWidth - 32) + 'px';
    if (inlineState === 'ready') {
      if (!metadataHomes.length) document.querySelectorAll('[data-post-editor-title-card],[data-post-editor-settings]').forEach(node => { const home=document.createComment('post metadata home');node.before(home);metadataHomes.push({node,home}); });
      const fields=host.querySelector('[data-post-inline-fields]');metadataHomes.forEach(({node})=>fields.append(node));
    }
    animatePostInline(reveal, previousHeight);
    if (window.ResizeObserver) {
      inlineResizeObserver = new ResizeObserver(() => { if (host.isConnected && Math.abs(host.getBoundingClientRect().height - inlineTargetHeight) > 1) animatePostInline(reveal); });
      inlineResizeObserver.observe(host);
    }
  }
  window.togglePostInlineEdit = async (id, retry = false) => {
    if (editorSaving) return;
    id = Number(id);
    if (!Number.isInteger(id) || !posts.some(post => Number(post.id) === id)) return;
    if (inlineEditingId === id && !retry) { closePostInline(); return; }
    closePostInline();
    const session = ++editorSession;
    inlineEditingId = id; inlineState = 'loading';mountPostInline();
    try {
      const post = await api('/analytics/admin-posts/' + id);
      if (session !== editorSession || inlineEditingId !== id) return;
      editingId = id;
      inlineText = {content:post.content??'',summary:post.summary??'',description:post.description??''};
      fillEditor(post, true);inlineState = 'ready';mountPostInline();
    } catch (error) {
      if (session !== editorSession) return;
      inlineState = 'error';inlineError = error.message;mountPostInline();
    }
  };
  async function api(url, data = null) {
    if (data !== null) { cache.clear(); generation++; }
    const options = {
      method: data === null ? 'GET' : 'POST',
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    };
    if (data !== null) {
      options.headers['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';
      options.headers['X-CSRF-TOKEN'] = window.adminCsrfToken || '';
      options.body = new URLSearchParams({
        _token: window.adminCsrfToken || '',
        payload_b64: enc(data),
      });
    }
    const response = await fetch(url, options),
      raw = await response.text();
    let payload;
    try {
      payload = JSON.parse(raw);
    } catch (_) {
      throw new Error('پاسخ معتبر از سرور دریافت نشد.');
    }
    const body = payload.data ?? payload;
    if (!response.ok || body.success === false)
      throw new Error(body.message || 'عملیات نوشته ناموفق بود.');
    return body.data ?? body;
  }
  function query() {
    const params = new URLSearchParams({ page, perPage, excludeType: 'page' });
    const search = document.getElementById('postSearch')?.value.trim(),
      type = document.getElementById('postTypeFilter')?.value,
      visibility = document.getElementById('postVisibilityFilter')?.value,
      category = document.getElementById('postCategoryFilter')?.value;
    if (search) params.set('search', search);
    if (type) params.set('type', type);
    if (visibility) params.set('visibility', visibility);
    if (category) params.set('category', category);
    if (status !== 'all') params.set('status', status);
    return params;
  }
  async function load(nextPage = 1) {
    window.loadPostCategoryFilter();
    page = nextPage;
    try {
      const key = query().toString(), version = ++requestVersion, requestedGeneration = generation;
      const cached = cache.get(key);
      let data;
      if (cached && Date.now() - cached.at < 15000) data = cached.data;
      else {
        const pendingKey = generation + ':' + key;
        if (!pending.has(pendingKey)) {
          const epoch = generation;
          const request = api('/analytics/admin-posts?' + key).then(data => {
            if (epoch === generation) {
              if (cache.size >= 20) cache.delete(cache.keys().next().value);
              cache.set(key, { data, at: Date.now() });
            }
            return data;
          }).finally(() => pending.delete(pendingKey));
          pending.set(pendingKey, request);
        }
        data = await pending.get(pendingKey);
      }
      if (version !== requestVersion || requestedGeneration !== generation) return;
      posts = data.posts || [];
      total = Number(data.total || 0);
      page = Number(data.page || 1);
      perPage = 10;
      render(data.statusCounts || {});
    } catch (error) {
      alert(error.message);
    }
  }
  function render(counts) {
    const tbody = document.querySelector('#postsTable tbody');
    if (!tbody) return;
    const signature = JSON.stringify([posts, counts, page, perPage, total]);
    if (rendered === signature && tbody.rows.length) return;
    rendered = signature;
    const rows = posts.length
      ? posts
          .map(
            (post) =>
              ({ cells: [{ html: `<a href="#post-editor" data-post-title="${Number(post.id)}" onclick="editPost(${Number(post.id)});return false" class="font-medium text-indigo-600 hover:underline">${esc(post.title)}</a><div class="mt-0.5 text-xs text-gray-400">${esc(post.slug)}</div>`, attributes: ` class="py-4 px-5"` }, { html: `${esc(post.author_name || '#' + post.author_id)}`, attributes: ` class="py-4 px-5"` }, { html: `${esc((post.category_names || []).join(', ') || '—')}`, attributes: ` class="py-4 px-5 text-xs text-gray-500"` }, { html: `<span class="rounded-lg bg-indigo-50 px-2 py-1 text-xs text-indigo-700">${esc(typeLabels[post.type] || post.type)}</span>`, attributes: ` class="py-4 px-5"` }, { html: `<button type="button" onclick="cyclePostStatus(${Number(post.id)},this)" class="rounded-full px-3 py-1 text-xs ${statusColors[post.status] || 'bg-gray-100 text-gray-600'}">${esc(statusLabels[post.status] || post.status)}</button>`, attributes: ` class="py-4 px-5"` }, { html: `${post.views_count || 0}`, attributes: ` class="py-4 px-5"` }, { html: `${esc(post.published_at || post.created_at || '—')}`, attributes: ` class="py-4 px-5 text-xs"` }, { html: `<button onclick="previewPost(${post.id})" class="ml-2 text-sm text-emerald-600">پیش‌نمایش</button><button onclick="viewPost(${post.id})" class="ml-2 text-sm text-indigo-600">جزئیات</button><button type="button" data-post-inline-edit="${Number(post.id)}" aria-expanded="false" aria-controls="postInlineRow-${Number(post.id)}" onclick="togglePostInlineEdit(${Number(post.id)})" class="ml-2 text-sm text-indigo-600">ویرایش</button>${post.status === 'trash' ? `<button onclick="restorePost(${post.id})" class="ml-2 text-sm text-green-600">بازیابی</button>` : ''}<button onclick="deletePost(${post.id})" class="text-sm text-red-500">${post.status === 'trash' ? 'حذف دائمی' : 'زباله‌دان'}</button>`, attributes: ` class="py-4 px-5 text-left whitespace-nowrap"` }], attributes: ` data-post-row-id="${Number(post.id)}" class="hover:bg-gray-50"` })
          )
      : [{ cells: [{ html: "نوشته‌ای یافت نشد", attributes: " colspan=\"8\" class=\"py-12 text-center text-gray-400\"" }], attributes: "" }];
    restorePostMetadata();
    window.AdminPanelTable.setRows(tbody.closest('table'), rows);
    mountPostInline();
    document.querySelectorAll('[data-post-status-count]').forEach((element) => {
      const key = element.dataset.postStatusCount;
      element.textContent =
        key === 'all'
          ? Object.values(counts).reduce((sum, n) => sum + Number(n), 0)
          : Number(counts[key] || 0);
    });
    window.AdminPanelTable.renderPagination(document.getElementById('postsPagination'), { total, perPage: 10, page, onChange: next => load(next) });
  }
  window.filterPosts = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => load(1), 250);
  };
  window.filterPostsByStatus = (next) => {
    status = next;
    document.querySelectorAll('.post-status-tab').forEach((button) => {
      const active = button.dataset.status === next;
      button.classList.remove('bg-gray-900');
      button.classList.toggle('bg-indigo-600', active);
      button.classList.toggle('hover:bg-gray-50', !active);
      button.classList.toggle('border-gray-200', !active);
      button.setAttribute('aria-pressed', String(active));
      button.classList.toggle('text-white', active);
      button.classList.toggle('border', !active);
    });
    load(1);
  };
  window.changePostsPerPage = () => load(1); // Compatibility with an already-served legacy template.
  window.changePostsPage = (next) => {
    const pages = Math.max(1, Math.ceil(total / perPage));
    if (next >= 1 && next <= pages) load(next);
  };
  function setValue(id, value) {
    const element = document.getElementById(id);
    if (element) element.value = value ?? '';
  }
  const editorSelection = { categories: new Set(), related: new Set() };
  let categoryChoices = null, categoryChoicesPending = null, categoryChoicesVersion = 0;
  function loadCategoryChoices() {
    if (categoryChoices) return Promise.resolve(categoryChoices);
    if (categoryChoicesPending) return categoryChoicesPending;
    const version = categoryChoicesVersion;
    const request = api('/analytics/admin-post-categories').then(items => {
      if (version === categoryChoicesVersion) categoryChoices = items;
      return items;
    }).finally(() => { if (version === categoryChoicesVersion) categoryChoicesPending = null; });
    categoryChoicesPending = request;
    return request;
  }
  window.loadPostCategoryFilter = async () => {
    const select = document.getElementById('postCategoryFilter');
    if (!select || select.dataset.categoryChoicesVersion === String(categoryChoicesVersion)) return;
    const version = categoryChoicesVersion;
    select.setAttribute('aria-busy', 'true');
    try {
      const items = await loadCategoryChoices();
      if (version !== categoryChoicesVersion || select.dataset.categoryChoicesVersion === String(version)) return;
      const selected = select.value;
      const options = items.map(item => new Option(editorText(item.title_fa || item.slug, item.title_en || item.title_fa || item.slug), String(item.id)));
      select.replaceChildren(new Option(editorText('همه دسته‌ها', 'All categories'), ''), ...options);
      select.value = selected && options.some(option => option.value === selected) ? selected : '';
      select.dataset.categoryChoicesVersion = String(version);
      select.removeAttribute('title');
      if (selected && !select.value) load(1);
    } catch (_) {
      select.title = editorText('بارگیری دسته‌ها ناموفق بود؛ دوباره فهرست را باز کنید.', 'Categories could not be loaded. Open the list again to retry.');
    } finally { if (version === categoryChoicesVersion) select.removeAttribute('aria-busy'); }
  };
  let editorOptionsVersion = 0, editorCoverMediaId = null, editorCoverUrl = '', editorCoverUpload = null, editorSaving = false, editorAudience = null;
  let relatedOptions = [], relatedSearch = '';
  const editorText = (fa, en) => document.documentElement.lang.startsWith('en') ? en : fa;
  const splitSelection = value => new Set(String(value || '').split(/[,،]/).map(value => value.trim()).filter(Boolean));
  function renderEditorChoices(containerId, options, selected, search = '') {
    const container = document.getElementById(containerId);
    if (!container) return;
    const known = new Set(options.map(item => String(item.id)));
    const choices = [...options, ...[...selected].filter(id => !known.has(id)).map(id => ({ id, title: containerId === 'peCategories' && !/^\d+$/.test(id) ? id : editorText('مورد قبلی در دسترس نیست', 'Previously selected item unavailable') }))];
    container.replaceChildren();
    for (const item of choices) {
      if (search && !String(item.title).toLocaleLowerCase().includes(search.toLocaleLowerCase())) continue;
      const label = document.createElement('label'); label.className = 'flex items-center gap-2 text-sm';
      const input = document.createElement('input'); input.type = 'checkbox'; input.value = String(item.id);
      input.checked = selected.has(input.value); input.className = 'accent-indigo-600';
      input.addEventListener('change', () => input.checked ? selected.add(input.value) : selected.delete(input.value));
      const text = document.createElement('span'); text.textContent = item.title;
      label.append(input, text); container.append(label);
    }
    if (!container.children.length) container.textContent = editorText('موردی یافت نشد', 'No items found');
  }
  window.filterPostEditorRelated = value => {
    relatedSearch = value;
    renderEditorChoices('peRelated', relatedOptions, editorSelection.related, value);
  };
  window.loadPostEditorSelections = async () => {
    const version = ++editorOptionsVersion;
    const results = await Promise.allSettled([loadCategoryChoices(), api('/analytics/admin-posts?editorOptions=1')]);
    if (version !== editorOptionsVersion) return;
    if (results[0].status === 'fulfilled') {
      const options = results[0].value.map(item => ({ id: item.id, title: editorText(item.title_fa || item.slug, item.title_en || item.title_fa || item.slug) }));
      renderEditorChoices('peCategories', options, editorSelection.categories);
    }
    if (results[1].status === 'fulfilled') {
      relatedOptions = (results[1].value.posts || []).filter(item => String(item.id) !== String(editingId));
      renderEditorChoices('peRelated', relatedOptions, editorSelection.related, relatedSearch);
    }
    results.forEach((result, index) => {
      if (result.status !== 'rejected') return;
      const message = document.createElement('button'); message.type = 'button';
      message.className = 'block text-sm text-indigo-600';
      message.textContent = editorText('بارگیری ناموفق بود؛ تلاش دوباره', 'Loading failed; retry');
      message.onclick = window.loadPostEditorSelections;
      document.getElementById(index === 0 ? 'peCategories' : 'peRelated')?.append(message);
    });
  };
  function editorData(metadataOnly = inlineEditingId !== null) {
    const title = document.getElementById('peTitle').value.trim();
    if (!title) {
      alert('عنوان نوشته الزامی است.');
      return null;
    }
    if (!metadataOnly) peSyncToCode();
    return {
      title,
      slug: document.getElementById('peSlug').value.trim(),
      ...(!metadataOnly ? {content: document.getElementById('peContent').value, summary: document.getElementById('peSummary').value.trim(), description: document.getElementById('peDescription').value.trim(), visibility_user_id: editorAudience} : {}),
      status: document.getElementById('peStatus').value,
      visibility: document.getElementById('peVisibility').value,
      published_at: document.getElementById('pePublishedAt').value || null,
      password: document.getElementById('pePassword').value,
      type: document.getElementById('peType').value,
      categories: [...editorSelection.categories].join(','),
      cover: editorCoverUrl,
      cover_media_id: editorCoverMediaId,
      related_posts_id: [...editorSelection.related].join(','),
      guid: document.getElementById('peGuid').value.trim(),
    };
  }
  function fillEditor(post = {}, metadataOnly = false) {
    editorAudience = post.visibility_user_id ?? null;
    setValue('peTitle', post.title);
    setValue('peSlug', post.slug);
    if (!metadataOnly) {
      setValue('peContent', post.content);
      document.getElementById('peVisualContent').innerHTML = post.content || '';
      setValue('peSummary', post.summary);
      setValue('peDescription', post.description);
    }
    const statusField = document.getElementById('peStatus');
    if (post.status && ![...statusField.options].some(option => option.value === post.status)) {
      const option = document.createElement('option'); option.value = post.status;
      option.textContent = statusLabels[post.status] || post.status; statusField.append(option);
    }
    setValue('peStatus', post.status || 'draft');
    setValue('peVisibility', post.visibility || 'public');
    setValue(
      'pePublishedAt',
      post.published_at ? String(post.published_at).replace(' ', 'T').slice(0, 16) : ''
    );
    setValue('pePassword', post.password);
    setValue('peType', post.type || 'post');
    editorSelection.categories = splitSelection(post.categories);
    editorSelection.related = splitSelection(post.related_posts_id);
    relatedOptions = []; relatedSearch = '';
    setValue('peRelatedSearch', '');
    renderEditorChoices('peCategories', [], editorSelection.categories);
    renderEditorChoices('peRelated', [], editorSelection.related);
    window.loadPostEditorSelections();
    setValue('peCover', '');
    document.getElementById('peCover').disabled = false;
    editorCoverUpload = null;
    editorCoverMediaId = post.cover_media_id || null;
    editorCoverUrl = post.cover || '';

    setValue('peGuid', post.guid);
    document.getElementById('peViewsCount').textContent = post.views_count || 0;
    document.getElementById('peCommentCount').textContent = post.comment_count || 0;
    document.getElementById('pePostId').textContent = post.id || '—';
    const panelTitle = document.getElementById('panelPageTitle');
    if (panelTitle) panelTitle.textContent = editorText('نوشته ها', 'Posts');
    document.title = editorText('نوشته ها', 'Posts');
    if (!metadataOnly) { peSetEditorMode('visual'); peUpdateWordCount(); }
    updateCoverPreview();
  }
  window.openPostEditor = () => {
    if (editorSaving) return;
    closePostInline(); editorSession++;
    editingId = null;
    showSection('post-editor');
    fillEditor();
  };
  window.openAddPostModal = window.openPostEditor;
  window.editPost = async (id) => {
    if (editorSaving) return;
    closePostInline();
    const session = ++editorSession;
    try {
      const post = await api('/analytics/admin-posts/' + id);
      if (session !== editorSession) return;
      editingId = id;
      closeModal();
      showSection('post-editor');
      fillEditor(post);
    } catch (error) {
      alert(error.message);
    }
  };
  window.closePostEditor = () => { editorSession++; editorOptionsVersion++; showSection('posts'); };
  window.savePostEditor = async (action) => {
    if (editorSaving) return;
    const session = editorSession, inlineId = inlineEditingId, postId = editingId;
    if (inlineId !== null && inlineState !== 'ready') return;
    if (editorCoverUpload) {
      try { await editorCoverUpload; } catch (_) { return; }
      if (editorSaving) return;
    }
    if (session !== editorSession) return;
    const data = editorData();
    if (!data) return;
    if (action === 'draft') data.status = 'draft';
    editorSaving = true;
    const inlineFields = document.querySelector('[data-post-inline-fields]');
    if (inlineFields) inlineFields.disabled = true;
    document.querySelector('.post-inline-editor')?.setAttribute('aria-busy','true');
    const cancel = document.querySelector('[data-post-inline-cancel]');
    if (cancel) cancel.disabled = true;

    try {
      if (inlineId !== null) await api(`/analytics/admin-posts/${inlineId}/metadata`, data);
      else if (postId) await api(`/analytics/admin-posts/${postId}/update`, data);
      else {
        const result = await api('/analytics/admin-posts', data);
        if (session === editorSession) editingId = result.id;
      }
      if (session !== editorSession) return;
      alert('نوشته با موفقیت ذخیره شد.');
      if (inlineId !== null) closePostInline();
      showSection('posts');
      await load(inlineId !== null ? page : 1);
    } catch (error) {
      if (session === editorSession) alert(error.message);
    } finally {
      editorSaving = false;
      const fields = document.querySelector('[data-post-inline-fields]');
      if (fields) fields.disabled = false;
      document.querySelector('.post-inline-editor')?.setAttribute('aria-busy','false');
      const cancel = document.querySelector('[data-post-inline-cancel]');
      if (cancel) cancel.disabled = false;
    }
  };
  window.movePostToTrash = async () => {
    if (!editingId) return;
    if (await AppDialog.confirm('این نوشته به زباله‌دان منتقل شود؟')) {
      await api(`/analytics/admin-posts/${editingId}/trash`, {});
      showSection('posts');
      load(1);
    }
  };
  window.deletePost = async (id) => {
    const post = posts.find((item) => item.id === id);
    if (!post) return;
    if (post.status === 'trash') {
      if (
        await AppDialog.confirmDelete(post, id, 'نوشته', {
          message: `نوشته «${post.title}» برای همیشه حذف شود؟`,
        })
      ) {
        try {
          await api(`/analytics/admin-posts/${id}/delete`, {});
          load(page);
        } catch (error) {
          alert(error.message);
        }
      }
    } else if (await AppDialog.confirm(`نوشته «${post.title}» به زباله‌دان منتقل شود؟`)) {
      try {
        await api(`/analytics/admin-posts/${id}/trash`, {});
        load(page);
      } catch (error) {
        alert(error.message);
      }
    }
  };
  window.restorePost = async (id) => {
    try {
      await api(`/analytics/admin-posts/${id}/restore`, {});
      load(page);
    } catch (error) {
      alert(error.message);
    }
  };
  window.viewPost = async (id) => {
    try {
      const post = await api('/analytics/admin-posts/' + id),
        color = statusColors[post.status] || 'bg-gray-100 text-gray-600';
      document.getElementById('modalContainer').innerHTML =
        `<div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 p-4" onclick="if(event.target===this)closeModal()"><div class="my-8 w-full max-w-4xl rounded-3xl bg-white shadow-2xl"><div class="flex items-center justify-between border-b px-8 py-5"><div><h2 class="text-2xl font-bold">${esc(post.title)}</h2><p class="mt-1 text-sm text-gray-500"><span class="rounded px-2 py-0.5 text-xs ${color}">${esc(statusLabels[post.status])}</span> · ${esc(typeLabels[post.type])} · ${esc(visibilityLabels[post.visibility])}</p></div><div class="flex gap-3"><button onclick="editPost(${post.id})" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm text-white">ویرایش</button><button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div></div><div class="max-h-[75vh] space-y-5 overflow-y-auto p-8">${post.cover ? `<img src="${esc(post.cover)}" class="max-h-80 w-full rounded-2xl object-cover" alt="">` : ''}${post.summary ? `<p class="text-lg font-medium text-indigo-600">${esc(post.summary)}</p>` : ''}${post.description ? `<p class="text-gray-600">${esc(post.description)}</p>` : ''}<article class="prose max-w-none rounded-2xl border bg-gray-50 p-5">${post.content || '<span class="text-gray-400">بدون محتوا</span>'}</article><div class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2"><div>نویسنده: ${esc(post.author_name)}</div><div>نامک: ${esc(post.slug)}</div><div>دسته‌ها: ${esc(post.categories || '—')}</div><div>تاریخ انتشار: ${esc(post.published_at || '—')}</div><div>بازدید: ${post.views_count}</div><div>دیدگاه‌ها: ${post.comment_count}</div></div></div></div></div>`;
    } catch (error) {
      alert(error.message);
    }
  };
  window.previewPostEditor = async () => {
    const session = editorSession;
    if (editorCoverUpload) { try { await editorCoverUpload; } catch (_) { return; } }
    if (session !== editorSession) return;
    const data = editorData();
    if (data) return window.previewPost(editingId, {...data,...(inlineText||{})});
  };
  window.previewPost = async (id, draft = null) => {
    try {
      const post = draft || await api('/analytics/admin-posts/' + id),
        editAction = draft ? 'closeModal()' : `editPost(${Number(post.id)})`,
        categories = String(post.categories || '')
          .split(',')
          .map((item) => item.trim())
          .filter(Boolean),
        documentHtml = `<!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><style>*{box-sizing:border-box}body{margin:0;background:#fff;color:#374151;font-family:Tahoma,Arial,sans-serif;line-height:2}.page{max-width:850px;margin:auto;padding:48px 28px}h1{color:#111827;font-size:2.25rem;line-height:1.45;margin:0 0 20px}.meta{display:flex;flex-wrap:wrap;gap:12px 28px;border-bottom:1px solid #eee;padding-bottom:20px;margin-bottom:28px;color:#6b7280;font-size:14px}.cover{display:block;width:100%;max-height:430px;object-fit:cover;border-radius:20px;margin-bottom:28px}.tags{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:24px}.tag{background:#eef2ff;color:#4338ca;border-radius:999px;padding:3px 12px;font-size:13px}.summary{background:#eef2ff;border:1px solid #e0e7ff;color:#312e81;padding:20px;border-radius:18px;margin-bottom:28px}.description{color:#6b7280;margin-bottom:28px}.content{font-size:16px;text-align:justify}.content img{max-width:100%;height:auto}.content h2{color:#111827;font-size:1.55rem;margin-top:38px}.content h3{color:#111827;font-size:1.25rem;margin-top:30px}.content a{color:#4f46e5}.content blockquote{border-right:4px solid #c7d2fe;background:#f8fafc;margin:24px 0;padding:14px 20px;border-radius:12px}.empty{color:#9ca3af;text-align:center;padding:40px}</style></head><body><main class="page"><h1>${esc(post.title)}</h1><div class="meta"><span>نویسنده: ${esc(post.author_name || '—')}</span><span>تاریخ انتشار: ${esc(post.published_at || 'منتشر نشده')}</span><span>${post.views_count || 0} بازدید</span></div>${post.cover ? `<img class="cover" src="${esc(post.cover)}" alt="${esc(post.title)}">` : ''}${categories.length ? `<div class="tags">${categories.map((category) => `<span class="tag">${esc(category)}</span>`).join('')}</div>` : ''}${post.summary ? `<div class="summary">${esc(post.summary)}</div>` : ''}${post.description ? `<div class="description">${esc(post.description)}</div>` : ''}<article class="content">${post.content || '<div class="empty">این نوشته هنوز محتوایی ندارد.</div>'}</article></main></body></html>`;
      document.getElementById('modalContainer').innerHTML =
        `<div class="fixed inset-0 z-50 flex flex-col bg-gray-900/80 p-3 md:p-6"><div class="mx-auto flex w-full max-w-6xl items-center justify-between rounded-t-2xl bg-gray-900 px-5 py-3 text-white"><div><span class="font-medium">پیش‌نمایش نوشته</span><span class="mr-3 text-xs text-gray-400">${esc(statusLabels[post.status] || post.status)}</span></div><div class="flex items-center gap-3"><button onclick="${editAction}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm">ویرایش</button><button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div></div><iframe id="postPreviewFrame" sandbox="" class="mx-auto h-full w-full max-w-6xl rounded-b-2xl bg-white" title="پیش‌نمایش ${esc(post.title)}"></iframe></div>`;
      document.getElementById('postPreviewFrame').srcdoc = documentHtml;
    } catch (error) {
      alert(error.message);
    }
  };
  window.selectPostCover = async input => {
    const file = input.files?.[0];
    if (!file) return;
    const allowed = { png: 'image/png', jpg: 'image/jpeg', webp: 'image/webp' };
    const extension = file.name.split('.').pop().toLowerCase();
    if (!allowed[extension] || (file.type && file.type !== allowed[extension]) || !file.size || file.size > 50 * 1024 * 1024) {
      input.value = '';
      alert(editorText('فقط تصویر PNG، JPG یا WebP تا حجم ۵۰ مگابایت مجاز است.', 'Select a PNG, JPG or WebP image up to 50 MB.'));
      return;
    }
    const version = editorOptionsVersion;
    const preview = URL.createObjectURL(file);
    updateCoverPreview(preview);
    input.disabled = true;
    const form = new FormData();
    form.append('file', file); form.append('_token', window.adminCsrfToken || '');
    const pending = (async () => {
      const response = await fetch('/analytics/admin-media/upload-post-cover', {
        method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': window.adminCsrfToken || '' }, body: form,
      });
      const payload = await response.json(), envelope = payload.data ?? payload, body = envelope.data ?? envelope;
      if (!response.ok || payload.success === false || envelope.success === false || !body.url || !body.id) throw new Error(envelope.message || payload.message || editorText('بارگذاری تصویر ناموفق بود.', 'Image upload failed.'));
      if (version !== editorOptionsVersion) throw new Error('Editor changed');
      editorCoverUrl = body.url; editorCoverMediaId = Number(body.id);
      updateCoverPreview();
    })();
    editorCoverUpload = pending;
    try { await pending; }
    catch (error) { if (version === editorOptionsVersion) { updateCoverPreview(); input.value = ''; alert(error.message); } }
    finally {
      URL.revokeObjectURL(preview);
      if (editorCoverUpload === pending) editorCoverUpload = null;
      if (version === editorOptionsVersion) input.disabled = false;
    }
  };
  window.updateCoverPreview = (previewUrl = editorCoverUrl) => {
    const url = previewUrl,
      box = document.getElementById('peCoverPreview'),
      image = document.getElementById('peCoverImg');
    if (!box || !image) return;
    image.classList.toggle('hidden', !url);
    if (url) image.src = url;
    else image.removeAttribute('src');
  };
  function peSyncToCode() {
    const visual = document.getElementById('peVisualContent'),
      code = document.getElementById('peContent');
    if (editorMode === 'visual' && visual && code) code.value = visual.innerHTML;
  }
  function peSyncToVisual() {
    const visual = document.getElementById('peVisualContent'),
      code = document.getElementById('peContent');
    if (visual && code) visual.innerHTML = code.value;
  }
  function peUpdateWordCount() {
    const source =
        editorMode === 'visual'
          ? document.getElementById('peVisualContent')?.innerText
          : document.getElementById('peContent')?.value.replace(/<[^>]*>/g, ' '),
      count = String(source || '')
        .trim()
        .split(/\s+/u)
        .filter(Boolean).length,
      label = document.getElementById('peWordCount');
    if (label) label.textContent = `${count.toLocaleString('fa-IR')} واژه`;
  }
  window.peSetEditorMode = (mode) => {
    if (mode === 'code' && editorMode === 'visual') peSyncToCode();
    if (mode === 'visual' && editorMode === 'code') peSyncToVisual();
    editorMode = mode === 'code' ? 'code' : 'visual';
    document.getElementById('peVisualContent')?.classList.toggle('hidden', editorMode !== 'visual');
    document.getElementById('peVisualToolbar')?.classList.toggle('hidden', editorMode !== 'visual');
    document.getElementById('peContent')?.classList.toggle('hidden', editorMode !== 'code');
    for (const [id, active] of [
      ['peVisualTab', editorMode === 'visual'],
      ['peCodeTab', editorMode === 'code'],
    ]) {
      const tab = document.getElementById(id);
      tab?.classList.toggle('bg-white', active);
      tab?.classList.toggle('border', active);
      tab?.classList.toggle('border-b-white', active);
      tab?.classList.toggle('text-indigo-600', active);
      tab?.classList.toggle('text-gray-500', !active);
    }
    peUpdateWordCount();
  };
  window.peVisualChanged = () => {
    peSyncToCode();
    peUpdateWordCount();
  };
  window.peCodeChanged = peUpdateWordCount;
  window.peExec = (command, value = null) => {
    if (editorMode !== 'visual') peSetEditorMode('visual');
    document.getElementById('peVisualContent')?.focus();
    document.execCommand(command, false, value);
    peVisualChanged();
  };
  window.peInsertLink = () => {
    const url = prompt('آدرس لینک را وارد کنید:', 'https://');
    if (!url) return;
    const text = window.getSelection()?.toString();
    if (text) peExec('createLink', url);
    else peInsertHtml(`<a href="${esc(url)}" target="_blank" rel="noopener">${esc(url)}</a>`);
  };
  window.peInsertImage = () => {
    const url = prompt('آدرس تصویر را وارد کنید:', 'https://');
    if (!url) return;
    const alt = prompt('متن جایگزین تصویر:', '') || '';
    peInsertHtml(`<img src="${esc(url)}" alt="${esc(alt)}" style="max-width:100%;height:auto">`);
  };
  window.peInsertTable = () => {
    const rows = Math.min(20, Math.max(1, Number(prompt('تعداد ردیف‌ها:', '3')) || 0)),
      columns = Math.min(10, Math.max(1, Number(prompt('تعداد ستون‌ها:', '3')) || 0));
    if (!rows || !columns) return;
    let html = '<table><tbody>';
    for (let row = 0; row < rows; row++) {
      html += '<tr>';
      for (let column = 0; column < columns; column++)
        html += `<${row === 0 ? 'th' : 'td'}>${row === 0 ? 'عنوان' : 'متن'}</${row === 0 ? 'th' : 'td'}>`;
      html += '</tr>';
    }
    peInsertHtml(html + '</tbody></table><p><br></p>');
  };
  function peInsertHtml(html) {
    if (editorMode !== 'visual') peSetEditorMode('visual');
    document.getElementById('peVisualContent')?.focus();
    document.execCommand('insertHTML', false, html);
    peVisualChanged();
  }
  window.peToggleFullscreen = () =>
    document.getElementById('post-editor')?.classList.toggle('pe-editor-fullscreen');
  window.peFormat = window.peExec;
  window.peInsertHeading = () => peExec('formatBlock', 'h2');
  window.peInsertList = () => peExec('insertUnorderedList');
  window.loadPosts = () => load(page);
  function invalidatePostChoices(url) {
    if (!/\/analytics\/admin-post-categories(?:[/?]|$)/.test(url)) return;
    categoryChoices = null; categoryChoicesPending = null; categoryChoicesVersion++;
    window.loadPostCategoryFilter();
  }
  window.addEventListener('sornaz:data-mutated', event => {
    invalidatePostChoices(event.detail?.url || '');
    if (/\/analytics\/admin-post(?:s|-categories)(?:[/?]|$)/.test(event.detail?.url || '')) { cache.clear(); generation++; }
  });
  window.addEventListener('sornaz:remote-mutation', event => {
    invalidatePostChoices(event.detail?.url || '');
    if (!/\/analytics\/admin-post(?:s|-categories)(?:[/?]|$)/.test(event.detail?.url || '')) return;
    cache.clear(); generation++;
    if (!document.getElementById('posts')?.classList.contains('hidden')) load(page);
  });
  window.renderPostsBranchTabs = () => {};
  window.filterPostsByBranch = () => load(1);
  setTimeout(() => {
    if (document.querySelector('#posts:not(.hidden) #postsTable tbody')) load(1);
  }, 200);
})();
