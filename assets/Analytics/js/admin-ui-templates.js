(function () {
  let tableMarkup;
  function tablePart(name, values) {
    tableMarkup ||= JSON.parse(document.getElementById('adminPanelTableMarkup').textContent);
    return tableMarkup[name].replace(/\{\{(\w+)\}\}/g, (_, key) => String(values[key] ?? ''));
  }

  function tableText(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) =>
      ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]
    );
  }

  // HTML/attribute fragments are escaped by the page's existing trusted cell renderer.
  // Plain `value` is always escaped here. No rendered DOM is read back into a data model.
  const tableSources = new WeakMap();
  const compareValues = (a, b, direction) => {
    const clean = value => String(value ?? '').trim().replace(/[۰-۹٠-٩]/g, c => String(c.charCodeAt(0) % 16)).replace(/[٬,]/g, '').replace(/٫/g, '.');
    const av = clean(a), bv = clean(b);
    if (!av || !bv) return av === bv ? 0 : !av ? 1 : -1;
    const number = value => /^[+-]?\d+(?:\.\d+)?[%٪]?$/.test(value) ? Number(value.replace(/[%٪]$/, '')) : NaN;
    const an = number(av), bn = number(bv);
    return (Number.isFinite(an) && Number.isFinite(bn) ? an - bn : av.localeCompare(bv, document.documentElement.lang || undefined, { numeric: true })) * (direction === 'desc' ? -1 : 1);
  };
  function projectedValues(project, row) {
    const cells = project(row);
    if (Array.isArray(cells)) return cells.map(cell => {
      if (cell?.sortValue != null || cell?.value != null) return cell.sortValue ?? cell.value;
      if (cell?.html != null) {
        const template = document.createElement('template'); template.innerHTML = cell.html;
        return template.content.textContent;
      }
      return cell;
    });
    // Parse a detached, trusted row template, never the displayed/paginated rows.
    const template = document.createElement('template');
    template.innerHTML = '<table><tbody>' + (/^\s*<tr[ >]/i.test(cells) ? cells : '<tr>' + cells + '</tr>') + '</tbody></table>';
    return [...template.content.querySelector('tr').cells].map(cell => cell.dataset.sortValue ?? cell.textContent);
  }
  function enhanceSortHeaders(table) {
    const headings = table.tHead?.rows[table.tHead.rows.length - 1];
    if (!headings || table.closest('[data-admin-ui-native],#schedulesWeeklyTables')) return;
    let column = 0;
    [...headings.cells].forEach(heading => {
      if (heading.hasAttribute('data-admin-identity-column')) return;
      const index = column++;
      if (heading.querySelector('button,input,select') || heading.hasAttribute('data-panel-no-sort') || heading.colSpan > 1) return;
      const label = heading.textContent.trim();
      if (!label || /^(Actions?|Operations?|عملیات|اقدامات)$/i.test(label)) return;
      const button = document.createElement('button');
      button.type = 'button'; button.className = 'flex items-center gap-2';
      button.dataset.panelAutoSort = String(index);
      button.append(...heading.childNodes);
      const icon = document.createElement('span'); icon.dataset.panelAutoSortIcon = ''; icon.textContent = '↕';
      button.append(icon); heading.append(button); heading.setAttribute('aria-sort', 'none');
      button.onclick = () => {
        const direction = table.dataset.adminSortColumn === String(index) && table.dataset.adminSortDirection === 'asc' ? 'desc' : 'asc';
        table.dataset.adminSortColumn = String(index); table.dataset.adminSortDirection = direction; table.dataset.adminIdentityActive = '0';
        table.querySelectorAll('[data-panel-auto-sort]').forEach(other => {
          const active = other === button;
          other.closest('th').setAttribute('aria-sort', active ? (direction === 'asc' ? 'ascending' : 'descending') : 'none');
          other.querySelector('[data-panel-auto-sort-icon]').textContent = active ? (direction === 'asc' ? '↑' : '↓') : '↕';
        });
        const source = tableSources.get(table);
        if (source) { source.render(); return; }
        // Non-paginated tables keep expanded rows attached to their parent.
        const groups = [];
        [...table.tBodies[0].rows].forEach(row => {
          if (row.cells.length === 1 && row.cells[0].colSpan > 1 && groups.length) groups[groups.length - 1].push(row);
          else groups.push([row]);
        });
        const offset = headings.querySelector('[data-admin-identity-column]') ? 1 : 0;
        groups.sort((a,b) => compareValues(a[0].cells[index + offset]?.textContent, b[0].cells[index + offset]?.textContent, direction));
        groups.forEach(group => group.forEach(row => table.tBodies[0].append(row)));
      };
    });
  }
  window.AdminPanelTable = {
    renderPagination(footer, config) {
      if (!footer) return;
      const total = Math.max(0, Number(config.total) || 0), size = Math.max(1, Number(config.perPage) || 10);
      const pages = Math.max(1, Math.ceil(total / size)), current = Math.max(1, Math.min(pages, Number(config.page) || 1));
      const start = (current - 1) * size;
      const english = document.documentElement.lang.startsWith('en');
      const number = value => Number(value).toLocaleString(english ? 'en' : 'fa-IR');
      footer.replaceChildren();
      footer.hidden = total <= size;
      footer.classList.toggle('hidden', footer.hidden);
      if (footer.hidden) return;
      const info = document.createElement('span');
      info.textContent = english
        ? 'Showing ' + number(start + 1) + ' to ' + number(Math.min(start + size, total)) + ' of ' + number(total)
        : 'نمایش ' + number(start + 1) + ' تا ' + number(Math.min(start + size, total)) + ' از ' + number(total);
      const controls = document.createElement('div');
      controls.className = 'flex items-center gap-2';
      const button = (label, page) => {
        const node = document.createElement('button');
        node.type = 'button'; node.textContent = label;
        node.className = 'rounded-lg px-3 py-2 border disabled:opacity-40 hover:bg-gray-50';
        node.disabled = page < 1 || page > pages || page === current;
        if (page === current && label === number(page)) {
          node.className = 'rounded-lg px-3 py-2 bg-indigo-600 text-white';
          node.setAttribute('aria-current', 'page');
        }
        node.addEventListener('click', () => config.onChange(page));
        controls.appendChild(node);
      };
      button(english ? 'First' : 'اول', 1);
      button(english ? 'Previous' : 'قبلی', current - 1);
      const first = Math.max(1, Math.min(current - 2, pages - 4));
      for (let page = first; page <= Math.min(pages, first + 4); page++) button(number(page), page);
      button(english ? 'Next' : 'بعدی', current + 1);
      button(english ? 'Last' : 'آخر', pages);
      footer.append(info, controls);
    },
    paginate(table, config) {
      if (typeof table === 'string') table = document.querySelector(table);
      const box = table?.closest('.admin-ui-table');
      if (!table || !box) return;
      const footer = box.querySelector('[data-panel-pagination]');
      if (!footer) return;
      const rows = config.rows || [], size = Math.max(1, Math.min(100, Number(config.perPage) || 10));
      let current = 1, sortColumn = null, sortDirection = 1;
      const controls = [...document.querySelectorAll('[data-panel-filter]')].filter(control => control.dataset.panelFilter === table.id);
      const headings = [...table.querySelectorAll('[data-panel-sort]')];
      const renderPage = () => {
        const start = (current - 1) * size;
        let filtered = rows.filter(row => controls.every(control => !control.value || String(row.cells?.[Number(control.dataset.panelFilterColumn)]?.value ?? '') === control.value));
        if (sortColumn !== null) {
          table.dataset.adminIdentityActive = '0';
          filtered = filtered.slice().sort((a, b) => {
            const av = a.cells?.[sortColumn]?.value ?? '', bv = b.cells?.[sortColumn]?.value ?? '';
            const numeric = table.querySelector('[data-panel-sort="' + sortColumn + '"]')?.dataset.panelSortType === 'number';
            return (numeric ? (Number(av) || 0) - (Number(bv) || 0) : String(av).localeCompare(String(bv), document.documentElement.lang || undefined, { numeric: true })) * sortDirection;
          });
        }
        const visible = this.page(table, filtered, start, start + size, renderPage, row => row.cells?.[0]?.value);
        this.setRows(table, visible.length ? visible : [{ cells: [{ value: config.emptyMessage || '', attributes: 'colspan="' + table.tHead.rows[0].cells.length + '" class="py-12 text-center text-gray-400"' }] }], { pageOffset: start });
        this.renderPagination(footer, { total: filtered.length, perPage: size, page: current, onChange: next => { current = next; renderPage(); } });
      };
      controls.forEach(control => control.addEventListener('change', () => { current = 1; renderPage(); }));
      headings.forEach(button => button.addEventListener('click', () => {
        const column = Number(button.dataset.panelSort);
        sortDirection = sortColumn === column ? -sortDirection : 1;
        sortColumn = column; current = 1;
        headings.forEach(heading => {
          const selected = heading === button;
          heading.closest('th').setAttribute('aria-sort', selected ? (sortDirection === 1 ? 'ascending' : 'descending') : 'none');
          const icon = heading.querySelector('[data-panel-sort-icon]');
          if (icon) icon.textContent = selected ? (sortDirection === 1 ? '↑' : '↓') : '↕';
        });
        renderPage();
      }));
      renderPage();
    },
    setRows(table, rows, options = {}) {
      if (typeof table === 'string') table = document.querySelector(table);
      if (!table?.tBodies[0]) return;
      if (options.onIdentitySort) tableSources.set(table, { render: options.onIdentitySort });
      if (options.pageOffset != null) table.dataset.adminPageOffset = String(options.pageOffset);
      table.tBodies[0].innerHTML = rows.map(row => this.rowHTML(row.cells || row, row.attributes)).join('');
      const section = table.closest('#mainContent > .section');
      if (section) {
        normalizeTable(table, section);
        applySharedAppearance(section);
      }
      enhanceIdentityColumn(table);
      enhanceSortHeaders(table);
    },
    // Sort the complete filtered dataset before taking the requested page.
    // Keep page renderers as the source of truth; never collect data from DOM rows.
    page(table, rows, start, end, render, identity = (row) => row.id ?? row.user_id, project = row => row.cells || []) {
      if (typeof table === 'string') table = document.querySelector(table);
      if (!table) return rows.slice(start, end);
      tableSources.set(table, { render });
      enhanceSortHeaders(table);
      table.dataset.adminPageOffset = String(start);
      table.dataset.adminIdentityActive ??= '1';
      table.dataset.adminIdentityDirection ||= 'asc';
      let ordered = rows;
      if (table.dataset.adminSortColumn != null && table.dataset.adminIdentityActive === '0') {
        const column = Number(table.dataset.adminSortColumn);
        const values = new Map(rows.map(row => [row, projectedValues(project, row)[column]]));
        ordered = rows.slice().sort((a,b) => compareValues(values.get(a), values.get(b), table.dataset.adminSortDirection));
      } else if (table.dataset.adminIdentityActive === '1') {
        const factor = table.dataset.adminIdentityDirection === 'desc' ? -1 : 1;
        ordered = rows.slice().sort((a, b) => {
          const av = identity(a), bv = identity(b);
          if (av == null) return bv == null ? 0 : 1;
          if (bv == null) return -1;
          return String(av).localeCompare(String(bv), undefined, { numeric: true }) * factor;
        });
      }
      return ordered.slice(start, end);
    },
    cellsHTML(cells, heading = false) {
      return cells.map((cell) => {
        if (cell && typeof cell === 'object' && 'fragment' in cell) return cell.fragment || '';
        cell = cell && typeof cell === 'object' ? cell : { value: cell };
        return tablePart(heading || cell.tag === 'th' ? 'heading' : 'cell', {
          attributes: cell.attributes || '',
          html: cell.html ?? tableText(cell.value),
        });
      }).join('');
    },
    rowHTML(cells, attributes = '') {
      return tablePart('row', { cells: this.cellsHTML(cells), attributes });
    },
    createRow(attributes = '') {
      const fragment = document.createElement('template');
      fragment.innerHTML = this.tableHTML({ columns: [], rows: [{ cells: [], attributes }] });
      return fragment.content.querySelector('tbody tr');
    },
    createCell(heading = false) {
      const row = this.createRow();
      row.innerHTML = this.cellsHTML([{ value: '' }], heading);
      return row.firstElementChild;
    },
    tableHTML(config) {
      return tablePart('table', {
        attributes: config.attributes ?? 'class="w-full"',
        headAttributes: config.headAttributes ?? 'class="border-b bg-gray-50"',
        headerRowAttributes: config.headerRowAttributes ?? '',
        bodyAttributes: config.bodyAttributes ?? 'class="divide-y text-sm"',
        columns: this.cellsHTML(config.columns || [], true),
        rows: (config.rows || []).map((row) => this.rowHTML(row.cells || row, row.attributes)).join(''),
      });
    },
    render(target, config) {
      if (typeof target === 'string') target = document.querySelector(target);
      if (!target) return;
      const fragment = document.createElement('template');
      fragment.innerHTML = this.tableHTML(config);
      const table = fragment.content.querySelector('table');
      if (target.tagName === 'TABLE') target.replaceChildren(...table.childNodes);
      else target.replaceChildren(table);
    },
  };
  const template = (id) => document.getElementById(id);

  function clone(id) {
    const source = template(id);
    return source ? source.content.firstElementChild.cloneNode(true) : null;
  }

  function fill(node, values) {
    Object.entries(values || {}).forEach(([slot, value]) => {
      const target = node.querySelector(`[data-slot="${slot}"]`);
      if (!target || value == null) return;
      if (value instanceof Node) target.appendChild(value);
      else if (Array.isArray(value))
        value.forEach((item) => item instanceof Node && target.appendChild(item));
      else target.innerHTML = String(value);
    });
    return node;
  }

  function render(id, values) {
    const node = clone(id);
    return node ? fill(node, values) : null;
  }

  function inheritAttributes(target, source, excluded) {
    excluded = excluded || [];
    if (!target || !source) return target;
    Array.from(source.attributes || []).forEach((attribute) => {
      if (excluded.includes(attribute.name)) return;
      if (attribute.name === 'class')
        attribute.value
          .split(/\s+/)
          .filter(Boolean)
          .forEach((name) => target.classList.add(name));
      else target.setAttribute(attribute.name, attribute.value);
    });
    return target;
  }

  window.AdminPanelTemplates = {
    pageHeader: (data) => render('adminPageHeaderTemplate', data),
    organizationTabs: (data) => render('adminOrganizationTabsTemplate', data),
    filters: (data) => render('adminFiltersTemplate', data),
    table: (data) => render('adminTableTemplate', data),
    helpButton: (data) => render('adminHelpButtonTemplate', data),
    mount(section, config) {
      if (typeof section === 'string') section = document.getElementById(section);
      if (!section || !config) return;
      ['pageHeader', 'organizationTabs', 'filters', 'table'].forEach((key) => {
        if (!config[key]) return;
        const node = this[key](config[key]);
        if (node) section.appendChild(node);
      });
    },
    register(sectionId, config) {
      const mount = () => this.mount(sectionId, config);
      if (document.readyState === 'loading')
        document.addEventListener('DOMContentLoaded', mount, { once: true });
      else mount();
    },
  };

  function replaceBox(box, templateId, slotName, children) {
    if (!box || box.dataset.adminUiTemplate) return null;
    const rendered = render(templateId, { [slotName]: children });
    if (!rendered) return null;
    inheritAttributes(rendered, box, ['data-admin-ui-template']);
    rendered.dataset.adminUiTemplate = templateId;
    box.replaceWith(rendered);
    return rendered;
  }

  function normalizeHeader(section) {
    const heading = section.querySelector(':scope > div h1');
    if (!heading) return;
    let box = heading.parentElement;
    while (box && box.parentElement !== section) box = box.parentElement;
    if (!box || box.dataset.adminUiTemplate) return;
    const parts = Array.from(box.children);
    const headingPart = parts.find((part) => part.contains(heading));
    const actionParts = parts.filter((part) => part !== headingPart);
    const rendered = clone('adminPageHeaderTemplate');
    if (!rendered) return;
    rendered.dataset.adminUiTemplate = 'adminPageHeaderTemplate';
    inheritAttributes(rendered, box, ['data-admin-ui-template']);
    rendered.querySelector('[data-slot="heading"]').appendChild(headingPart);
    const actions = rendered.querySelector('[data-slot="actions"]');
    const actionContainer =
      actionParts.length === 1 && !actionParts[0].matches('button,a') ? actionParts[0] : null;
    if (actionContainer) inheritAttributes(actions, actionContainer, ['class']);
    actionParts.forEach((part) => {
      if (part.matches('button,a')) actions.appendChild(part);
      else while (part.firstChild) actions.appendChild(part.firstChild);
    });
    box.replaceWith(rendered);
  }

  function normalizeTabs(section) {
    const tabs = section.querySelector('[id$="BranchTabs"]');
    if (!tabs) return;
    let box = tabs.parentElement;
    while (box && box.parentElement !== section) box = box.parentElement;
    if (!box || box.dataset.adminUiTemplate || box.closest('.admin-ui-organization-tabs')) return;
    replaceBox(box, 'adminOrganizationTabsTemplate', 'tabs', tabs);
  }

  function normalizeFilters(section) {
    const search = Array.from(section.querySelectorAll('input')).find(
      (input) =>
        /search/i.test(input.id) || input.type === 'search' || /جستجو/.test(input.placeholder || '')
    );
    if (!search || search.closest('table,.admin-ui-filters')) return;
    let box = search.parentElement;
    while (box && box.parentElement !== section) box = box.parentElement;
    if (
      !box ||
      box.querySelector('h1') ||
      box.querySelector('table') ||
      box.dataset.adminUiTemplate
    )
      return;
    const content = document.createDocumentFragment();
    while (box.firstChild) content.appendChild(box.firstChild);
    replaceBox(box, 'adminFiltersTemplate', 'filters', content);
  }

  function normalizeTable(table, section) {
    if (!table || table.closest('.admin-ui-table')) return;
    let box = table.parentElement;
    while (
      box &&
      box !== section &&
      !(
        box.classList.contains('overflow-hidden') &&
        (box.classList.contains('shadow') || box.classList.contains('bg-white'))
      )
    )
      box = box.parentElement;
    if (!box || box === section || box.dataset.adminUiTemplate) return;
    const scroller = table.parentElement;
    const pagination = Array.from(box.children).find(
      (child) =>
        child !== scroller &&
        (child.id.toLowerCase().includes('pagination') || child.querySelector('[id*="Pagination"]'))
    );
    const extras = Array.from(box.children).filter(
      (child) => child !== scroller && child !== pagination
    );
    const rendered = clone('adminTableTemplate');
    if (!rendered) return;
    inheritAttributes(rendered, box, ['data-admin-ui-template']);
    rendered.dataset.adminUiTemplate = 'adminTableTemplate';
    const tableSlot = rendered.querySelector('[data-slot="table"]');
    inheritAttributes(tableSlot, scroller, ['class']);
    Array.from(scroller.childNodes).forEach((child) => tableSlot.appendChild(child));
    extras.forEach((extra) => tableSlot.appendChild(extra));
    const paginationSlot = rendered.querySelector('[data-slot="pagination"]');
    if (pagination) {
      inheritAttributes(paginationSlot, pagination);
      while (pagination.firstChild) paginationSlot.appendChild(pagination.firstChild);
    } else paginationSlot.hidden = true;
    box.replaceWith(rendered);
  }

  function applySharedAppearance(section) {
    section
      .querySelectorAll(
        '.admin-ui-header [data-slot="actions"] button,.admin-ui-header [data-slot="actions"] a'
      )
      .forEach((button, index) => {
        button.classList.add(
          'inline-flex',
          'items-center',
          'gap-2',
          'rounded-2xl',
          'px-5',
          'py-3',
          'transition'
        );
        const isExport = /^\s*export/i.test(button.getAttribute('onclick') || '');
        button.classList.toggle('admin-ui-primary-action', index === 0 && !isExport);
      });
    section
      .querySelectorAll('.admin-ui-organization-tabs button')
      .forEach((button) =>
        button.classList.add(
          'rounded-2xl',
          'px-5',
          'py-2.5',
          'text-sm',
          'font-medium',
          'transition'
        )
      );
    section
      .querySelectorAll('.admin-ui-filters input,.admin-ui-filters select')
      .forEach((control) =>
        control.classList.add(
          'w-full',
          'rounded-2xl',
          'border',
          'border-gray-300',
          'px-4',
          'py-3',
          'outline-none'
        )
      );
    section
      .querySelectorAll('.admin-ui-table th')
      .forEach((cell) => cell.classList.add('px-5', 'py-5', 'text-right', 'font-medium'));
    section
      .querySelectorAll('.admin-ui-table tbody')
      .forEach((body) => body.classList.add('divide-y', 'text-sm'));
    section
      .querySelectorAll('.admin-ui-table [data-slot="pagination"] button')
      .forEach((button) => button.classList.add('rounded-lg', 'border', 'px-3', 'py-1.5'));
  }

  function rowIdentity(row) {
    const direct =
      row.dataset.id || row.dataset.rowId || row.dataset.entityId || row.dataset.itemId;
    if (direct) return direct;
    const rowId = String(row.id || '').match(/(?:^|[-_])(\d+)$/);
    if (rowId) return rowId[1];
    for (const element of row.querySelectorAll('*')) {
      for (const [key, value] of Object.entries(element.dataset || {})) {
        if (value && (key === 'id' || /Id$/.test(key))) return value;
      }
    }
    for (const element of row.querySelectorAll('[onclick]')) {
      const match = String(element.getAttribute('onclick') || '').match(
        /(?:edit|view|details|delete|remove|open|toggle|status|inline)\w*\(\s*['"]?(\d+)/i
      );
      if (match) return match[1];
    }
    return null;
  }

  function dataRows(body) {
    return Array.from(body.rows).filter(
      (row) => !(row.cells.length === 1 && row.cells[0].hasAttribute('colspan'))
    );
  }

  function sortIdentityRows(table, direction) {
    const body = table.tBodies[0];
    if (!body) return;
    const rows = Array.from(body.rows),
      groups = [];
    rows.forEach((row) => {
      if (row.cells.length === 1 && row.cells[0].hasAttribute('colspan') && groups.length)
        groups[groups.length - 1].push(row);
      else groups.push([row]);
    });
    if (table.dataset.adminColumnKind === 'row') groups.reverse();
    else
      groups.sort((a, b) => {
        const rawA = rowIdentity(a[0]),
          rawB = rowIdentity(b[0]);
        const av = rawA === null ? NaN : Number(rawA),
          bv = rawB === null ? NaN : Number(rawB);
        if (!Number.isFinite(av) && !Number.isFinite(bv)) return 0;
        if (!Number.isFinite(av)) return 1;
        if (!Number.isFinite(bv)) return -1;
        return (av - bv) * (direction === 'desc' ? -1 : 1);
      });
    groups.forEach((group) => group.forEach((row) => body.appendChild(row)));
    table.dataset.adminIdentityDirection = direction;
  }

  function updateIdentityCells(table, kind) {
    Array.from(table.tBodies).forEach((body) => {
      let index = Number(table.dataset.adminPageOffset || 0);
      dataRows(body).forEach((row) => {
        index++;
        let cell = row.querySelector(':scope > [data-admin-identity-cell]');
        if (!cell) {
          cell = window.AdminPanelTable.createCell();
          cell.dataset.adminIdentityCell = '1';
          cell.className = 'whitespace-nowrap px-5 py-4 font-medium text-gray-500';
          row.prepend(cell);
        }
        cell.textContent =
          kind === 'id' ? rowIdentity(row) || '—' : Number(index).toLocaleString('fa-IR');
      });
      Array.from(body.rows)
        .filter((row) => row.cells.length === 1 && row.cells[0].hasAttribute('colspan'))
        .forEach((row) => {
          const cell = row.cells[0];
          if (!cell.dataset.adminIdentityColspan) {
            cell.dataset.adminIdentityColspan = '1';
            cell.colSpan = Math.max(1, Number(cell.colSpan || 1) + 1);
          }
        });
    });
  }

  function enhanceIdentityColumn(table) {
    if (!table.tHead || !table.tBodies.length || table.hasAttribute('data-no-identity-column'))
      return;
    if (table.closest('[data-admin-ui-native],#peVisualContent,#schedulesWeeklyTables')) return;
    const headerRow = table.tHead.rows[table.tHead.rows.length - 1];
    if (!headerRow) return;
    let injectedHeading = headerRow.querySelector(':scope > [data-admin-identity-column]');
    const hasNativeIdentity = Array.from(headerRow.cells).some(
      (cell) =>
        !cell.hasAttribute('data-admin-identity-column') &&
        /^(شناسه|ردیف|ID)$/i.test(cell.textContent.trim())
    );
    if (hasNativeIdentity) return;
    const rows = dataRows(table.tBodies[0]);
    if (!rows.length) return;
    const identities = rows.map(rowIdentity);
    const kind = identities.some((value) => value !== null) ? 'id' : 'row';
    if (!injectedHeading) {
      injectedHeading = window.AdminPanelTable.createCell(true);
      injectedHeading.dataset.adminIdentityColumn = '1';
      injectedHeading.scope = 'col';
      injectedHeading.className = 'whitespace-nowrap px-5 py-5 text-right font-medium';
      headerRow.prepend(injectedHeading);
    }
    injectedHeading.dataset.adminColumnKind = kind;
    table.dataset.adminColumnKind = kind;
    table.dataset.adminIdentityDirection ||= 'asc';
    const direction = table.dataset.adminIdentityDirection || 'asc';
    const identityIsActive = table.dataset.adminIdentityActive !== '0';
    if (kind === 'id') {
      const icon = identityIsActive ? (direction === 'asc' ? '↑' : '↓') : '↕';
      injectedHeading.innerHTML = `<button type="button" class="flex items-center gap-2" aria-label="مرتب‌سازی شناسه">شناسه <span data-admin-identity-sort>${icon}</span></button>`;
      injectedHeading.querySelector('button').onclick = () => {
        const next = table.dataset.adminIdentityDirection === 'asc' ? 'desc' : 'asc';
        table.dataset.adminIdentityActive = '1';
        delete table.dataset.adminSortColumn;
        table.querySelectorAll('[data-panel-auto-sort]').forEach(button => { button.closest('th').setAttribute('aria-sort','none'); button.querySelector('[data-panel-auto-sort-icon]').textContent = '↕'; });
        table.dataset.adminIdentityDirection = next;
        const source = tableSources.get(table);
        if (source) {
          Promise.resolve(source.render()).then(() => enhanceIdentityColumn(table));
          return;
        }
        sortIdentityRows(table, next);
        updateIdentityCells(table, kind);
        table.dataset.adminIdentitySignature = dataRows(table.tBodies[0])
          .map(rowIdentity)
          .join('|');
        injectedHeading.querySelector('[data-admin-identity-sort]').textContent =
          next === 'asc' ? '↑' : '↓';
      };
    } else injectedHeading.textContent = 'ردیف';
    const signature = identities.join('|');
    if (kind === 'id' && table.dataset.adminIdentityActive !== '0') {
      // Non-paginated tables contain the complete dataset in their body.
      if (!tableSources.has(table)) sortIdentityRows(table, direction);
      table.dataset.adminIdentityInitialized = '1';
    }
    table.dataset.adminIdentitySignature = dataRows(table.tBodies[0]).map(rowIdentity).join('|');
    updateIdentityCells(table, kind);
  }

  function enhanceAllIdentityColumns() {
    document
      .querySelectorAll('#mainContent > .section:not([data-admin-ui-native]) table')
      .forEach(table => { enhanceIdentityColumn(table); enhanceSortHeaders(table); });
  }

  function wrapTableRenderers() {
    Object.keys(window)
      .filter((name) => /^render[A-Za-z0-9_]*Table$/.test(name))
      .forEach((name) => {
        const renderer = window[name];
        if (typeof renderer !== 'function' || renderer.adminIdentityWrapped) return;
        const wrapped = function (...args) {
          const result = renderer.apply(this, args);
          if (result && typeof result.finally === 'function') {
            return result.finally(enhanceAllIdentityColumns);
          }
          enhanceAllIdentityColumns();
          return result;
        };
        wrapped.adminIdentityWrapped = true;
        window[name] = wrapped;
      });
    enhanceAllIdentityColumns();
  }

  function normalizeSection(section) {
    if (
      !section ||
      section.dataset.adminUiNormalized ||
      section.hasAttribute('data-admin-ui-native')
    )
      return;
    const panels = [...section.querySelectorAll(':scope > [data-panel-content]')];
    (panels.length ? panels : [section]).forEach(scope => {
      normalizeHeader(scope);
      normalizeTabs(scope);
      normalizeFilters(scope);
      Array.from(scope.querySelectorAll('table')).forEach(table => normalizeTable(table, scope));
    });
    applySharedAppearance(section);
    section.dataset.adminUiNormalized = '1';
    section.dispatchEvent(new CustomEvent('admin-ui:ready', { bubbles: true }));
  }

  function normalizeAll() {
    document
      .querySelectorAll('#mainContent > .section[id]:not([data-admin-ui-native])')
      .forEach(normalizeSection);
    enhanceAllIdentityColumns();
  }

  function initializeTemplates() {
    document.querySelectorAll('[data-panel-pagination-source]').forEach(source => {
      const table = source.closest('.admin-ui-table')?.querySelector('table');
      if (!table || source.dataset.initialized) return;
      window.AdminPanelTable.paginate(table, JSON.parse(source.textContent));
      source.dataset.initialized = '1';
    });
    normalizeAll();
    // پس از ثبت handlerهای صفحه، رندرکننده‌های جدول فقط یک‌بار بسته‌بندی می‌شوند.
    // queueMicrotask اجرای آن را به انتهای همان رویداد، پیش از repaint، منتقل می‌کند.
    window.queueMicrotask(wrapTableRenderers);
    // Deactivate identity sorting BEFORE a page's native column handler renders.
    document.addEventListener('click', (event) => {
      const heading = event.target.closest?.('#mainContent th:not([data-admin-identity-column])');
      if (heading) {
        const table = heading.closest('table');
        table.dataset.adminIdentityActive = '0';
        if (!event.target.closest('[data-panel-auto-sort]')) {
          delete table.dataset.adminSortColumn;
          table.querySelectorAll('[data-panel-auto-sort]').forEach(button => { button.closest('th').setAttribute('aria-sort','none'); button.querySelector('[data-panel-auto-sort-icon]').textContent = '↕'; });
        }
      }
    }, true);
    // رویداد در فاز bubble اجرا می‌شود؛ بنابراین handler خود صفحه ابتدا جدول را
    // بازسازی می‌کند و ستون مشترک پیش از repaint همان فریم برمی‌گردد.
    const refresh = (event) => {
      if (!event.target.closest?.('#mainContent')) return;
      enhanceAllIdentityColumns();
    };
    document.addEventListener('click', refresh);
    document.addEventListener('change', refresh);
    document.addEventListener('input', refresh);
  }

  if (document.readyState === 'loading')
    document.addEventListener('DOMContentLoaded', initializeTemplates, { once: true });
  else initializeTemplates();
})();
