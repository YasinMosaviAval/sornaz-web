(function () {
  'use strict';
  window.initSiteEnrollment = function (root, data, dialog = false) {
    const form = root.querySelector('#siteEnrollmentForm');
    if (!form || form.dataset.initialized) return;
    form.dataset.initialized = '1';
    const en = document.documentElement.lang === 'en';
    const term = form.elements.term_id, level = form.elements.level_id;
    const message = form.querySelector('#siteEnrollMessage');
    const terms = data.terms || {};
    if (dialog) {
      for (const key of ['name', 'national_id', 'father_name', 'birth_date', 'phone']) {
        const input = form.elements[key];
        if (input && String(data.profile?.[key] || '').trim()) input.closest('label').classList.add('hidden');
      }
    }
    const setLevels = () => {
      level.replaceChildren(new Option(en ? 'Select a level' : 'انتخاب سطح', ''));
      for (const item of terms[term.value]?.levels || []) level.add(new Option(item.title, item.id));
      level.disabled = level.options.length === 1;
      if (level.options.length === 2) level.value = level.options[1].value;
    };
    term.addEventListener('change', setLevels);
    if (terms[data.selectedTerm]) {
      term.value = String(data.selectedTerm);
      if (dialog) term.parentElement.classList.add('hidden');
    }
    setLevels();
    const province = form.elements.province_id, county = form.elements.county_id;
    const counties = [...county.options].slice(1).map(option => option.cloneNode(true));
    const updateCounties = () => {
      const selected = county.value;
      county.replaceChildren(new Option(en ? 'Select city' : 'انتخاب شهر', ''));
      counties.filter(option => option.dataset.province === province.value).forEach(option => county.add(option.cloneNode(true)));
      county.value = selected;
      county.disabled = !province.value;
    };
    province.addEventListener('change', updateCounties);
    updateCounties();
    form.querySelector('[data-enrollment-map]').addEventListener('click', () => {
      const lat = Number(form.elements.latitude.value) || 35.6892;
      const lng = Number(form.elements.longitude.value) || 51.389;
      window.open(`https://www.google.com/maps/@${lat},${lng},15z`, '_blank', 'noopener');
    });
    window.initLocalizedDateInputs?.(form);
    form.addEventListener('submit', async event => {
      event.preventDefault();
      const button = form.querySelector('[type=submit]');
      button.disabled = true;
      message.classList.add('hidden');
      try {
        const response = await fetch(form.action, { method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': form.elements._token.value }, body: new FormData(form) });
        const payload = await response.json();
        const result = payload.data ?? payload;
        if (!response.ok || payload.success === false || result.success === false) throw Error(result.message || payload.message || (en ? 'Registration failed.' : 'ثبت درخواست انجام نشد.'));
        location.href = payload.redirect || result.redirect || '/analytics/admin-panel#chat';
      } catch (error) {
        message.textContent = error.message;
        message.className = 'text-sm text-red-600';
        button.disabled = false;
      }
    });
  };
  const data = document.getElementById('siteEnrollmentData');
  if (data) window.initSiteEnrollment(document, JSON.parse(data.textContent));
})();
