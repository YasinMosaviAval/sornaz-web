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
  const staffAccessPage = ['teachers', 'member-schedules'].includes(id) || (id === 'availability-exceptions' && window.holidayLeaveMode === 'member');
  if (staffAccessPage && document.getElementById(id)?.querySelector('.panel-page-tabs button[onclick*="roles"]')) {
    return english ? 'Users' : 'کاربران';
  }
  const groups = [
    [['users', 'roles', 'permissions'], ['کاربران', 'Users']],
    [['posts', 'post-categories', 'media', 'comments', 'post-editor'], ['نوشته ها', 'Posts']],
    [['awards', 'badges', 'experiences', 'certificates', 'educations', 'events', 'polls', 'publications'], ['سوابق و دستاوردها', 'Records and achievements']],
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

function alignPanelPageActions(page) {
  if (!page) return;
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
    const handler = button.getAttribute('onclick') || '', label = button.textContent.trim();
    const add = button.hasAttribute('data-panel-page-action') || button.hasAttribute('data-profile-add') || /^openAdd\w*\s*\(/.test(handler) || /^(?:افزودن|add\b|new\b|create\b|\+\s*)/i.test(label) || (/^open\w*\s*\(/.test(handler) && /جدید\s*$/.test(label));
    const exportFile = /^export\w*(?:Excel|PDF)\w*\s*\(/i.test(handler) || /(?:خروجی\s*(?:اکسل|پی[\s‌]*دی[\s‌]*اف|excel|pdf)|export\s*(?:excel|pdf))/i.test(label);
    if (!add && !exportFile) return;
    const previous = button.parentElement;
    actions.append(button);
    let container = previous;
    while (container && container !== page && container !== toolbar && !container.matches('nav,form,aside')) {
      if ([...container.children].some(child => !child.classList.contains('hidden') && !['STYLE','SCRIPT'].includes(child.tagName))) break;
      container.classList.add('hidden');
      container = container.parentElement;
    }
  });
}

function showSection(id) {
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
