(function () {
    const template = id => document.getElementById(id);

    function clone(id) {
        const source = template(id);
        return source ? source.content.firstElementChild.cloneNode(true) : null;
    }

    function fill(node, values) {
        Object.entries(values || {}).forEach(([slot, value]) => {
            const target = node.querySelector(`[data-slot="${slot}"]`);
            if (!target || value == null) return;
            if (value instanceof Node) target.appendChild(value);
            else if (Array.isArray(value)) value.forEach(item => item instanceof Node && target.appendChild(item));
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
        Array.from(source.attributes || []).forEach(attribute => {
            if (excluded.includes(attribute.name)) return;
            if (attribute.name === 'class') attribute.value.split(/\s+/).filter(Boolean).forEach(name => target.classList.add(name));
            else target.setAttribute(attribute.name, attribute.value);
        });
        return target;
    }

    window.AdminPanelTemplates = {
        pageHeader: data => render('adminPageHeaderTemplate', data),
        organizationTabs: data => render('adminOrganizationTabsTemplate', data),
        filters: data => render('adminFiltersTemplate', data),
        table: data => render('adminTableTemplate', data),
        helpButton: data => render('adminHelpButtonTemplate', data),
        mount(section, config) {
            if (typeof section === 'string') section = document.getElementById(section);
            if (!section || !config) return;
            ['pageHeader', 'organizationTabs', 'filters', 'table'].forEach(key => {
                if (!config[key]) return;
                const node = this[key](config[key]);
                if (node) section.appendChild(node);
            });
        },
        register(sectionId, config) {
            const mount = () => this.mount(sectionId, config);
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mount, { once: true });
            else mount();
        }
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
        const headingPart = parts.find(part => part.contains(heading));
        const actionParts = parts.filter(part => part !== headingPart);
        const rendered = clone('adminPageHeaderTemplate');
        if (!rendered) return;
        rendered.dataset.adminUiTemplate = 'adminPageHeaderTemplate';
        inheritAttributes(rendered, box, ['data-admin-ui-template']);
        rendered.querySelector('[data-slot="heading"]').appendChild(headingPart);
        const actions = rendered.querySelector('[data-slot="actions"]');
        const actionContainer = actionParts.length === 1 && !actionParts[0].matches('button,a') ? actionParts[0] : null;
        if (actionContainer) inheritAttributes(actions, actionContainer, ['class']);
        actionParts.forEach(part => {
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
        const search = Array.from(section.querySelectorAll('input')).find(input => /search/i.test(input.id) || input.type === 'search' || /جستجو/.test(input.placeholder || ''));
        if (!search || search.closest('table,.admin-ui-filters')) return;
        let box = search.parentElement;
        while (box && box.parentElement !== section) box = box.parentElement;
        if (!box || box.querySelector('h1') || box.querySelector('table') || box.dataset.adminUiTemplate) return;
        const content = document.createDocumentFragment();
        while (box.firstChild) content.appendChild(box.firstChild);
        replaceBox(box, 'adminFiltersTemplate', 'filters', content);
    }

    function normalizeTable(table, section) {
        if (!table || table.closest('.admin-ui-table')) return;
        let box = table.parentElement;
        while (box && box !== section && !(box.classList.contains('overflow-hidden') && (box.classList.contains('shadow') || box.classList.contains('bg-white')))) box = box.parentElement;
        if (!box || box === section || box.dataset.adminUiTemplate) return;
        const scroller = table.parentElement;
        const pagination = Array.from(box.children).find(child => child !== scroller && (child.id.toLowerCase().includes('pagination') || child.querySelector('[id*="Pagination"]')));
        const extras = Array.from(box.children).filter(child => child !== scroller && child !== pagination);
        const rendered = clone('adminTableTemplate');
        if (!rendered) return;
        inheritAttributes(rendered, box, ['data-admin-ui-template']);
        rendered.dataset.adminUiTemplate = 'adminTableTemplate';
        const tableSlot = rendered.querySelector('[data-slot="table"]');
        inheritAttributes(tableSlot, scroller, ['class']);
        Array.from(scroller.childNodes).forEach(child => tableSlot.appendChild(child));
        extras.forEach(extra => tableSlot.appendChild(extra));
        const paginationSlot = rendered.querySelector('[data-slot="pagination"]');
        if (pagination) {
            inheritAttributes(paginationSlot, pagination);
            while (pagination.firstChild) paginationSlot.appendChild(pagination.firstChild);
        }
        else paginationSlot.hidden = true;
        box.replaceWith(rendered);
    }

    function applySharedAppearance(section) {
        section.querySelectorAll('.admin-ui-header [data-slot="actions"] button,.admin-ui-header [data-slot="actions"] a').forEach((button, index) => {
            button.classList.add('inline-flex', 'items-center', 'gap-2', 'rounded-2xl', 'px-5', 'py-3', 'transition');
            if (index === 0) button.classList.add('admin-ui-primary-action');
        });
        section.querySelectorAll('.admin-ui-organization-tabs button').forEach(button => button.classList.add('rounded-2xl', 'px-5', 'py-2.5', 'text-sm', 'font-medium', 'transition'));
        section.querySelectorAll('.admin-ui-filters input,.admin-ui-filters select').forEach(control => control.classList.add('w-full', 'rounded-2xl', 'border', 'border-gray-300', 'px-4', 'py-3', 'outline-none'));
        section.querySelectorAll('.admin-ui-table th').forEach(cell => cell.classList.add('px-5', 'py-5', 'text-right', 'font-medium'));
        section.querySelectorAll('.admin-ui-table tbody').forEach(body => body.classList.add('divide-y', 'text-sm'));
        section.querySelectorAll('.admin-ui-table [data-slot="pagination"] button').forEach(button => button.classList.add('rounded-lg', 'border', 'px-3', 'py-1.5'));
    }

    function rowIdentity(row) {
        const direct = row.dataset.id || row.dataset.rowId || row.dataset.entityId || row.dataset.itemId;
        if (direct) return direct;
        const rowId = String(row.id || '').match(/(?:^|[-_])(\d+)$/);
        if (rowId) return rowId[1];
        for (const element of row.querySelectorAll('*')) {
            for (const [key, value] of Object.entries(element.dataset || {})) {
                if (value && (key === 'id' || /Id$/.test(key))) return value;
            }
        }
        for (const element of row.querySelectorAll('[onclick]')) {
            const match = String(element.getAttribute('onclick') || '').match(/(?:edit|view|details|delete|remove|open|toggle|status|inline)\w*\(\s*['"]?(\d+)/i);
            if (match) return match[1];
        }
        return null;
    }

    function dataRows(body) {
        return Array.from(body.rows).filter(row => !(row.cells.length === 1 && row.cells[0].hasAttribute('colspan')));
    }

    function sortIdentityRows(table, direction) {
        const body = table.tBodies[0];
        if (!body) return;
        const rows = Array.from(body.rows), groups = [];
        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].hasAttribute('colspan') && groups.length) groups[groups.length - 1].push(row);
            else groups.push([row]);
        });
        if (table.dataset.adminColumnKind === 'row') groups.reverse();
        else groups.sort((a, b) => {
                const rawA = rowIdentity(a[0]), rawB = rowIdentity(b[0]);
                const av = rawA === null ? NaN : Number(rawA), bv = rawB === null ? NaN : Number(rawB);
                if (!Number.isFinite(av) && !Number.isFinite(bv)) return 0;
                if (!Number.isFinite(av)) return 1;
                if (!Number.isFinite(bv)) return -1;
                return (av - bv) * (direction === 'desc' ? -1 : 1);
            });
        groups.forEach(group => group.forEach(row => body.appendChild(row)));
        table.dataset.adminIdentityDirection = direction;
    }

    function updateIdentityCells(table, kind) {
        Array.from(table.tBodies).forEach(body => {
            let index = 0;
            dataRows(body).forEach(row => {
                index++;
                let cell = row.querySelector(':scope > [data-admin-identity-cell]');
                if (!cell) {
                    cell = document.createElement('td');
                    cell.dataset.adminIdentityCell = '1';
                    cell.className = 'whitespace-nowrap px-5 py-4 font-medium text-gray-500';
                    row.prepend(cell);
                }
                cell.textContent = kind === 'id' ? (rowIdentity(row) || '—') : Number(index).toLocaleString('fa-IR');
            });
            Array.from(body.rows).filter(row => row.cells.length === 1 && row.cells[0].hasAttribute('colspan')).forEach(row => {
                const cell = row.cells[0];
                if (!cell.dataset.adminIdentityColspan) {
                    cell.dataset.adminIdentityColspan = '1';
                    cell.colSpan = Math.max(1, Number(cell.colSpan || 1) + 1);
                }
            });
        });
    }

    function enhanceIdentityColumn(table) {
        if (!table.tHead || !table.tBodies.length || table.hasAttribute('data-no-identity-column')) return;
        if (table.closest('[data-admin-ui-native],#peVisualContent,#schedulesWeeklyTables')) return;
        const headerRow = table.tHead.rows[table.tHead.rows.length - 1];
        if (!headerRow) return;
        let injectedHeading = headerRow.querySelector(':scope > [data-admin-identity-column]');
        const hasNativeIdentity = Array.from(headerRow.cells).some(cell => !cell.hasAttribute('data-admin-identity-column') && /^(شناسه|ردیف|ID)$/i.test(cell.textContent.trim()));
        if (hasNativeIdentity) return;
        const rows = dataRows(table.tBodies[0]);
        if (!rows.length) return;
        const identities = rows.map(rowIdentity);
        const kind = identities.some(value => value !== null) ? 'id' : 'row';
        if (!injectedHeading) {
            injectedHeading = document.createElement('th');
            injectedHeading.dataset.adminIdentityColumn = '1';
            injectedHeading.scope = 'col';
            injectedHeading.className = 'whitespace-nowrap px-5 py-5 text-right font-medium';
            headerRow.prepend(injectedHeading);
        }
        injectedHeading.dataset.adminColumnKind = kind;
        table.dataset.adminColumnKind = kind;
        const direction = table.dataset.adminIdentityDirection || 'asc';
        const identityIsActive = table.dataset.adminIdentityActive !== '0';
        if (kind === 'id') {
            const icon = identityIsActive ? (direction === 'asc' ? '↑' : '↓') : '↕';
            injectedHeading.innerHTML = `<button type="button" class="flex items-center gap-2" aria-label="مرتب‌سازی شناسه">شناسه <span data-admin-identity-sort>${icon}</span></button>`;
            injectedHeading.querySelector('button').onclick = () => {
                const next = table.dataset.adminIdentityDirection === 'asc' ? 'desc' : 'asc';
                table.dataset.adminIdentityActive = '1';
                sortIdentityRows(table, next);
                updateIdentityCells(table, kind);
                table.dataset.adminIdentitySignature = dataRows(table.tBodies[0]).map(rowIdentity).join('|');
                injectedHeading.querySelector('[data-admin-identity-sort]').textContent = next === 'asc' ? '↑' : '↓';
            };
        } else injectedHeading.textContent = 'ردیف';
        const signature = identities.join('|');
        if (kind === 'id' && table.dataset.adminIdentityInitialized !== '1') {
            sortIdentityRows(table, 'asc');
            table.dataset.adminIdentityDirection = 'asc';
            table.dataset.adminIdentityActive = '1';
            table.dataset.adminIdentityInitialized = '1';
        }
        table.dataset.adminIdentitySignature = dataRows(table.tBodies[0]).map(rowIdentity).join('|');
        updateIdentityCells(table, kind);
    }

    function enhanceAllIdentityColumns() {
        document.querySelectorAll('#mainContent > .section:not([data-admin-ui-native]) table').forEach(enhanceIdentityColumn);
    }

    function wrapTableRenderers() {
        Object.keys(window).filter(name => /^render[A-Za-z0-9_]*Table$/.test(name)).forEach(name => {
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
        if (!section || section.dataset.adminUiNormalized || section.hasAttribute('data-admin-ui-native')) return;
        normalizeHeader(section);
        normalizeTabs(section);
        normalizeFilters(section);
        Array.from(section.querySelectorAll('table')).forEach(table => normalizeTable(table, section));
        applySharedAppearance(section);
        section.dataset.adminUiNormalized = '1';
        section.dispatchEvent(new CustomEvent('admin-ui:ready', { bubbles: true }));
    }

    function normalizeAll() {
        document.querySelectorAll('#mainContent > .section[id]:not([data-admin-ui-native])').forEach(normalizeSection);
        enhanceAllIdentityColumns();
    }

    function initializeTemplates() {
        normalizeAll();
        // پس از ثبت handlerهای صفحه، رندرکننده‌های جدول فقط یک‌بار بسته‌بندی می‌شوند.
        // queueMicrotask اجرای آن را به انتهای همان رویداد، پیش از repaint، منتقل می‌کند.
        window.queueMicrotask(wrapTableRenderers);
        // رویداد در فاز bubble اجرا می‌شود؛ بنابراین handler خود صفحه ابتدا جدول را
        // بازسازی می‌کند و ستون مشترک پیش از repaint همان فریم برمی‌گردد.
        const refresh=event=>{
            if (!event.target.closest?.('#mainContent')) return;
            const nativeHeading = event.type === 'click'
                ? event.target.closest('th:not([data-admin-identity-column])')
                : null;
            if (nativeHeading) {
                const section = nativeHeading.closest('#mainContent > .section');
                (section || document).querySelectorAll('table[data-admin-identity-initialized="1"]').forEach(table => {
                    table.dataset.adminIdentityActive = '0';
                });
            }
            enhanceAllIdentityColumns();
        };
        document.addEventListener('click',refresh);
        document.addEventListener('change',refresh);
        document.addEventListener('input',refresh);
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeTemplates, { once: true });
    else initializeTemplates();
})();
