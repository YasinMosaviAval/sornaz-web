(function () {
  'use strict';
  const english = document.documentElement.lang.startsWith('en');
  const language = english ? 'en' : 'fa';
  let guides = {};
  const canEdit = window.canEditPanelGuides === true;
  const sections = () => [...document.querySelectorAll('#mainContent > .section[id]')];
  const titleNode = guide => guide?.querySelector('[data-guide-title],summary > span');
  const bodyNode = guide => guide?.querySelector('[data-guide-paragraphs],summary + div');

  const guideAnimations = new WeakMap();
  document.addEventListener('click', event => {
    const summary = event.target.closest?.('.panel-page-guide > summary');
    if (!summary || event.defaultPrevented) return;
    const guide = summary.parentElement, body = bodyNode(guide);
    if (!body) return;
    if (!guide.animate || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      summary.setAttribute('aria-expanded', String(!guide.open));
      return;
    }
    event.preventDefault();
    const previous = guideAnimations.get(guide);
    const opening = !(previous ? previous.opening : guide.open);
    const start = guide.getBoundingClientRect().height;
    previous?.height.cancel();
    previous?.content.cancel();
    guide.open = true;
    const end = opening ? guide.getBoundingClientRect().height : summary.getBoundingClientRect().height +
      parseFloat(getComputedStyle(guide).borderTopWidth || 0) + parseFloat(getComputedStyle(guide).borderBottomWidth || 0);
    guide.style.overflow = 'hidden';
    const height = guide.animate([{height: start + 'px', boxSizing: 'border-box'}, {height: end + 'px', boxSizing: 'border-box'}], {
      duration: 290, easing: 'cubic-bezier(.4,0,.2,1)', fill: 'both'
    });
    const content = body.animate([
      {opacity: opening ? 0 : 1, transform: opening ? 'translateY(-6px)' : 'translateY(0)'},
      {opacity: opening ? 1 : 0, transform: opening ? 'translateY(0)' : 'translateY(-6px)'}
    ], {duration: 225, easing: 'cubic-bezier(.4,0,.2,1)', fill: 'both'});
    const state = {opening, height, content};
    guideAnimations.set(guide, state);
    summary.setAttribute('aria-expanded', String(opening));
    height.finished.then(() => {
      if (guideAnimations.get(guide) !== state) return;
      guide.open = opening;
      height.cancel();
      content.cancel();
      guide.style.removeProperty('overflow');
      guideAnimations.delete(guide);
    }).catch(() => {});
  });

  async function request(url, data) {
    const options = { credentials: 'same-origin', headers: { Accept: 'application/json' } };
    if (data) {
      const token = window.adminCsrfToken || window.siteCsrfToken || '';
      options.method = 'POST';
      options.headers['X-CSRF-TOKEN'] = token;
      options.body = new URLSearchParams({ _token: token, payload_b64: btoa(unescape(encodeURIComponent(JSON.stringify(data)))) });
    }
    const response = await fetch(url, options), payload = await response.json();
    const body = payload.data ?? payload;
    if (!response.ok || body.success === false) throw new Error(body.message || (english ? 'Could not save page guidance.' : 'ذخیرهٔ توضیحات انجام نشد.'));
    return body.data ?? body;
  }

  function place(section, node) {
    section = section.id === 'terms' ? section.querySelector('[data-term-panel="terms"]') || section : section;
    const children = [...section.children];
    const anchor = children.find(child => child.matches('.admin-ui-header')) || children.find(child => !child.matches('nav') && child.querySelector('button[onclick*="export"],button[onclick*="openAdd"]')) || children.filter(child => child.matches('nav')).at(-1);
    if (anchor) anchor.after(node);
    else section.prepend(node);
  }

  function apply() {
    const template = document.getElementById('panelPageGuideTemplate');
    if (!template) return;
    sections().forEach(section => {
      const saved = guides[section.id];
      const host = section.id === 'terms' ? section.querySelector('[data-term-panel="terms"]') || section : section;
      let guide = host.querySelector('.panel-page-guide') || section.querySelector('.panel-page-guide');
      if (guide && section.id === 'terms') {
        section.querySelectorAll('.panel-page-guide').forEach(other => { if (other !== guide) other.remove(); });
        if (!host.contains(guide)) place(section, guide);
      }
      const title = saved?.title?.[language] || saved?.title?.fa || saved?.title?.en || '';
      const body = saved?.body?.[language] || saved?.body?.fa || saved?.body?.en || '';
      if (saved && (title || body)) {
        if (!guide) {
          guide = template.content.firstElementChild.cloneNode(true);
          place(section, guide);
        }
        titleNode(guide).textContent = title || (english ? 'Page guidance' : 'توضیحات صفحه');
        const paragraphs = bodyNode(guide);
        paragraphs.replaceChildren(...body.split(/\n\s*\n/).filter(Boolean).map(text => {
          const paragraph = document.createElement('p');
          paragraph.className = 'whitespace-pre-line';
          paragraph.textContent = text;
          return paragraph;
        }));
      }
      if (canEdit && !section.querySelector('[data-edit-page-guide]')) {
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.editPageGuide = section.id;
        button.className = 'mb-3 rounded-xl border border-indigo-200 px-4 py-2 text-sm text-indigo-600 dark:border-indigo-700 dark:text-indigo-300';
        button.textContent = guide ? (english ? 'Edit page guidance' : 'ویرایش توضیحات صفحه') : (english ? 'Add page guidance' : 'افزودن توضیحات صفحه');
        button.addEventListener('click', () => edit(section));
        if (guide) guide.before(button);
        else place(section, button);
      }
      const editor = section.querySelector('[data-edit-page-guide]');
      if (editor && guide) editor.textContent = english ? 'Edit page guidance' : 'ویرایش توضیحات صفحه';
      if (editor && section.id === 'terms') {
        let layout = host.querySelector('[data-page-guide-layout]');
        if (!layout) {
          layout = document.createElement('div');
          layout.dataset.pageGuideLayout = '';
          layout.className = 'mb-4 flex flex-wrap items-start gap-3';
          place(section, layout);
        }
        if (guide) { guide.classList.add('flex-1', 'min-w-0'); layout.append(guide); }
        layout.append(editor);
      }
    });
  }

  function edit(section) {
    document.getElementById('panelGuideEditor')?.remove();
    const dialog = document.createElement('dialog');
    dialog.id = 'panelGuideEditor';
    dialog.className = 'w-full max-w-2xl rounded-2xl bg-white p-6 text-gray-900 shadow-xl dark:bg-slate-900 dark:text-white';
    dialog.setAttribute('aria-labelledby', 'panelGuideEditorTitle');
    dialog.innerHTML = `<form method="dialog"><h2 id="panelGuideEditorTitle" class="mb-5 text-xl font-bold">${english ? 'Page guidance' : 'توضیحات صفحه'}</h2><div data-guide-fields class="space-y-4"></div><p data-guide-error role="alert" class="my-3 text-sm text-red-600"></p><div class="mt-5 flex gap-3"><button type="button" data-guide-save class="rounded-xl bg-indigo-600 px-5 py-2 text-white">${english ? 'Save' : 'ذخیره'}</button><button class="rounded-xl border px-5 py-2">${english ? 'Cancel' : 'انصراف'}</button></div></form>`;
    const saved = guides[section.id] || {};
    const defaults = {
      title: titleNode(section.querySelector('.panel-page-guide'))?.textContent || '',
      body: [...(bodyNode(section.querySelector('.panel-page-guide'))?.querySelectorAll('p') || [])].map(p => p.textContent).join('\n\n'),
    };
    for (const locale of ['fa', 'en']) {
      for (const field of ['title', 'body']) {
        const label = document.createElement('label');
        label.className = 'block text-sm';
        label.textContent = `${locale === 'fa' ? 'فارسی' : 'English'} — ${field === 'title' ? (english ? 'Title' : 'عنوان') : (english ? 'Description' : 'توضیحات')}`;
        const input = document.createElement(field === 'title' ? 'input' : 'textarea');
        input.name = locale + '-' + field;
        input.dir = locale === 'fa' ? 'rtl' : 'ltr';
        input.maxLength = field === 'title' ? 200 : 20000;
        if (field === 'body') input.rows = 5;
        input.className = 'mt-2 w-full rounded-xl border border-gray-300 bg-transparent p-3 dark:border-slate-600';
        input.value = saved[field]?.[locale] || (locale === language ? defaults[field] : '');
        label.append(input);
        dialog.querySelector('[data-guide-fields]').append(label);
      }
    }
    dialog.querySelector('[data-guide-save]').addEventListener('click', async (event) => {
      event.target.disabled = true;
      try {
        const data = { page: 'panel-' + section.id };
        for (const locale of ['fa', 'en']) data[locale] = Object.fromEntries(['title', 'body'].map(field => [field, dialog.querySelector(`[name="${locale}-${field}"]`).value]));
        await request('/analytics/admin-panel-guide', data);
        guides = await request('/analytics/panel-guides');
        apply();
        dialog.close();
        dialog.remove();
      } catch (error) {
        dialog.querySelector('[data-guide-error]').textContent = error.message;
      } finally { event.target.disabled = false; }
    });
    dialog.style.maxHeight = '90vh';
    dialog.style.overflowY = 'auto';
    document.body.append(dialog);
    dialog.showModal();
  }

  async function initialize() {
    if (!document.getElementById('mainContent')) return;
    try { guides = await request('/analytics/panel-guides'); apply(); }
    catch (error) { console.error(error.message); }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize, { once: true });
  else initialize();
})();
