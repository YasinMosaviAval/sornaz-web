(function () {
  'use strict';
  const L = {
    type: { general: 'عمومی', professional: 'حرفه‌ای' },
    category: {
      attendance: 'حضور',
      academic: 'آموزشی',
      event: 'رویداد',
      social: 'اجتماعی',
      financial: 'مالی',
      profile: 'پروفایل',
      achievement: 'دستاورد',
      engagement: 'تعامل',
      management: 'مدیریت',
    },
    source: { database: 'رویداد قطعی دیتابیس', tracking: 'رفتار کاربر', manual: 'اعطای دستی' },
    repeat: { event: 'هر رویداد یکتا', daily: 'دارای سقف روزانه', once: 'فقط یک‌بار' },
  };
  window.pointTypeLabels = L.type;
  window.pointCategoryLabels = L.category;
  let rows = [],
    filtered = [],
    orgs = [],
    page = 1,
    branch = 'all',
    sort = 'id',
    dir = 'desc',
    canManage = false;
  const per = 10,
    esc = (v) =>
      String(v ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;'),
    enc = (d) =>
      btoa(unescape(encodeURIComponent(JSON.stringify(d))))
        .replace(/\+/g, '-')
        .replace(/\//g, '_')
        .replace(/=+$/, '');
  async function api(url, data = null) {
    const o = {
      method: data === null ? 'GET' : 'POST',
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    };
    if (data !== null) {
      o.headers['Content-Type'] = 'application/x-www-form-urlencoded';
      o.headers['X-CSRF-TOKEN'] = window.adminCsrfToken || '';
      o.body = new URLSearchParams({ _token: window.adminCsrfToken || '', payload_b64: enc(data) });
    }
    const r = await fetch(url, o),
      j = await r.json(),
      b = j.data ?? j;
    if (!r.ok || b.success === false) throw new Error(b.message || 'عملیات امتیاز ناموفق بود.');
    return b.data ?? b;
  }
  async function load(silent = false) {
    const oldPage = page;
    const previous = silent ? JSON.stringify([rows, orgs, canManage]) : '';
    try {
      const d = await api('/analytics/admin-points');
      rows = d.rules || [];
      orgs = d.organizations || [];
      window.syncPanelOrganizationSelect?.(document.getElementById('pointRuleForm')?.elements.namedItem('branchId'), orgs);
      canManage = Boolean(d.canManage);
      const general = Number(d.balance?.general || 0),
        professional = Number(d.balance?.professional || 0),
        specialized = Number(d.balance?.specialized || 0);
      document.getElementById('pointGeneralBalance').textContent = general.toLocaleString('fa-IR');
      document.getElementById('pointProfessionalBalance').textContent =
        professional.toLocaleString('fa-IR');
      const specializedCard = document.getElementById('pointSpecializedBalance');
      if (specializedCard) specializedCard.textContent = specialized.toLocaleString('fa-IR');
      window.renderPersonalPointsChart?.(d.balance || {});
      document.getElementById('pointActiveRules').textContent = rows
        .filter((x) => x.status === 'active')
        .length.toLocaleString('fa-IR');
      if (document.getElementById('headerPointBalance'))
        document.getElementById('headerPointBalance').textContent = (
          general + professional + specialized
        ).toLocaleString('fa-IR');
      if (silent && previous === JSON.stringify([rows, orgs, canManage])) return;
      tabs();
      filter(!silent, !silent);
      if (silent) {
        page = Math.min(oldPage, Math.max(1, Math.ceil(filtered.length / per)));
        render();
      }
    } catch (e) {
      if (!silent) alert(e.message);
    }
  }
  function tabs() {
    window.renderPanelOrganizationTabs?.('pointsBranchTabs', branch, window.filterPointsByBranch, {
      tabClass: 'point-branch-tab', branches: () => orgs.filter((organization) => organization.id), showAcademy: false,
    });
  }
  window.filterPointsByBranch = (v) => {
    branch = v;
    document.querySelectorAll('.point-branch-tab').forEach((b) => {
      const a = String(b.dataset.value) === String(v);
      b.classList.toggle('bg-indigo-600', a);
      b.classList.toggle('text-white', a);
    });
    filter();
  };
  function filter(reset = true, paint = true) {
    const q = (document.getElementById('pointSearch')?.value || '').toLowerCase(),
      t = document.getElementById('filterPointType')?.value || '',
      c = document.getElementById('filterPointCategory')?.value || '',
      s = document.getElementById('filterPointStatus')?.value || '';
    filtered = rows.filter(
      (x) =>
        (branch === 'all' || String(x.branchId) === String(branch)) &&
        (!t || x.type === t) &&
        (!c || x.category === c) &&
        (!s || (s === 'فعال' ? 'active' : 'inactive') === x.status) &&
        (!q ||
          [x.title, x.action, x.summary, x.description].some((v) =>
            String(v || '')
              .toLowerCase()
              .includes(q)
          ))
    );
    filtered.sort((a, b) => {
      let x = a[sort] ?? '',
        y = b[sort] ?? '';
      if (sort === 'type') ((x = L.type[x] || x), (y = L.type[y] || y));
      if (sort === 'category') ((x = L.category[x] || x), (y = L.category[y] || y));
      if (sort === 'source') ((x = L.source[x] || x), (y = L.source[y] || y));
      if (sort === 'points' || sort === 'id') ((x = Number(x)), (y = Number(y)));
      return (
        String(x).localeCompare(String(y), 'fa', { numeric: true, sensitivity: 'base' }) *
        (dir === 'asc' ? 1 : -1)
      );
    });
    if (reset) page = 1;
    if (paint) render();
  }
  window.filterPoints = () => filter(true);
  function sortIcons() {
    ['title', 'type', 'category', 'points', 'source', 'branchName', 'status'].forEach((f) => {
      const e = document.getElementById('pointSortIcon-' + f);
      if (e) e.textContent = sort === f ? (dir === 'asc' ? '↑' : '↓') : '↕';
    });
  }
  window.sortPointsBy = (f) => {
    dir = sort === f && dir === 'asc' ? 'desc' : 'asc';
    sort = f;
    filter(false);
  };
  window.changePointsPage = (p) => {
    const m = Math.max(1, Math.ceil(filtered.length / per));
    if (p >= 1 && p <= m) {
      page = p;
      render();
    }
  };
  const pointRowHTML = x => window.AdminPanelTable.rowHTML([{ html: `<b>${esc(x.title)}</b><div class="text-xs text-gray-400">${esc(x.summary || '')}</div>`, attributes: ` class="p-5"` }, { html: `<span class="rounded-full px-3 py-1 text-xs ${x.type === 'professional' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'}">${L.type[x.type]}</span>`, attributes: ` class="p-5"` }, { html: `${esc(L.category[x.category] || x.category)}`, attributes: ` class="p-5"` }, { html: `+${Number(x.points).toLocaleString('fa-IR')}`, attributes: ` class="p-5 font-bold text-emerald-600"` }, ...(canManage ? [{ html: `<div>${esc(L.source[x.source])}</div><code class="text-xs text-gray-400" dir="ltr">${esc(x.action)}</code>`, attributes: ` class="p-5"` }, { html: `${esc(x.branchName || 'سراسری')}`, attributes: ` class="p-5"` }] : []), { html: `<div class="text-xs">${esc(L.repeat[x.repeatMode])}${x.dailyCap ? ' · سقف ' + x.dailyCap : ''}${x.cooldownMinutes ? ' · فاصله ' + x.cooldownMinutes + ' دقیقه' : ''}</div>`, attributes: ` class="p-5"` }, ...(canManage ? [{ html: `<button type="button" aria-pressed="${x.status === 'active'}" onclick="togglePointStatus(${x.id}, this)" class="rounded-full px-3 py-1 text-xs ${x.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${x.status === 'active' ? 'فعال' : 'غیرفعال'}</button>`, attributes: ` class="p-5 whitespace-nowrap"` }] : []), { html: `${canManage ? `<button onclick="editPoint(${x.id})" class="ml-3 text-indigo-600">ویرایش</button><button onclick="deletePoint(${x.id})" class="text-red-500">حذف</button>` : ''}`, attributes: ` class="p-5 whitespace-nowrap"` }], ` class="border-b hover:bg-gray-50"`);
  function render() {
    const body = document.querySelector('#pointsTable tbody'),
      start = (page - 1) * per,
      items = window.AdminPanelTable.page?.('#pointsTable', filtered, start, start + per, render, undefined, row => pointRowHTML(row)) ?? filtered.slice(start, start + per);
    if (!body) return;
    body.innerHTML = items.length
      ? items
          .map(
            (x) =>
              pointRowHTML(x)
          )
          .join('')
      : window.AdminPanelTable.rowHTML([{ html: "قانونی یافت نشد", attributes: ` colspan="${canManage ? 9 : 6}" class="p-12 text-center text-gray-400"` }], "");
    const pages = Math.max(1, Math.ceil(filtered.length / per));
    document.getElementById('pointsPaginationInfo').textContent =
      `نمایش ${filtered.length ? start + 1 : 0} تا ${Math.min(start + per, filtered.length)} از ${filtered.length} قانون`;
    let numbers = '',
      from = Math.max(1, page - 2),
      to = Math.min(pages, from + 4);
    from = Math.max(1, to - 4);
    for (let i = from; i <= to; i++)
      numbers += `<button onclick="changePointsPage(${i})" class="rounded-lg px-3 py-2 ${i === page ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50'}">${i.toLocaleString('fa-IR')}</button>`;
    document.getElementById('pointsPaginationButtons').innerHTML =
      `<button onclick="changePointsPage(1)" ${page === 1 ? 'disabled' : ''} class="border rounded-lg px-3 py-2 disabled:opacity-40">اول</button><button onclick="changePointsPage(${page - 1})" ${page === 1 ? 'disabled' : ''} class="border rounded-lg px-3 py-2 disabled:opacity-40">قبلی</button>${numbers}<button onclick="changePointsPage(${page + 1})" ${page === pages ? 'disabled' : ''} class="border rounded-lg px-3 py-2 disabled:opacity-40">بعدی</button><button onclick="changePointsPage(${pages})" ${page === pages ? 'disabled' : ''} class="border rounded-lg px-3 py-2 disabled:opacity-40">آخر</button>`;
    sortIcons();
  }
  const options = (obj, val) =>
    Object.entries(obj)
      .map(([v, l]) => `<option value="${v}" ${v === val ? 'selected' : ''}>${l}</option>`)
      .join('');
  function form(x = {}) {
    return `<form id="pointRuleForm" class="grid gap-4 md:grid-cols-2"><label>عنوان<input name="title" value="${esc(x.title || '')}" class="mt-2 w-full rounded-2xl border p-3" required></label><label>امتیاز<input name="points" type="number" min="1" max="100000" value="${x.points || 10}" class="mt-2 w-full rounded-2xl border p-3" required></label><label>نوع امتیاز<select name="type" class="mt-2 w-full rounded-2xl border p-3">${options(L.type, x.type || 'general')}</select></label><label>دسته<select name="category" class="mt-2 w-full rounded-2xl border p-3">${options(L.category, x.category || 'engagement')}</select></label><label>منبع رویداد<select name="source" class="mt-2 w-full rounded-2xl border p-3">${options(L.source, x.source || 'database')}</select></label><label>کد عملیات<input name="action" dir="ltr" value="${esc(x.action || '')}" placeholder="table.insert یا form_submit" class="mt-2 w-full rounded-2xl border p-3" required></label><label>روش تکرار<select name="repeatMode" class="mt-2 w-full rounded-2xl border p-3">${options(L.repeat, x.repeatMode || 'event')}</select></label><label>سقف روزانه (۰ = بدون سقف)<input name="dailyCap" type="number" min="0" value="${x.dailyCap || 0}" class="mt-2 w-full rounded-2xl border p-3"></label><label>فاصله تکرار (دقیقه)<input name="cooldownMinutes" type="number" min="0" value="${x.cooldownMinutes || 0}" class="mt-2 w-full rounded-2xl border p-3"></label><label>سازمان<select name="branchId" class="mt-2 w-full rounded-2xl border p-3">${orgs.map((o) => `<option value="${o.id}" data-academy="${o.academyId}" ${Number(x.branchId || 0) === o.id ? 'selected' : ''}>${esc(o.name)}</option>`).join('')}</select></label><label>وضعیت<select name="status" class="mt-2 w-full rounded-2xl border p-3"><option value="active" ${x.status !== 'inactive' ? 'selected' : ''}>فعال</option><option value="inactive" ${x.status === 'inactive' ? 'selected' : ''}>غیرفعال</option></select></label><label class="md:col-span-2">خلاصه<input name="summary" value="${esc(x.summary || '')}" class="mt-2 w-full rounded-2xl border p-3"></label><label class="md:col-span-2">توضیحات<textarea name="description" class="mt-2 w-full rounded-2xl border p-3">${esc(x.description || '')}</textarea></label></form>`;
  }
  function modal(x) {
    document.getElementById('modalContainer').innerHTML =
      `<div id="pointModal" class="fixed inset-0 z-50 overflow-auto bg-black/60 p-4"><div class="mx-auto my-8 max-w-4xl rounded-3xl bg-white"><header class="flex justify-between border-b p-6"><h2 class="text-2xl font-bold">${x ? 'ویرایش' : 'افزودن'} قانون امتیاز</h2><button onclick="pointModal.remove()" class="text-3xl">×</button></header><div class="p-6">${form(x)}<div class="mt-6 flex gap-3"><button onclick="savePointRule(${x?.id || 'null'})" class="flex-1 rounded-2xl bg-indigo-600 p-3 text-white">ذخیره در دیتابیس</button><button onclick="pointModal.remove()" class="flex-1 rounded-2xl border p-3">انصراف</button></div></div></div></div>`;
  }
  window.openAddPointModal = () => modal();
  window.editPoint = (id) => modal(rows.find((x) => x.id === id));
  window.savePointRule = async (id) => {
    const f = document.getElementById('pointRuleForm'),
      d = Object.fromEntries(new FormData(f)),
      o = f.branchId.selectedOptions[0];
    d.points = +d.points;
    d.dailyCap = +d.dailyCap;
    d.cooldownMinutes = +d.cooldownMinutes;
    d.branchId = +d.branchId;
    d.academyId = +o.dataset.academy || 0;
    try {
      await api(id ? `/analytics/admin-points/${id}/update` : '/analytics/admin-points', d);
      pointModal.remove();
      await load();
    } catch (e) {
      alert(e.message);
    }
  };
  const updatingPointStatus = new Set();
  window.togglePointStatus = async (id, button) => {
    const x = rows.find((r) => r.id === id);
    if (!canManage || !x || updatingPointStatus.has(id)) return;
    updatingPointStatus.add(id);
    if (button) button.disabled = true;
    try {
      await api('/analytics/admin-points/' + id + '/update', { ...x, status: x.status === 'active' ? 'inactive' : 'active' });
      await load(true);
    } catch (error) {
      alert(error.message);
    } finally {
      updatingPointStatus.delete(id);
      if (button?.isConnected) button.disabled = false;
    }
  };
  window.deletePoint = async (id) => {
    if (window.AppDialog && !(await AppDialog.confirm('این قانون امتیاز حذف شود؟'))) return;
    await api(`/analytics/admin-points/${id}/delete`, {});
    load();
  };
  window.showPointTab = (selected) => {
    if (!['personal', 'rules'].includes(selected)) return;
    document.querySelectorAll('#points [data-point-panel]').forEach(panel => panel.classList.toggle('hidden', panel.dataset.pointPanel !== selected));
    document.querySelectorAll('#points [data-point-tab]').forEach(button => {
      const active = button.dataset.pointTab === selected;
      if (active) button.setAttribute('aria-current', 'page'); else button.removeAttribute('aria-current');
      ['bg-indigo-600','text-white'].forEach(name => button.classList.toggle(name, active));
      ['bg-white','text-gray-700','hover:bg-indigo-50','dark:bg-slate-800','dark:text-white'].forEach(name => button.classList.toggle(name, !active));
    });
  };
  setTimeout(() => document.getElementById('pointsTable') && load(), 250);
  setInterval(
    () =>
      document.visibilityState === 'visible' &&
      document.getElementById('points') &&
      !document.getElementById('points').classList.contains('hidden') &&
      load(true),
    12000
  );
  window.addEventListener('panel-organizations-changed', () => { Promise.resolve(load(true)).catch(console.error); });
})();
