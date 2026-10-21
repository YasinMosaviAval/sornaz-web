(function () {
  'use strict';
  const text = (fa, en) => document.documentElement.lang.startsWith('en') ? en : fa;
  const esc = value => String(value ?? '').replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]);
  const selectedTerm = prefix => (window.studentTermGroups || []).find(row => String(row.key) === document.getElementById(prefix + 'Instrument')?.value);
  window.getStudentTermScheduleHTML = (prefix, term, proposal) => {
    const available = term?.status === 'open' && term?.scheduleDraft && !term.hasSessions;
    const initial = proposal?.termId === Number(term?.id) ? proposal : {};
    const weekday = term?.scheduleDraft?.recurrence?.weekday;
    const weekdays = document.documentElement.lang.startsWith('en') ? ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] : ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'];
    return `<section id="${prefix}TermSchedule" data-term-id="${Number(term?.id || 0)}" class="${available ? '' : 'hidden'} rounded-2xl border p-5 space-y-4">
      <h3 class="font-semibold text-indigo-700">${text('تعیین تاریخ جلسات ترم', 'Schedule term sessions')}</h3>
      ${!term?.scheduleDraft?.classroomId ? `<label class="block text-sm">${text('کلاس برگزاری', 'Classroom')}<select data-student-classroom class="mt-2 w-full rounded-2xl border px-4 py-3" onchange="previewStudentTermSchedule('${prefix}',true)"><option value="">${text('انتخاب کلاس', 'Select classroom')}</option>${(window.studentClassrooms || []).filter(room => Number(room.organizationUserId) === Number(term?.organizationUserId) || (room.organizationUserIds || []).map(Number).includes(Number(term?.organizationUserId))).map(room => `<option value="${Number(room.id)}" ${Number(initial.classroomId) === Number(room.id) ? 'selected' : ''}>${esc(room.name)}</option>`).join('')}</select></label>` : ''}
      <p class="text-sm text-gray-500">${esc(weekdays[weekday] || '')} · ${Number(term?.sessionCount || 0)} ${text('جلسه', 'sessions')} · ${esc(term?.scheduleDraft?.startTime || '')} – ${esc(term?.scheduleDraft?.endTime || '')}</p>
      <p class="text-xs text-gray-500">${text('تاریخ شروع را انتخاب کنید و سپس تاریخ جلسه‌ها را بررسی کنید. تاریخ‌ها باید با روز و دورهٔ برگزاری ترم سازگار باشند. برای ثبت بدون زمان‌بندی این بخش را خالی بگذارید.', 'Choose the first date, then check the session dates. Dates must match the term recurrence. Leave empty to register without scheduling.')}</p>
      <label class="block text-sm">${text('تاریخ جلسهٔ اول', 'First session date')}<input type="date" data-student-first-date class="mt-2 w-full rounded-2xl border px-4 py-3" value="${esc(initial.firstDate || '')}" onchange="previewStudentTermSchedule('${prefix}',true)"></label>
      <div data-student-session-dates>${initial.dates?.length && window.getTermSessionFieldsHTML ? window.getTermSessionFieldsHTML(initial.dates.map(date => ({ date }))) : ''}</div>
      <button type="button" onclick="previewStudentTermSchedule('${prefix}',false)" class="rounded-2xl border px-4 py-3 text-sm">${text('بررسی تاریخ جلسات', 'Check session dates')}</button>
      <div data-student-schedule-result role="status" class="text-sm text-gray-500"></div>
    </section>`;
  };
  window.renderStudentTermSchedule = prefix => {
    const box = document.getElementById(prefix + 'TermSchedule');
    if (!box) return;
    box.outerHTML = window.getStudentTermScheduleHTML(prefix, selectedTerm(prefix));
    window.initLocalizedDateInputs?.(document.getElementById(prefix + 'TermSchedule'));
  };
  window.readStudentTermSchedule = prefix => {
    const box = document.getElementById(prefix + 'TermSchedule');
    if (!box || box.classList.contains('hidden')) return null;
    const firstDate = box.querySelector('[data-student-first-date]').value;
    const classroomId = Number(box.querySelector('[data-student-classroom]')?.value || 0);
    return firstDate || classroomId ? { firstDate, classroomId, dates: [...box.querySelectorAll('input.term-session-date:not([data-jdp])')].map(input => input.value) } : null;
  };
  window.previewStudentTermSchedule = async (prefix, regenerate) => {
    const box = document.getElementById(prefix + 'TermSchedule'), data = window.readStudentTermSchedule(prefix);
    if (!box) return;
    if (!data?.firstDate) {
      box.previewVersion = (box.previewVersion || 0) + 1;
      box.querySelector('[data-student-session-dates]').replaceChildren();
      box.querySelector('[data-student-schedule-result]').textContent = '';
      return;
    }
    if (regenerate) data.dates = [];
    const version = (box.previewVersion || 0) + 1;
    box.previewVersion = version;
    const result = box.querySelector('[data-student-schedule-result]');
    result.textContent = text('در حال بررسی…', 'Checking…');
    try {
      const response = await window.branchRequest(`/academy/admin/terms/${Number(box.dataset.termId)}/student-schedule-preview`, { payload_b64: window.encodeBranchPayload(data) });
      if (!box.isConnected || box.previewVersion !== version || box.querySelector('[data-student-first-date]').value !== data.firstDate) return;
      box.querySelector('[data-student-session-dates]').innerHTML = window.getTermSessionFieldsHTML(response.sessions || []);
      window.initLocalizedDateInputs?.(box);
      const errors = (response.sessions || []).filter(row => row.error).map(row => `${window.localizedDateValue?.(row.date) || row.date}: ${row.error}`);
      result.textContent = errors.length ? errors.join(' · ') : text('تاریخ جلسات قابل ثبت است؛ هنگام ذخیره دوباره بررسی می‌شود.', 'Dates are available and will be checked again when saving.');
    } catch (error) { if (box.isConnected && box.previewVersion === version) result.textContent = error.message; }
  };
})();
