(function () {
  'use strict';
  function form(kind) {
    return document.getElementById(kind === 'lesson' ? 'personalLessonForm' : kind === 'invoice' ? 'personalInvoiceForm' : 'personalScheduleForm');
  }
  function open(kind, row) {
    const element = form(kind);
    if (!element) return;
    element.reset();
    element.elements.id.value = String(row?.id || 0);
    if (row) {
      for (const [key, value] of Object.entries(row)) {
        if (key === 'id' || !element.elements[key]) continue;
        if (element.elements[key].type === 'checkbox') element.elements[key].checked = Boolean(value);
        else element.elements[key].value = String(value ?? '');
      }
    }
    if (kind === 'schedule') {
      setRanges(row?.ranges?.length ? row.ranges : [{ start: '', end: '', status: 'فعال' }]);
      refreshRepeat();
    }
    element.querySelector('[data-personal-error]')?.classList.add('hidden');
    element.classList.remove('hidden');
    element.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
  }
  function close(kind) {
    form(kind)?.classList.add('hidden');
  }
  function addRange(range = {}) {
    const container = form('schedule')?.querySelector('[data-personal-ranges]');
    if (!container) return;
    const row = document.createElement('div');
    row.className = 'personal-range grid gap-3 rounded-xl border p-3 md:grid-cols-4';
    row.innerHTML = '<label class="text-sm">شروع<input type="time" data-range-start required class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></label>' +
      '<label class="text-sm">پایان<input type="time" data-range-end required class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></label>' +
      '<label class="text-sm">وضعیت<select data-range-status class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"><option value="فعال">فعال</option><option value="غیرفعال">غیرفعال</option></select></label>' +
      '<button type="button" data-personal-remove-range class="self-end rounded-xl border px-3 py-3 text-red-600">حذف بازه</button>';
    row.querySelector('[data-range-start]').value = range.start || '';
    row.querySelector('[data-range-end]').value = range.end || '';
    row.querySelector('[data-range-status]').value = range.status || 'فعال';
    container.appendChild(row);
  }
  function setRanges(ranges) {
    const container = form('schedule')?.querySelector('[data-personal-ranges]');
    if (!container) return;
    container.replaceChildren();
    ranges.forEach(addRange);
  }
  function refreshRepeat() {
    const element = form('schedule');
    if (!element) return;
    const repeat = element.elements.repeatPeriod.value;
    const dateOnly = ['ماهانه', 'سالانه', 'بی‌تکرار'].includes(repeat);
    element.querySelector('[data-personal-day]').classList.toggle('hidden', dateOnly);
    element.querySelector('[data-personal-date]').classList.toggle('hidden', repeat === 'هفتگی');
    element.elements.repeatDate.required = repeat !== 'هفتگی';
    element.elements.day.required = !dateOnly;
  }
  form('schedule')?.elements.repeatPeriod?.addEventListener('change', refreshRepeat);
  form('schedule')?.querySelector('[data-personal-add-range]')?.addEventListener('click', () => addRange());
  form('schedule')?.querySelector('[data-personal-ranges]')?.addEventListener('click', (event) => {
    if (!event.target.closest('[data-personal-remove-range]')) return;
    const container = event.currentTarget;
    if (container.querySelectorAll('.personal-range').length > 1) event.target.closest('.personal-range').remove();
  });
  function filterRows(sectionId) {
    const section = document.getElementById(sectionId);
    const query = (section?.querySelector('[data-personal-filter]')?.value || '').trim().toLocaleLowerCase();
    const status = section?.querySelector('[data-personal-status]')?.value || '';
    section?.querySelectorAll('tbody tr[data-personal-status-value]').forEach((row) => {
      const matches = (!query || row.textContent.toLocaleLowerCase().includes(query)) &&
        (!status || row.dataset.personalStatusValue === status);
      row.classList.toggle('hidden', !matches);
    });
  }
  for (const sectionId of ['lessons', 'member-schedules']) {
    document.querySelectorAll('#' + sectionId + ' [data-personal-filter],#' + sectionId + ' [data-personal-status]')
      .forEach((input) => input.addEventListener('input', () => filterRows(sectionId)));
  }
  async function save(kind, element) {
    const values = Object.fromEntries(new FormData(element));
    const id = Number(values.id || 0);
    delete values.id;
    let payload;
    if (kind === 'lesson') {
      payload = {
        lesson_id: Number(values.lesson_id),
        level_id: Number(values.level_id),
        start_date: values.start_date,
        is_primary: values.is_primary === '1' ? 1 : 0,
        summary: values.summary || '',
        description: values.description || '',
      };
    } else if (kind === 'invoice') {
      payload = { amount: values.amount, statusCode: values.statusCode, dueDate: values.dueDate,
        title: values.title, summary: values.summary || '', description: values.description || '' };
    } else {
      const ranges = [...element.querySelectorAll('.personal-range')].map((row) => ({
        start: row.querySelector('[data-range-start]').value,
        end: row.querySelector('[data-range-end]').value,
        status: row.querySelector('[data-range-status]').value,
      }));
      payload = {
        day: values.day,
        repeatPeriod: values.repeatPeriod,
        repeatDate: values.repeatPeriod === 'هفتگی' ? '' : values.repeatDate,
        timezone: values.timezone,
        ranges,
        summary: values.summary || '',
        description: values.description || '',
      };
    }
    const name = kind === 'lesson' ? 'lessons' : 'schedules';
    const encoded = btoa(unescape(encodeURIComponent(JSON.stringify(payload))))
      .replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    const url = kind === 'invoice' ? '/analytics/personal-invoices/' + id + '/update' : '/analytics/personal-offerings/' + name + (id ? '/' + id : '');
    const response = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-CSRF-TOKEN': window.adminCsrfToken || '',
      },
      body: new URLSearchParams({ _token: window.adminCsrfToken || '', payload_b64: encoded }),
    });
    const result = await response.json();
    if (!response.ok || result.success === false) throw new Error(result.message || 'ثبت اطلاعات انجام نشد.');
    window.location.reload();
  }
  function exportTable(sectionId) {
    const tables = document.querySelectorAll('#' + sectionId + ' table');
    if (!tables.length) return;
    const lines = [...tables].flatMap((table, index) => [
      ...(index ? [''] : []),
      ...[...table.rows].map((row) => [...row.cells]
        .slice(0, sectionId === 'finance' && index > 0 ? undefined : -1)
        .map((cell) => '"' + cell.textContent.trim().replace(/"/g, '""') + '"').join(',')),
    ]);
    const link = document.createElement('a');
    link.href = URL.createObjectURL(new Blob(['\ufeff', lines.join('\r\n')], { type: 'text/csv;charset=utf-8' }));
    link.download = sectionId + '.csv';
    link.click();
    setTimeout(() => URL.revokeObjectURL(link.href), 1000);
  }
  function printSection(sectionId) {
    const style = document.createElement('style');
    style.textContent = '@media print { body * { visibility: hidden !important; } #' + sectionId + ', #' + sectionId + ' * { visibility: visible !important; } #' + sectionId + ' { position: absolute !important; inset: 0 !important; width: 100% !important; } }';
    document.head.appendChild(style);
    window.addEventListener('afterprint', () => style.remove(), { once: true });
    window.print();
  }
  async function remove(kind, id) {
    if (window.AppDialog && !(await AppDialog.confirm('این مورد حذف شود؟'))) return;
    const response = await fetch('/analytics/personal-offerings/' + kind + '/' + id + '/delete', {
      method: 'POST', credentials: 'same-origin',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': window.adminCsrfToken || '' },
      body: new URLSearchParams({ _token: window.adminCsrfToken || '' }),
    });
    const result = await response.json();
    if (!response.ok || result.success === false) throw new Error(result.message || 'حذف انجام نشد.');
    window.location.reload();
  }
  for (const kind of ['lesson', 'schedule']) {
    document.querySelectorAll('[data-personal-edit-' + kind + ']').forEach((button) => {
      const row = JSON.parse(button.dataset['personalEdit' + kind[0].toUpperCase() + kind.slice(1)]);
      const removeButton = document.createElement('button');
      removeButton.type = 'button';
      removeButton.className = 'ms-3 text-red-600';
      removeButton.textContent = document.documentElement.lang === 'en' ? 'Delete' : 'حذف';
      removeButton.dataset.personalDelete = kind + ':' + row.id;
      button.after(removeButton);
    });
  }
  document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-personal-open],[data-personal-cancel],[data-personal-edit-lesson],[data-personal-edit-schedule],[data-personal-edit-invoice],[data-personal-export],[data-personal-print],[data-personal-delete]');
    if (!button) return;
    if (button.dataset.personalOpen) open(button.dataset.personalOpen);
    else if (button.dataset.personalCancel) close(button.dataset.personalCancel);
    else if (button.dataset.personalEditLesson) open('lesson', JSON.parse(button.dataset.personalEditLesson));
    else if (button.dataset.personalEditSchedule) open('schedule', JSON.parse(button.dataset.personalEditSchedule));
    else if (button.dataset.personalEditInvoice) open('invoice', JSON.parse(button.dataset.personalEditInvoice));
    else if (button.dataset.personalExport) exportTable(button.dataset.personalExport);
    else if (button.dataset.personalPrint) printSection(button.dataset.personalPrint);
    else if (button.dataset.personalDelete) {
      const [kind, id] = button.dataset.personalDelete.split(':');
      remove(kind, Number(id)).catch((error) => window.AppDialog?.alert(error.message));
    }
  });
  for (const kind of ['lesson', 'schedule', 'invoice']) {
    form(kind)?.addEventListener('submit', async (event) => {
      event.preventDefault();
      const element = event.currentTarget;
      const submit = element.querySelector('[type=submit]');
      const error = element.querySelector('[data-personal-error]');
      submit.disabled = true;
      try { await save(kind, element); }
      catch (caught) {
        error.textContent = caught.message || 'ثبت اطلاعات انجام نشد.';
        error.classList.remove('hidden');
        submit.disabled = false;
      }
    });
  }
})();
