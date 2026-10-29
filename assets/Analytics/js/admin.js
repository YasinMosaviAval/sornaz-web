function showOrganizationPage(id) {
  if (id === 'organization-holidays') {
    window.setHolidayLeaveMode?.('organization');
    showSection('availability-exceptions');
  } else showSection(id);
}
function showStaffPage(id) {
  if (id === 'employee-leaves') {
    window.setHolidayLeaveMode?.('member');
    showSection('availability-exceptions');
  } else showSection(id);
}
function panelPageHeading(section) {
  return section?.querySelector('h1:not(:is([role="dialog"],[id$="Modal"],[contenteditable],article,.prose) h1)') || null;
}

function hidePanelIntroduction(section) {
  if (!section) return;
  const heading = panelPageHeading(section);
  const elements = [...section.querySelectorAll('[data-panel-description]')];
  if (heading) {
    elements.push(heading);
    if (heading.nextElementSibling?.tagName === 'P') elements.push(heading.nextElementSibling);
  }
  elements.forEach(element => element.classList.add('hidden'));
  elements.forEach(element => {
    let parent = element.parentElement;
    while (parent && parent !== section && !parent.matches('nav,aside,form')) {
      if (![...parent.children].every(child => child.classList.contains('hidden') || ['STYLE', 'SCRIPT'].includes(child.tagName))) break;
      parent.classList.add('hidden');
      parent = parent.parentElement;
    }
  });
  section.querySelectorAll('.admin-ui-header').forEach(header => {
    if (!header.querySelector('[data-slot="actions"] :is(button,a,input,select)')) header.classList.add('hidden');
  });
}

function panelWorkspaceTitle(id) {
  const english = document.documentElement.lang.startsWith('en');
  if (id === 'polls') return english ? 'Polls' : 'نظرسنجی‌ها';
  if (id === 'database-management') return english ? 'Database management' : 'مدیریت دیتابیس';
  const staffAccessPage = ['teachers', 'member-schedules'].includes(id) || (id === 'availability-exceptions' && window.holidayLeaveMode === 'member');
  if (staffAccessPage && document.getElementById(id)?.querySelector('.panel-page-tabs button[onclick*="roles"]')) {
    return english ? 'Users' : 'کاربران';
  }
  const groups = [
    [['users', 'roles', 'permissions'], ['کاربران', 'Users']],
    [['posts', 'post-categories', 'media', 'comments', 'post-editor'], ['نوشته ها', 'Posts']],
    [['educations', 'certificates', 'awards', 'experiences', 'events', 'publications', 'badges'], ['سوابق و دستاوردها', 'Records and achievements']],
    [['gallery', 'gallery-cover', 'gallery-logo', 'gallery-intro-video', 'gallery-collection'], ['گالری', 'Gallery']],
    [['branches', 'branch-types', 'availabilities', 'scheduling-rules', ...(window.holidayLeaveMode !== 'member' ? ['availability-exceptions'] : [])], ['سازمان', 'Organization']],
  ];
  return groups.find(([ids]) => ids.includes(id))?.[1][english ? 1 : 0] || '';
}

function updatePanelHeaderActions() {
  const slot = document.getElementById('panelPageActions');
  if (!slot) return;
  slot.classList.add('hidden');
  slot.classList.remove('flex');
}

function panelAddActionLabel(button) {
  const text = button.querySelector(':scope > .panel-add-label')?.textContent || button.textContent;
  let label = (button.dataset.panelAddAccessibleLabel || text || '').trim().replace(/\s+/g, ' ');
  if ((window.adminLocale || document.documentElement.lang || '').startsWith('en')) {
    const dictionary = window.adminUiMap || {};
    Object.keys(dictionary).sort((a, b) => b.length - a.length).forEach(key => {
      label = label.split(key).join(dictionary[key]);
    });
  }
  return label || (document.documentElement.lang.startsWith('en') ? 'Add' : 'افزودن');
}

function isPanelAddAction(button) {
  if (button.matches('[data-no-panel-add-icon],[data-notation-new],[data-post-title],[type="submit"]') || button.closest('[contenteditable],article,.prose,.guide-md')) return false;
  if (button.dataset.panelAddIcon) return true;
  const handler = button.getAttribute('onclick') || '', label = button.textContent.trim();
  const delegatedSave = [...button.attributes].some(attribute =>
    /^data-.*(?:save|submit|confirm)(?:-|$)/i.test(attribute.name) ||
    (/^data-.*action$/i.test(attribute.name) && /^(?:save|submit|confirm|create|register|send|upload|store)(?:-|$)/i.test(attribute.value))
  );
  // A confirmation still needs its explicit wording; this only changes add/open controls.
  if (delegatedSave || button.tagName === 'BUTTON' && button.closest('form') && button.type === 'submit' ||
      /^(?:save|submit|confirm|create|register|send|upload|store|export|filter|showSection)\w*\s*\(/i.test(handler) ||
      button.matches('[data-personal-export],[data-personal-print]') ||
      /(?:خروجی|export\b)/i.test(label)) return false;
  // Stored article titles and ordinary links may themselves begin with “Add” or “New”.
  if (button.tagName === 'A' && !button.matches('[data-panel-page-action],[data-profile-add]') &&
      !/^open(?:Add|New)\w*\s*\(/.test(handler) && !button.querySelector('.fa-plus,.fa-user-plus') &&
      !/\/(?:add|new|create)(?:[/?#]|$)/i.test(button.getAttribute('href') || '')) return false;
  return button.hasAttribute('data-panel-page-action') || button.hasAttribute('data-profile-add') ||
    button.hasAttribute('data-personal-add-range') || button.hasAttribute('data-notation-create-list') ||
    /^open(?:Add|New)\w*\s*\(/.test(handler) ||
    /^(?:\+\s*)?(?:افزودن|add\b|new\b|create\b)/i.test(label) ||
    (/^open\w*\s*\(/.test(handler) && /جدید\s*$/.test(label)) ||
    !!button.querySelector('.fa-plus,.fa-user-plus');
}

function normalizePanelAddActions(root) {
  if (!root) return;
  const buttons = root.matches?.('button,a') ? [root] : [...root.querySelectorAll('button,a')];
  buttons.forEach(button => {
    if (!isPanelAddAction(button)) return;
    if (!button.dataset.panelAddIcon) {
      const explicitLabel = button.getAttribute('aria-label') || button.getAttribute('title');
      if (explicitLabel) button.dataset.panelAddAccessibleLabel = explicitLabel;
    }
    const label = panelAddActionLabel(button);
    if (button.getAttribute('aria-label') !== label) button.setAttribute('aria-label', label);
    if (button.getAttribute('title') !== label) button.setAttribute('title', label);
    if (!button.querySelector(':scope > [data-panel-add-symbol]')) {
      const original = document.createElement('span');
      original.className = 'panel-add-label';
      while (button.firstChild) original.appendChild(button.firstChild);
      button.appendChild(original);
      const symbol = button.dataset.panelAddKind === 'list'
        ? '<path d="M4 5h15M4 10h15M4 15h6M16 13v8M12 17h8"/>'
        : '<path d="M12 5v14M5 12h14"/>';
      button.insertAdjacentHTML('afterbegin', '<svg data-panel-add-symbol viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">' + symbol + '</svg>');
    }
    if (button.dataset.panelAddIcon !== '1') button.dataset.panelAddIcon = '1';
    if (!button.classList.contains('panel-add-icon')) button.classList.add('panel-add-icon');
  });
}

function panelExportActionType(button) {
  if (button.matches('[data-no-panel-export-icon],[data-post-title],[type="submit"]') || button.closest('[contenteditable],article,.prose,.guide-md')) return '';
  const handler = button.getAttribute('onclick') || '';
  if (/^(?:generate|save|submit|confirm)\w*\s*\(/i.test(handler) || button.tagName === 'BUTTON' && button.closest('form') && button.type === 'submit') return '';
  if (button.dataset.panelExportIcon) return button.dataset.panelExportIcon;
  if (button.hasAttribute('data-personal-export')) return 'excel';
  if (button.hasAttribute('data-personal-print')) return 'pdf';
  if (/^export\w*Excel\w*\s*\(/i.test(handler)) return 'excel';
  if (/^(?:export|open)\w*PDF\w*\s*\(/i.test(handler)) return 'pdf';
  const label = button.textContent.trim();
  if (!/^(?:خروجی\s*|export\s+)/i.test(label)) return '';
  if (/(?:اکسل|excel|excell)/i.test(label)) return 'excel';
  return /(?:pdf|پی[\s‌]*دی[\s‌]*اف)/i.test(label) ? 'pdf' : '';
}

function normalizePanelExportActions(root) {
  if (!root) return;
  const buttons = root.matches?.('button,a') ? [root] : [...root.querySelectorAll('button,a')];
  buttons.forEach(button => {
    const type = panelExportActionType(button);
    if (!type) return;
    const english = (window.adminLocale || document.documentElement.lang || '').startsWith('en');
    const label = english ? (type === 'excel' ? 'Export Excel' : 'Export PDF') : (type === 'excel' ? 'خروجی Excell' : 'خروجی PDF');
    if (button.getAttribute('aria-label') !== label) button.setAttribute('aria-label', label);
    if (button.dataset.panelExportLabel !== label) button.dataset.panelExportLabel = label;
    if (!button.querySelector(':scope > [data-panel-export-symbol]')) {
      const original = document.createElement('span');
      original.className = 'panel-export-label';
      while (button.firstChild) original.appendChild(button.firstChild);
      button.appendChild(original);
      const mark = type === 'excel'
        ? '<path d="m7 11 5 7m0-7-5 7M15 11h2m-2 3h2m-2 3h2"/>'
        : '<path d="M5.5 17v-5h1.3a1.3 1.3 0 0 1 0 2.6H5.5m4.8 2.4v-5h1c2.6 0 2.6 5 0 5h-1m5.1 0v-5h2.2m-2.2 2.4h1.8"/>';
      button.insertAdjacentHTML('afterbegin', '<svg data-panel-export-symbol viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9zm0 0v6h6"/>' + mark + '</svg>');
    }
    if (button.dataset.panelExportIcon !== type) button.dataset.panelExportIcon = type;
    if (!button.classList.contains('panel-export-icon')) button.classList.add('panel-export-icon');
  });
}

function panelTableActionType(button) {
  if (!button.closest('table') || button.matches('[data-no-panel-table-icon],[data-post-title],[type="submit"]') ||
      button.closest('form,[contenteditable],article,.prose,.guide-md,[data-admin-inline-expand],[class*="inline-expand"]')) return '';
  // A title or media preview can itself begin with “Edit” or “Delete”.
  if (button.querySelector('img,video,audio,picture,canvas,iframe,strong,small')) return '';
  const handler = button.getAttribute('onclick') || '';
  if (/\b(?:cycle|toggle|change|set|update)\w*Status\w*\s*\(/i.test(handler) || button.hasAttribute('data-status')) return '';
  const text = (button.querySelector(':scope > .panel-table-action-label')?.textContent || button.textContent || '').trim();
  const label = (text || button.dataset.panelTableActionAccessibleLabel || button.getAttribute('aria-label') || button.getAttribute('title') || '').replace(/\s+/g, ' ');
  if (/^(?:پیش[\s‌-]*نمایش|preview)(?:\s|$)/i.test(label)) return 'preview';
  if (/^(?:جزئیات|جزییات|مشاهده[\s‌]+جزئیات|details|view details|information)(?:\s|$)/i.test(label) || /^(?:مشاهده|view)$/i.test(label)) return 'details';
  if (/^(?:ویرایش|edit)(?:\s|$)/i.test(label)) return 'edit';
  if (/^(?:حذف|زباله[\s‌-]*دان|انتقال به زباله[\s‌-]*دان|delete|remove|trash|move to trash)(?:\s|$)/i.test(label)) return 'delete';
  // Icon-only controls already have an accessible title in several table renderers.
  if (!label) {
    if (button.querySelector('.fa-info,.fa-info-circle,.fa-circle-info')) return 'details';
    if (button.querySelector('.fa-edit,.fa-pencil,.fa-pencil-alt,.fa-pen,.fa-pen-to-square')) return 'edit';
    if (button.querySelector('.fa-trash,.fa-trash-alt,.fa-trash-can')) return 'delete';
  }
  return '';
}

function normalizePanelTableActions(root) {
  if (!root) return;
  const buttons = root.matches?.('button,a') ? [root] : [...root.querySelectorAll('button,a')];
  const symbols = {
    details:'<circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10v.1"/>',
    edit:'<path d="m16 3 5 5-12 12-6 1 1-6Zm-1 1 5 5M4 15l5 5"/>',
    delete:'<path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7"/>',
    preview:'<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
  };
  buttons.forEach(button => {
    const type = panelTableActionType(button);
    if (!type) return;
    if (!button.dataset.panelTableAction) {
      const explicitLabel = button.getAttribute('aria-label') || button.getAttribute('title');
      if (explicitLabel) button.dataset.panelTableActionAccessibleLabel = explicitLabel;
    }
    const original = button.querySelector(':scope > .panel-table-action-label');
    let label = ((original?.textContent || button.textContent || '').trim() || button.dataset.panelTableActionAccessibleLabel || '').trim().replace(/\s+/g, ' ');
    const english = (window.adminLocale || document.documentElement.lang || '').startsWith('en');
    if (english) {
      const dictionary = { 'انتقال به زباله‌دان':'Move to trash', 'انتقال به زباله دان':'Move to trash', 'حذف دائمی':'Delete permanently', 'زباله‌دان':'Trash', 'زباله دان':'Trash', 'پیش‌نمایش':'Preview', 'پیش نمایش':'Preview', 'مشاهده جزئیات':'View details', 'جزئیات':'Details', 'جزییات':'Details', 'مشاهده':'View', 'ویرایش':'Edit', 'حذف':'Delete', ...(window.adminUiMap || {}) };
      Object.keys(dictionary).sort((a,b) => b.length-a.length).forEach(key => { label = label.split(key).join(dictionary[key]); });
    }
    if (!label) label = ({details:['جزئیات','Details'],edit:['ویرایش','Edit'],delete:['حذف','Delete'],preview:['پیش‌نمایش','Preview']})[type][english?1:0];
    if (button.getAttribute('aria-label') !== label) button.setAttribute('aria-label', label);
    if (button.dataset.panelTableActionLabel !== label) button.dataset.panelTableActionLabel = label;
    if (!button.querySelector(':scope > [data-panel-table-action-symbol]')) {
      const hidden = document.createElement('span');
      hidden.className = 'panel-table-action-label';
      while (button.firstChild) hidden.appendChild(button.firstChild);
      button.appendChild(hidden);
      button.insertAdjacentHTML('afterbegin', '<svg data-panel-table-action-symbol viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' + symbols[type] + '</svg>');
    }
    if (button.dataset.panelTableAction !== type) {
      button.dataset.panelTableAction = type;
      button.querySelector(':scope > [data-panel-table-action-symbol]').innerHTML = symbols[type];
    }
    if (!button.classList.contains('panel-table-action-icon')) button.classList.add('panel-table-action-icon');
  });
}

function normalizePanelActions(root) {
  normalizePanelExportActions(root);
  normalizePanelAddActions(root);
  normalizePanelTableActions(root);
}

let panelActionTooltipTarget = null;
function hidePanelActionTooltip() {
  if (panelActionTooltipTarget) {
    const ids = (panelActionTooltipTarget.getAttribute('aria-describedby') || '').split(/\s+/).filter(id => id && id !== 'panelActionTooltip');
    if (ids.length) panelActionTooltipTarget.setAttribute('aria-describedby', ids.join(' '));
    else panelActionTooltipTarget.removeAttribute('aria-describedby');
    panelActionTooltipTarget = null;
  }
  const tooltip = document.getElementById('panelActionTooltip');
  if (tooltip) tooltip.hidden = true;
}

function showPanelActionTooltip(button) {
  hidePanelActionTooltip();
  if (!button?.isConnected || !button.getClientRects().length) return;
  let tooltip = document.getElementById('panelActionTooltip');
  if (!tooltip) {
    tooltip = document.createElement('div');
    tooltip.id = 'panelActionTooltip';
    tooltip.className = 'panel-action-tooltip';
    tooltip.setAttribute('role', 'tooltip');
    document.body.appendChild(tooltip);
  }
  const label = button.dataset.panelExportLabel || button.dataset.panelTableActionLabel || button.dataset.panelTooltip;
  const explanation = button.dataset.exportSubscriptionRequired === '1' || button.dataset.panelTableAction ? button.getAttribute('title') : '';
  tooltip.textContent = label + (explanation && explanation !== label ? '\n' + explanation : '');
  tooltip.dir = document.documentElement.dir || (document.documentElement.lang.startsWith('en') ? 'ltr' : 'rtl');
  tooltip.hidden = false;
  tooltip.style.left = '8px';
  tooltip.style.top = '8px';
  const rect = button.getBoundingClientRect(), box = tooltip.getBoundingClientRect();
  const viewportWidth = document.documentElement.clientWidth, viewportHeight = window.innerHeight;
  tooltip.style.left = Math.max(8, Math.min(rect.left + (rect.width - box.width) / 2, viewportWidth - box.width - 8)) + 'px';
  const top = rect.top - box.height - 8;
  tooltip.style.top = Math.max(8, Math.min(top >= 8 ? top : rect.bottom + 8, viewportHeight - box.height - 8)) + 'px';
  const ids = new Set((button.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean));
  ids.add(tooltip.id);
  button.setAttribute('aria-describedby', [...ids].join(' '));
  panelActionTooltipTarget = button;
}

function watchPanelActionTooltips() {
  const control = target => target.closest?.('[data-panel-export-icon],[data-panel-table-action],[data-panel-tooltip]');
  document.addEventListener('pointerover', event => {
    const button = control(event.target);
    if (button && !button.contains(event.relatedTarget)) showPanelActionTooltip(button);
  });
  document.addEventListener('pointerout', event => {
    const button = control(event.target);
    if (button && !button.contains(event.relatedTarget)) hidePanelActionTooltip();
  });
  document.addEventListener('focusin', event => { const button = control(event.target); if (button) showPanelActionTooltip(button); });
  document.addEventListener('focusout', hidePanelActionTooltip);
  document.addEventListener('click', hidePanelActionTooltip);
  document.addEventListener('keydown', event => { if (event.key === 'Escape') hidePanelActionTooltip(); });
  document.addEventListener('scroll', hidePanelActionTooltip, true);
  window.addEventListener('scroll', hidePanelActionTooltip);
  window.addEventListener('resize', hidePanelActionTooltip);
}

let panelActionsWatching = false;
function watchPanelAddActions() {
  if (panelActionsWatching) return;
  panelActionsWatching = true;
  watchPanelActionTooltips();
  const hosts = [...document.querySelectorAll('#mainContent,#modalContainer')];
  hosts.forEach(normalizePanelActions);
  const pending = new Set();
  let scheduled = false;
  const enqueue = node => {
    const element = node.nodeType === Node.ELEMENT_NODE ? node : node.parentElement;
    if (!element?.closest('#mainContent,#modalContainer')) return;
    pending.add(element.closest('button,a') || element);
  };
  const observer = new MutationObserver(records => {
    if (panelActionTooltipTarget && !panelActionTooltipTarget.isConnected) hidePanelActionTooltip();
    records.forEach(record => {
      if (record.type === 'characterData') enqueue(record.target);
      else {
        if (record.target.closest?.('button,a')) enqueue(record.target);
        record.addedNodes.forEach(enqueue);
      }
    });
    if (!pending.size || scheduled) return;
    scheduled = true;
    queueMicrotask(() => {
      scheduled = false;
      const roots = [...pending];pending.clear();
      roots.filter(root => root.isConnected).forEach(normalizePanelActions);
    });
  });
  hosts.forEach(host => observer.observe(host, { childList: true, subtree: true, characterData: true }));
}

function alignPanelPageActions(page) {
  if (!page) return;
  normalizePanelActions(page);
  const tabs = [...page.querySelectorAll('.panel-page-tabs')];
  if (!tabs.length) return;
  let toolbar = page.querySelector(':scope > [data-panel-page-toolbar]');
  if (!toolbar) {
    toolbar = document.createElement('div');
    toolbar.dataset.panelPageToolbar = '1';
    toolbar.className = 'panel-page-toolbar';
    const navigation = document.createElement('div');
    navigation.className = 'panel-page-toolbar-tabs';
    const actions = document.createElement('div');
    actions.dataset.panelPageActions = '1';
    actions.className = 'panel-page-toolbar-actions';
    const hosts = [...new Set(tabs.map(tab => {
      let host = tab;
      while (host.parentElement !== page) host = host.parentElement;
      return host;
    }))];
    page.insertBefore(toolbar, hosts[0]);
    toolbar.append(navigation, actions);
    hosts.forEach(host => navigation.append(host));
  }
  const actions = toolbar.querySelector('[data-panel-page-actions]');
  page.querySelectorAll('button,a').forEach(button => {
    if (button.closest('[data-panel-page-toolbar],form,table,nav,[role="dialog"],[id$="Modal"],.fixed,[contenteditable]')) return;
    const add = isPanelAddAction(button);
    const exportFile = !!panelExportActionType(button);
    if (!add && !exportFile) return;
    const action = button.closest('[data-panel-page-action-group]') || button;
    const previous = action.parentElement;
    actions.append(action);
    let container = previous;
    while (container && container !== page && container !== toolbar && !container.matches('nav,form,aside')) {
      if ([...container.children].some(child => !child.classList.contains('hidden') && !['STYLE','SCRIPT'].includes(child.tagName))) break;
      container.classList.add('hidden');
      container = container.parentElement;
    }
  });
}

function showSection(id) {
  hidePanelActionTooltip();
  if (id === 'gallery' && !document.getElementById(id)) id = 'gallery-cover';
  if (!document.getElementById(id)) id = 'dashboard';
  const section = id;
  updatePanelHeaderActions(section);

  document.querySelectorAll('.section').forEach((s) => s.classList.add('hidden'));

  const target = document.getElementById(section);
  if (target) {
    target.classList.remove('hidden');
    const heading = panelPageHeading(target);
    const title = panelWorkspaceTitle(section) || heading?.textContent?.trim() ||
      [...document.querySelectorAll('#sidebar a[onclick]')].find((link) => link.getAttribute('onclick') === `showSection('${section}')`)?.textContent?.trim() || section;
    const panelTitle = document.getElementById('panelPageTitle');
    if (panelTitle) panelTitle.textContent = title;
    hidePanelIntroduction(target);
    alignPanelPageActions(target);
  }

  document.getElementById(id).classList.remove('hidden');

  if (section === 'messages') {
    if (typeof renderMessagesBranchTabs === 'function') renderMessagesBranchTabs();
    if (typeof filterMessagesByBranch === 'function') filterMessagesByBranch('all');
  }
  if (section === 'database-management') window.loadDatabaseManagement?.();
  if (section === 'notifications') {
    if (typeof renderNotificationsBranchTabs === 'function') renderNotificationsBranchTabs();
    if (typeof filterNotificationsByBranch === 'function') filterNotificationsByBranch('all');
  }
  if (section === 'contracts') {
    if (typeof renderContractsBranchTabs === 'function') renderContractsBranchTabs();
    if (typeof filterContractsByBranch === 'function') filterContractsByBranch('all');
  }
  if (section === 'account') {
    if (typeof renderAccountInfo === 'function') renderAccountInfo();
  }
  if (section === 'roles') {
    if (typeof renderRolesBranchTabs === 'function') renderRolesBranchTabs();
    if (typeof filterRolesByBranch === 'function') filterRolesByBranch('all');
  }
  if (section === 'permissions') {
    if (typeof renderPermissionsBranchTabs === 'function') renderPermissionsBranchTabs();
    if (typeof filterPermissionsByBranch === 'function') filterPermissionsByBranch('all');
  }
  if (section === 'schedules') {
    if (typeof renderSchedulesBranchTabs === 'function') renderSchedulesBranchTabs();
    if (typeof filterSchedules === 'function') filterSchedules();
  }
  if (section === 'scheduling-rules') {
    if (typeof renderRulesBranchTabs === 'function') renderRulesBranchTabs();
    if (typeof filterRulesByBranch === 'function') filterRulesByBranch('all');
  }
  if (section === 'dashboard') {
    if (typeof renderDashboardBranchTabs === 'function') renderDashboardBranchTabs();
    if (typeof renderDashboard === 'function') renderDashboard();
  }
  if (section === 'member-schedules' && !target?.dataset.personalSection) {
    if (typeof loadMemberSchedules === 'function')
      loadMemberSchedules().catch((error) => alert(error.message));
    else {
      if (typeof renderMemberSchedulesBranchTabs === 'function') renderMemberSchedulesBranchTabs();
      if (typeof filterMemberSchedules === 'function') filterMemberSchedules();
    }
  }
  // if (section === 'experiences') {
  //     if (typeof renderExperiencesBranchTabs === 'function') renderExperiencesBranchTabs();
  //     if (typeof filterExperiencesByBranch === 'function') filterExperiencesByBranch('all');
  // }
  if (section === 'experiences') {
    setTimeout(() => {
      if (typeof renderExperiencesBranchTabs === 'function') renderExperiencesBranchTabs();
      if (typeof filterExperiencesByBranch === 'function') filterExperiencesByBranch('all');
    }, 100);
  }
  if (section === 'awards') {
    if (typeof renderAwardsBranchTabs === 'function') renderAwardsBranchTabs();
    if (typeof filterAwardsByBranch === 'function') filterAwardsByBranch('all');
  }
  if (section === 'certificates') {
    if (typeof renderCertificatesBranchTabs === 'function') renderCertificatesBranchTabs();
    if (typeof filterCertificatesByBranch === 'function') filterCertificatesByBranch('all');
  }
  if (section === 'educations') {
    setTimeout(() => {
      if (typeof renderEducationsBranchTabs === 'function') renderEducationsBranchTabs();
      if (typeof filterEducationsByBranch === 'function') filterEducationsByBranch('all');
    }, 100);
  }
  if (section === 'events') {
    setTimeout(() => {
      if (typeof renderEventsBranchTabs === 'function') renderEventsBranchTabs();
      if (typeof filterEventsByBranch === 'function') filterEventsByBranch('all');
    }, 100);
  }
  if (section === 'polls') {
    setTimeout(() => {
      if (typeof renderPollsBranchTabs === 'function') renderPollsBranchTabs();
      if (typeof filterPollsByBranch === 'function') filterPollsByBranch('all');
    }, 100);
  }
  if (section === 'favorites') {
    setTimeout(() => {
      if (typeof renderFavoritesBranchTabs === 'function') renderFavoritesBranchTabs();
      if (typeof filterFavoritesByBranch === 'function') filterFavoritesByBranch('all');
    }, 100);
  }
  if (section === 'instruments') {
    setTimeout(() => {
      if (typeof renderInstrumentsBranchTabs === 'function') renderInstrumentsBranchTabs();
      if (typeof filterInstrumentsByBranch === 'function') filterInstrumentsByBranch('all');
    }, 100);
  }
  if (section === 'lessons' && !target?.dataset.personalSection) {
    setTimeout(async () => {
      try {
        if (typeof loadLessonDatabaseData === 'function') await loadLessonDatabaseData();
        else {
          if (typeof renderLessonsBranchTabs === 'function') renderLessonsBranchTabs();
          if (typeof filterLessonsByBranch === 'function') filterLessonsByBranch('all');
        }
      } catch (error) {
        console.error('Lesson database loading failed:', error);
        alert(error.message || 'بارگذاری درس‌های شعب ناموفق بود.');
      }
    }, 100);
  }
  if (section === 'chart-gallery' && typeof window.openChartGallery === 'function') {
    window.openChartGallery();
  }
  if (section === 'publications') {
    setTimeout(() => {
      if (typeof renderPublicationsBranchTabs === 'function') renderPublicationsBranchTabs();
      if (typeof filterPublicationsByBranch === 'function') filterPublicationsByBranch('all');
    }, 100);
  }
  if (section === 'ratings') {
    setTimeout(() => {
      if (typeof renderRatingsBranchTabs === 'function') renderRatingsBranchTabs();
      if (typeof filterRatingsByBranch === 'function') filterRatingsByBranch('all');
    }, 100);
  }
  if (section === 'points') {
    setTimeout(() => {
      if (typeof renderPointsBranchTabs === 'function') renderPointsBranchTabs();
      if (typeof filterPointsByBranch === 'function') filterPointsByBranch('all');
    }, 100);
  }
  if (section === 'availabilities') {
    setTimeout(() => {
      if (typeof renderAvailabilitiesBranchTabs === 'function') renderAvailabilitiesBranchTabs();
      if (typeof filterAvailabilitiesByBranch === 'function') filterAvailabilitiesByBranch('all');
    }, 100);
  }
  if (section === 'availability-exceptions') {
    setTimeout(() => {
      if (typeof renderExceptionsBranchTabs === 'function') renderExceptionsBranchTabs();
      if (typeof filterExceptionsByBranch === 'function') filterExceptionsByBranch('all');
    }, 100);
  }
  if (section === 'badges') {
    setTimeout(() => {
      if (typeof renderBadgesBranchTabs === 'function') renderBadgesBranchTabs();
      if (typeof filterBadgesByBranch === 'function') filterBadgesByBranch('all');
    }, 100);
  }
  if (section === 'approvals') {
    setTimeout(() => {
      if (typeof renderApprovalsBranchTabs === 'function') renderApprovalsBranchTabs();
      if (typeof filterApprovalsByBranch === 'function') filterApprovalsByBranch('all');
    }, 100);
  }
  if (section === 'profiles') {
    setTimeout(() => {
      if (typeof renderProfilesBranchTabs === 'function') renderProfilesBranchTabs();
      if (typeof filterProfilesByBranch === 'function') filterProfilesByBranch('all');
    }, 100);
  }
  if (section === 'rating-summaries') {
    setTimeout(() => {
      if (typeof renderRatingSummariesBranchTabs === 'function') renderRatingSummariesBranchTabs();
      if (typeof filterRatingSummariesByBranch === 'function') filterRatingSummariesByBranch('all');
    }, 100);
  }
  if (section === 'media') window.loadAdminMedia?.(1);
  if (section === 'comments') window.loadAdminComments?.();
  if (section === 'posts') window.loadPosts?.();
  if (section === 'post-categories') window.loadPostCategories?.();
  if (section === 'about-us') {
    setTimeout(() => {
      if (typeof renderAboutUs === 'function') renderAboutUs();
    }, 100);
  }
  if (section === 'contact-us') {
    setTimeout(() => {
      if (typeof renderContactUs === 'function') renderContactUs();
    }, 100);
  }
  if (section === 'articles') {
    setTimeout(() => {
      if (typeof renderArticleCategoryTabs === 'function') renderArticleCategoryTabs();
      if (typeof filterArticlesByCategory === 'function') filterArticlesByCategory('all');
    }, 100);
  }
  if (section === 'academies') {
    setTimeout(() => {
      if (typeof renderAcademies === 'function') renderAcademies();
    }, 100);
  }
  if (section === 'academy-enroll') {
    setTimeout(() => {
      if (typeof renderEnrollPage === 'function') renderEnrollPage();
    }, 100);
  }
  if (section === 'academy-requests') {
    setTimeout(() => {
      if (typeof renderAcademyRequestsTable === 'function') renderAcademyRequestsTable();
    }, 100);
  }
  if (section === 'home') {
    setTimeout(() => {
      if (typeof renderHome === 'function') renderHome();
    }, 100);
  }
  if (section === 'login' || section === 'register') {
    // صفحات احراز هویت معمولاً بدون سایدبار ادمین نمایش داده می‌شوند
    // اگر سایدبار داری می‌توانی اینجا مخفی‌اش کنی:
    // document.getElementById('sidebar')?.classList.add('hidden');
  }
  if (section === 'forgot-password') {
    setTimeout(() => {
      if (typeof setFpMethod === 'function') setFpMethod('email');
      if (typeof fpGoStep === 'function') fpGoStep(1);
    }, 50);
  }
}

function showNotifications() {
  alert('نوتیفیکیشن‌ها:\n- پرداخت جدید\n- غیبت ۲ هنرجو\n- درخواست رزرو اتاق تمرین');
}

function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (!sidebar) return;

  // ---------- Mobile ----------
  if (window.innerWidth < 1024) {
    const isClosed = sidebar.classList.contains('-translate-x-full');

    if (isClosed) {
      sidebar.classList.remove('-translate-x-full');
      overlay?.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    } else {
      sidebar.classList.add('-translate-x-full');
      overlay?.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }

    return;
  }

  // ---------- Desktop ----------
  sidebar.classList.toggle('w-72');
  sidebar.classList.toggle('w-20');

  localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('w-20'));
}

document.addEventListener('DOMContentLoaded', () => {
  watchPanelAddActions();
  document.querySelectorAll('#mainContent > .section').forEach(hidePanelIntroduction);
  document.querySelectorAll('#mainContent > .section').forEach(alignPanelPageActions);
  const activeSection = document.querySelector('#mainContent > .section:not(.hidden)');
  if (activeSection) updatePanelHeaderActions(activeSection.id);
  const requestedSection = window.location.hash.replace('#', '');
  if (requestedSection && document.getElementById(requestedSection)) showSection(requestedSection);
  const conversation = Number(new URLSearchParams(location.search).get('conversation'));
  if (requestedSection === 'chat' && Number.isSafeInteger(conversation) && conversation > 0) {
    window.reloadChat?.().then(() => window.openChat?.(conversation)).catch(error => console.error('Unable to open registration conversation', error));
  }

  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (!sidebar) return;

  // وضعیت ذخیره شده دسکتاپ
  if (window.innerWidth >= 1024) {
    const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';

    if (collapsed) {
      sidebar.classList.remove('w-72');
      sidebar.classList.add('w-20');
    }
  }

  // بستن با کلیک روی Overlay
  overlay?.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  });

  // بستن با ESC
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;

    if (window.innerWidth >= 1024) return;

    sidebar.classList.add('-translate-x-full');
    overlay?.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  });
});

document.addEventListener('admin-ui:ready', event => {
  const section = event.target.closest?.('.section');
  if (section) hidePanelIntroduction(section);
  if (section) alignPanelPageActions(section);
  if (section && !section.classList.contains('hidden')) updatePanelHeaderActions(section.id);
});

// این تابع را در main.js یا یک فایل مشترک بگذار
window.closeModal = function () {
  const container = document.getElementById('modalContainer');
  if (container) {
    container.innerHTML = '';
  } else {
    console.warn('modalContainer پیدا نشد');
  }
};
