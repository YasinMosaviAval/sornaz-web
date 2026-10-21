(function () {
  'use strict';
  const english = () => document.documentElement.lang.startsWith('en');
  const text = (fa, en) => english() ? en : fa;
  const fields = ['Name', 'Phone', 'NationalId', 'Gender', 'BirthDate', 'FatherName'];
  const key = field => field[0].toLowerCase() + field.slice(1);
  const picker = prefix => document.querySelector(`[data-member-user-picker="${prefix}"]`);
  const organization = prefix => Number(document.getElementById(`${prefix}Organization`)?.value || 0);
  const fieldWrapper = input => {
    if (!input) return null;
    const field = input.closest('[data-member-identity-field]');
    if (field) return field;
    const calendar = input.closest('.localized-date-field');
    return (calendar ? calendar.parentElement : input)?.closest('div');
  };
  function previousStaffOptions() {
    const users = [...new Map((window.staffExistingUsers || []).filter(user => user.username || user.phone).map(user => [user.user_id || user.username || user.phone, user])).values()];
    return users.map(user => {
      const option = document.createElement('option');
      option.value = user.username || user.phone;
      option.textContent = user.name || user.username || user.phone;
      return option.outerHTML;
    }).join('');
  }

  window.getMemberUserPickerHTML = prefix => `<div data-member-user-picker="${prefix}" class="rounded-2xl border p-4 space-y-3">
    <label class="block text-sm font-medium">${text('انتخاب از کاربران سایت (اختیاری)', 'Select an existing site user (optional)')}</label>
    ${prefix === 'staff' && previousStaffOptions() ? `<label class="block text-sm">${text('کارمندان ثبت‌شده', 'Previously registered employees')}<select data-existing-staff class="mt-2 w-full rounded-2xl border px-4 py-3"><option value="">${text('انتخاب کارمند', 'Choose an employee')}</option>${previousStaffOptions()}</select></label>` : ''}
    <div class="flex gap-3"><input data-user-query type="search" class="min-w-0 flex-1 rounded-2xl border px-4 py-3" placeholder="${text('نام کاربری یا شماره موبایل کامل', 'Exact username or mobile number')}">
    <button type="button" data-user-search class="rounded-2xl border px-4 py-3">${text('جستجو', 'Search')}</button></div>
    <div data-user-results class="space-y-2" aria-live="polite"></div>
    <button type="button" data-user-reset class="hidden text-sm text-indigo-600">${text('لغو انتخاب و ثبت کاربر جدید', 'Clear selection and create a new user')}</button>
  </div>`;

  function clear(prefix) {
    const host = picker(prefix);
    if (!host) return;
    host.selection = null;
    host.querySelector('[data-user-reset]').classList.add('hidden');
    for (const suffix of [...fields, 'ProfileVisibility']) {
      const input = document.getElementById(prefix + suffix);
      const wrapper = fieldWrapper(input);
      if (wrapper?.dataset.existingUserHidden) {
        wrapper.classList.remove('hidden');
        delete wrapper.dataset.existingUserHidden;
      }
    }
    if (prefix === 'stu') window.toggleStudentParentFields?.('stuBirthDate', 'stuParentWrap');
  }

  window.getMemberUserSelection = prefix => {
    const host = picker(prefix);
    if (host?.selection && host.selection.organization !== organization(prefix)) {
      clear(prefix);
      host.querySelector('[data-user-results]').textContent = text('سازمان تغییر کرده است؛ کاربر را دوباره انتخاب کنید.', 'Organization changed. Select the user again.');
    }
    return host?.selection || null;
  };

  document.addEventListener('change', event => {
    if (event.target.matches('[data-existing-staff]') && event.target.value) {
      const host = event.target.closest('[data-member-user-picker]');
      host.querySelector('[data-user-query]').value = event.target.value;
      host.querySelector('[data-user-search]').click();
    }
    if (event.target.id === 'staffOrganization') window.getMemberUserSelection('staff');
    if (event.target.id === 'stuOrganization') window.getMemberUserSelection('stu');
  }, true);

  document.addEventListener('click', async event => {
    const host = event.target.closest('[data-member-user-picker]');
    if (!host) return;
    const prefix = host.dataset.memberUserPicker;
    const results = host.querySelector('[data-user-results]');
    if (event.target.closest('[data-user-reset]')) { clear(prefix); results.replaceChildren(); return; }
    const search = event.target.closest('[data-user-search]');
    if (!search || search.disabled) return;
    const org = organization(prefix), query = host.querySelector('[data-user-query]').value.trim();
    if (!org || query.length < 3) { results.textContent = text('ابتدا سازمان و نام کاربری یا شماره موبایل کامل را وارد کنید.', 'Choose an organization and enter the exact username or mobile number.'); return; }
    clear(prefix);
    search.disabled = true;
    results.textContent = text('در حال جستجو…', 'Searching…');
    try {
      const response = await window.branchRequest('/academy/admin/member-users/lookup', { payload_b64: window.encodeBranchPayload({ organizationUserId: org, query }) });
      if (!host.isConnected || organization(prefix) !== org) return;
      results.replaceChildren();
      for (const user of response.users || []) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'w-full rounded-2xl border p-3 text-start';
        button.textContent = `${user.name || user.username} (@${user.username})`;
        button.addEventListener('click', () => {
          clear(prefix);
          host.selection = { ...user, organization: org };
          for (const suffix of fields) {
            const input = document.getElementById(prefix + suffix);
            if (input && user.fields[key(suffix)]) {
              input.value = '';
              window.syncLocalizedDateInput?.(input);
              const wrapper = fieldWrapper(input);
              wrapper.classList.add('hidden');
              wrapper.dataset.existingUserHidden = '1';
            }
          }
          const visibility = document.getElementById(prefix + 'ProfileVisibility')?.closest('div');
          if (visibility) { visibility.classList.add('hidden'); visibility.dataset.existingUserHidden = '1'; }
          if (prefix === 'stu') document.getElementById('stuParentWrap')?.classList.toggle('hidden', !user.under18);
          results.textContent = text('کاربر انتخاب شد؛ اطلاعات ثبت‌شده از حساب او استفاده می‌شود.', 'User selected. Saved account information will be used.');
          host.querySelector('[data-user-reset]').classList.remove('hidden');
        });
        results.appendChild(button);
      }
      if (!results.childElementCount) results.textContent = text('کاربری پیدا نشد.', 'No user found.');
    } catch (error) { results.textContent = error.message || text('جستجو ناموفق بود.', 'Search failed.'); }
    finally { search.disabled = false; }
  }, true);
})();
