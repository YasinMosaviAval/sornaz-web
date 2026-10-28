(function () {
  'use strict';
  const root = document.getElementById('database-management');
  if (!root) return;
  const text = (fa, en) => document.documentElement.lang.startsWith('en') ? en : fa;
  const escape = value => String(value ?? '').replace(/[&<>"']/g, character => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[character]);
  const number = value => Number(value).toLocaleString(document.documentElement.lang.startsWith('en') ? 'en' : 'fa-IR');
  let selected = '', tablesPage = 1, rowsPage = 1, searchTimer, tablesVersion = 0, rowsVersion = 0;
  let tablesRequest, rowsRequest, downloadFrame, accessDenied = false;
  function setAccess(allowed) {
    accessDenied = !allowed;
    root.querySelectorAll('[data-database-action]').forEach(button => {
      if (/^(?:download-|table-schema$|table-data$)/.test(button.dataset.databaseAction)) button.disabled = !allowed;
    });
  }
  const status = (message = '', error = false) => {
    const node = root.querySelector('[data-database-status]');
    node.textContent = message;
    node.classList.toggle('text-red-600', error);
    node.setAttribute('role', error ? 'alert' : 'status');
  };
  async function request(path, query, signal) {
    const response = await fetch('/analytics/admin-database/' + path + '?' + new URLSearchParams(query), {credentials:'same-origin',cache:'no-store',signal,headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
    const raw = await response.json(), envelope = raw.data ?? raw;
    if (!response.ok || raw.success === false || envelope.success === false) {
      const error = new Error(envelope.message || raw.message || text('دریافت اطلاعات دیتابیس انجام نشد.', 'Database information could not be loaded.'));
      error.status = response.status; throw error;
    }
    return envelope.data ?? envelope;
  }
  function messageRow(table, message, columns) {
    window.AdminPanelTable.setRows(table, [{cells:[{value:message,attributes:`colspan="${Math.max(1,columns)}" class="p-8 text-center text-gray-500"`}]}]);
  }
  function handleError(error) {
    if (error.name === 'AbortError') return;
    status(error.message || text('دریافت اطلاعات انجام نشد.', 'Information could not be loaded.'), true);
    if ([401,403].includes(error.status)) {
      tablesVersion++; rowsVersion++; tablesRequest?.abort(); rowsRequest?.abort(); selected = '';
      tablesPage = rowsPage = 1; setAccess(false);
      messageRow('#databaseTables', text('دسترسی لازم را ندارید.', 'Access denied.'), 5);
      const heading = document.createElement('th'), header = document.createElement('tr');
      heading.textContent = text('سطرهای جدول', 'Table rows'); heading.className = 'px-5 py-4 text-start'; header.append(heading);
      document.querySelector('#databaseRows thead').replaceChildren(header);
      messageRow('#databaseRows', text('دسترسی لازم را ندارید.', 'Access denied.'), 1);
      document.getElementById('databaseSelectedTable').textContent = '';
      document.getElementById('databaseTableSearch').value = '';
      root.querySelector('[data-database-tab="rows"]').disabled = true;
      root.querySelectorAll('[data-database-panel]').forEach(panel=>panel.setAttribute('aria-busy','false'));
      ['databaseTablesPagination','databaseRowsPagination'].forEach(id => window.AdminPanelTable.renderPagination(document.getElementById(id), {total:0,perPage:10,page:1}));
      if (downloadFrame) {downloadFrame.onload = null; downloadFrame.remove(); downloadFrame = null;}
      window.showDatabaseTab('tables');
    }
  }
  function rowActions(name) {
    const attribute = `data-database-table="${escape(name)}" type="button"`;
    const schema = text('دانلود ساختار جدول', 'Download table schema');
    const data = text('دانلود ساختار و اطلاعات جدول', 'Download table schema and data');
    return `<div class="inline-flex items-center gap-2 whitespace-nowrap"><button ${attribute} data-database-action="view-rows" class="text-indigo-600" title="${escape(text('مشاهده سطرهای جدول', 'View table rows'))}">${text('مشاهده','View')}</button><button ${attribute} data-database-action="table-schema" data-panel-tooltip="${escape(schema)}" aria-label="${escape(schema)}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border text-indigo-600"><i class="fas fa-file-code" aria-hidden="true"></i></button><button ${attribute} data-database-action="table-data" data-panel-tooltip="${escape(data)}" aria-label="${escape(data)}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border text-indigo-600"><i class="fas fa-file-download" aria-hidden="true"></i></button></div>`;
  }
  window.loadDatabaseTables = async function (page = tablesPage) {
    tablesPage = page; const version = ++tablesVersion;
    tablesRequest?.abort(); tablesRequest = new AbortController();
    root.querySelector('[data-database-panel="tables"]').setAttribute('aria-busy','true'); status();
    messageRow('#databaseTables', text('در حال بارگیری…', 'Loading…'), 5);
    try {
      const data = await request('tables', {page,search:document.getElementById('databaseTableSearch').value.trim()}, tablesRequest.signal);
      if (version !== tablesVersion) return;
      setAccess(true);
      tablesPage = Number(data.page || page);
      const rows = (data.tables || []).map(table => ({cells:[
        {value:table.name,attributes:'class="px-5 py-4 font-mono" dir="ltr"'},
        {value:table.type,attributes:'class="px-5 py-4"'},
        {value:table.estimatedRows == null ? '—' : number(table.estimatedRows),attributes:'class="px-5 py-4"'},
        {value:table.bytes == null ? '—' : number(table.bytes) + ' B',attributes:'class="px-5 py-4" dir="ltr"'},
        {html:rowActions(table.name),attributes:'class="px-5 py-4"'},
      ]}));
      if (rows.length) window.AdminPanelTable.setRows('#databaseTables', rows);
      else messageRow('#databaseTables', text('جدولی یافت نشد.', 'No tables found.'), 5);
      window.AdminPanelTable.renderPagination(document.getElementById('databaseTablesPagination'), {total:data.total,perPage:10,page:tablesPage,onChange:next=>window.loadDatabaseTables(next)});
    } catch (error) {
      if (version !== tablesVersion || error.name === 'AbortError') return;
      messageRow('#databaseTables', text('بارگیری جدول‌ها ناموفق بود؛ دوباره تلاش کنید.', 'Could not load tables. Please try again.'), 5);
      handleError(error);
    } finally {if (version === tablesVersion) root.querySelector('[data-database-panel="tables"]').setAttribute('aria-busy','false');}
  };
  function cell(value) {
    if (value === null) return '<span class="italic text-gray-400">NULL</span>';
    if (value && typeof value === 'object' && value.type === 'binary') return `<details><summary class="cursor-pointer text-xs">${escape(text('داده باینری (Base64)', 'Binary data (Base64)'))}</summary><pre class="mt-2 max-h-64 max-w-lg overflow-auto whitespace-pre-wrap break-all text-xs" dir="ltr">${escape(value.value)}</pre></details>`;
    const string = typeof value === 'object' ? JSON.stringify(value) : String(value);
    if (string.length > 240) return `<details><summary class="max-w-sm cursor-pointer whitespace-pre-wrap break-words">${escape(string.slice(0,240))}…</summary><pre class="mt-2 max-h-64 max-w-lg overflow-auto whitespace-pre-wrap break-words text-xs" dir="auto">${escape(string)}</pre></details>`;
    return `<span class="block max-w-md whitespace-pre-wrap break-words" dir="auto">${escape(string)}</span>`;
  }
  async function loadRows(page = 1) {
    if (!selected) return;
    rowsPage = page; const version = ++rowsVersion, tableName = selected;
    rowsRequest?.abort(); rowsRequest = new AbortController();
    const table = document.getElementById('databaseRows');
    root.querySelector('[data-database-panel="rows"]').setAttribute('aria-busy','true'); status();
    messageRow(table, text('در حال بارگیری…', 'Loading…'), table.tHead.rows[0].cells.length);
    try {
      const data = await request('rows', {table:tableName,page}, rowsRequest.signal);
      if (version !== rowsVersion || selected !== tableName) return;
      setAccess(true);
      rowsPage = Number(data.page || page);
      const header = document.createElement('tr');
      (data.columns || []).forEach(column => {
        const heading = document.createElement('th'); heading.className = 'px-5 py-4 text-start font-mono';
        heading.textContent = column.name; heading.title = column.type; heading.dir = 'ltr'; header.append(heading);
      });
      table.tHead.replaceChildren(header);
      const rows = (data.rows || []).map(row => ({cells:data.columns.map(column=>({html:cell(row[column.name]),attributes:'class="px-5 py-4 align-top"'}))}));
      if (rows.length) window.AdminPanelTable.setRows(table, rows);
      else messageRow(table, text('این جدول سطری ندارد.', 'This table has no rows.'), data.columns.length);
      window.AdminPanelTable.renderPagination(document.getElementById('databaseRowsPagination'), {total:data.total,perPage:10,page:rowsPage,onChange:loadRows});
    } catch (error) {
      if (version !== rowsVersion || error.name === 'AbortError') return;
      messageRow(table, text('بارگیری سطرها ناموفق بود؛ دوباره تلاش کنید.', 'Could not load rows. Please try again.'), table.tHead.rows[0].cells.length);
      handleError(error);
    } finally {if (version === rowsVersion) root.querySelector('[data-database-panel="rows"]').setAttribute('aria-busy','false');}
  }
  window.loadDatabaseManagement = () => selected && !root.querySelector('[data-database-panel="rows"]').classList.contains('hidden') ? loadRows(rowsPage) : window.loadDatabaseTables(tablesPage);
  window.showDatabaseTab = function (view) {
    if (!['tables','rows'].includes(view) || view === 'rows' && !selected) return;
    root.querySelectorAll('[data-database-panel]').forEach(panel=>panel.classList.toggle('hidden',panel.dataset.databasePanel!==view));
    root.querySelectorAll('[data-database-tab]').forEach(button=>{
      const active=button.dataset.databaseTab===view;
      button.classList.toggle('bg-indigo-600',active); button.classList.toggle('text-white',active);
      button.classList.toggle('bg-white',!active); button.classList.toggle('text-gray-700',!active);
      if(active)button.setAttribute('aria-current','page');else button.removeAttribute('aria-current');
    });
  };
  function download(mode, table = '') {
    if (accessDenied) return;
    if (!downloadFrame) {
      downloadFrame = document.createElement('iframe'); downloadFrame.hidden = true;
      downloadFrame.title = text('دانلود خروجی دیتابیس', 'Database download');
      downloadFrame.onload = () => {
        try {
          const body=downloadFrame.contentDocument?.body?.textContent?.trim();
          if (!body) return;
          const raw=JSON.parse(body), envelope=raw.data??raw;
          if(raw.success===false||envelope.success===false)handleError(Object.assign(new Error(envelope.message||raw.message||text('دانلود ناموفق بود.', 'Download failed.')), {status:Number(raw.status||envelope.status||0)}));
        } catch (_) { handleError(Object.assign(new Error(text('دانلود انجام نشد؛ وضعیت ورود خود را بررسی کنید.', 'Download could not be started. Check your sign-in status.')), {status:401})); }
      };
      document.body.append(downloadFrame);
    }
    const query = new URLSearchParams({mode}); if(table)query.set('table',table);
    status(text('درخواست دانلود ارسال شد.', 'Download requested.'));
    downloadFrame.src = '/analytics/admin-database/download?' + query;
  }
  root.addEventListener('input', event=>{
    if(event.target.id!=='databaseTableSearch')return;
    clearTimeout(searchTimer); searchTimer=setTimeout(()=>window.loadDatabaseTables(1),250);
  });
  root.addEventListener('click', event=>{
    const button=event.target.closest('[data-database-action]'); if(!button)return;
    const action=button.dataset.databaseAction, table=button.dataset.databaseTable;
    if(action==='refresh-tables')return window.loadDatabaseTables(tablesPage);
    if(action==='refresh-rows')return loadRows(rowsPage);
    if(action==='view-rows'){
      selected=table; document.getElementById('databaseSelectedTable').textContent=selected;
      root.querySelector('[data-database-tab="rows"]').disabled=false;
      window.showDatabaseTab('rows'); return loadRows(1);
    }
    if(action==='download-all-schema')download('schema');
    if(action==='download-all-data')download('data');
    if(action==='download-table-schema'&&selected)download('schema',selected);
    if(action==='download-table-data'&&selected)download('data',selected);
    if(action==='table-schema'&&table)download('schema',table);
    if(action==='table-data'&&table)download('data',table);
  });
})();
