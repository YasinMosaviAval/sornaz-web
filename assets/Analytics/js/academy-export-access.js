(function () {
  'use strict';
  const sources = new Map(), pending = new Map();
  let refreshVersion = 0, refreshTimer;
  const english = () => document.documentElement.lang.startsWith('en');
  const activePage = () => document.querySelector('#mainContent > .section:not(.hidden)');
  function queryFor(records) {
    const query = new URLSearchParams(), academies = new Set(), branches = new Set(), organizations = new Set();
    let complete = Array.isArray(records) && records.length > 0;
    for (const original of records || []) {
      const course = original.courseId ? (window.termCourses || []).find(row => Number(row.id) === Number(original.courseId)) : null;
      const row = {...(course || {}), ...original};
      const academy = Number(row.academyId || row.academy_id || 0), branch = Number(row.branchId || row.branch_id || 0), organization = Number(row.organizationUserId || 0);
      if (academy > 0) academies.add(academy);
      else if (organization > 0) organizations.add(organization);
      else if (branch > 0) branches.add(branch);
      else complete = false;
    }
    if (complete) {
      academies.forEach(id => query.append('academyIds[]', id));
      branches.forEach(id => query.append('branchIds[]', id));
      organizations.forEach(id => query.append('organizationUserIds[]', id));
    }
    return query;
  }
  async function status(records) {
    const query = queryFor(records).toString();
    if (pending.has(query)) return pending.get(query);
    const request = (async () => {
      const response = await fetch('/academy/exports/access' + (query ? '?' + query : ''), {credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
      const body = await response.json(), envelope = body.data ?? body;
      if (!response.ok || body.success === false || envelope.success === false) throw new Error(english()?'Export access could not be checked. Please try again.':'بررسی دسترسی خروجی ممکن نشد؛ دوباره تلاش کنید.');
      return envelope.data ?? envelope;
    })().finally(() => pending.delete(query));
    pending.set(query, request);
    return request;
  }
  function exportControls(page) {
    if (!page) return [];
    return [...page.querySelectorAll('button,a')].filter(button => /(?:export\w*(?:Excel|PDF)|open\w*PDF\w*|generate\w*PDF|exportFinance\b)/i.test(button.getAttribute('onclick') || '') || button.hasAttribute('data-personal-export') || button.hasAttribute('data-personal-print'));
  }
  function paint(page, allowed) {
    const badge=document.getElementById('academySubscriptionPrice');
    if (badge) badge.textContent = allowed ? (english()?'Exports enabled':'خروجی‌ها فعال‌اند') : (english()?'Export subscription required':'اشتراک خروجی لازم است');
    exportControls(page).forEach(button => {
      if (!button.hasAttribute('data-export-original-title')) button.dataset.exportOriginalTitle = button.getAttribute('title') || '';
      button.setAttribute('aria-disabled', String(!allowed));
      button.classList.toggle('opacity-50', !allowed);
      button.dataset.exportSubscriptionRequired = allowed ? '0' : '1';
      button.title = allowed ? button.dataset.exportOriginalTitle : (english()?'Excel/PDF exports require an active subscription after the first month. All other features are free.':'پس از ماه اول، خروجی اکسل و PDF به اشتراک فعال نیاز دارد؛ سایر امکانات رایگان‌اند.');
    });
  }
  async function refresh() {
    const page=activePage();
    if (!page || !exportControls(page).length) return;
    const version=++refreshVersion;
    try { const result=await status(sources.get(page.id)?.()); if(version===refreshVersion && page===activePage())paint(page,result.allowed===true); }
    catch (_) { if(version===refreshVersion && page===activePage())paint(page,false); }
  }
  function scheduleRefresh() { clearTimeout(refreshTimer);refreshTimer=setTimeout(refresh,150); }
  window.AcademyExports = {
    register(section, source) { sources.set(section, source); scheduleRefresh(); },
    refresh,
    async authorize(records) {
      try {
        const result=await status(records);
        paint(activePage(), result.allowed===true);
        if (result.allowed===true) return true;
        const message=english()?'The first month of Excel/PDF exports is free. An active export subscription is now required. All other panel features remain free. Open subscription plans?':'ماه اول خروجی اکسل و PDF رایگان است. اکنون برای این خروجی‌ها اشتراک لازم است؛ تمام امکانات دیگر پنل رایگان‌اند. صفحهٔ اشتراک را باز می‌کنید؟';
        if (window.AppDialog?.confirm) {
          if(await AppDialog.confirm(message)){window.closeModal?.();window.showSection?.('finance');window.loadAcademySubscriptions?.();}
        } else window.alert(message);
      } catch (error) { window.alert(error.message); }
      return false;
    }
  };
  document.addEventListener('DOMContentLoaded',()=>{
    if(typeof window.showSection==='function'){
      const show=window.showSection;
      window.showSection=function(...args){const result=show.apply(this,args);scheduleRefresh();return result;};
    }
    scheduleRefresh();
  });
  document.addEventListener('change',event=>{if(event.target.closest?.('#mainContent'))scheduleRefresh();});
  document.addEventListener('click',event=>{if(event.target.closest?.('.organization-filter-tabs,.panel-page-tabs'))scheduleRefresh();});
  window.addEventListener('academy-data-loaded',scheduleRefresh);
  window.addEventListener('panel-organizations-changed',scheduleRefresh);
})();
