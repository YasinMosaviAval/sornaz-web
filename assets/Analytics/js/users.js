(function () {
  'use strict';
  let users = [],
    roles = [],
    permissions = [],
    branches = [],
    total = 0,
    page = 1,
    perPage = 10,
    timer;
  let loadSequence = 0;
  let organizationFilter = 'all';
  window.renderAccessUserBranchTabs = () =>
    window.renderPanelOrganizationTabs?.('accessUsersBranchTabs', organizationFilter, window.filterAccessUsersByBranch);
  window.filterAccessUsersByBranch = (value) => {
    organizationFilter = String(value);
    const select = document.getElementById('accessUserBranch');
    if (select) select.value = value === 'all' || value === 'academy' ? '' : value;
    window.renderAccessUserBranchTabs();
    window.loadAccessUsers(1);
  };
  const esc = (v) =>
    String(v ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  const enc = (d) =>
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
      p = await r.json(),
      b = p.data ?? p;
    if (!r.ok || b.success === false) throw new Error(b.message || 'عملیات کاربران ناموفق بود.');
    return b.data ?? b;
  }
  const statusLabel = {
    pending: 'در انتظار',
    approved: 'تأییدشده',
    rejected: 'ردشده',
    inactive: 'غیرفعال',
    banned: 'مسدود',
  };
  window.loadAccessUsers = async (next = 1) => {
    const sequence = ++loadSequence;
    page = next;
    const p = new URLSearchParams({ page, perPage }),
      q = document.getElementById('accessUserSearch')?.value.trim(),
      s = document.getElementById('accessUserStatus')?.value,
      b = document.getElementById('accessUserBranch')?.value;
    if (q) p.set('search', q);
    if (s) p.set('status', s);
    if (b) p.set('branchId', b);
    const table = document.getElementById('accessUsersTable') || document.getElementById('accessUsersBody')?.closest('table');
    p.set('sortDirection', table?.dataset.adminIdentityDirection || 'desc');
    if (organizationFilter === 'academy') p.set('organization', 'academy');
    try {
      const d = await api('/analytics/admin-user-access?' + p);
      if (sequence !== loadSequence) return;
      users = d.users || [];
      roles = d.roles || [];
      permissions = d.permissions || [];
      branches = d.branches || [];
      total = Number(d.total || 0);
      page = Number(d.page || 1);
      renderBranchFilter();
      window.renderAccessUserBranchTabs();
      render();
    } catch (e) {
      if (sequence === loadSequence) alert(e.message);
    }
  };
  function renderBranchFilter() {
    const select = document.getElementById('accessUserBranch');
    if (!select) return;
    const selected = select.value;
    select.innerHTML =
      '<option value="">همه شعبه‌ها</option>' +
      branches
        .map((b) => `<option value="${b.id}">${esc(b.academyName)} — ${esc(b.name)}</option>`)
        .join('');
    select.value = selected;
  }
  function badges(list, kind) {
    if (!list.length) return '<span class="text-gray-400">—</span>';
    return (
      list
        .slice(0, 3)
        .map(
          (x) =>
            `<span class="inline-flex rounded-full px-2 py-1 text-xs ${kind === 'role' ? 'text-white' : 'bg-indigo-50 text-indigo-700'}" ${kind === 'role' ? `style="background:${esc(x.color)}"` : ''}>${esc(x.title)}</span>`
        )
        .join(' ') +
      (list.length > 3 ? ` <small class="text-gray-400">+${list.length - 3}</small>` : '')
    );
  }
  function render() {
    const tableRows = users.length
      ? users
          .map(
            (u) =>
              ({ cells: [{ html: `<strong>${esc(u.name)}</strong>`, attributes: ` class="p-5"` }, { html: `${badges(u.roles, 'role')}`, attributes: ` class="p-5 space-x-1 space-x-reverse"` }, { html: `${badges(u.permissions, 'permission')}`, attributes: ` class="p-5 space-x-1 space-x-reverse"` }, { html: `${esc(statusLabel[u.status] || u.status || '—')}`, attributes: ` class="p-5"` }, { html: `<div class="flex flex-wrap gap-2">${u.canEdit === false ? '' : `<button type="button" onclick="openUserAccess(${u.id}, 'roles')" class="rounded-xl border px-4 py-2 text-indigo-600">تعیین نقش‌ها</button><button type="button" onclick="openUserAccess(${u.id}, 'permissions')" class="rounded-xl border px-4 py-2 text-indigo-600">تعیین دسترسی‌ها</button>`}<button type="button" onclick="viewUserAccess(${u.id})" class="rounded-xl border px-4 py-2 text-indigo-600">مشاهده دسترسی‌ها</button></div>`, attributes: ` class="p-5 text-left"` }], attributes: `data-id="${u.id}"` })
          )
      : [{ cells: [{ html: "کاربری یافت نشد", attributes: " colspan=\"5\" class=\"p-12 text-center text-gray-400\"" }] }];
    const table = document.getElementById('accessUsersTable') || document.getElementById('accessUsersBody')?.closest('table');
    if (window.AdminPanelTable.setRows) {
      window.AdminPanelTable.setRows(table, tableRows, { onIdentitySort: () => loadAccessUsers(1), pageOffset: (page - 1) * perPage });
    } else {
      document.getElementById('accessUsersBody').innerHTML = tableRows.map(row => window.AdminPanelTable.rowHTML(row.cells, row.attributes)).join('');
    }
    const footer = table?.closest('.admin-ui-table')?.querySelector('[data-panel-pagination]') || document.getElementById('accessUsersPagination');
    window.AdminPanelTable.renderPagination?.(footer, { total, perPage, page, onChange: next => loadAccessUsers(next) });
  }
  window.searchAccessUsers = () => {
    clearTimeout(timer);
    timer = setTimeout(() => loadAccessUsers(1), 250);
  };
  window.changeAccessUsersPage = (p) => {
    if (p >= 1 && p <= Math.max(1, Math.ceil(total / perPage))) loadAccessUsers(p);
  };
  window.changeAccessUsersPerPage = (v) => {
    perPage = [10, 20, 30, 50, 100].includes(Number(v)) ? Number(v) : 10;
    loadAccessUsers(1);
  };
  function choiceCards(list, selected) {
    return list.length
      ? `<div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">${list.map((x) => `<button type="button" data-access-choice="${x.id}" aria-pressed="${selected.includes(x.id)}" class="access-choice rounded-xl border p-3 text-right transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">${esc(x.title)}</button>`).join('')}</div>`
      : '<p class="rounded-xl p-4 text-gray-500">موردی برای انتخاب وجود ندارد.</p>';
  }
  window.openUserAccess = (id, kind) => {
    const u = users.find((x) => x.id === id);
    if (!u || u.canEdit === false || !['roles', 'permissions'].includes(kind)) return;
    const isRole = kind === 'roles';
    const title = isRole ? 'نقش‌ها' : 'دسترسی‌ها';
    const inheritedIds = new Set(u.rolePermissionIds || []);
    const list = isRole ? roles : permissions.filter((permission) => !inheritedIds.has(permission.id));
    const selected = (isRole ? u.roles : u.permissions).map((x) => x.id);
    const html = `<div id="userAccessModal" role="dialog" aria-modal="true" aria-labelledby="userAccessModalTitle" class="fixed inset-0 z-50 overflow-auto bg-black/60 p-4 md:p-8" onclick="if(event.target===this)this.remove()"><div class="mx-auto max-w-5xl rounded-3xl bg-white p-6 dark:bg-slate-900"><div class="mb-5 flex justify-between gap-4"><h2 id="userAccessModalTitle" class="text-2xl font-bold">تعیین ${title}</h2><button type="button" onclick="document.getElementById('userAccessModal').remove()" aria-label="بستن" class="text-3xl">×</button></div><div id="userAccessChoices">${choiceCards(list, selected)}</div><div class="mt-6 flex gap-3"><button type="button" data-access-save onclick="saveUserAccess(${id}, '${kind}')" class="rounded-xl bg-indigo-600 px-6 py-3 text-white disabled:opacity-50">ذخیره ${title}</button><button type="button" onclick="document.getElementById('userAccessModal').remove()" class="rounded-xl border px-6 py-3">انصراف</button></div></div></div>`;
    document.getElementById('modalContainer').innerHTML = html;
    document.getElementById('userAccessChoices').addEventListener('click', (event) => {
      const card = event.target.closest('[data-access-choice]');
      if (card) card.setAttribute('aria-pressed', String(card.getAttribute('aria-pressed') !== 'true'));
    });
  };
  window.viewUserAccess = (id) => {
    const u = users.find((x) => x.id === id);
    if (!u) return;
    const inheritedIds = new Set(u.rolePermissionIds || []);
    const inherited = u.rolePermissions || [];
    const direct = u.permissions.filter((permission) => !inheritedIds.has(permission.id));
    const cards = (items) => items.length
      ? `<div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">${items.map((item) => `<div class="rounded-xl border p-3">${esc(item.title)}</div>`).join('')}</div>`
      : '<p class="text-sm text-gray-500">موردی ثبت نشده است.</p>';
    document.getElementById('modalContainer').innerHTML = `<div id="userAccessModal" role="dialog" aria-modal="true" aria-labelledby="userAccessModalTitle" class="fixed inset-0 z-50 overflow-auto bg-black/60 p-4 md:p-8" onclick="if(event.target===this)this.remove()"><div class="mx-auto max-w-5xl rounded-3xl bg-white p-6 dark:bg-slate-900"><div class="mb-6 flex justify-between"><h2 id="userAccessModalTitle" class="text-2xl font-bold">مشاهده دسترسی‌ها</h2><button type="button" onclick="document.getElementById('userAccessModal').remove()" aria-label="بستن" class="text-3xl">×</button></div><section class="mb-6"><h3 class="mb-3 font-semibold">دسترسی‌های نقش‌ها</h3>${cards(inherited)}</section><section><h3 class="mb-3 font-semibold">دسترسی‌های مستقل از نقش</h3>${cards(direct)}</section><div class="mt-6"><button type="button" onclick="document.getElementById('userAccessModal').remove()" class="rounded-xl border px-6 py-3">بستن</button></div></div></div>`;
  };
  window.saveUserAccess = async (id, kind) => {
    const modal = document.getElementById('userAccessModal');
    if (!modal || !['roles', 'permissions'].includes(kind)) return;
    const selected = [...modal.querySelectorAll('[data-access-choice][aria-pressed="true"]')].map((x) => Number(x.dataset.accessChoice));
    const button = modal.querySelector('[data-access-save]');
    button.disabled = true;
    try {
      const user = users.find((item) => item.id === id);
      const covered = new Set(user?.rolePermissionIds || []);
      const retained = (user?.permissions || []).filter((permission) => covered.has(permission.id)).map((permission) => permission.id);
      await api('/analytics/admin-user-access/' + id, kind === 'roles' ? { roleIds: selected } : { permissionIds: [...selected, ...retained] });
      modal.remove();
      await loadAccessUsers(page);
    } catch (e) {
      alert(e.message);
      button.disabled = false;
    }
  };
  window.addEventListener('staff:data-updated', () => {
    if (document.getElementById('accessUsersBody')) loadAccessUsers(1);
  });
  setTimeout(() => {
    if (document.getElementById('accessUsersBody')) loadAccessUsers(1);
  }, 300);
})();
