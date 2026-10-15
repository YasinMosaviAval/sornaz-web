(function () {
  'use strict';
  const node = document.getElementById('pageSourceManifest');
  if (!node) return;
  const manifest = JSON.parse(node.textContent);
  const requests = new Map();
  const aliases = { branches: 'branch', teachers: 'teacher', availabilities: 'availability', 'availability-exceptions': 'availability-exception', 'personal-lessons': 'personal-panel-sections', 'personal-schedules': 'personal-panel-sections', 'personal-finance': 'personal-panel-sections' };
  const pair = (fa, en) => document.documentElement.lang === 'en' ? en : fa;
  const clean = (path) => typeof path === 'string' && !/(?:^|\/)\.\.(?:\/|$)|[<>\u0000]/.test(path) && /^(?:assets\/|core\/|Modules\/|routes\/|bootstrap\/|index\.php$)/.test(path);

  function role(path) {
    const name = path.split('/').pop();
    const known = {
      'View.php': ['ترکیب قالب اصلی، چیدمان و کامپوننت‌های صفحه', 'Assembles the page view, layout and components'],
      'PanelTableRenderer.php': ['تولید جدول‌ها از فهرست ستون‌ها و ردیف‌ها', 'Builds tables from column and row definitions'],
      'panel-table-template.php': ['قالب مشترک جدول، سرستون، ردیف و سلول', 'Shared table, heading, row and cell markup'],
      'admin-ui-templates.js': ['رندر اجزای مشترک پنل، از جمله جدول‌ها و سربرگ‌ها', 'Renders shared panel components, including tables and headers'],
      'panel-organization-filters.js': ['ساخت فیلتر مشترک آموزشگاه و شعبه‌ها', 'Builds shared academy and branch filters'],
      'theme.js': ['اعمال رنگ، حالت روشن و تاریک و تنظیمات ظاهر', 'Applies colors, light/dark mode and appearance settings'],
      'theme.css': ['استایل مشترک، رنگ و ظاهر اجزای سایت', 'Shared site component styling and theme colors'],
      'sidebar.php': ['ساخت منوهای کناری و مشخصات حساب', 'Builds sidebar navigation and account identity'],
      'header.php': ['ساخت هدر و کنترل‌های بالای پنل', 'Builds the panel header and its controls'],
      'help-center.js': ['ساخت همین دیالوگ راهنمای صفحه', 'Builds this page help dialog'],
      'page-source-trace.js': ['جمع‌آوری منابع مرورگر و درخواست‌های داده برای این فهرست', 'Collects browser sources and data-request traces for this list'],
      'PageSourceTrace.php': ['ثبت نام قالب‌ها و فایل‌های سرور فقط برای مدیر شمارهٔ ۱', 'Records server and view filenames exclusively for user 1'],
    };
    if (known[name]) return pair(...known[name]);
    if (/\/layouts\//.test(path)) return pair('چیدمان کلی صفحه و بارگذاری فایل‌های مشترک', 'Page layout and shared asset loading');
    if (/\/Views\//.test(path)) return pair('قالب HTML و کنترل‌های بخش «' + name.replace('.php', '') + '»', 'HTML view and controls for ' + name.replace('.php', ''));
    if (/\/Controllers\//.test(path)) return pair('کنترلر درخواست و آماده‌سازی پاسخ: ' + name, 'Request controller and response preparation: ' + name);
    if (/\/Services\//.test(path)) return pair('سرویس داده و قواعد کسب‌وکار: ' + name, 'Data and business-rule service: ' + name);
    if (/\/routes\/|^routes\//.test(path)) return pair('تعریف مسیرها و اتصال URL به کنترلر', 'Registers routes and connects URLs to controllers');
    if (/^core\//.test(path)) return pair('زیرساخت درخواست، احراز هویت یا رندر: ' + name, 'Request, authentication or rendering infrastructure: ' + name);
    if (/templates\.js$/.test(path)) return pair('قالب‌های تعاملی ردیف‌ها، فرم‌ها و دیالوگ‌ها: ' + name, 'Interactive row, form and dialog templates: ' + name);
    if (/\.js$/.test(path)) return pair('رفتار تعاملی و مدیریت رویدادهای «' + name + '»', 'Interactive behavior and event handling: ' + name);
    if (/\.css$/.test(path)) return pair('استایل و چیدمان بصری صفحه', 'Visual styling and page layout');
    return pair('راه‌اندازی و زیرساخت اجرای صفحه', 'Page startup and runtime infrastructure');
  }

  window.PageSourceTrace = {
    entries(id) {
      const result = new Map();
      const own = manifest.sections?.[id] || manifest.sections?.[aliases[id]] || [];
      const add = (path, group) => {
        if (!clean(path) || result.has(path)) return;
        result.set(path, { path, group, role: role(path) });
      };
      own.forEach((path) => add(path, 'section'));
      const pageIds = new Set([...document.querySelectorAll('.section[id],.site-page[id]')].map((root) => root.id));
      (manifest.files || []).filter((path) => !/\/Views\/sections\//.test(path) || !pageIds.has(path.split('/').pop().replace('.php', ''))).forEach((path) => add(path, 'server'));
      const stems = [id, aliases[id], id.replace(/s$/, '')].filter(Boolean);
      document.querySelectorAll('script[src],link[rel="stylesheet"][href]').forEach((element) => {
        const url = new URL(element.src || element.href, location.href);
        if (url.origin !== location.origin || !url.pathname.startsWith('/assets/')) return;
        const path = url.pathname.slice(1);
        const name = path.split('/').pop().replace(/(?:-templates)?\.(?:js|css)$/, '');
        add(path, stems.includes(name) ? 'section' : 'assets');
      });
      for (const trace of requests.values()) {
        (trace.files || []).forEach((path) => add(path, 'requests'));
      }
      return [...result.values()];
    },
  };

  const original = window.fetch;
  if (typeof original !== 'function') return;
  window.fetch = function (input, init) {
    let url;
    try { url = new URL(input instanceof Request ? input.url : input, location.href); }
    catch (_) { return original.call(this, input, init); }
    if (url.origin !== location.origin) return original.call(this, input, init);
    const headers = new Headers(init?.headers ?? (input instanceof Request ? input.headers : undefined));
    headers.set('X-Sornaz-Source-Trace', '1');
    const stack = String(new Error().stack || '');
    const initiator = stack.match(/\/assets\/(?:Analytics|Academy|Page|Home|System)\/js\/([\w-]+)\.js/)?.[1] || '';
    return original.call(this, input, { ...init, headers }).then((response) => {
      if (response.headers.get('X-Sornaz-Source-Trace') === '1') {
        response.clone().json().then((body) => {
          if (!body?._pageSources) return;
          requests.set(initiator + ':' + url.pathname, { initiator, files: body._pageSources.files });
          document.dispatchEvent(new CustomEvent('page-sources:updated'));
        }).catch(() => {});
      }
      return response;
    });
  };
})();
