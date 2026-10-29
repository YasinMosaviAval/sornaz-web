(function () {
  'use strict';
  const t = (fa, en) => document.documentElement.lang.startsWith('en') ? en : fa;
  const escape = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
  const validId = value => /^[1-9][0-9]*$/.test(String(value));
  let active = null;

  window.sessionFilesButtonHTML = id => validId(id) ? `<button type="button" data-session-files="${Number(id)}" data-panel-tooltip="${t('فایل‌های جلسه','Session files')}" aria-label="${t('فایل‌های جلسه','Session files')}" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800"><i class="fas fa-paperclip" aria-hidden="true"></i></button>` : '';

  function closeFiles() {
    if (!active) return;
    const previous = active;
    active = null;
    previous.controller?.abort();
    previous.root.remove();
    if (previous.trigger?.isConnected) previous.trigger.focus({preventScroll:true});
  }

  function message(state, text, denied = false) {
    if (active !== state) return;
    state.status.textContent = text;
    state.status.className = 'text-sm text-red-600 dark:text-red-400';
    if (denied) {
      state.readVersion++;
      state.denied = true;
      state.data = null;
      state.list.replaceChildren();
      state.upload.hidden = true;
      state.root.querySelectorAll('iframe').forEach(frame => frame.remove());
    }
  }

  async function request(state, suffix = '', body = null) {
    const response = await fetch(`/academy/sessions/${state.id}/files${suffix}`, {
      method: body ? 'POST' : 'GET', credentials:'same-origin', cache:'no-store',
      signal:state.controller.signal,
      headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest', ...(body ? {'X-CSRF-TOKEN':window.siteCsrfToken || ''} : {})},
      ...(body ? {body} : {}),
    });
    let result;
    try { result = await response.json(); } catch (_) {
      const error = new Error(t('پاسخ سرور معتبر نیست. دوباره وارد شوید یا تلاش کنید.','Invalid server response. Sign in again or retry.'));
      error.denied = response.redirected || response.status === 401 || response.status === 403;
      throw error;
    }
    const payload = result.data ?? result;
    if (!response.ok || payload.success === false) {
      const error = new Error(payload.message || t('دریافت فایل‌های جلسه ناموفق بود.','Unable to load session files.'));
      error.denied = response.status === 401 || response.status === 403;
      throw error;
    }
    return payload.data ?? payload;
  }

  function fileSize(bytes) {
    const value = Number(bytes) || 0;
    return `${new Intl.NumberFormat(document.documentElement.lang || 'fa', {maximumFractionDigits:1}).format(value / (value >= 1048576 ? 1048576 : 1024))} ${value >= 1048576 ? 'MB' : 'KB'}`;
  }

  function renderFiles(state, data) {
    if (active !== state) return;
    state.data = data;
    state.denied = false;
    state.status.className = 'text-sm text-gray-500 dark:text-slate-400';
    state.status.textContent = '';
    state.upload.hidden = data.canUpload !== true;
    state.max.textContent = `${t('حداکثر حجم هر فایل:','Maximum file size:')} ${fileSize(data.maxBytes)}`;
    state.input.accept = Array.isArray(data.extensions) ? data.extensions.map(extension => '.' + extension).join(',') : '';
    state.list.innerHTML = (data.files || []).map(file => `<li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border p-3 dark:border-slate-700" data-session-file-row="${Number(file.id)}"><div class="min-w-0 flex-1"><p class="break-all font-medium">${escape(file.name)}</p><p class="mt-1 text-xs text-gray-500 dark:text-slate-400">${escape(fileSize(file.size))}</p></div><div class="flex items-center gap-2"><button type="button" data-session-file-download="${Number(file.id)}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border text-indigo-600" data-panel-tooltip="${t('دانلود فایل','Download file')}" aria-label="${t('دانلود فایل','Download file')}"><i class="fas fa-download" aria-hidden="true"></i></button>${file.canDelete ? `<button type="button" data-session-file-delete="${Number(file.id)}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border text-red-600" data-panel-tooltip="${t('حذف فایل','Delete file')}" aria-label="${t('حذف فایل','Delete file')}"><i class="fas fa-trash" aria-hidden="true"></i></button>` : ''}</div></li>`).join('') || `<li class="py-6 text-center text-gray-500 dark:text-slate-400">${t('هنوز فایلی برای این جلسه بارگذاری نشده است.','No files have been uploaded for this session.')}</li>`;
  }

  async function load(state) {
    const version = ++state.readVersion;
    state.status.textContent = t('در حال دریافت فایل‌ها…','Loading files…');
    try {
      const data = await request(state);
      if (version === state.readVersion) renderFiles(state, data);
    } catch (error) {
      if (error.name !== 'AbortError' && version === state.readVersion) message(state, error.message, error.denied);
    }
  }

  function formData() {
    const data = new FormData();
    data.append('_token', window.siteCsrfToken || '');
    return data;
  }

  async function uploadFile(state, event) {
    event.preventDefault();
    const file = state.input.files[0];
    if (state.busy || state.denied || !state.data?.canUpload || !file) return;
    if (!file.size || file.size > Number(state.data.maxBytes)) {
      message(state, t('حجم فایل معتبر نیست یا از حد مجاز بیشتر است.','The file is empty or exceeds the size limit.'));
      return;
    }
    await mutate(state, '', file);
  }

  async function mutate(state, suffix, file = null) {
    if (state.busy || state.denied) return;
    state.busy = true;
    state.readVersion++;
    state.root.querySelectorAll('button:not([data-session-files-close]),input').forEach(control => control.disabled = true);
    state.status.textContent = t('در حال ذخیره…','Saving…');
    try {
      const body = formData();
      if (file) body.append('file', file);
      await request(state, suffix, body);
      if (active === state) { state.input.value = ''; await load(state); }
    } catch (error) { if (error.name !== 'AbortError') message(state, error.message, error.denied); }
    finally {
      state.busy = false;
      if (active === state) state.root.querySelectorAll('button:not([data-session-files-close]),input').forEach(control => control.disabled = state.denied);
      if (active === state) state.root.querySelector('[data-session-files-retry]').disabled = false;
    }
  }

  function downloadFile(state, file) {
    if (state.busy || state.denied || !validId(file) || !state.data?.files?.some(row => String(row.id) === file)) return;
    const frame = document.createElement('iframe');
    frame.hidden = true;
    frame.title = t('دانلود فایل جلسه','Session file download');
    frame.addEventListener('load', () => {
      if (active !== state) return;
      try {
        const raw = frame.contentDocument?.body?.textContent || '';
        if (!raw) return;
        const result = JSON.parse(raw), payload = result.data ?? result;
        if (payload.success === false) message(state, payload.message || t('دانلود ناموفق بود.','Download failed.'), [401,403].includes(Number(result.status)));
      } catch (_) {
        if (frame.contentDocument?.querySelector('form[action*="login"]')) message(state, t('برای ادامه دوباره وارد شوید.','Sign in again to continue.'), true);
      }
    });
    frame.src = `/academy/sessions/${state.id}/files/${file}/download`;
    state.root.append(frame);
  }

  function confirmDelete(state, button) {
    const id = button.dataset.sessionFileDelete;
    if (!validId(id) || state.denied || state.busy || !state.data?.files?.some(file => String(file.id) === id && file.canDelete)) return;
    const group = button.parentElement;
    group.replaceChildren();
    const label = document.createElement('span');
    label.className = 'text-sm'; label.textContent = t('حذف فایل؟','Delete file?');
    const yes = document.createElement('button'), no = document.createElement('button');
    yes.type = no.type = 'button';
    yes.className = 'rounded-lg border px-3 py-2 text-red-600';
    no.className = 'rounded-lg border px-3 py-2';
    yes.textContent = t('حذف','Delete'); no.textContent = t('انصراف','Cancel');
    yes.addEventListener('click', () => mutate(state, `/${id}/delete`));
    no.addEventListener('click', () => renderFiles(state, state.data));
    group.append(label, yes, no); yes.focus({preventScroll:true});
  }

  function openFiles(id, trigger) {
    if (!validId(id)) return;
    closeFiles();
    const root = document.createElement('div');
    root.className = 'fixed inset-0 flex items-center justify-center p-4'; root.style.zIndex = '90';
    root.innerHTML = `<section role="dialog" aria-modal="true" aria-labelledby="sessionFilesTitle" class="flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"><header class="flex items-center justify-between gap-3 border-b p-4 dark:border-slate-700"><h2 id="sessionFilesTitle" class="font-bold">${t('فایل‌های جلسه','Session files')}</h2><button type="button" data-session-files-close class="inline-flex h-9 w-9 items-center justify-center rounded-xl border" aria-label="${t('بستن','Close')}"><i class="fas fa-times" aria-hidden="true"></i></button></header><div class="space-y-4 overflow-y-auto p-4"><p data-session-files-status role="status" aria-live="polite" class="text-sm text-gray-500"></p><form data-session-files-upload hidden class="space-y-3 rounded-xl border p-3 dark:border-slate-700"><label class="block text-sm">${t('انتخاب فایل','Choose file')}<input type="file" required class="mt-2 block w-full rounded-lg border p-2 dark:bg-slate-800"></label><p data-session-files-max class="text-xs text-gray-500 dark:text-slate-400"></p><button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white" aria-label="${t('بارگذاری فایل','Upload file')}" data-panel-tooltip="${t('بارگذاری فایل','Upload file')}"><i class="fas fa-plus" aria-hidden="true"></i></button></form><ul data-session-files-list class="space-y-3"></ul><button type="button" data-session-files-retry class="rounded-xl border px-3 py-2 text-sm">${t('تازه‌سازی','Refresh')}</button></div></section>`;
    const state = {id:Number(id),root,trigger,controller:new AbortController(),busy:false,denied:false,readVersion:0};
    for (const [key,selector] of Object.entries({status:'[data-session-files-status]',upload:'[data-session-files-upload]',input:'input',max:'[data-session-files-max]',list:'[data-session-files-list]'})) state[key] = root.querySelector(selector);
    active = state;
    root.addEventListener('click', event => {
      if (event.target === root || event.target.closest('[data-session-files-close]')) return closeFiles();
      const download = event.target.closest('[data-session-file-download]');
      if (download) downloadFile(state, download.dataset.sessionFileDownload);
      const remove = event.target.closest('[data-session-file-delete]');
      if (remove) confirmDelete(state, remove);
      if (event.target.closest('[data-session-files-retry]') && !state.busy) load(state);
    });
    state.upload.addEventListener('submit', event => uploadFile(state, event));
    (document.getElementById('modalContainer') || document.body).append(root);
    root.querySelector('[data-session-files-close]').focus({preventScroll:true});
    load(state);
  }

  document.addEventListener('click', event => {
    const trigger = event.target.closest('[data-session-files]');
    if (trigger) { event.preventDefault(); event.stopPropagation(); openFiles(trigger.dataset.sessionFiles, trigger); }
  });
  document.addEventListener('keydown', event => {
    if (!active) return;
    if (event.key === 'Escape') { event.preventDefault(); event.stopImmediatePropagation(); closeFiles(); }
    if (event.key !== 'Tab') return;
    const controls = [...active.root.querySelectorAll('button,input')].filter(control => !control.disabled && control.getClientRects().length);
    const first = controls[0], last = controls[controls.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
  }, true);
  window.addEventListener('pagehide', closeFiles);
  window.addEventListener('hashchange', closeFiles);
  window.addEventListener('panel-organizations-changed', closeFiles);
  new MutationObserver(() => { if (active && !active.root.isConnected) closeFiles(); })
    .observe(document.body, {childList:true,subtree:true});
})();
