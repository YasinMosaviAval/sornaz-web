(function () {
  'use strict';
  let items = [],
    editingId = null,
    timer = null;
  const esc = (value) =>
    String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  const enc = (data) =>
    btoa(unescape(encodeURIComponent(JSON.stringify(data))))
      .replace(/\+/g, '-')
      .replace(/\//g, '_')
      .replace(/=+$/, '');
  const cache = new Map(), pending = new Map();
  let generation = 0, requestVersion = 0;
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
      payload = await response.json(),
      body = payload.data ?? payload;
    if (!response.ok || body.success === false)
      throw new Error(body.message || 'عملیات دسته‌بندی ناموفق بود.');
    return body.data ?? body;
  }
  async function load() {
    const search = document.getElementById('postCategorySearch')?.value.trim() || '';
    try {
      const version = ++requestVersion, epoch = generation;
      const cached = cache.get(search);
      const key = epoch + ':' + search;
      let data;
      if (cached && Date.now() - cached.at < 15000) data = cached.data;
      else {
        if (!pending.has(key)) pending.set(key,
          api('/analytics/admin-post-categories?search=' + encodeURIComponent(search)).then(data => {
            if (epoch === generation) {
              if (cache.size >= 20) cache.delete(cache.keys().next().value);
              cache.set(search, { data, at: Date.now() });
            }
            return data;
          }).finally(() => pending.delete(key)));
        data = await pending.get(key);
      }
      if (version !== requestVersion || epoch !== generation) return;
      if (search !== lastSearch) categoryPage = 1;
      lastSearch = search;
      items = data;
      render();
    } catch (error) {
      alert(error.message);
    }
  }
  let rendered = '', categoryPage = 1, lastSearch = '';
  const categoryCells = item => [{ html: `${esc(item.title_fa || '—')}`, attributes: ` class="px-5 py-4 font-medium"` }, { html: `${esc(item.title_en || '—')}`, attributes: ` class="px-5 py-4" dir="ltr"` }, { html: `${esc(item.slug)}`, attributes: ` class="px-5 py-4 text-gray-500" dir="ltr"` }, { html: `${esc(item.group)}`, attributes: ` class="px-5 py-4"` }, { html: `<span class="rounded-full bg-indigo-50 px-3 py-1 text-indigo-700">${Number(item.posts_count || 0).toLocaleString('fa-IR')}</span>`, attributes: ` class="px-5 py-4"` }, { html: `<button onclick="editPostCategory(${item.id})" class="ml-4 text-indigo-600">ویرایش</button><button onclick="deletePostCategory(${item.id})" class="text-red-500 ${item.posts_count ? 'opacity-40' : ''}" ${item.posts_count ? 'title="دسته‌بندی دارای نوشته است"' : ''}>حذف</button>`, attributes: ` class="whitespace-nowrap px-5 py-4 text-left"` }];
  function render() {
    const tbody = document.querySelector('#postCategoriesTable tbody');
    if (!tbody) return;
    categoryPage = Math.max(1, Math.min(categoryPage, Math.ceil(items.length / 10) || 1));
    const start = (categoryPage - 1) * 10;
    const visible = window.AdminPanelTable.page(tbody.closest('table'), items, start, start + 10, () => render(), item => item.id, categoryCells);
    const signature = JSON.stringify([visible, categoryPage, items.length]);
    if (rendered === signature && tbody.rows.length) return;
    rendered = signature;
    const rows = visible.length ? visible.map(item => ({cells: categoryCells(item), attributes: 'data-id="' + Number(item.id) + '"'})) : [{cells:[{value: document.documentElement.lang.startsWith('en') ? 'No categories found' : 'دسته‌بندی‌ای یافت نشد', attributes:'colspan="6" class="px-5 py-12 text-center text-gray-400"'}]}];
    window.AdminPanelTable.setRows(tbody.closest('table'), rows, {pageOffset: start});
    window.AdminPanelTable.renderPagination(document.getElementById('postCategoriesPagination'), {total:items.length, perPage:10, page:categoryPage, onChange: next => {categoryPage = next; render();}});
  }
  function form(item = {}) {
    editingId = item.id || null;
    document.getElementById('modalContainer').innerHTML =
      `<div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 p-4" onclick="if(event.target===this)closeModal()"><form onsubmit="savePostCategory(event)" class="my-8 w-full max-w-3xl rounded-3xl bg-white shadow-2xl"><div class="flex items-center justify-between border-b px-7 py-5"><div><h2 class="text-2xl font-bold">${item.id ? 'ویرایش' : 'افزودن'} دسته‌بندی</h2><p class="mt-1 text-sm text-gray-500">عنوان‌ها و توضیحات هر زبان را جداگانه وارد کنید.</p></div><button type="button" onclick="closeModal()" class="text-3xl text-gray-300">×</button></div><div class="grid max-h-[70vh] grid-cols-1 gap-5 overflow-y-auto p-7 md:grid-cols-2"><label class="space-y-2"><span class="text-sm font-medium">عنوان فارسی *</span><input id="pcTitleFa" required value="${esc(item.title_fa)}" class="w-full rounded-xl border px-4 py-3"></label><label class="space-y-2"><span class="text-sm font-medium">عنوان انگلیسی</span><input id="pcTitleEn" dir="ltr" value="${esc(item.title_en)}" class="w-full rounded-xl border px-4 py-3"></label><label class="space-y-2"><span class="text-sm font-medium">نامک</span><input id="pcSlug" dir="ltr" value="${esc(item.slug)}" placeholder="music-theory" class="w-full rounded-xl border px-4 py-3"></label><label class="space-y-2"><span class="text-sm font-medium">گروه</span><input id="pcGroup" dir="ltr" value="${esc(item.group || 'posts')}" class="w-full rounded-xl border px-4 py-3"></label><label class="space-y-2"><span class="text-sm font-medium">خلاصه فارسی</span><textarea id="pcSummaryFa" rows="3" class="w-full rounded-xl border px-4 py-3">${esc(item.summary_fa)}</textarea></label><label class="space-y-2"><span class="text-sm font-medium">خلاصه انگلیسی</span><textarea id="pcSummaryEn" dir="ltr" rows="3" class="w-full rounded-xl border px-4 py-3">${esc(item.summary_en)}</textarea></label><label class="space-y-2"><span class="text-sm font-medium">توضیحات فارسی</span><textarea id="pcDescriptionFa" rows="5" class="w-full rounded-xl border px-4 py-3">${esc(item.description_fa)}</textarea></label><label class="space-y-2"><span class="text-sm font-medium">توضیحات انگلیسی</span><textarea id="pcDescriptionEn" dir="ltr" rows="5" class="w-full rounded-xl border px-4 py-3">${esc(item.description_en)}</textarea></label></div><div class="flex justify-end gap-3 border-t px-7 py-5"><button type="button" onclick="closeModal()" class="rounded-xl border px-5 py-2.5">انصراف</button><button class="rounded-xl bg-indigo-600 px-6 py-2.5 text-white">ذخیره دسته‌بندی</button></div></form></div>`;
  }
  window.openPostCategoryForm = () => form();
  window.editPostCategory = (id) => {
    const item = items.find((x) => x.id === id);
    if (item) form(item);
  };
  window.savePostCategory = async (event) => {
    event.preventDefault();
    const data = {
      title_fa: document.getElementById('pcTitleFa').value.trim(),
      title_en: document.getElementById('pcTitleEn').value.trim(),
      slug: document.getElementById('pcSlug').value.trim(),
      group: document.getElementById('pcGroup').value.trim(),
      summary_fa: document.getElementById('pcSummaryFa').value.trim(),
      summary_en: document.getElementById('pcSummaryEn').value.trim(),
      description_fa: document.getElementById('pcDescriptionFa').value.trim(),
      description_en: document.getElementById('pcDescriptionEn').value.trim(),
    };
    try {
      await api(
        editingId
          ? `/analytics/admin-post-categories/${editingId}/update`
          : '/analytics/admin-post-categories',
        data
      );
      closeModal();
      await load();
    } catch (error) {
      alert(error.message);
    }
  };
  window.deletePostCategory = async (id) => {
    const item = items.find((x) => x.id === id);
    if (!item) return;
    if (item.posts_count) {
      alert('این دسته‌بندی به نوشته‌ها متصل است و قابل حذف نیست.');
      return;
    }
    if (await AppDialog.confirm(`دسته‌بندی «${item.title_fa}» حذف شود؟`)) {
      try {
        await api(`/analytics/admin-post-categories/${id}/delete`, {});
        await load();
      } catch (error) {
        alert(error.message);
      }
    }
  };
  window.filterPostCategories = () => {
    clearTimeout(timer);
    timer = setTimeout(load, 250);
  };
  window.loadPostCategories = load;
  window.addEventListener('sornaz:data-mutated', event => {
    if (/\/analytics\/admin-post(?:s|s-categories)(?:[/?]|$)/.test(event.detail?.url || '')) { cache.clear(); generation++; }
  });
  window.addEventListener('sornaz:remote-mutation', event => {
    if (!/\/analytics\/admin-post(?:s|s-categories)(?:[/?]|$)/.test(event.detail?.url || '')) return;
    cache.clear(); generation++;
  });
})();
