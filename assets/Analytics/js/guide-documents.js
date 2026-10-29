(function () {
  'use strict';
  const library = document.getElementById('guides');
  if (!library) return;
  const text = (fa, en) => document.documentElement.lang.startsWith('en') ? en : fa;
  let active;
  async function request(operation, data, signal) {
    const token = window.adminCsrfToken || window.siteCsrfToken || '';
    const body = new FormData();
    if (operation) for (const [key,value] of Object.entries({_token:token,...data})) body.append(key,value);
    const response = await fetch('/analytics/admin-guide-documents' + (operation ? '/' + operation : '?' + new URLSearchParams(data)), {
      method: operation ? 'POST' : 'GET', credentials:'same-origin', cache:'no-store', signal,
      headers:{Accept:'application/json', ...(operation ? {'X-CSRF-TOKEN':token} : {})},
      ...(operation ? {body} : {}),
    });
    const result = await response.json(), payload = result.data ?? result;
    if (!response.ok || payload.success === false) {
      const error = new Error(payload.message || text('عملیات فایل راهنما انجام نشد.','The guide file operation failed.'));
      error.status = response.status;
      throw error;
    }
    return payload.data ?? payload;
  }
  function refreshLibrary() {
    const count = library.querySelectorAll('[data-guide-path]').length;
    library.querySelector('[data-guide-file-count]').textContent = `${count.toLocaleString(document.documentElement.lang || 'fa')} ${text('فایل Markdown','Markdown files')}`;
    window.filterGuideDocuments?.(document.getElementById('guideDocumentSearch')?.value || '');
  }
  function close() {
    if (!active || active.busy) return;
    const state = active;
    active = null;
    state.controller.abort();
    state.dialog.close();
    state.dialog.remove();
    if (state.trigger.isConnected) state.trigger.focus({preventScroll:true});
  }
  function fail(state, error) {
    if (active !== state || error.name === 'AbortError') return;
    state.status.textContent = error.message;
    if ([401,403].includes(error.status)) {
      state.content.value = '';
      state.document = null;
      state.submit.disabled = true;
    }
  }
  async function submit(state, event) {
    event.preventDefault();
    if (state.busy || !state.document) return;
    if (state.operation === 'edit' && new TextEncoder().encode(state.content.value).length > 2097152) {
      state.status.textContent = text('متن راهنما نباید بیش از ۲ مگابایت باشد.','Guide text must not exceed 2 MiB.');
      return;
    }
    state.busy = true;
    state.submit.disabled = true;
    state.content.disabled = true;
    state.status.textContent = text('در حال ذخیره…','Saving…');
    try {
      const data = {path:state.document.path, version:state.document.version};
      if (state.operation === 'edit') data.content = state.content.value;
      const result = await request(state.operation === 'edit' ? 'update' : 'delete', data, state.controller.signal);
      if (active !== state) return;
      if (state.operation === 'delete') state.card.remove();
      else {
        state.card.querySelector('[data-guide-file-title]').textContent = result.title;
        state.card.querySelector('.guide-md').innerHTML = result.html;
        state.card.dataset.guideSearch = (result.title + ' ' + result.path).toLocaleLowerCase();
      }
      state.busy = false;
      close();
      refreshLibrary();
    } catch (error) { fail(state, error); }
    finally {
      state.busy = false;
      if (active === state) {
        state.content.disabled = false;
        state.submit.disabled = !state.document;
      }
    }
  }
  async function open(card, operation, trigger) {
    if (active?.busy) return;
    close();
    const dialog = document.createElement('dialog');
    dialog.id = 'guideDocumentDialog';
    dialog.setAttribute('aria-labelledby','guideDocumentEditorTitle');
    dialog.style.cssText = 'width:min(56rem,calc(100vw - 32px));max-height:85vh;padding:0;border:1px solid var(--border,#d1d5db);border-radius:var(--site-corner-radius,12px);background:var(--surface-card,#fff);color:var(--text,#111827);box-shadow:0 16px 40px #0003';
    dialog.innerHTML = `<form class="space-y-4 p-5"><header class="flex items-center justify-between gap-3"><h2 id="guideDocumentEditorTitle" class="text-lg font-bold"></h2><button type="button" data-guide-file-close class="h-9 w-9 rounded-xl border" aria-label="${text('بستن','Close')}">×</button></header><p data-guide-file-name dir="ltr" class="break-all text-sm text-gray-500"></p><p data-guide-file-warning class="text-sm text-red-600"></p><label data-guide-file-label class="block text-sm">${text('متن Markdown','Markdown content')}<textarea data-guide-file-content rows="18" dir="auto" spellcheck="false" class="mt-2 block w-full rounded-xl border p-3 font-mono dark:bg-slate-800" style="min-height:12rem;box-sizing:border-box"></textarea></label><p data-guide-file-status role="status" aria-live="polite" class="text-sm text-red-600"></p><div class="flex gap-3"><button type="submit" data-guide-file-submit disabled class="rounded-xl bg-indigo-600 px-4 py-2 text-white"></button><button type="button" data-guide-file-close class="rounded-xl border px-4 py-2">${text('انصراف','Cancel')}</button></div></form>`;
    const state = {dialog,card,operation,trigger,controller:new AbortController(),busy:false,document:null};
    for (const name of ['content','status','submit']) state[name] = dialog.querySelector(`[data-guide-file-${name}]`);
    dialog.querySelector('h2').textContent = operation === 'edit' ? text('ویرایش فایل راهنما','Edit guide file') : text('حذف فایل راهنما','Delete guide file');
    dialog.querySelector('[data-guide-file-name]').textContent = card.dataset.guidePath;
    dialog.querySelector('[data-guide-file-warning]').textContent = operation === 'delete' ? text('خود فایل Markdown از سرور حذف می‌شود. حذف این فایل را تأیید می‌کنید؟','The Markdown file itself will be deleted from the server. Confirm deletion?') : '';
    dialog.querySelector('[data-guide-file-label]').hidden = operation === 'delete';
    state.submit.textContent = operation === 'delete' ? text('حذف فایل','Delete file') : text('ذخیره فایل','Save file');
    state.status.textContent = text('در حال دریافت فایل…','Loading file…');
    dialog.addEventListener('cancel', event => {event.preventDefault();close();});
    dialog.addEventListener('click', event => {if(event.target.closest('[data-guide-file-close]'))close();});
    dialog.querySelector('form').addEventListener('submit', event => submit(state,event));
    document.body.append(dialog);
    active = state;
    dialog.showModal();
    try {
      const document = await request('',{path:card.dataset.guidePath},state.controller.signal);
      if (active !== state) return;
      state.document = document;
      state.content.value = document.content;
      state.status.textContent = '';
      state.submit.disabled = false;
      if (operation === 'edit') state.content.focus({preventScroll:true});
    } catch (error) { fail(state,error); }
  }
  library.addEventListener('click', event => {
    const button = event.target.closest('[data-guide-file-action]');
    if (!button) return;
    event.preventDefault();event.stopPropagation();
    open(button.closest('[data-guide-path]'),button.dataset.guideFileAction,button);
  },true);
})();
