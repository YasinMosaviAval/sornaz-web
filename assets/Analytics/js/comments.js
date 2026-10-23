(function () {
  'use strict';
  let rows = [],
    total = 0,
    page = 1,
    perPage = 10,
    status = 'all',
    timer = null;
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
    if (!r.ok || b.success === false) throw new Error(b.message || 'عملیات دیدگاه ناموفق بود.');
    return b.data ?? b;
  }
  let commentsView = 'table';
  window.setCommentsView = view => {
    commentsView = view === 'cards' ? 'cards' : 'table';
    document.getElementById('commentsCards')?.classList.toggle('hidden', commentsView !== 'cards');
    document.getElementById('commentsTableWrap')?.classList.toggle('hidden', commentsView !== 'table');
    for (const [id, mode] of [['commentsTableViewButton', 'table'], ['commentsCardViewButton', 'cards']]) {
      const button = document.getElementById(id); if (!button) continue;
      const active = commentsView === mode;
      for (const name of ['border-indigo-600', 'bg-indigo-600', 'text-white']) button.classList.toggle(name, active);
      for (const name of ['border-gray-200', 'bg-white', 'text-gray-700']) button.classList.toggle(name, !active);
      button.setAttribute('aria-pressed', String(active));
    }
  };
  function params() {
    const p = new URLSearchParams({ page, perPage, includePostOptions: '0' });
    const q = document.getElementById('commentSearch')?.value.trim();
    if (q) p.set('search', q);
    if (status !== 'all') p.set('status', status);
    return p;
  }
  let requestVersion = 0;
  async function load(next = 1) {
    const version = ++requestVersion;
    page = next;
    try {
      const d = await api('/analytics/admin-comments?' + params());
      if (version !== requestVersion) return;
      rows = d.comments || [];
      total = Number(d.total || 0);
      page = Number(d.page || 1);
      render(d.statusCounts || {}, d.posts || []);
    } catch (e) {
      alert(e.message);
    }
  }
  function render(counts, posts) {
    const labels = { pending: 'در انتظار', approved: 'تأییدشده', rejected: 'ردشده' },
      colors = {
        pending: 'bg-yellow-100 text-yellow-700',
        approved: 'bg-green-100 text-green-700',
        rejected: 'bg-red-100 text-red-700',
      },
      body = document.querySelector('#adminCommentsTable tbody');
    const cellsFor = x => [{ html: `<strong>${esc(x.author)}</strong><div class="text-xs text-gray-400">${esc(x.email)}</div>${x.ip ? `<div class="text-xs text-gray-300">${esc(x.ip)}</div>` : ''}`, attributes: ` class="p-5"` }, { html: `${esc(x.content)}${x.parentId ? `<div class="text-xs text-gray-400">پاسخ به دیدگاه #${x.parentId}</div>` : ''}`, attributes: ` class="max-w-md p-5 leading-6"` }, { html: `${esc(x.postTitle)}`, attributes: ` class="p-5"` }, { html: `<button type="button" onclick="cycleCommentStatus(${Number(x.id)},this)" class="rounded-full px-3 py-1 text-xs ${colors[x.status]}">${labels[x.status]}</button>`, attributes: ` class="p-5"` }, { html: `${esc(x.createdAt)}`, attributes: ` class="p-5 text-xs"` }, { html: `${x.status !== 'approved' ? `<button onclick="setCommentStatus(${x.id},'approved')" class="ml-2 text-green-600">تأیید</button>` : ''}${x.status !== 'rejected' ? `<button onclick="setCommentStatus(${x.id},'rejected')" class="ml-2 text-orange-600">رد</button>` : ''}<button onclick="editComment(${x.id})" class="ml-2 text-indigo-600">ویرایش</button><button onclick="replyComment(${x.id})" class="ml-2 text-blue-600">پاسخ</button><button onclick="deleteComment(${x.id})" class="text-red-500">حذف</button>`, attributes: ` class="p-5 text-left whitespace-nowrap"` }];
    window.AdminPanelTable.setRows(body.closest('table'), rows.length ? rows.map(x => ({ cells: cellsFor(x), attributes: 'data-id="' + Number(x.id) + '"' })) : [{ cells: [{ value: document.documentElement.lang.startsWith('en') ? 'No comments found' : 'دیدگاهی یافت نشد', attributes: 'colspan="6" class="p-12 text-center text-gray-400"' }] }]);
    document.getElementById('commentsCards').innerHTML = rows.map(x => '<article class="rounded-3xl bg-white p-6 shadow-sm space-y-4"><div class="flex items-center justify-between gap-3"><strong>' + esc(x.author) + '</strong><span class="rounded-full px-3 py-1 text-xs ' + colors[x.status] + '">' + esc(labels[x.status]) + '</span></div><p class="text-sm text-gray-500">' + esc(x.postTitle) + '</p><p class="whitespace-pre-wrap break-words leading-7">' + esc(x.content) + '</p><p class="text-xs text-gray-500">' + esc(x.createdAt) + '</p><div class="flex flex-wrap gap-2">' + cellsFor(x).at(-1).html + '</div></article>').join('');
    if (!rows.length) document.getElementById('commentsCards').textContent = document.documentElement.lang.startsWith('en') ? 'No comments found' : 'دیدگاهی یافت نشد';
    document.querySelectorAll('[data-comment-status-count]').forEach((el) => {
      el.textContent =
        el.dataset.commentStatusCount === 'all'
          ? Object.values(counts).reduce((s, n) => s + Number(n), 0)
          : Number(counts[el.dataset.commentStatusCount] || 0);
    });
    window.AdminPanelTable.renderPagination(document.getElementById('commentsPagination'), { total, perPage: 10, page, onChange: next => load(next) });
  }
  window.filterAdminComments = () => {
    clearTimeout(timer);
    timer = setTimeout(() => load(1), 250);
  };
  window.filterCommentsByStatus = (s) => {
    status = s;
    document.querySelectorAll('.comment-status-tab').forEach((b) => {
      const a = b.dataset.commentStatus === s;
      b.classList.remove('bg-gray-900');
      b.classList.toggle('bg-indigo-600', a);
      b.setAttribute('aria-pressed', String(a));
      b.classList.toggle('text-white', a);
      b.classList.toggle('border', !a);
    });
    load(1);
  };
  window.changeCommentsPerPage = () => load(1);
  window.changeCommentsPage = (p) => {
    if (p >= 1 && p <= Math.max(1, Math.ceil(total / perPage))) load(p);
  };
  window.cycleCommentStatus = async (id, button) => {
    if (button?.disabled) return;
    const row = rows.find(item => Number(item.id) === Number(id));
    if (!row) return;
    const statuses = ['pending', 'approved', 'rejected'];
    if (button) button.disabled = true;
    try { await window.setCommentStatus(id, statuses[(statuses.indexOf(row.status) + 1) % statuses.length]); }
    finally { if (button?.isConnected) button.disabled = false; }
  };
  window.setCommentStatus = async (id, s) => {
    const x = rows.find((r) => r.id === id);
    try {
      await api(`/analytics/admin-comments/${id}/update`, { content: x.content, status: s });
      await load(page);
    } catch (e) {
      alert(e.message);
    }
  };
  window.editComment = async (id) => {
    const x = rows.find((r) => r.id === id),
      content = await AppDialog.prompt('ویرایش دیدگاه:', x.content);
    if (content === null) return;
    try {
      await api(`/analytics/admin-comments/${id}/update`, { content, status: x.status });
      load(page);
    } catch (e) {
      alert(e.message);
    }
  };
  window.replyComment = async (id) => {
    const content = await AppDialog.prompt('متن پاسخ:', '');
    if (!content) return;
    try {
      await api(`/analytics/admin-comments/${id}/reply`, { content });
      load(page);
    } catch (e) {
      alert(e.message);
    }
  };
  window.deleteComment = async (id) => {
    if (await AppDialog.confirm('این دیدگاه به‌صورت نرم حذف شود؟')) {
      try {
        await api(`/analytics/admin-comments/${id}/delete`, {});
        load(page);
      } catch (e) {
        alert(e.message);
      }
    }
  };
  window.loadAdminComments = () => load(page);
  window.setCommentsView(commentsView);
  setTimeout(() => {
    if (document.querySelector('#comments:not(.hidden) #adminCommentsTable')) load(1);
  }, 300);
})();
