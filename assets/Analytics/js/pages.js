(function () {
  'use strict';
  let pages = [];
  const esc = (v) =>
    String(v ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  async function load() {
    try {
      const r = await fetch('/analytics/admin-site-pages', {
          headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        }),
        p = await r.json(),
        b = p.data ?? p;
      if (!r.ok || b.success === false) throw new Error(b.message || 'دریافت برگه‌ها ناموفق بود.');
      pages = (b.data ?? b).pages || [];
      render();
    } catch (e) {
      alert(e.message);
    }
  }
  function render() {
    const q = (document.getElementById('sitePageSearch')?.value || '').trim().toLowerCase(),
      list = pages.filter(
        (p) => !q || p.title.toLowerCase().includes(q) || p.route.toLowerCase().includes(q)
      ),
      body = document.querySelector('#adminPagesTable tbody');
    body.innerHTML = list.length
      ? list
          .map(
            (p) =>
              window.AdminPanelTable.rowHTML([{ html: `<strong>${esc(p.title)}</strong><div class="mt-1 font-mono text-xs text-gray-400" dir="ltr">${esc(p.route)}</div>`, attributes: ` class="p-5"` }, { html: `<span class="rounded-lg bg-indigo-50 px-2 py-1 text-indigo-700">${p.texts}</span>`, attributes: ` class="p-5"` }, { html: `<span class="rounded-lg bg-emerald-50 px-2 py-1 text-emerald-700">${p.images}</span>`, attributes: ` class="p-5"` }, { html: `<span class="rounded-lg bg-violet-50 px-2 py-1 text-violet-700">${p.icons}</span>`, attributes: ` class="p-5"` }, { html: `<span class="rounded-full bg-green-100 px-3 py-1 text-xs text-green-700">فعال</span>`, attributes: ` class="p-5"` }, { html: `<a href="${esc(p.route)}" target="_blank" class="ml-3 text-gray-500">مشاهده</a><a href="${editorUrl(p)}" target="_blank" class="rounded-xl bg-indigo-600 px-4 py-2 text-white">ویرایش محتوا</a>`, attributes: ` class="p-5 text-left whitespace-nowrap"` }], ` class="hover:bg-gray-50"`)
          )
          .join('')
      : window.AdminPanelTable.rowHTML([{ html: "برگه‌ای یافت نشد", attributes: " colspan=\"6\" class=\"p-12 text-center text-gray-400\"" }], "");
    document.getElementById('sitePagesCount').textContent = `${list.length} برگه`;
  }
  function editorUrl(p) {
    const separator = p.route.includes('?') ? '&' : '?';
    return p.route + separator + 'cms=1&cms_page=' + encodeURIComponent(p.id);
  }
  window.filterSitePages = render;
  setTimeout(() => {
    if (document.getElementById('adminPagesTable')) load();
  }, 200);
})();
