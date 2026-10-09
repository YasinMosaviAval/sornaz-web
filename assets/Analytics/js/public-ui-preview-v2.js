(() => {
  const root = document.getElementById('sornazPreview');
  const source = document.getElementById('sornazPreviewData');
  if (!root || !source) return;
  const sections = JSON.parse(source.textContent);
  const byKey = new Map(sections.map(section => [section.key, section]));
  const body = document.getElementById('sornazPreviewBody');
  const tabs = document.getElementById('sornazPreviewTabs');
  const add = document.getElementById('sornazPreviewAdd');
  const excel = document.getElementById('sornazPreviewExcel');
  const pdf = document.getElementById('sornazPreviewPdf');
  const original = document.getElementById('sornazPreviewOriginal');
  const params = new URLSearchParams(location.search);
  const role = ['admin', 'academy', 'branch', 'user'].includes(params.get('role')) ? params.get('role') : 'admin';
  const sectionAliases = {'notation': 'notation-preview', 'gallery-cover': 'gallery', 'gallery-logo': 'gallery', 'gallery-intro-video': 'gallery', 'gallery-collection': 'gallery'};
  let activeKey = params.get('section') || 'dashboard';
  let activeAction = params.get('action') || 'list';

  const element = (tag, classes = '', label) => {
    const result = document.createElement(tag);
    result.className = classes;
    if (label !== undefined) result.textContent = label;
    return result;
  };
  const fieldValue = field => {
    const key = String(field.key || '').toLowerCase();
    if (key.includes('date')) return '۱۴۰۵/۰۷/۱۷';
    if (key.includes('time')) return '۱۰:۳۰';
    if (key.includes('amount') || key.includes('price')) return '۲٬۵۰۰٬۰۰۰';
    if (key.includes('status')) return 'فعال';
    if (field.type === 'email') return 'sample@example.com';
    if (field.type === 'phone') return '۰۹۱۲•••••••';
    if (field.type === 'number') return '۱۲';
    return 'نمونهٔ ' + field.label;
  };
  const activeSection = () => {
    const key = role === 'user' && activeKey.startsWith('gallery-') ? 'my-gallery'
      : role === 'user' && activeKey === 'member-schedules' ? 'my-schedule'
      : role === 'user' && activeKey === 'points' ? 'my-points'
      : sectionAliases[activeKey] || (activeKey === 'settings' && role === 'user' ? 'my-settings' : activeKey === 'lessons' && role === 'user' ? 'my-lessons' : activeKey === 'finance' && role === 'user' ? 'my-finance' : activeKey);
    return byKey.get(key) || byKey.get('dashboard') || sections[0];
  };
  const availableAction = (section, name) => section.actions.find(item => item.key === name);
  const syncUrl = () => {
    const url = new URL(location.href);
    url.searchParams.set('role', role);
    url.searchParams.set('section', activeKey);
    url.searchParams.set('action', activeAction);
    history.replaceState(null, '', url);
  };

  function renderNavigation() {
    for (const item of root.querySelectorAll('#sidebar .nav-link, #sidebar a[onclick]')) {
      const match = item.getAttribute('onclick')?.match(/showSection\('([^']+)'\)/);
      item.classList.toggle('is-active', match?.[1] === activeKey);
    }
  }

  function renderTabs(section) {
    tabs.replaceChildren();
    if (!['notation-preview', 'account', 'dashboard', 'my-courses', 'my-terms', 'my-classrooms'].includes(section.key)) return;
    const actions = section.key === 'dashboard'
      ? [{key: 'list', label: 'نمای کلی'}, {key: 'my-terms', label: 'ترم‌های من'}, {key: 'my-classrooms', label: 'کلاس‌های من'}]
      : section.actions.filter(action => section.key !== 'account' || ['list', 'profile', 'security', 'document', 'privacy'].includes(action.key));
    for (const action of actions) {
      const button = element('button', action.key === activeAction ? 'is-active' : '', action.label);
      button.type = 'button';
      button.addEventListener('click', () => {
        if (section.key === 'dashboard' && action.key.startsWith('my-')) window.showSection(action.key);
        else { activeAction = action.key; render(); }
      });
      tabs.append(button);
    }
  }

  function renderFilters(section) {
    const fragment = document.getElementById('adminFiltersTemplate').content.cloneNode(true);
    const slot = fragment.querySelector('[data-slot="filters"]');
    slot.className = 'grid gap-3 md:grid-cols-3';
    const search = element('input');
    search.type = 'search';
    search.placeholder = 'جست‌وجو در ' + section.label;
    const state = element('select');
    for (const label of ['همه وضعیت‌ها', 'فعال', 'در انتظار']) state.append(element('option', '', label));
    const date = element('input');
    date.type = 'text';
    date.placeholder = 'بازهٔ زمانی';
    slot.append(search, state, date);
    body.append(fragment);
  }

  function renderTable(section) {
    const fragment = document.getElementById('adminTableTemplate').content.cloneNode(true);
    const slot = fragment.querySelector('[data-slot="table"]');
    const table = element('table');
    const head = element('thead');
    const headings = element('tr');
    const fields = section.fields.length ? section.fields.slice(0, 4) : [{label: 'عنوان'}, {label: 'تاریخ', key: 'date'}, {label: 'وضعیت', key: 'status'}];
    for (const title of ['ردیف', ...fields.map(field => field.label), 'عملیات']) headings.append(element('th', '', title));
    head.append(headings);
    const rows = element('tbody');
    for (let index = 1; index <= 5; index++) {
      const row = element('tr');
      row.append(element('td', '', String(index)));
      for (const field of fields) {
        const cell = element('td');
        if (field.key === 'status') cell.append(element('span', 'rounded-full bg-green-100 px-3 py-1 text-xs text-green-700', index === 5 ? 'در انتظار' : 'فعال'));
        else cell.textContent = fieldValue(field);
        row.append(cell);
      }
      const controls = element('td');
      const edit = element('button', 'rounded-lg px-2 py-1 text-indigo-600 hover:bg-indigo-50', 'ویرایش');
      edit.type = 'button';
      edit.addEventListener('click', () => openForm(section, availableAction(section, 'update') || availableAction(section, 'profile')));
      controls.append(edit);
      row.append(controls);
      rows.append(row);
    }
    table.append(head, rows);
    slot.append(table);
    const pagination = fragment.querySelector('[data-slot="pagination"]');
    pagination.append(element('span', '', 'نمایش ۱ تا ۵ از ۱۲ مورد'), element('span', '', 'صفحه ۱ از ۳'));
    body.append(fragment);
  }

  function renderDashboard() {
    const cards = element('div', 'mb-6 grid gap-4 md:grid-cols-3');
    for (const [label, value, icon] of [['اعضای فعال', '۱۲۸', 'fa-users'], ['کلاس‌های من', '۶', 'fa-chalkboard'], ['امتیازهای من', '۱۲۵', 'fa-coins']]) {
      const card = element('div', 'rounded-3xl bg-white p-6 shadow-sm');
      const symbol = element('i', 'fas ' + icon + ' mb-4 text-2xl text-indigo-600');
      card.append(symbol, element('p', 'text-sm text-gray-500', label), element('strong', 'mt-2 block text-3xl font-bold', value));
      cards.append(card);
    }
    body.append(cards);
    renderTable(activeSection());
  }

  function inputFor(field) {
    const label = element('label', ['multiline', 'rows', 'object', 'file'].includes(field.type) ? 'md:col-span-2' : '');
    label.append(element('span', 'mb-1 block font-medium', field.label + (field.required ? ' *' : '')));
    let control;
    if (['multiline', 'rows', 'object'].includes(field.type)) control = element('textarea');
    else if (field.type === 'select') {
      control = element('select');
      for (const option of field.options.length ? field.options : ['انتخاب کنید', 'گزینهٔ نمونه']) control.append(element('option', '', option));
    } else {
      control = element('input');
      control.type = field.type === 'file' ? 'file' : field.type === 'bool' ? 'checkbox' : ['email', 'password', 'number', 'date', 'time'].includes(field.type) ? field.type : 'text';
      if (control.type !== 'file' && control.type !== 'checkbox') control.placeholder = fieldValue(field);
    }
    label.append(control);
    return label;
  }

  function openForm(section, action) {
    if (!action) return;
    closeModal();
    const overlay = element('div', 'sornaz-preview-modal');
    overlay.id = 'sornazPreviewModal';
    const dialog = element('div', 'sornaz-preview-dialog');
    const head = element('div', 'flex items-center justify-between border-b px-7 py-5');
    head.append(element('h2', 'text-xl font-bold', action.label + ' ' + section.label));
    const close = element('button', 'text-2xl text-gray-400', '×');
    close.type = 'button';
    close.addEventListener('click', closeModal);
    head.append(close);
    const content = element('div', 'grid gap-4 p-7 md:grid-cols-2');
    const fields = action.fields?.length ? action.fields : section.fields;
    if (!fields.length) content.append(element('p', 'md:col-span-2 text-gray-500', 'این بخش در سایت اصلی فرم جداگانه ندارد.'));
    else for (const field of fields) content.append(inputFor(field));
    const actions = element('div', 'flex gap-3 border-t px-7 py-5');
    const save = element('button', 'flex-1 rounded-2xl bg-indigo-600 px-4 py-3 text-white', 'ذخیره');
    save.type = 'button';
    save.addEventListener('click', closeModal);
    const cancel = element('button', 'flex-1 rounded-2xl border px-4 py-3', 'انصراف');
    cancel.type = 'button';
    cancel.addEventListener('click', closeModal);
    actions.append(save, cancel);
    dialog.append(head, content, actions);
    overlay.append(dialog);
    overlay.addEventListener('click', event => { if (event.target === overlay) closeModal(); });
    root.append(overlay);
  }
  function closeModal() { document.getElementById('sornazPreviewModal')?.remove(); }

  function render() {
    const section = activeSection();
    if (!section) return;
    closeModal();
    const candidate = document.getElementById(activeKey);
    const realSection = candidate?.classList.contains('section') && original.contains(candidate) ? candidate : null;
    document.getElementById('panelPageTitle').textContent = realSection?.querySelector('h1')?.textContent?.trim() || section.label;
    renderNavigation();
    for (const item of original.querySelectorAll('.section')) item.classList.add('hidden');
    if (realSection) {
      realSection.classList.remove('hidden');
      body.classList.add('hidden');
      tabs.classList.add('hidden');
      document.querySelector('.sornaz-preview-actions').classList.add('hidden');
      prepareRealSection(realSection);
      syncUrl();
      return;
    }
    body.classList.remove('hidden');
    tabs.classList.remove('hidden');
    document.querySelector('.sornaz-preview-actions').classList.remove('hidden');
    renderTabs(section);
    body.replaceChildren();
    const isDashboard = section.key === 'dashboard';
    const isList = ['list', 'all', 'mine', 'saved', 'feed', 'learning', 'progress', 'my-terms', 'my-classrooms'].includes(activeAction);
    const create = availableAction(section, 'create') || availableAction(section, 'post');
    add.hidden = isDashboard || !create;
    excel.hidden = pdf.hidden = isDashboard || !isList;
    if (isDashboard) renderDashboard();
    else if (isList) { renderFilters(section); renderTable(section); }
    else {
      const action = availableAction(section, activeAction);
      if (action) openForm(section, action);
      renderFilters(section);
      renderTable(section);
    }
    syncUrl();
  }

  function prepareRealSection(section) {
    const heading = section.querySelector('h1');
    if (heading) {
      heading.classList.add('hidden');
      const description = heading.nextElementSibling;
      if (description?.tagName === 'P') description.classList.add('hidden');
      const intro = heading.parentElement;
      if (intro && [...intro.children].every(child => child.classList.contains('hidden'))) intro.classList.add('hidden');
    }
    for (const table of section.querySelectorAll('table')) {
      const rows = table.tBodies[0];
      if (!rows) continue;
      if (rows.children.length === 1 && /در حال بارگذاری|داده‌ای موجود نیست|اطلاعاتی یافت نشد/.test(rows.textContent)) rows.replaceChildren();
      if (rows.children.length) continue;
      const headings = [...table.querySelectorAll('thead th')].map(th => th.textContent.trim());
      const count = headings.length || 4;
      for (let index = 1; index <= 3; index++) {
        const row = element('tr');
        for (let column = 0; column < count; column++) {
          const title = headings[column] || '';
          const value = column === 0 ? String(index) : /تاریخ|زمان/.test(title) ? '۱۴۰۵/۰۷/۱۷' : /وضعیت/.test(title) ? 'فعال' : /مبلغ|شهریه/.test(title) ? '۲٬۵۰۰٬۰۰۰' : /عملیات/.test(title) ? 'مشاهده  ·  ویرایش' : /نام|عنوان/.test(title) ? 'نمونهٔ سرناز' : 'نمونه';
          row.append(element('td', 'px-4 py-3', value));
        }
        rows.append(row);
      }
    }
    for (const container of section.querySelectorAll('.gallery-grid')) {
      if (container.children.length) continue;
      for (let index = 1; index <= 3; index++) {
        const card = element('article', 'overflow-hidden rounded-3xl bg-white shadow');
        const media = element('div', 'flex aspect-video items-center justify-center bg-indigo-100 text-indigo-400');
        media.append(element('i', 'fas fa-image text-5xl'));
        card.append(media, element('div', 'p-5 font-medium', 'رسانهٔ نمونهٔ ' + index));
        container.append(card);
      }
    }
    for (const id of ['dashboardStats', 'financeSummaryCards']) {
      const container = section.querySelector('#' + id);
      if (!container || container.children.length) continue;
      for (const [label, value] of [['مجموع موارد', '۱۲۸'], ['فعال', '۹۶'], ['در انتظار', '۳۲']]) {
        const card = element('div', 'rounded-3xl bg-white p-6 shadow-sm');
        card.append(element('p', 'text-sm text-gray-500', label), element('strong', 'mt-3 block text-2xl text-indigo-700', value));
        container.append(card);
      }
    }
    for (const list of section.querySelectorAll('#todayClassesList, #urgentItemsList, #dashboardActionItems')) {
      if (!list.children.length) list.append(element('div', 'rounded-xl border border-gray-100 bg-white p-4 text-sm', 'مورد نمونه برای نمایش چیدمان'));
    }
  }

  window.showSection = key => {
    activeKey = key;
    activeAction = 'list';
    document.getElementById('sidebar')?.classList.remove('is-open');
    document.getElementById('sidebarOverlay')?.classList.add('hidden');
    render();
  };
  window.toggleSidebarSubmenu = (id, button) => {
    document.getElementById(id)?.classList.toggle('hidden');
    button?.querySelector('.submenu-chevron')?.classList.toggle('rotate-180');
  };
  window.toggleSidebar = () => {
    document.getElementById('sidebar')?.classList.toggle('is-open');
    document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
  };
  window.closeMobileSidebar = () => {
    document.getElementById('sidebar')?.classList.remove('is-open');
    document.getElementById('sidebarOverlay')?.classList.add('hidden');
  };
  document.getElementById('sidebarOverlay')?.addEventListener('click', window.closeMobileSidebar);
  original.addEventListener('click', event => {
    const control = event.target.closest('button, a, label');
    if (!control) return;
    event.preventDefault();
    event.stopImmediatePropagation();
    const label = control.textContent.trim();
    if (/افزودن|ویرایش|ثبت تراکنش/.test(label)) {
      const section = activeSection();
      openForm(section, availableAction(section, /ویرایش/.test(label) ? 'update' : 'create') || availableAction(section, 'profile'));
    }
  }, true);
  original.addEventListener('submit', event => event.preventDefault(), true);
  for (const item of original.querySelectorAll('*')) {
    for (const attribute of [...item.attributes]) if (attribute.name.startsWith('on')) item.removeAttribute(attribute.name);
    if (item.tagName === 'A' && item.getAttribute('href')?.startsWith('/')) item.setAttribute('href', '#');
  }
  add.addEventListener('click', () => openForm(activeSection(), availableAction(activeSection(), 'create') || availableAction(activeSection(), 'post')));
  excel.addEventListener('click', () => openForm(activeSection(), {label: 'تنظیمات خروجی اکسل', fields: [{key: 'columns', label: 'ستون‌های خروجی', type: 'select', options: ['همهٔ ستون‌ها', 'ستون‌های انتخابی']}]}));
  pdf.addEventListener('click', () => openForm(activeSection(), {label: 'تنظیمات خروجی پی‌دی‌اف', fields: [{key: 'orientation', label: 'جهت صفحه', type: 'select', options: ['عمودی', 'افقی']}, {key: 'columns', label: 'ستون‌های خروجی', type: 'select', options: ['همهٔ ستون‌ها', 'ستون‌های انتخابی']}]}));
  if (!byKey.has(activeKey) && !sectionAliases[activeKey]) activeKey = 'dashboard';
  render();
})();
